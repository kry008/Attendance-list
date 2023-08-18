<?php
require_once 'db.php';
require_once 'checkLogin.php';
@session_start();
if($_SESSION['user_admin'] != 1)
{
    //redirect to panel.php
    $_SESSION["innfoError"] = "Brak uprawnień do tej strony";
    header("Location: panel.php");
    exit();
}
$temp = "abcdedfghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789.!@#$%^&*()-?";
//stwórz 10 znakowe hasło
$haslo = substr(str_shuffle($temp), 0, 11);
//echo $haslo;

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user']) && !empty($_POST['user']))
{
    //UPDATE uzytkownicy SET haslo = SHA1(:haslo) WHERE id = :id"
    $user = $_POST['user'];
    $sql = "UPDATE uzytkownicy SET haslo = SHA1(:haslo) WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':haslo', $haslo, PDO::PARAM_STR);
    $stmt->bindValue(':id', $user, PDO::PARAM_INT);
    $stmt->execute();
    //$_SESSION["innfoError"] = "Hasło użytkownika zostało zresetowane";
}
else
{
    $_SESSION["innfoError"] = "Błąd przetwarzania danych";
    header("Location: passReset.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel <?php echo $_SESSION["user_login"]; ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div id="panel">
        <div id="panel">
            <header>
                <h1>Witaj <?php echo $_SESSION["user_imie"]; ?></h1>
            </header>
            <nav>
                <?php
                    require_once 'nav.php';
                ?>
            </nav>
            <main id="work">

                <div class="error">
                    <?php
                    if(isset($_SESSION['innfoError']))
                    {
                        echo $_SESSION['innfoError'];
                        unset($_SESSION['innfoError']);
                    }
                    ?>
                </div>
                <table class="form">
                    <tr>
                        <td colspan="2">
                            <h3>Hasło użytkownika zostało zresetowane</h3>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Nowe hasło:
                        </td>
                        <td>
                            <?php
                            echo $haslo;
                            ?>
                        </td>
                    <tr>
                        <td colspan="2">
                            <a href="passReset.php">
                                <h3 style="text-align: center;">Wróć</h3>
                            </a>
                        </td>
                    </tr>
                </table>
            </main>
        </div>
    </div>
    <?php
    require_once 'footer.php';
    ?>
</body>
</html>