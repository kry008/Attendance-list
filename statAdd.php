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
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["statAdd"]))
{
    //sprawdź czy podany skrot i nazwa nie jest już wpisany do bazy
    $skrot = $_POST['skrot'];
    $nazwa = $_POST['nazwa'];
    $sql = "SELECT * FROM statusy WHERE skrot = :skrot OR nazwa = :nazwa";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':skrot', $skrot, PDO::PARAM_STR);
    $stmt->bindValue(':nazwa', $nazwa, PDO::PARAM_STR);
    $stmt->execute();
    $count = $stmt->rowCount();
    if($count > 0)
    {
        $_SESSION['innfoError'] = "Podany skrot lub nazwa jest już w bazie";
        header("Location: statAdd.php");
        exit();
    }
    $oznaczaWolne = 0;
    if(isset($_POST['oznaczaWolne']))
    {
        $oznaczaWolne = 1;
    }
    $aktywne = 0;
    if(isset($_POST['aktywne']))
    {
        $aktywne = 1;
    }
    $sql = "INSERT INTO statusy (skrot, nazwa, oznaczaWolne, aktywne) VALUES (:skrot, :nazwa, :oznaczaWolne, :aktywne)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':skrot', $skrot, PDO::PARAM_STR);
    $stmt->bindValue(':nazwa', $nazwa, PDO::PARAM_STR);
    $stmt->bindValue(':oznaczaWolne', $oznaczaWolne, PDO::PARAM_INT);
    $stmt->bindValue(':aktywne', 1, PDO::PARAM_INT);
    $stmt->execute();
    $_SESSION['innfoError'] = "Dodano status";
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
                <form action="statAdd.php" method="post">
                    <table class="form">
                        <tr>
                            <td>Skrot</td>
                            <td><input type="text" name="skrot" required></td>
                        </tr>
                        <tr>
                            <td>Nazwa</td>
                            <td><input type="text" name="nazwa" required></td>
                        </tr>
                        <tr>
                            <td>Oznacza wolne</td>
                            <td><input type="checkbox" name="oznaczaWolne"></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" name="statAdd" value="Dodaj"></td>
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