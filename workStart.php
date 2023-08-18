<?php
require_once 'db.php';
require_once 'checkLogin.php';
@session_start();
//print_r($_SESSION);
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
                <form action="workStart_form.php" method="post">
                    <table class="form">
                        <tr>
                            <td>
                                <label for="start">Rozpoczęcie pracy</label>
                            </td>
                            <td>
                                <input type="time" name="start" id="start" value="<?php echo date('H:00'); ?>" max="<?php echo date('TH:i'); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label for="remote">Praca zdalna</label>
                                <input type="checkbox" name="remote" id="remote" value="0">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" name="submit" value="Rozpocznij pracę">
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