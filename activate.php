<?php
//activate.php?id=ID
require_once 'db.php';  
@session_start();
if($_SERVER['REQUEST_METHOD'] === 'GET')
{
    if(isset($_GET['id']))
    {
        $id = $_GET['id'];
        $sql = "UPDATE uzytkownicy SET aktywne = 1 WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['innfoError'] = "Aktywowano użytkownika";
        header("Location: activateWorker.php");
        exit();
    }
    else
    {
        $_SESSION['innfoError'] = "Nie podano id użytkownika";
        header("Location: activateWorker.php");
        exit();
    }
}
else
{
    $_SESSION['innfoError'] = "Nie podano id użytkownika";
    header("Location: activateWorker.php");
    exit();
}
?>