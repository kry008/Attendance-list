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
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addDepartment']))
{
    $name = trim($_POST['name']);
    $skrot = trim($_POST['skrot']);
    $sql = "INSERT INTO dzialy (skrot, nazwa) VALUES (:skrot, :nazwa)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':skrot', $skrot, PDO::PARAM_STR);
    $stmt->bindValue(':nazwa', $name, PDO::PARAM_STR);
    $result = $stmt->execute();
    if($result)
    {
        $_SESSION["innfoError"] = "Dodano dział";
        header("Location: dictionaries.php");
        exit();
    }
    else
    {
        $_SESSION["innfoError"] = "Błąd dodawania działu";
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
                <form action="addDepartment.php" method="post">
                    <table class="form">
                        <tr>
                            <td>Nazwa działu</td>
                            <td><input type="text" name="name" required></td>
                        </tr>
                        <tr>
                            <td>Skrót działu</td>
                            <td><input type="text" name="skrot" required></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" name="addDepartment" value="Dodaj"></td>
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