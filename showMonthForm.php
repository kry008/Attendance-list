<?php
require_once 'db.php';
require_once 'checkLogin.php';
@session_start();
/*
CREATE TABLE `obecnosc` (
  `id` int(10) UNSIGNED NOT NULL,
  `kto` int(10) UNSIGNED NOT NULL,
  `data` date NOT NULL,
  `czasZaczecia` time DEFAULT NULL,
  `czasKonca` time DEFAULT NULL,
  `status` int(10) UNSIGNED NOT NULL,
  `zaakceptowane` tinyint(4) NOT NULL DEFAULT 0,
  `zdalne` tinyint(1) NOT NULL DEFAULT 0,
  `aktywne` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `statusy` (
  `id` int(10) UNSIGNED NOT NULL,
  `skrot` varchar(10) NOT NULL,
  `nazwa` text NOT NULL,
  `oznaczaWolne` tinyint(1) NOT NULL DEFAULT 0,
  `aktywne` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE `dniwolne` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `data` DATE NOT NULL , `nazwaSwieta` TEXT NOT NULL , `aktywne` INT(1) NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
*/
// Lp | Numer Dnia | Czas rozpoczęcia - Czas zakończenia | Status (z tabeli statusy XX - Nazwa) | Zdalne | Zaakceptowane
//nagłówek nad tabelą MIESIĄC - ROK
//poniżej ilość dni pracy zdalnej w danym miesiącu
//pokaż w tabeli dany miesiąc, jeżeli na dany dzień nie ma wpisu zostaw puste pole godzina i czas pracy
// dane podawane przez $_GET['month'] = 2023-08
//dla soboty i niedzieli dodaj klasę weekend
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel <?php echo $_SESSION["user_login"]; ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div id="panel">
        <div id="panel">
            <header>
                <h1>Witaj <?php echo $_SESSION["user_imie"]; ?></h1>
            </header>
            <nav>
                <?php
                    require_once 'nav.php';
                ?>
            </nav>
            <main id="monthShow">
                <div id="doPodzialu">
                    <h2>
                        <?php
                        if(isset($_GET['month']))
                        {
                            $mArray = ["Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec","Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień"];
                            $month = $_GET['month'];
                            $month = explode('-', $month);
                            echo $mArray[$month[1]-1]." ".$month[0];

                        }
                        ?>
                    </h2>
                    <div class="addInfo">
                        Imię i nazwisko: <b><?php echo $_SESSION['user_imie']." ".$_SESSION['user_nazwisko']; ?> </b><br>
                        Dział: <b><?php echo $_SESSION['user_dzial_nazwa']; ?></b><br>
                        <?php
                        //sprawdź ilość dni pracy zdalnej w danym miesiącu
                        $sql = "SELECT COUNT(*) FROM obecnosc WHERE kto = :kto AND data LIKE :data AND zdalne = 1 AND aktywne = 1";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
                        //dodaj zero wiodące do miesiąca
                        $m = $month[1];
                        if($m < 10 && strlen($m) < 2)
                        {
                            $m = "0".$m;
                        }
                        $stmt->bindValue(':data', $month[0]."-".$m."%", PDO::PARAM_STR);
                        $stmt->execute();
                        $zdalne = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo "Ilość dni pracy zdalnej: <b>".$zdalne['COUNT(*)']."</b>";
                        //sprawdź czy są dni niezaakceptowane
                        $sql = "SELECT COUNT(*) FROM obecnosc WHERE kto = :kto AND zaakceptowane = 0 AND aktywne = 1 AND data LIKE :data";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindValue(':data', $month[0]."-".$m."%", PDO::PARAM_STR);
                        $stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
                        $stmt->execute();
                        $niezaakceptowane = $stmt->fetch(PDO::FETCH_ASSOC);
                        if($niezaakceptowane['COUNT(*)'] > 0)
                        {
                            ?>
                            <br />
                            <span class="bigError">Masz niezaakceptowane dni pracy</span>
                            <?php
                        }
                        ?>
                    </div>
                    <table class="month">
                        <tr>
                            <th>Dzień</th>
                            <th>Czas pracy</th>
                        </tr>
                        <?php
                        //sprawdź ilość dni w miesiącu, następnie wykonaj pętlę tyle razy ile jest dni w miesiącu
                        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month[1], $month[0]);
                        //pobierz dni wolne na dany miesiąc (o ile są)
                        $sql = "SELECT * FROM dniwolne WHERE data LIKE :data AND aktywne = 1";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindValue(':data', $month[0]."-".$m."%", PDO::PARAM_STR);
                        $stmt->execute();
                        $dniWolne = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $dniWolneArray = [];
                        foreach($dniWolne as $dzienWolny)
                        {
                            $dniWolneArray[] = $dzienWolny['data'];
                        }

                        for($i = 1; $i <= $daysInMonth; $i++)
                        {
                            //sprawdź czy jest wpis w bazie dla danego dnia
                            $sql = "SELECT * FROM obecnosc, statusy WHERE obecnosc.kto = :kto AND obecnosc.data = :data AND obecnosc.aktywne = 1 AND obecnosc.status = statusy.id";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
                            $stmt->bindValue(':data', $month[0]."-".$month[1]."-".$i, PDO::PARAM_STR);
                            $stmt->execute();
                            $work = $stmt->fetch(PDO::FETCH_ASSOC);
                            //sprawdź czy sobota lub niedziela
                            if(date('N', strtotime($month[0]."-".$month[1]."-".$i)) > 5 || in_array($month[0]."-".$month[1]."-".$i, $dniWolneArray))
                            {
                                echo "<tr class='weekend'>";
                            }
                            else
                            {
                                echo "<tr>";
                            }
                            if($work === false)
                            {
                                //nie ma wpisu w bazie
                                echo "<td>".$i."</td>";
                                echo "<td></td>";
                            }
                            else
                            {
                                //jest wpis w bazie
                                echo "<td>".$i;
                                //jeżeli niezaakceptowane ❌
                                if($work['zaakceptowane'] == 0)
                                {
                                    echo " ❌";
                                }
                                else
                                {
                                    echo " ✔️";
                                }
                                echo "</td>";
                                if($work['czasZaczecia'] != NULL && $work['czasKonca'] != NULL)
                                {
                                    echo "<td>".$work['czasZaczecia']." - ".$work['czasKonca']."";
                                    if($work['skrot'] != "OB")
                                    {
                                        echo " - ".$work['skrot']."";
                                    }
                                }
                                else if($work['czasZaczecia'] != NULL && $work['czasKonca'] == NULL)
                                {
                                    echo "<td>".$work['czasZaczecia']." - ";
                                    if($work['skrot'] != "OB")
                                    {
                                        echo " - ".$work['skrot']."";
                                    }
                                }
                                else if($work['czasZaczecia'] == NULL && $work['czasKonca'] != NULL)
                                {
                                    echo "<td> - ".$work['czasKonca']."";
                                    if($work['skrot'] != "OB")
                                    {
                                        echo " - ".$work['skrot']."";
                                    }
                                }
                                else
                                {
                                    echo "<td>".$work['skrot']."";
                                    //
                                }
                                if($work['zdalne'] == 1)
                                {
                                    echo " - PZ";
                                }
                                else
                                {
                                    echo "</td>";
                                }
                            }
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
                <div id="legenda">
                    <a href="showMonthFormPrint.php?month=<?php echo $_GET['month']; ?>" target="_blank">
                        <button>Wydrukuj</button>
                    </a>
                    <a href="showMonth.php">
                        <button>Wróć</button>
                    </a>
                    <?php
                        //Wypisz legendę z tabeli statusy
                        $sql = "SELECT * FROM statusy WHERE aktywne = 1";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        $statusy = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        echo "<h2>Legenda</h2>";
                        echo "<table>";
                        foreach($statusy as $status)
                        {
                            echo "<tr>";
                            echo "<td>".$status['skrot']."</td>";
                            echo "<td>".$status['nazwa']."</td>";
                            echo "</tr>";
                        }
                        echo "</table>";

                    ?>
                </div>
            </main>
        </div>
    </div>
    <?php
    require_once 'footer.php';
    ?>
</body>
</html>
