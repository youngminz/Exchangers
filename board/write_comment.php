<?php
session_start();
require_once("../config.php");
require_once("../function.php");
if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    header('Location: /login.php?error=session');
    exit;
}
if ($_POST) {
    if (isset($_POST['mode']) && isset($_POST['parent_id']) && 
        isset($_POST['text']) && isset($_POST['parent_article']) &&
        !empty($_POST['mode']) && !empty($_POST['parent_id']) && 
        !empty($_POST['text']) && !empty($_POST['parent_article']) && 
        $_POST['mode'] === 'exchange'
       ) {
        if ($_POST['parent_id'] == 'NULL') {
            $_POST['parent_id'] = NULL;
        }
        $query = "INSERT INTO exchange_comment VALUES (NULL, ?, DEFAULT, ?, 1, ?, ?)";
        $result = execute_query($query, "siii", htmlspecialchars($_POST['text']), 
                                $_SESSION['ID'], $_POST['parent_id'], $_POST['parent_article']);
        if ($result === false) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            echo "<h1 style='color: red'>Error Processing SQL Query</h1>";
            exit;
        }
        else {
            // Success!
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
    else {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        echo "<h1 style='color: red'>Incorrect Parameter</h1>";
        exit;
    }
}
else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "<h1 style='color: red'>Incorrect Parameter</h1>";
    exit;
}
// post parameter: mode=exchange, parent_id, contents, parent_article, 