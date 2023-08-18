<?php
@session_start();
require_once 'db.php';
if($_SESSION['user_login_mark'] != 1)
{
    header("Location: index.php");
    exit();
}