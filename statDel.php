<?php
/*
CREATE TABLE IF NOT EXISTS `statusy` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `skrot` varchar(10) NOT NULL,
    `nazwa` text NOT NULL,
    `oznaczaWolne` tinyint(1) NOT NULL DEFAULT 0,
    `aktywne` tinyint(4) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `skrot` (`skrot`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
*/
//nie wyświetlaj statusu o id 1 (zabezpieczony przed usunięciem)
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

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["statDel"]))
{
    //sprawdź czy nie jest to status 1
    $id = $_POST['id'];
    if($id == 1)
    {
        $_SESSION['innfoError'] = "Nie można usunąć tego statusu";
        header("Location: dictionaries.php");
        exit();
    }
    $sql = "UPDATE statusy SET aktywne = 0 WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $_SESSION['innfoError'] = "Usunięto status";
    header("Location: dictionaries.php");
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
                <form action="statDel.php" method="post">
                    <table class="form">
                        <tr>
                            <td>Wybierz status</td>
                            <td>
                                <select name="id">
                                    <?php
                                    $sql = "SELECT * FROM statusy WHERE aktywne = 1 AND id != 1";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute();
                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                                    {
                                        echo "<option value=\"".$row['id']."\">".$row['skrot']." - ".$row['nazwa']."</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" name="statDel" value="Usuń status">
                            </td>
                        </tr>
                        <!-- anuluj -->
                        <tr>
                            <td colspan="2">
                                <button class="cancel" type="button" onclick="window.location.href='dictionaries.php'">Anuluj</button>
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