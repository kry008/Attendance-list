<?php
require_once 'db.php';
require_once 'checkLogin.php';
@session_start();
/*
CREATE TABLE IF NOT EXISTS `dzialy` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `skrot` varchar(25) NOT NULL,
    `nazwa` text NOT NULL,
    `aktywne` int(11) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `skrot` (`skrot`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
*/
if($_SESSION['user_admin'] != 1)
{
    //redirect to panel.php
    $_SESSION["innfoError"] = "Brak uprawnień do tej strony";
    header("Location: panel.php");
    exit();
}
//chodzi o zaaktualizowanie aktywne na 0
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delDepartment']))
{
    $id = $_POST['id'];
    $sql = "UPDATE dzialy SET aktywne = 0 WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $result = $stmt->execute();
    if($result)
    {
        $_SESSION["innfoError"] = "Usunięto dział";
        header("Location: dictionaries.php");
        exit();
    }
    else
    {
        $_SESSION["innfoError"] = "Błąd usuwania działu";
        header("Location: dictionaries.php");
        exit();
    }
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
                <form action="delDepartment.php" method="post">
                    <table class="form">
                        <tr>
                            <td>Wybierz dział</td>
                            <td>
                                <select name="id">
                                    <?php
                                    $sql = "SELECT * FROM dzialy WHERE aktywne = 1";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute();
                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                                    {
                                        echo "<option value='".$row['id']."'>".$row['nazwa']."(".$row['skrot'].")</option>";
                                    }

                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" name="delDepartment" value="Usuń dział"></td>
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