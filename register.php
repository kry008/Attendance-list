<?php
require_once 'db.php';
@session_start();

/*
CREATE TABLE `uzytkownicy` (
  `id` int(10) UNSIGNED NOT NULL,
  `login` text NOT NULL,
  `haslo` text NOT NULL,
  `imie` text NOT NULL,
  `nazwisko` text NOT NULL,
  `dzial` int(10) UNSIGNED NOT NULL,
  `przelozony` int(10) UNSIGNED DEFAULT NULL,
  `aktywne` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `dzialy` (
  `id` int(10) UNSIGNED NOT NULL,
  `skrot` varchar(25) NOT NULL,
  `nazwa` text NOT NULL,
  `aktywne` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

*/
//Przełożony to id innego użytkownika, domyślnie aktywne = 0 do momentu aż przełożony zaakceptuje lub odrzuci (wówczas aktywne 2)
//Dział to id działu
//pozwól wybrać z list rozwijanych dział i przełożonych


//jeżeli POST
if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    //sprawdź czy wszystkie pola są wypełnione, hasło min 8 znaków, hasło = hasło2, login nie jest zajęty
    $login = $_POST['login'];
    $pass1 = $_POST['password'];
    $pass2 = $_POST['password2'];
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $dzial = $_POST['dzial'];
    $przelozony = $_POST['przelozony'];
    if($pass1 !== $pass2)
    {
        $_SESSION['error'] = "Hasła nie są takie same";
        header("Location: register.php");
        exit();
    }
    if(strlen($pass1) < 8)
    {
        $_SESSION['error'] = "Hasło musi mieć minimum 8 znaków";
        header("Location: register.php");
        exit();
    }
    if(empty($login))
    {
        $_SESSION['error'] = "Login jest wymagany";
        header("Location: register.php");
        exit();
    }
    if(empty($imie))
    {
        $_SESSION['error'] = "Imię jest wymagane";
        header("Location: register.php");
        exit();
    }
    if(empty($nazwisko))
    {
        $_SESSION['error'] = "Nazwisko jest wymagane";
        header("Location: register.php");
        exit();
    }
    if(empty($dzial))
    {
        $_SESSION['error'] = "Dział jest wymagany";
        header("Location: register.php");
        exit();
    }
    if(empty($przelozony))
    {
        $_SESSION['error'] = "Przełożony jest wymagany";
        header("Location: register.php");
        exit();
    }
    //sprawdź czy login jest zajęty
    $sql = "SELECT * FROM uzytkownicy WHERE login = :login";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':login', $login, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if($user !== false)
    {
        $_SESSION['error'] = "Login jest zajęty";
        header("Location: register.php");
        exit();
    }
    //dodaj użytkownika
    $sql = "INSERT INTO uzytkownicy (login, haslo, imie, nazwisko, dzial, przelozony, aktywne) VALUES (:login, sha1(:haslo), :imie, :nazwisko, :dzial, :przelozony, 0)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':login', $login, PDO::PARAM_STR);
    $stmt->bindValue(':haslo', $pass1, PDO::PARAM_STR);
    $stmt->bindValue(':imie', $imie, PDO::PARAM_STR);
    $stmt->bindValue(':nazwisko', $nazwisko, PDO::PARAM_STR);
    $stmt->bindValue(':dzial', $dzial, PDO::PARAM_INT);
    $stmt->bindValue(':przelozony', $przelozony, PDO::PARAM_INT);
    $stmt->execute();
    $_SESSION['error'] = "Użytkownik dodany";
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rejestracja</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div id="panel">
        <div id="panel">
            <header>
                <h1>Witaj</h1>
            </header>
            <main id="register">
                <?php
                    if(@isset($_SESSION['error']))
                    {
                        echo '<p class="error">'.$_SESSION['error'].'</p>';
                        unset($_SESSION['error']);
                    }
                ?>
                <form action="register.php" method="post">
                    <table class="form">
                        <tr>
                            <td>
                                <label for="login">Login</label>
                            </td>
                            <td>
                                <input type="text" name="login" id="login" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="password">Hasło</label>
                            </td>
                            <td>
                                <input type="password" name="password" id="password" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="password">Powtórz hasło</label>
                            </td>
                            <td>
                                <input type="password" name="password2" id="password2" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="imie">Imię</label>
                            </td>
                            <td>
                                <input type="text" name="imie" id="imie" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="nazwisko">Nazwisko</label>
                            </td>
                            <td>
                                <input type="text" name="nazwisko" id="nazwisko" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="dzial">Dział</label>
                            </td>
                            <td>
                                <select name="dzial" id="dzial">
                                    <?php
                                    $sql = "SELECT * FROM dzialy WHERE aktywne = 1";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute();
                                    while($dzial = $stmt->fetch(PDO::FETCH_ASSOC))
                                    {
                                        echo "<option value=\"".$dzial['id']."\">".$dzial['nazwa']."</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="przelozony">Przełożony</label>
                            </td>
                            <td>
                                <select name="przelozony" id="przelozony">
                                    <?php
                                    $sql = "SELECT * FROM uzytkownicy, dzialy WHERE uzytkownicy.aktywne = 1";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute();
                                    while($przelozony = $stmt->fetch(PDO::FETCH_ASSOC))
                                    {
                                        //dopisz skrót działu
                                        echo "<option value=\"".$przelozony['id']."\">".$przelozony['imie']." ".$przelozony['nazwisko']." (".$przelozony['nazwa'].")</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" name="submit" value="Zarejestruj">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button class="cancel" type="button" onclick="window.location.href='index.php'">Anuluj</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </main>
        </div>
    </div>
    <?php
    require_once 'footer.php';
    ?>
</body>
</html>

