<?php
session_start();
require_once("config.php");
require_once("function.php");
if (!isset($_SESSION["ID"]) || empty($_SESSION["ID"])) {
    header('Location: /login.php?error=session');
    exit;
}

if (strpos($_SERVER['HTTP_REFERER'], "leave_ask.php") === false) {
    header('Location: /');
    exit;
}

execute_query("UPDATE users SET enabled = '0' WHERE ID = ?", 'i', $_SESSION['ID']);

session_start();
session_destroy();
header('Location: /login.php?leave=done');
exit;
