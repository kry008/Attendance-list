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
//pobierz wszystkich pracowników, najpierw wyświetl aktywnych, potem nieaktywnych
$sql = "SELECT * FROM uzytkownicy WHERE aktywne IN (1, 0) ORDER BY aktywne DESC, nazwisko ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$uzytkownicyArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <form action="printWorker.php" method="post">
                    <table class="form">
                        <tr>
                            <td>Wybierz pracownika</td>
                            <td>
                                <select name="id">
                                    <option disabled selected>Wybierz pracownika</option>
                                    <?php
                                    foreach($uzytkownicyArray as $user)
                                    {
                                        $aktywny = "nieaktywny";
                                        if($user['aktywne'] == 1)
                                        {
                                            $aktywny = "aktywny";
                                        }
                                        echo "<option value='".$user['id']."'>".$user['nazwisko']." ".$user['imie']." - (".$aktywny.")</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <!-- wybór miesiąca -->
                        <tr>
                            <td>Wybierz miesiąc</td>
                            <td>
                                <input type="month" name="month" value="<?php echo date('Y-m'); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Pokaż czy są niezaakceptowane dni?: <input type="checkbox" name="notAccepted" value="1">
                            </td>
                        <tr>
                            <td colspan="2"><input type="submit" name="printWorker" value="Pokaż"></td>
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
