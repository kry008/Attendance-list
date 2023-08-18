<?php
require_once 'db.php';
require_once 'checkLogin.php';
@session_start();
//sprawdź czy dane zostały przesłane w post, $_POST['date'] a także czy to jest data
if(!(strtotime($_POST["date"])!== false))
{
    $_SESSION['innfoError'] = "Nioeprawidłowa data";
    header("Location: editDaySelect.php");
    exit();
}
//pobierz dane o tym dniu z bazy, połącz z tabelą statusy
$sql = "SELECT * FROM obecnosc, statusy WHERE obecnosc.kto = :kto AND obecnosc.data = :data AND obecnosc.status = statusy.id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kto', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindValue(':data', $_POST['date'], PDO::PARAM_STR);
$stmt->execute();
$work = $stmt->fetch(PDO::FETCH_ASSOC);
//sprawdź czy jest wpis w bazie dla danego dnia
if($work === false)
{
    $_SESSION['innfoError'] = "Brak danych o tym dniu";
    header("Location: editDaySelect.php");
    exit();
}
//sprawdź czy sobota lub niedziela
if(date('N', strtotime($_POST['date'])) > 5)
{
    $_SESSION['innfoError'] = "Nie można edytować weekendu";
    header("Location: editDaySelect.php");
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
                <form action="editDaySelectFormEdit.php" method="post">
                    <table class="form">
                        <tr>
                            <td>
                                <label>Wybrany dzień</label>
                            </td>
                            <td>
                                <input type="date"value="<?php echo $_POST['date']; ?>" disabled>
                                <input type="hidden" name="date" value="<?php echo $_POST['date']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="start">Rozpoczęcie pracy*</label>
                            </td>
                            <td>
                                <input type="time" name="start" id="start" value="<?php echo $work['czasZaczecia']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="end">Zakończenie pracy*</label>
                            </td>
                            <td>
                                <input type="time" name="end" id="end" value="<?php echo $work['czasKonca']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <hr />
                                <label for="clearTime">Zaznacz tutaj aby wyczyścić czas pracy, <br />jeżeli status oznacza wolne zostanie automatycznie czas usunięty</label><br />
                                <input type="checkbox" name="clearTime" id="clearTime">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="status">Status</label>
                            </td>
                            <td>
                                <select name="status" id="status">
                                    <?php
                                    $sql = "SELECT * FROM statusy";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute();
                                    $statusy = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($statusy as $status)
                                    {
                                        if($status['id'] == $work['status'])
                                        {
                                            echo "<option value='".$status['id']."' selected>".$status['skrot']." - ".$status['nazwa']."</option>";
                                        }
                                        else
                                        {
                                            echo "<option value='".$status['id']."'>".$status['skrot']." - ".$status['nazwa']."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <label for="zdalne">Praca zdalna (inna niż okazjonalna)</label>
                                <input type="checkbox" name="zdalne" id="zdalne" <?php if($work['zdalne'] == 1) echo "checked"; ?>>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <input type="submit" value="Edytuj">
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
