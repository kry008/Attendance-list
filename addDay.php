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
                <div class="error">
                    <?php
                    if(isset($_SESSION['innfoError']))
                    {
                        echo $_SESSION['innfoError'];
                        unset($_SESSION['innfoError']);
                    }
                    ?>
                </div>
                <form action="addDay_form.php" method="post">
                    <table class="form">
                        <tr>
                            <td>
                                <label for="day">Wybierz dzień</label>
                            </td>
                            <td>
                                <input type="date" name="day" id="day" value="<?php echo date('Y-m-d'); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="start">Rozpoczęcie pracy*</label>
                            </td>
                            <td>
                                <input type="time" name="start" id="start">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="end">Zakończenie pracy*</label>
                            </td>
                            <td>
                                <input type="time" name="end" id="end">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="status">Status</label>
                            </td>
                            <td>
                                <select name="status" id="status">
                                    <?php
                                    $sql = "SELECT * FROM statusy";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute();
                                    $statusy = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($statusy as $status)
                                    {
                                        echo "<option value='".$status['id']."'>".$status['skrot']." - ".$status['nazwa']."</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <label for="zdalne">Praca zdalna (inna niż okazjonalna)</label>
                                <input type="checkbox" name="zdalne" id="zdalne" value="1">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                * - opcjonalne przy statusach oznaczających wolne
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" name="submit" value="Dodaj dzień">
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