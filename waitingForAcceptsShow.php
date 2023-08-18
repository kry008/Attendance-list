<?php
require_once 'db.php';
@session_start();

//sprawdź czy zalogowany użytkownik jest przełożonym $_POST["user"]

$sql = "SELECT * FROM uzytkownicy WHERE id = :id AND przelozony = :przelozony";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $_POST["user"], PDO::PARAM_INT);
$stmt->bindValue(':przelozony', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

if($stmt->rowCount() === 0)
{
    $_SESSION["innfoError"] = "Nie jesteś przełożonym tego użytkownika";
    header("Location: panel.php");
    exit();
}
else
{
    //pobierz dane z tabeli uzytkownicy, dzialy
    $sql = "SELECT uzytkownicy.id AS id, login, imie, nazwisko, uzytkownicy.dzial AS dzial, `przelozony`, dzialy.nazwa AS nazwa, dzialy.skrot AS skrot FROM uzytkownicy, dzialy WHERE uzytkownicy.id = :id AND dzialy.id = uzytkownicy.dzial AND uzytkownicy.aktywne = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $_POST["user"], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    //print_r($user);
    //pobierz wszystkie obecności z danego miesiąca
    $sql = "SELECT * FROM obecnosc WHERE kto = :kto AND aktywne = 1 AND data LIKE :data";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':kto', $_POST["user"], PDO::PARAM_INT);
    $stmt->bindValue(':data', $_POST['mm'].'%', PDO::PARAM_STR);
    $stmt->execute();
    $obecnosci = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //print_r($obecnosci);
    //$_POST['mm'] = YYYY-MM
    //pobierz ilość dni w miesiącu
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, explode('-', $_POST['mm'])[1], explode('-', $_POST['mm'])[0]);

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
                        <?php
                        echo @$_SESSION["innfoError"];
                        $_SESSION["innfoError"] = "";
                        ?>
                    </div>
                    Imię i nazwisko: <b><?php echo $user['imie']." ".$user['nazwisko']; ?></b><br>
                    Dział: <b><?php echo $user['nazwa']; ?></b><br>
                    <?php
                    //sprawdź ilość dni pracy zdalnej w danym miesiącu
                    $sql = "SELECT * FROM obecnosc WHERE kto = :kto AND aktywne = 1 AND data LIKE :data AND zdalne = 1";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':kto', $_POST["user"], PDO::PARAM_INT);
                    $stmt->bindValue(':data', $_POST['mm'].'%', PDO::PARAM_STR);
                    $stmt->execute();
                    $zdalnie = $stmt->rowCount();
                    echo "Ilość dni pracy zdalnej: <b>".$zdalnie."</b>";
                    ?>
                    <table class="month">
                        <tr>
                            <th>Dzień</th>
                            <th>Czas pracy</th>
                        </tr>
                        <?php
                        $sql = "SELECT * FROM dniwolne WHERE data LIKE :data AND aktywne = 1";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindValue(':data', $_POST['mm'].'%', PDO::PARAM_STR);
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
                            $day = $_POST['mm']."-".($i < 10 ? "0".$i : $i);
                            $sql = "SELECT * FROM obecnosc, statusy WHERE obecnosc.kto = :kto AND obecnosc.data = :data AND obecnosc.aktywne = 1 AND obecnosc.status = statusy.id";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindValue(':kto', $_POST["user"], PDO::PARAM_INT);
                            $stmt->bindValue(':data', $day, PDO::PARAM_STR);
                            $stmt->execute();
                            $obecnosc = $stmt->fetch(PDO::FETCH_ASSOC);
                            //sprawdź czy sobota lub niedziela
                            $dayOfWeek = date('w', strtotime($day));
                            if($dayOfWeek == 0 || $dayOfWeek == 6)
                            {
                                echo '<tr class="weekend">';
                            }
                            else if(in_array($day, $dniWolneArray))
                            {
                                echo '<tr class="weekend">';
                            }
                            else
                            {
                                echo '<tr>';
                            }
                            //brak wpisu w bazie
                            if($obecnosc === false)
                            {
                                echo '<td>'.$i.'</td>';
                                echo '<td></td>';
                            }
                            else
                            {
                                //jest wpis w bazie
                                echo "<td>".$i;
                                //jeżeli niezaakceptowane ❌
                                if($obecnosc['zaakceptowane'] == 0)
                                {
                                    echo " ❌";
                                }
                                //jeżeli zaakceptowane ✔️
                                else if($obecnosc['zaakceptowane'] == 1)
                                {
                                    echo " ✔️";
                                }
                                echo "</td>";
                                if($obecnosc['czasZaczecia'] != NULL && $obecnosc['czasKonca'] != NULL)
                                {
                                    echo "<td>".$obecnosc['czasZaczecia']." - ".$obecnosc['czasKonca']."";
                                    if($obecnosc['skrot'] != "OB")
                                    {
                                        echo " - ".$obecnosc['skrot']."";
                                    }
                                }
                                else if($obecnosc['czasZaczecia'] != NULL && $obecnosc['czasKonca'] == NULL)
                                {
                                    echo "<td>".$obecnosc['czasZaczecia']." - ";
                                    if($obecnosc['skrot'] != "OB")
                                    {
                                        echo " - ".$obecnosc['skrot']."";
                                    }
                                }
                                else if($obecnosc['czasZaczecia'] == NULL && $obecnosc['czasKonca'] != NULL)
                                {
                                    echo "<td> - ".$obecnosc['czasKonca']."";
                                    if($obecnosc['skrot'] != "OB")
                                    {
                                        echo " - ".$obecnosc['skrot']."";
                                    }
                                }
                                else
                                {
                                    echo "<td>".$obecnosc['skrot']."";
                                    //
                                }
                                if($obecnosc['zdalne'] == 1)
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
                    <div style="height: 20px;"></div>
                    <h3>Opcje</h3>
                    <form action="accept.php" method="post">
                        <input type="hidden" name="user" value="<?php echo $_POST['user']; ?>">
                        <input type="hidden" name="month" value="<?php echo $_POST['mm']; ?>">
                        <button type="submit" name="akceptuj">Akceptuj</button>
                    </form>
                    <form action="print.php" method="post">
                        <input type="hidden" name="user" value="<?php echo $_POST['user']; ?>">
                        <input type="hidden" name="month" value="<?php echo $_POST['mm']; ?>">
                        <button type="submit" name="drukuj">Drukuj</button>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <?php
    require_once 'footer.php';
    ?>
</body>
</html>
<?php
}


//print_r($_POST);