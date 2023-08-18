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
                <form action="addFreeDay_form.php" method="post">
                    <table class="form">
                        <tr>
                            <td>
                                <label for="dateFirst">Wybierz pierwszy dzień</label>
                            </td>
                            <td>
                                <input type="date" name="dateFirst" id="dateFirst" value="<?php echo date('Y-m-d'); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="dateLast">Wybierz ostatni dzień</label>
                            </td>
                            <td>
                                <input type="date" name="dateLast" id="dateLast" value="<?php echo date('Y-m-d'); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="status">Status</label>
                            </td>
                            <td>
                                <select name="status" id="status">
                                <option value="0" disabled selected>Wybierz status</option>
                                    <?php
                                    $sql = "SELECT * FROM statusy WHERE oznaczaWolne = 1";
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