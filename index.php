<?php
session_start();
if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    header('Location: /login.php');
    exit;
}

header('Location: /board/exchange.php');
