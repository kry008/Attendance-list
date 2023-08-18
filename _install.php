<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    require_once 'db.php';
    $sql = "INSERT INTO `dzialy` (`id`, `skrot`, `nazwa`, `aktywne`) VALUES
    (NULL, '".$_POST['departmentShort']."', '".$_POST['department']."', 1);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    //wstaw pierwszego użytkownika
    $sql = "INSERT INTO `users` (`id`, `login`, `haslo`, `imie`, `nazwisko`, `dzial`, `przelozony`, `aktywne`) VALUES
    (NULL, '".$_POST['usernameUser']."', SHA1('".($_POST['passwordUser'])."'), '".$_POST['nameUser']."', '".$_POST['surnameUser']."', 1, 1, 1);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    //usun plik index.php
    unlink("index.html");
    //zamien nazwę index.php.temp na index.php
    rename("index.php.temp", "index.php");
    //przejdź do index.php
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Instalacja</title>
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
                <table class="form">
                    <form method="post" action="_install.php">
                        <tr>
                            <td colspan="2">
                                Skrypt zakłada że baza danych jest uzupełniona plikiem db.sql i config.php jest uzupełniony
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Login pierwszego użytkownika</label>
                            </td>
                            <td>
                                <input type="text" name="usernameUser">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Hasło pierwszego użytkownika</label>
                            </td>
                            <td>
                                <input type="password" name="passwordUser">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Imie</label>
                            </td>
                            <td>
                                <input type="text" name="nameUser">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Nazwisko</label>
                            </td>
                            <td>
                                <input type="text" name="surnameUser">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Nazwa działu</label>
                            </td>
                            <td>
                                <input type="text" name="department">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Skrót działu</label>
                            </td>
                            <td>
                                <input type="text" name="departmentShort" max="5">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" name="install" class="btn" value="Zainstaluj"/>
                            </td>
                        </tr>
                    </form>
                </table>
            </main>
        </div>
    </div>
    <?php
    require_once 'footer.php';
    ?>
</body>
</html>
