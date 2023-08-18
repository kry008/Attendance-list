<?php
require_once 'db.php';
require_once 'checkLogin.php';
@session_start();
//print_r($_POST);
//sprawdź czy dane zostały przesłane w post, data (DD-MM-RRRR), start (HH:MM), end (HH:MM), status (id statusu), zdalne (checkbox), clearTime (checkbox)
//zweryfikuj poprawność wszystkich danych, a także czy dany dzień jest w bazie
if(!(strtotime($_POST["date"])!== false))
{
    $_SESSION['innfoError'] = "Nioeprawidłowa data1";
    header("Location: editDaySelect.php");
    exit();
}
if(!(strtotime($_POST["start"])!== false))
{
    $_SESSION['innfoError'] = "Nioeprawidłowa godzina rozpoczęcia";
    header("Location: editDaySelect.php");
    exit();
}
if(!(strtotime($_POST["end"])!== false))
{
    $_SESSION['innfoError'] = "Nioeprawidłowa godzina zakończenia";
    header("Location: editDaySelect.php");
    exit();
}
//clearTime może być tylko true lub false
$clearTime = false;
if(isset($_POST['clearTime']))
{
    $clearTime = true;
}
$zdalne = false;
if(isset($_POST['zdalne']))
{
    $zdalne = true;
}
//sprawdź czy jest wpis w bazie dla danego dnia
$sql = "SELECT * FROM obecnosc, statusy WHERE obecnosc.kto = :kto AND obecnosc.data = :data AND obecnosc.status = statusy.id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindValue(':data', $_POST['date'], PDO::PARAM_STR);
$stmt->execute();
$work = $stmt->fetch(PDO::FETCH_ASSOC);
//sprawdź czy jest wpis w bazie dla danego dnia
if($work === false)
{
    $_SESSION['innfoError'] = "Brak danych o tym dniu";
    header("Location: editDaySelect.php");
    exit();
}
//jeżeli statusy oznaczaWolne ustaw clearTime na true
$sql = "SELECT * FROM statusy WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $_POST['status'], PDO::PARAM_INT);
$stmt->execute();
$status = $stmt->fetch(PDO::FETCH_ASSOC);
if($status['oznaczaWolne'] == 1)
{
    $clearTime = true;
}
//jeżeli $status['oznaczaWolne'] == 0 to musi być ustawiony czas rozpoczęcia i zakończenia
if($status['oznaczaWolne'] == 0 && $clearTime == true)
{
    $_SESSION['innfoError'] = "Nie można ustawić czasu rozpoczęcia i zakończenia na NULL";
    header("Location: editDaySelect.php");
    exit();
}
//sprawdź czy już jest zaakceptowane, takich nie można edytować
if($work['zaakceptowane'] == 1)
{
    $_SESSION['innfoError'] = "Nie można edytować już zaakceptowanego dnia";
    header("Location: editDaySelect.php");
    exit();
}
//update
$sql = "UPDATE obecnosc SET czasZaczecia = :czasZaczecia, czasKonca = :czasKonca, status = :status, zdalne = :zdalne WHERE kto = :kto AND data = :data";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindValue(':data', $_POST['date'], PDO::PARAM_STR);
$stmt->bindValue(':czasZaczecia', $_POST['start'], PDO::PARAM_STR);
$stmt->bindValue(':czasKonca', $_POST['end'], PDO::PARAM_STR);
$stmt->bindValue(':status', $_POST['status'], PDO::PARAM_INT);
$stmt->bindValue(':zdalne', $zdalne, PDO::PARAM_INT);
$stmt->execute();
//jeżeli clearTime jest true to ustaw czas rozpoczęcia i zakończenia na NULL
if($clearTime)
{
    $sql = "UPDATE obecnosc SET czasZaczecia = NULL, czasKonca = NULL WHERE kto = :kto AND data = :data";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':data', $_POST['date'], PDO::PARAM_STR);
    $stmt->execute();
}
$_SESSION['innfoError'] = "Dane zostały zaktualizowane";
header("Location: editDaySelect.php");
exit();
?>
