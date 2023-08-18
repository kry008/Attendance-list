<?php

require_once 'db.php';
session_start();
/*

CREATE TABLE `uzytkownicy` (
`id` int(10) UNSIGNED NOT NULL,
`login` text NOT NULL,
`haslo` text NOT NULL,
`imie` text NOT NULL,
`nazwisko` text NOT NULL,
`dzial` int(10) UNSIGNED NOT NULL,
`przelozony` int(10) UNSIGNED DEFAULT NULL,
`akrywne` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `dzialy` (
`id` int(10) UNSIGNED NOT NULL,
`skrot` varchar(25) NOT NULL,
`nazwa` text NOT NULL,
`aktywne` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

*/


//validate login
if(isset($_POST['login']))
{
    $login = $_POST['username'];
    $password = $_POST['password'];

    if(empty($login))
    {
        $_SESSION['error'] = "Username is required";
        header("Location: index.php");
        exit();
    }
    else if(empty($password))
    {
        $_SESSION['error'] = "Password is required";
        header("Location: index.php");
        exit();
    }
    else
    {
        $sql = "SELECT uzytkownicy.id AS id, login, imie, nazwisko, uzytkownicy.dzial AS dzial, `przelozony`, dzialy.nazwa AS nazwa, dzialy.skrot AS skrot FROM uzytkownicy, dzialy WHERE uzytkownicy.login = :login AND dzialy.id = uzytkownicy.dzial AND uzytkownicy.aktywne = 1 AND uzytkownicy.haslo = sha1(:password)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user === false)
        {
            $_SESSION['error'] = "Zły login lub hasło lub użytkownik nie zaakceptowany przez przełożonego";
            header("Location: index.php");
            exit();
        }
        else
        {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_login'] = $user['login'];
            $_SESSION['user_imie'] = $user['imie'];
            $_SESSION['user_nazwisko'] = $user['nazwisko'];
            $_SESSION['user_dzial'] = $user['dzial'];
            $_SESSION['user_przelozony'] = $user['przelozony'];
            $sql = "SELECT * FROM uzytkownicy WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $_SESSION['user_przelozony'], PDO::PARAM_INT);
            $stmt->execute();
            $przelozony = $stmt->fetch(PDO::FETCH_ASSOC);
            if($przelozony !== false)
            {
                $_SESSION['user_przelozony_imie'] = $przelozony['imie'];
                $_SESSION['user_przelozony_nazwisko'] = $przelozony['nazwisko'];
            }
            else
            {
                $_SESSION['user_przelozony_imie'] = "Brak";
                $_SESSION['user_przelozony_nazwisko'] = "Przełożonego";
            }
            $_SESSION['user_dzial_nazwa'] = $user['nazwa'];
            $_SESSION['user_dzial_skrot'] = $user['skrot'];
            $_SESSION['user_login_mark'] = 1;
            /*
            CREATE TABLE `admini` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `kto` INT UNSIGNED NOT NULL , `odKiedy` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;
            */
            $sql = "SELECT * FROM admini WHERE kto = :kto";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            if($admin !== false)
            {
                $_SESSION['user_admin'] = 1;
            }
            else
            {
                $_SESSION['user_admin'] = 0;
            }
            header("Location: panel.php");
        }
    }
}