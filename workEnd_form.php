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

//dane za pomocą post przekazywane $_POST['end']
//sprawdź czy dziś już zaczął pracę, sprawdź czy już dziś nie zakończył pracę, sprawdź czy zakończenie pracy jest później niż rozpoczęcie
$sql = "SELECT * FROM obecnosc WHERE kto = :kto AND data = :data AND aktywne = 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindValue(':data', date('Y-m-d'), PDO::PARAM_STR);
$stmt->execute();
$work = $stmt->fetch(PDO::FETCH_ASSOC);
if($work === false)
{
    $_SESSION['innfoError'] = "Dziś nie rozpoczęto pracy";
    header("Location: panel.php");
    exit();
}
else
{
    if($work['czasZaczecia'] == NULL)
    {
        $_SESSION['innfoError'] = "Dziś nie rozpoczęto pracy";
        header("Location: panel.php");
        exit();
    }
    else
    {
        if($work['czasKonca'] != NULL)
        {
            $_SESSION['innfoError'] = "Dziś już zakończono pracę";
            header("Location: panel.php");
            exit();
        }
        else
        {
            if($_POST['end'] < $work['czasZaczecia'])
            {
                $_SESSION['innfoError'] = "Zakończenie pracy nie może być wcześniej niż rozpoczęcie";
                header("Location: panel.php");
                exit();
            }
            else
            {
                //zakończ pracę
                $sql = "UPDATE obecnosc SET czasKonca = :czasKonca WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':czasKonca', $_POST['end'], PDO::PARAM_STR);
                $stmt->bindValue(':id', $work['id'], PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['innfoError'] = "Zakończono pracę";
                header("Location: panel.php");
                exit();
            }
        }
    }
}