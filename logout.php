<?php
//usuń wszystkie zmienne
session_unset();
//usuń sesję
session_destroy();
//przekieruj na stronę logowania
header('Location: index.php');
exit();
?>