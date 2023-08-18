<?php
//print_r($_POST);
$kto = $_POST['user'];
$miesiac = $_POST['month']; //YYYY-MM

require_once 'db.php';
@session_start();
//pobierz dane o pracowniku
$sql = "SELECT * FROM uzytkownicy WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $kto, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
//pobierz dni z tabeli obecnosc dla pracownika
$sql = "SELECT * FROM obecnosc WHERE kto = :kto AND data LIKE :data";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kto', $kto, PDO::PARAM_INT);
$stmt->bindValue(':data', $miesiac.'%', PDO::PARAM_STR);
$stmt->execute();
$days = $stmt->fetchAll(PDO::FETCH_ASSOC);
//pobierz dni wolne z dniwolne
$sql = "SELECT * FROM dniwolne WHERE data LIKE :data";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':data', $miesiac.'%', PDO::PARAM_STR);
$stmt->execute();
$dniWolne = $stmt->fetchAll(PDO::FETCH_ASSOC);
$dniWolneArray = [];
foreach($dniWolne as $dzienWolny)
{
    $dniWolneArray[] = $dzienWolny['data'];
}

$sql = "SELECT skrot FROM dzialy WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $user['dzial'], PDO::PARAM_INT);
$stmt->execute();
$dzial = $stmt->fetch(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html>
<head>
    <title><?php echo $_POST['month']; ?> - ewidencja czasu - <?php echo $user['imie'].' '.$user['nazwisko']; ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body onLoad="window.print()">

    <main id="print">
        <h2 style="font-size: xx-large;">
            <?php
            $mArray = ["Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec","Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień"];
            $month = $_POST['month'];
            $month = explode('-', $month);
            echo $mArray[$month[1]-1]." ".$month[0];
            ?>
        </h2>
        <div>
            Imię i nazwisko: <b><?php echo $user['imie'].' '.$user['nazwisko']; ?></b><br>
            Dział: <b><?php echo $dzial['skrot']; ?></b><br>
            <?php
            //sprawdź ilość dni pracy zdalnej w danym miesiącu
            $sql = "SELECT * FROM obecnosc WHERE kto = :kto AND data LIKE :data AND zdalne = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':kto', $kto, PDO::PARAM_INT);
            $stmt->bindValue(':data', $miesiac.'%', PDO::PARAM_STR);
            $stmt->execute();
            $zdalnie = $stmt->rowCount();
            ?>
            Ilość dni pracy zdalnej: <b><?php echo $zdalnie; ?></b>
            <?php
            $sql = "SELECT COUNT(*) FROM obecnosc WHERE kto = :kto AND zaakceptowane = 0 AND aktywne = 1 AND data LIKE :data";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':kto', $kto, PDO::PARAM_INT);
            $stmt->bindValue(':data', $miesiac.'%', PDO::PARAM_STR);
            $stmt->execute();
            $niezaakceptowane = $stmt->fetch(PDO::FETCH_ASSOC);
            if($niezaakceptowane['COUNT(*)'] > 0)
            {
                ?>
                <br />
                <span class="bigError">Są niezaakceptowane dni pracy</span>
                <?php
            }
            ?>
            </div>
            <table class="month" style="margin: 0 0;">
                <tr>
                    <th>Dzień</th>
                    <th>Czas pracy</th>
                </tr>
            <?php
            //sprawdź ilość dni w miesiącu, następnie wykonaj pętlę tyle razy ile jest dni w miesiącu
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month[1], $month[0]);
            for($i = 1; $i <= $daysInMonth; $i++)
            {
                
                //sprawdź czy jest wpis w bazie dla danego dnia
                $sql = "SELECT * FROM obecnosc, statusy WHERE obecnosc.kto = :kto AND obecnosc.data = :data AND obecnosc.aktywne = 1 AND obecnosc.status = statusy.id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':kto', $kto, PDO::PARAM_INT);
                $stmt->bindValue(':data', $month[0].'-'.$month[1].'-'.$i, PDO::PARAM_STR);
                $stmt->execute();
                $work = $stmt->fetch(PDO::FETCH_ASSOC);
                //sprawdź czy to sobota czy niedziela lub czy to dzień wolny
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
    //przejdź do panel.php po 5 sekundach
    setTimeout(function(){ window.location.href = "panel.php"; }, 5000);
</script>
</html>