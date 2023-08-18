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
//pobierz wszystki użytkwoników którzy zanjdują się w tabeli admini po za obecnie zalogowanym
$sql = "SELECT * FROM uzytkownicy WHERE id IN (SELECT kto FROM admini) AND id != :id AND aktywne = 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$uzytkownicy = $stmt->fetchAll(PDO::FETCH_ASSOC);
//print_r($uzytkownicy);
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delAdmin']))
{
    $user = $_POST['user'];
    $sql = "DELETE FROM admini WHERE kto = :kto";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':kto', $user, PDO::PARAM_INT);
    $stmt->execute();
    $_SESSION['innfoError'] = "Usunięto admina";
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
                <form action="delAdmin.php" method="post">
                    <table class="form">
                        <tr>
                            <td>
                                <label for="user">Wybierz użytkownika</label>
                            </td>
                            <td>
                                <select name="user" id="user">
                                    <?php
                                    foreach($uzytkownicy as $user)
                                    {
                                        ?>
                                        <option value="<?php echo $user['id']; ?>"><?php echo $user['imie']." ".$user['nazwisko']." (".$user['login'].")"; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" name="delAdmin" value="Usuń admina">
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