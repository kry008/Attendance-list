<?php

require_once 'db.php';
require_once 'checkLogin.php';
@session_start();

?>

<ol>
    <?php
    if($_SESSION['user_admin'] == 1)
    {
        ?>
            <li><a href="panel.php">Start</a></li>
        <?php
    }
    else
    {
        ?>
            <li><a href="panel.php">🏠</a></li>
        <?php
    }
    ?>
    <li><a href="showMonth.php">Pokaż miesiąc</a></li>
    <li>Twój przełożony - <?php echo $_SESSION['user_przelozony_imie']." ".$_SESSION['user_przelozony_nazwisko']; ?></li>
    <li><a href="pass.php">Zmień hasło</a></li>
    <li><a href="logout.php">Wyloguj</a></li>
</ol>