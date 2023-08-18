<?php
require_once 'db.php';
require_once 'checkLogin.php';
@session_start();
//print_r($_SESSION);
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
*/
$workStarted = 0;
$workEnded = 0;
//sprawdzenie czy dzisiaj już rozpoczęto pracę, czy dziś już zakończono pracę, ustaw odpowiednio zmienne
$sql = "SELECT * FROM obecnosc WHERE kto = :kto AND data = :data AND aktywne = 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindValue(':data', date('Y-m-d'), PDO::PARAM_STR);
$stmt->execute();
$work = $stmt->fetch(PDO::FETCH_ASSOC);
if($work === false)
{
    $workStarted = 0;
    $workEnded = 0;
}
else
{

    if($work['czasZaczecia'] != NULL)
    {
        $workStarted = 1;
    }
    if($work['czasKonca'] != NULL)
    {
        $workEnded = 1;
    }
    $sql = "SELECT * FROM statusy WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $work['status'], PDO::PARAM_INT);
    $stmt->execute();
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    if($status['oznaczaWolne'] == 1)
    {
        $workStarted = 1;
        $workEnded = 1;
    }
}
//sprawdź czy jakiś pracownik czeka na aktywację i jest tego użytkownik przełożonym, ustaw zmienną $uzytkownicyWaiting na ilość
$sql = "SELECT * FROM uzytkownicy WHERE aktywne = 0 AND przelozony = :przelozony";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':przelozony', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$uzytkownicyWaiting = $stmt->rowCount();

//pobierz listę id pracowników których przełożonym jest zalogowany użytkownik
$podlega = array();
$sql = "SELECT id FROM uzytkownicy WHERE przelozony = :przelozony AND aktywne = 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':przelozony', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$podlega = $stmt->fetchAll(PDO::FETCH_ASSOC);
$podlegaIlosc = $stmt->rowCount();
//print_r($podlega);
//sprawdź czy któryś z tych pracowników ma nie zaaakceptowane dni, jeżeli tak to ustaw zmienną $daysWaiting na ilość
$daysWaiting = 0;
foreach($podlega as $podlega)
{
    $sql = "SELECT * FROM obecnosc WHERE kto = :kto AND zaakceptowane = 0 AND aktywne = 1 AND data < :data";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':kto', $podlega['id'], PDO::PARAM_INT);
    $stmt->bindValue(':data', date('Y-m-d'), PDO::PARAM_STR);
    $stmt->execute();
    $daysWaiting += $stmt->rowCount();
}
//print_r($daysWaiting);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel <?php echo $_SESSION["user_login"]; ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div id="panel">
        <header>
            <h1>Witaj <?php echo $_SESSION["user_imie"]; ?></h1>
        </header>
        <nav>
            <?php
                require_once 'nav.php';
            ?>
        </nav>
        <div id="info">
            <?php
            echo @$_SESSION["innfoError"];
            $_SESSION["innfoError"] = "";
            ?>
        </div>
        <main id="start">
            <?php
            if($workStarted == 0)
            {
                ?>
                <a href="workStart.php">
                    <h3>Rozpocznij dzisiejszą pracę</h3>
                </a>
                <?php
            }
            ?>
            <?php
            if($workStarted == 1 && $workEnded == 0)
            {
                ?>
                <a href="workEnd.php">
                    <h3>Zakończ dzisiejszą pracę</h3>
                </a>
                <?php
            }
            $y = date('Y');
            $m = date('m');
            //pokaż miesiąc wcześniej
            if($m == 1)
            {
                $m = 12;
                $y--;
            }
            else
            {
                $m--;
            }
            ?>
            <a href='showMonthForm.php?month=<?php echo $y."-".$m; ?>'>
                <h3>Raport za poprzedni miesiąc</h3>
            </a>
            <a href="showMonth.php">
                <h3>Raport miesięczny</h3>
            </a>
            <a href="addDay.php">
                <h3>Dodaj inny dzień</h3>
            </a>
            <a href="addFreeDay.php">
                <h3>Dodaj wolne (wszystkie formy)</h3>
            </a>
            <a href="editDaySelect.php">
                <h3>Edytuj dzień</h3>
            </a>
            <?php
            if($uzytkownicyWaiting > 0)
            {
                ?>
                <a href="activateWorker.php">
                    <h3>Aktywuj konta pracowników - <?php echo $uzytkownicyWaiting; ?></h3>
                </a>
                <?php
            }
            ?>
            <?php
            if($podlegaIlosc > 0)
            {
                ?>
                <a href="allWorkers.php">
                    <h3>Drukuj listy pracowników</h3>
                </a>
                <?php
            }
            ?>
            <?php
            if($daysWaiting > 0)
            {
                ?>
                <a href="waitingForAcceptsUser_form.php">
                    <h3>Oczekująca ilość dni na akceptację - <?php echo $daysWaiting; ?></h3>
                </a>
                <?php
            }
            ?>
            <?php
            if($_SESSION['user_admin'] == 1)
            {
                ?>
                    <a href="dictionaries.php">
                        <h3>Edytuj słowniki/ustawienia</h3>
                    </a>
                <?php
            }
            ?>
        </main>
    </div>
    <?php
    require_once 'footer.php';
    ?>
</body>
</html>