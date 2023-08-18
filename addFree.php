<?php
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
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addFree']))
{
    //sprawdź czy podany dzień nie jest już wpisany do bazy
    /*
        CREATE TABLE `dniwolne` (
        `id` int(10) UNSIGNED NOT NULL,
        `data` date NOT NULL,
        `nazwaSwieta` text NOT NULL,
        `aktywne` int(1) NOT NULL DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    */

    $data = $_POST['data'];
    //sprawdź czy data jest przyszła
    $today = date("Y-m-d");
    if($data < $today)
    {
        $_SESSION['innfoError'] = "Podana data jest z przeszłości";
        header("Location: dictionaries.php");
        exit();
    }

    $nazwaSwieta = $_POST['nazwaSwieta'];
    $sql = "SELECT * FROM dniwolne WHERE data = :data";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':data', $data, PDO::PARAM_STR);
    $stmt->execute();
    $count = $stmt->rowCount();
    if($count > 0)
    {
        $_SESSION['innfoError'] = "Podany dzień jest już w bazie";
        header("Location: dictionaries.php");
        exit();
    }
    $sql = "INSERT INTO dniwolne (data, nazwaSwieta) VALUES (:data, :nazwaSwieta)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':data', $data, PDO::PARAM_STR);
    $stmt->bindValue(':nazwaSwieta', $nazwaSwieta, PDO::PARAM_STR);
    $stmt->execute();
    $_SESSION['innfoError'] = "Dodano dzień wolny";
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
                <form action="addFree.php" method="post">
                    <table class="form">
                        <tr>
                            <td>Data</td>
                            <td><input type="date" name="data" required min="<?php echo date("Y-m-d"); ?>"></td>
                        </tr>
                        <tr>
                            <td>Nazwa święta</td>
                            <td><input type="text" name="nazwaSwieta" required></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" name="addFree" value="Dodaj"></td>
                        </tr>
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