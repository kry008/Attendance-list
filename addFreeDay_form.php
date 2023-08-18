<?php
require_once 'db.php';
require_once 'checkLogin.php';
@session_start();
//CREATE TABLE `dniwolne` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `data` DATE NOT NULL , `nazwaSwieta` TEXT NOT NULL , `aktywne` INT(1) NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;

//w post jest przekazany start i koniec dat, sprawdź czy w tym przedziale (włącznie) nie ma już wpisanej obecności
//jeżeli jest to wyświetl błąd $_SESSION['innfoError']
$start = $_POST['dateFirst'];
$end = $_POST['dateLast'];
//sprawdź czy data końcowa jest późniejsza niż data początkowa
if($end < $start)
{
    $_SESSION['innfoError'] = "Data końcowa nie może być wcześniejsza niż data początkowa";
    header('Location: addFreeDay.php');
    exit();
}
//sprawdź czy id statusu ma oznaczaWolne = 1
$sql = "SELECT * FROM statusy WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $_POST['status'], PDO::PARAM_INT);
$stmt->execute();
$status = $stmt->fetch(PDO::FETCH_ASSOC);
if($status['oznaczaWolne'] != 1)
{
    $_SESSION['innfoError'] = "Wybrany status nie oznacza wolnego";
    header('Location: addFreeDay.php');
    exit();
}
//wpisz do tablicy wszystkie daty, poniedziałek-piątek w danym przedziale
$dates = [];
$day = $start;
while($day <= $end)
{
    //sprawdź czy sobota lub niedziela
    if(date('N', strtotime($day)) < 6)
    {
        $dates[] = $day;
    }
    $day = date('Y-m-d', strtotime($day.' + 1 day'));
}
//sprawdź czy w danym przedziale nie ma już wpisanej obecności
$sql = "SELECT * FROM obecnosc WHERE kto = :kto AND data BETWEEN :start AND :end AND aktywne = 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindValue(':start', $start, PDO::PARAM_STR);
$stmt->bindValue(':end', $end, PDO::PARAM_STR);
$stmt->execute();
$work = $stmt->fetchAll(PDO::FETCH_ASSOC);
if($work !== false)
{
    foreach($work as $day)
    {
        if(in_array($day['data'], $dates))
        {
            $_SESSION['innfoError'] = "W danym przedziale jest już wpisana obecność";
            header('Location: addFreeDay.php');
            exit();
        }
    }
}
//pobierz wszystkie wolne z tego przedziału z tabeli dniwolne
$sql = "SELECT * FROM dniwolne WHERE data BETWEEN :start AND :end AND aktywne = 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':start', $start, PDO::PARAM_STR);
$stmt->bindValue(':end', $end, PDO::PARAM_STR);
$stmt->execute();
$wolne = $stmt->fetchAll(PDO::FETCH_ASSOC);
//sprawdź czy w danym przedziale nie ma już wpisanych dni wolnych, jeżeli tak to usuń je z tablicy $dates
if($wolne !== false)
{
    foreach($wolne as $day)
    {
        if(in_array($day['data'], $dates))
        {
            $key = array_search($day['data'], $dates);
            unset($dates[$key]);
        }
    }
}

//jeżeli nie ma wpisanej obecności w danym przedziale to dodaj wpisy
foreach($dates as $day)
{
    $sql = "INSERT INTO obecnosc VALUES (NULL, :kto, :data, NULL, NULL, :status, 0, 0, 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':data', $day, PDO::PARAM_STR);
    $stmt->bindValue(':status', $_POST['status'], PDO::PARAM_INT);
    $stmt->execute();
}
$_SESSION['innfoError'] = "Wolne zostało dodane";
header('Location: addFreeDay.php');
exit();
?>