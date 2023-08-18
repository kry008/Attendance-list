<?php
require_once 'db.php';
require_once 'checkLogin.php';
@session_start();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel <?php echo $_SESSION["user_login"]; ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
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
            <form action="editPass.php" method="post">
                <input type="hidden" name="username" value="<?php echo $_SESSION['user_login']; ?>">
                <table class="form">
                    <tr>
                        <td>
                            <label for="oldPass">Stare hasło:</label>
                        </td>
                        <td>
                            <input type="password" name="oldPass" id="oldPass">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="newPass">Nowe hasło:</label>
                        </td>
                        <td>
                            <input type="password" name="newPass" id="newPass">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="newPass2">Powtórz nowe hasło:</label>
                        </td>
                        <td>
                            <input type="password" name="newPass2" id="newPass2">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" name="change" value="Zmień hasło">
                        </td>
                    </tr>
                </table>
            </form>        
        </main>
    </div>
    <?php
    require_once 'footer.php';
    ?>
</body>
</html>