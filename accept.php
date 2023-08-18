<?php
require_once 'db.php';
@session_start();
//sprawdź czy użytkownik jest przełożonym $_POST['user'], jeżeli tak to dal danej daty $_POST['mm'] (YYYY-MM) ustaw obecności $_POST['user'] na zaakceptowane na 1
//jeżeli nie to $_SESSION["innfoError"] = "Nie jesteś przełożonym tego użytkownika" i wróć do panel.php
$sql = "SELECT * FROM uzytkownicy WHERE id = :id AND przelozony = :przelozony";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $_POST['user'], PDO::PARAM_INT);
$stmt->bindValue(':przelozony', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if($user === false)
{
    $_SESSION["innfoError"] = "Nie jesteś przełożonym tego użytkownika";
    header("Location: panel.php");
    exit();
}
else
{
    $sql = "UPDATE obecnosc SET zaakceptowane = 1 WHERE kto = :kto AND data LIKE :data";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':kto', $_POST['user'], PDO::PARAM_INT);
    $stmt->bindValue(':data', $_POST['month'].'%', PDO::PARAM_STR);
    $stmt->execute();
    $_SESSION["innfoError"] = "Zaakceptowano dni";
    header("Location: panel.php");
    exit();
}