<?php
require_once 'db.php';
@session_start();
//sprawdź czy użytkownik jest przełożonym tego pracownik $_POST["user"]
$sql = "SELECT * FROM uzytkownicy WHERE id = :id AND przelozony = :przelozony";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $_POST["user"], PDO::PARAM_INT);
$stmt->bindValue(':przelozony', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if($user === false)
{
    $_SESSION["innfoError"] = "Nie jesteś przełożonym tego pracownika";
    header("Location: panel.php");
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
                <form action="waitingForAcceptsShow.php" method="post">
                    <table class="form">
                        <tr>
                            <td>
                                <label for="mm">Pokaż miesiąc:</label>
                            </td>
                            <td>
                                <input type="month" name="mm" id="mm" value="<?php echo date('Y-m'); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" name="user" value="<?php echo $_POST['user']; ?>">
                                <input type="submit" name="show" value="Pokaż">
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