<?php
require_once 'db.php';
@session_start();

//pobierz listę danych pracowników których przełożonym jest zalogowany użytkownik
$podlega = array();
$sql = "SELECT id, imie, nazwisko FROM uzytkownicy WHERE przelozony = :przelozony AND aktywne = 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':przelozony', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$podlega = $stmt->fetchAll(PDO::FETCH_ASSOC);
//print_r($podlega);
//sprawdź czy któryś z tych pracowników ma nie zaaakceptowane dni
$uzytkownicyWithDaysWaiting = array();
foreach($podlega as $podlega)
{
    $sql = "SELECT * FROM obecnosc WHERE kto = :kto AND zaakceptowane = 0 AND aktywne = 1 AND data < :data";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':kto', $podlega['id'], PDO::PARAM_INT);
    $stmt->bindValue(':data', date('Y-m-d'), PDO::PARAM_STR);
    $stmt->execute();
    $daysWaiting = $stmt->rowCount();
    if($daysWaiting > 0)
    {
        $uzytkownicyWithDaysWaiting[] = $podlega;
    }
}
//print_r($uzytkownicyWithDaysWaiting);
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
                <form action="waitingForAcceptsMonth_form.php" method="post">
                    <table class="form">
                        <tr>
                            <td>
                                <label for="user">Wybierz pracownika:</label>
                            </td>
                            <td>
                                <select name="user" id="user">
                                    <?php
                                    foreach($uzytkownicyWithDaysWaiting as $user)
                                    {
                                        echo '<option value="'.$user['id'].'">'.$user['imie'].' '.$user['nazwisko'].'</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
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