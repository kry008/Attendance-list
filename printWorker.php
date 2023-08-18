<?php
//Array ( [id] => 1 [month] => 2023-08 [printWorker] => Pokaż )
//notAccepted - checkbox
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
CREATE TABLE IF NOT EXISTS `uzytkownicy` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `login` text NOT NULL,
    `haslo` text NOT NULL,
    `imie` text NOT NULL,
    `nazwisko` text NOT NULL,
    `dzial` int(10) UNSIGNED NOT NULL,
    `przelozony` int(10) UNSIGNED DEFAULT NULL,
    `aktywne` int(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `dzial` (`dzial`),
    KEY `przelozony` (`przelozony`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
*/
require_once 'db.php';
require_once 'checkLogin.php';
@session_start();
if($_SESSION['user_admin'] != 1 && $_POST["printWorker"] != "Pokaż")
{
    //redirect to panel.php
    $_SESSION["innfoError"] = "Brak uprawnień do tej strony";
    header("Location: panel.php");
    exit();
}
//sprawdź czy istnieje
$sql = "SELECT * FROM uzytkownicy WHERE aktywne IN (1, 0) AND id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
$stmt->execute();
$count = $stmt->rowCount();
if($count == 0)
{
    $_SESSION['innfoError'] = "Nie ma takiego pracownika";
    header("Location: printWorkerForm.php");
    exit();
}
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$year = substr($_POST['month'], 0, 4);
$month = substr($_POST['month'], 5, 2);
//pobierz skrót działu
$sql = "SELECT skrot FROM dzialy WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $user['dzial'], PDO::PARAM_INT);
$stmt->execute();
$dzial = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $month." - ".$year; ?> - ewidencja czasu - <?php echo $user['imie']." ".$user['nazwisko']; ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body onLoad="window.print()">
    <main id="print">
        <h2 style="font-size: xx-large;">
            <?php
            $mArray = ["Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec","Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień"];
            echo $mArray[$month-1]." ".$year;
            ?>
        </h2>
        <div>
            Imię i nazwisko: <b><?php echo $user['imie']." ".$user['nazwisko']; ?> </b><br>
            Dział: <b><?php echo $dzial['skrot']; ?></b><br>
            <?php
            //sprawdź ilość dni pracy zdalnej w danym miesiącu
            $sql = "SELECT COUNT(*) FROM obecnosc WHERE kto = :kto AND data LIKE :data AND zdalne = 1 AND aktywne = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':kto', $user['id'], PDO::PARAM_INT);
            //dodaj zero wiodące do miesiąca
            $m = $month;
            if($m < 10 && strlen($m) < 2)
            {
                $m = "0".$m;
            }
            $stmt->bindValue(':data', $year."-".$m."%", PDO::PARAM_STR);
            $stmt->execute();
            $zdalne = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Ilość dni pracy zdalnej: <b>".$zdalne['COUNT(*)']."</b>";
            $notAccepted = 0;
            if(isset($_POST['notAccepted']))
            {
                $notAccepted = 1;
            }
            if($notAccepted == 1)
            {
                //sprawdź czy są dni niezaakceptowane
                $sql = "SELECT COUNT(*) FROM obecnosc WHERE kto = :kto AND zaakceptowane = 0 AND aktywne = 1 AND data LIKE :data";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':data', $year."-".$m."%", PDO::PARAM_STR);
                $stmt->bindValue(':kto', $user['id'], PDO::PARAM_INT);
                $stmt->execute();
                $niezaakceptowane = $stmt->fetch(PDO::FETCH_ASSOC);
                if($niezaakceptowane['COUNT(*)'] > 0)
                {
                    ?>
                    <br />
                    <span class="bigError">Są niezaakceptowane dni pracy</span>
                    <?php
                }
            }
            ?>
        </div>
        <table class="month" style="margin: 0 0;">
            <tr>
                <th>Dzień</th>
                <th>Czas pracy</th>
            </tr>
            <?php
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $sql = "SELECT * FROM dniwolne WHERE data LIKE :data AND aktywne = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':data', $year."-".$m."%", PDO::PARAM_STR);
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
                $stmt->bindValue(':data', $year."-".$m."-".$i, PDO::PARAM_STR);
                $stmt->bindValue(':kto', $user['id'], PDO::PARAM_INT);
                $stmt->execute();
                $stmt->execute();
                $work = $stmt->fetch(PDO::FETCH_ASSOC);
                if(date('N', strtotime($month[0]."-".$month[1]."-".$i)) > 5 || in_array($year."-".$m."-".$i, $dniWolneArray))
                {
                    echo "<tr class=\"weekend\">";
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
                    echo "<td>".$i."</td>";
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
    </main>
    <?php
    require_once 'footer.php';
    ?>
</body>
<script>
    //Przejdź do strony printWorkerForm.php po 2 s
    setTimeout(function(){
        window.location.href = 'printWorkerForm.php';
    }, 2000);
</script>
</html>

