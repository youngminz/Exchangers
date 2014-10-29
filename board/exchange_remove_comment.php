<?php
session_start();
require_once("../config.php");
require_once("../function.php");
if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    header('Location: /login.php?error=session');
    exit;
}

if (!isset($_SERVER['HTTP_REFERER'])) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "<h1 style='color: red'>No referer found</h1>";
}
if (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === false) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "<h1 style='color: red'>Invalid referer found</h1>";
}

if ($_GET) {
    if (isset($_GET['mode']) && isset($_GET['comment'])) {
        if ($_GET['mode'] === 'exchange') {
            $row = fetch_first_row('SELECT * FROM exchange_comment WHERE id = ?', 'i', $_GET['comment']);
            if ($row === false) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                echo "<h1 style='color: red'>Error Processing SQL Query</h1>";
                exit;
            }
            else {
                if ($row['author'] != $_SESSION['ID']) {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                    echo "<h1 style='color: red'>ID Not Match</h1>";
                    exit;
                }
                else {
                    execute_query('UPDATE exchange_comment SET visible = "0" WHERE ID = ?', 'i', $_GET['comment']);
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                }
            }
        }
        else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            echo "<h1 style='color: red'>Not supported mode</h1>";
        }
    }
    else {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        echo "<h1 style='color: red'>Not enough parameters</h1>";
    }
}
else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "<h1 style='color: red'>\$_GET is false</h1>";
}

