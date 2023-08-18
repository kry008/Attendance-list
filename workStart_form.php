<?php

require_once 'db.php';
require_once 'checkLogin.php';
@session_start();

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

//dane za pomocą post przekazywane $_POST['start']

//sprawdź czy już nie jest już obecność rozpoczęta
$sql = "SELECT * FROM obecnosc WHERE kto = :kto AND data = :data AND aktywne = 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindValue(':data', date('Y-m-d'), PDO::PARAM_STR);
$stmt->execute();
$work = $stmt->fetch(PDO::FETCH_ASSOC);
//<input type="checkbox" name="remote" id="remote" value="0">
$remoteWork = 0;
if(isset($_POST['remote']))
{
    $remoteWork = 1;
}
if($work === false)
{
    //rozpocznij pracę
    $sql = "INSERT INTO obecnosc (kto, data, czasZaczecia, status, zdalne, aktywne) VALUES (:kto, :data, :czasZaczecia, :status, :zdalne, 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':data', date('Y-m-d'), PDO::PARAM_STR);
    $stmt->bindValue(':czasZaczecia', $_POST['start'], PDO::PARAM_STR);
    $stmt->bindValue(':status', 1, PDO::PARAM_INT);
    $stmt->bindValue(':zdalne', $remoteWork, PDO::PARAM_INT);
    $stmt->execute();
    $_SESSION['innfoError'] = "Rozpoczęto pracę";
    header("Location: panel.php");
    exit();
}
else
{
    
    $_SESSION['innfoError'] = "Dziś już rozpoczęto pracę";
    header("Location: panel.php");
    exit();
    
}