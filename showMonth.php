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
                <form action="showMonthForm.php" method="get">
                    <table class="form">
                        <tr>
                            <td>
                                <label for="month">Pokaż miesiąc</label>
                            </td>
                            <td>
                                <input type="month" name="month" id="month" value="<?php echo date('Y-m'); ?>" max="<?php echo date('Y-m'); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" name="submit" value="Pokaż miesiąc">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button class="cancel" type="button" onclick="window.location.href='panel.php'">Anuluj</button>
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