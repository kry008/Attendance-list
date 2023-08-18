<?php
require_once 'db.php';
@session_start();
//wypisz wszystki użytkowników którzy mają status aktywne 0 i przelozony = id zalogowanego użytkownika, jeżeli nie ma takich to $_SESSION["innfoError"] = "Brak pracowników do aktywacji" i wróć do panel.php
$sql = "SELECT * FROM uzytkownicy WHERE aktywne = 0 AND przelozony = :przelozony";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':przelozony', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$uzytkownicy = $stmt->fetchAll(PDO::FETCH_ASSOC);
if($uzytkownicy === false)
{
    $_SESSION["innfoError"] = "Brak pracowników do aktywacji";
    header("Location: panel.php");
    exit();
}
else
{
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
        <main id="uzytkownicy">
            <table>
                <tr>
                    <th>ID</th>
                    <th>IMIĘ</th>
                    <th>NAZWISKO</th>
                    <th>SKRÓT DZIAŁU</th>
                    <th>AKTYWOWAĆ?</th>
                </tr>

            <?php
            $i = 0;
            foreach($uzytkownicy as $user)
            {
                $i++;
                echo '<tr>';
                echo '<td>'.$user['id'].'</td>';
                echo '<td>'.$user['imie'].'</td>';
                echo '<td>'.$user['nazwisko'].'</td>';
                $sql = "SELECT * FROM dzialy WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':id', $user['dzial'], PDO::PARAM_INT);
                $stmt->execute();
                $dzial = $stmt->fetch(PDO::FETCH_ASSOC);
                echo '<td>'.$dzial['skrot'].'</td>';
                echo '<td><a href="activate.php?id='.$user['id'].'"><button>Aktywuj</button></a><br /><a href="delUser.php?id='.$user['id'].'"><button>Usuń</button></a></td>';
                echo '</tr>';
            }
            if($i == 0)
            {
                $_SESSION["innfoError"] = "Brak pracowników do aktywacji";
                header("Location: panel.php");
                exit();
            }   
}
?>
                </table>
        </main>
    </div>
    <?php
    require_once 'footer.php';
    ?>
</body>
</html>