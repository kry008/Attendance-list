<?php

require_once 'db.php';
require_once 'checkLogin.php';
@session_start();

//sprawdź czy to jest metodą post
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    //sprawdź czy hasła są takie same
    if($_POST['newPass'] != $_POST['newPass2'])
    {
        $_SESSION['innfoError'] = "Nowe hasła nie są takie same";
        header("Location: pass.php");
        exit();
    }
    //sprawdź czy hasło jest dłuższe niż 8 znaków
    if(strlen($_POST['newPass']) < 8)
    {
        $_SESSION['innfoError'] = "Nowe hasło jest za krótkie";
        header("Location: pass.php");
        exit();
    }
    //sprawdź czy hasło jest różne od starego
    if($_POST['newPass'] == $_POST['oldPass'])
    {
        $_SESSION['innfoError'] = "Nowe hasło musi być różne od starego";
        header("Location: pass.php");
        exit();
    }
    //sprawdź czy stare hasło jest poprawne
    $sql = "SELECT * FROM uzytkownicy WHERE id = :id AND haslo = SHA1(:haslo)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':haslo', $_POST['oldPass'], PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if($user === false)
    {
        $_SESSION['innfoError'] = "Stare hasło jest niepoprawne";
        header("Location: pass.php");
        exit();
    }
    else
    {
        //zmień hasło
        $sql = "UPDATE uzytkownicy SET haslo = :haslo WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':haslo', sha1($_POST['newPass']), PDO::PARAM_STR);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['innfoError'] = "Hasło zostało zmienione";
        header("Location: pass.php");
        exit();
    }
}
else
{
    $_SESSION['innfoError'] = "Niepoprawne wywołanie strony";
    header("Location: pass.php");
    exit();
}
?>
