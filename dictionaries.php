<?php
require_once 'db.php';
@session_start();

if($_SESSION['user_admin'] != 1)
{
    //redirect to panel.php
    $_SESSION["innfoError"] = "Brak uprawnień do tej strony";
    header("Location: panel.php");
    exit();
}

$sql = "SELECT * FROM uzytkownicy WHERE id NOT IN (SELECT kto FROM admini) AND aktywne = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$uzytkownicy = $stmt->fetchAll(PDO::FETCH_ASSOC);
//print_r($uzytkownicy);
$countuzytkownicy = $stmt->rowCount();
//policz adminów
$sql = "SELECT * FROM admini";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
$countAdmins = $stmt->rowCount();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel <?php echo $_SESSION["user_login"]; ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div id="panel">
        <header>
            <h1>Witaj <?php echo $_SESSION["user_imie"]; ?></h1>
        </header>
        <nav>
            <?php
                require_once 'nav.php';
            ?>
        </nav>
        <div id="info">
            <?php
            echo @$_SESSION["innfoError"];
            $_SESSION["innfoError"] = "";
            ?>
        </div>
        <main id="start">
            <a href="panel.php">
                <h3>Wróć</h3>
            </a>
            <a><h3>Nie usuwasz danych, tylko je dezaktywujesz (nie będzie możliwości ich wyboru)</h3></a>
            <?php
            if($countuzytkownicy > 0)
            {
                ?>
            <a href="addAdmin.php">
                <h3>Dodanie admina</h3>
            </a>
                <?php
            }
            ?>
            <?php
            if($countAdmins > 1)
            {
                ?>
            <a href="delAdmin.php">
                <h3>Usunięcie admina</h3>
            </a>
                <?php
            }
            ?>
            <a href="addDepartment.php">
                <h3>Dodanie działu</h3>
            </a>
            <a href="delDepartment.php">
                <h3>Usunięcie działu</h3>
            </a>
            <a href="addFree.php">
                <h3>Dodanie wolnego</h3>
            </a>
            <a href="statAdd.php">
                <h3>Dodaj status</h3>
            </a>
            <a href="statDel.php">
                <h3>Usuń status</h3>
            </a>
            <a href="printWorkerForm.php">
                <h3>Wydrukuj listy pracownika</h3>
            </a>
            <a href="passReset.php">
                <h3>Resetuj hasło użytkownika</h3>
            </a>
        </main>
    </div>
    <?php
    require_once 'footer.php';
    ?>
</body>
</html>