<?php
require_once('config.php');
$pdo = new PDO('mysql:host='.$host.';dbname='.$db.';charset=utf8',$dbUser,$dbPassword);
if(!$pdo) {
    die('Could not connect');
}
?>