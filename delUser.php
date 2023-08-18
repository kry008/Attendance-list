<?php
//delUser.php?id=ID
//zamień aktywne na 2
require_once 'db.php';
@session_start();
if($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $id = $_GET['id'];
    $sql = "UPDATE uzytkownicy SET aktywne = 2 WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $_SESSION["innfoError"] = "Użytkownik został usunięty";
    header("Location: activateWorker.php");
    exit();
}
else
{
    $_SESSION["innfoError"] = "Niepoprawne dane";
    header("Location: activateWorker.php");
    exit();
}