<?php
require_once 'db.php';
require_once 'checkLogin.php';
@session_start();

//sprawdź czy ten dzień jeszcze nie był dodany
$sql = "SELECT * FROM obecnosc WHERE kto = :kto AND data = :data AND aktywne = 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindValue(':data', $_POST['day'], PDO::PARAM_STR);
$stmt->execute();
$work = $stmt->fetch(PDO::FETCH_ASSOC);
if($work !== false)
{
    $_SESSION['innfoError'] = "Ten dzień już został dodany!";
    header('Location: addDay.php');
    exit();
}
//sprawdź czy podano godzinę rozpoczęcia i zakończenia pracy, jeżeli nie to ustaw NULL
if($_POST['start'] == "")
{
    $start = NULL;
}
else
{
    $start = $_POST['start'];
}
if($_POST['end'] == "")
{
    $end = NULL;
}
else
{
    $end = $_POST['end'];
}
//jeżeli status oznaczaWolne == 0 to sprawdź czy jest podana godzina rozpoczęcia i zakończenia pracy, zakończenie musi być większe od rozpoczęcia
$sql = "SELECT * FROM statusy WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $_POST['status'], PDO::PARAM_INT);
$stmt->execute();
$status = $stmt->fetch(PDO::FETCH_ASSOC);
if($status['oznaczaWolne'] == 0)
{
    if($_POST['start'] == "" || $_POST['end'] == "")
    {
        $_SESSION['innfoError'] = "Musisz podać godzinę rozpoczęcia i zakończenia pracy!";
        header('Location: addDay.php');
        exit();
    }
    if($_POST['start'] >= $_POST['end'])
    {
        $_SESSION['innfoError'] = "Godzina zakończenia pracy musi być większa od godziny rozpoczęcia pracy!";
        header('Location: addDay.php');
        exit();
    }
}
else
{
    $end = NULL;
    $start = NULL;
}
//jeżeli status oznaczaWolne == 1 to praca zdalna zawsze będzie 0
if($status['oznaczaWolne'] == 1)
{
    $zdalne = 0;
}
else
{
    $zdalne = $_POST['zdalne'];
}
//dodaj dzień do bazy
$sql = "INSERT INTO obecnosc VALUES (NULL, :kto, :data, :start, :end, :status, 0, :zdalne, 1)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindValue(':data', $_POST['day'], PDO::PARAM_STR);
$stmt->bindValue(':start', $start, PDO::PARAM_STR);
$stmt->bindValue(':end', $end, PDO::PARAM_STR);
$stmt->bindValue(':status', $_POST['status'], PDO::PARAM_INT);
$stmt->bindValue(':zdalne', $zdalne, PDO::PARAM_INT);
$stmt->execute();
$_SESSION['innfoError'] = "Dzień został dodany!";
header('Location: addDay.php');
exit();


?>