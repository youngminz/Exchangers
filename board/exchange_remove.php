<?php
session_start();
require_once("../config.php");
require_once("../function.php");
if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    header('Location: /login.php?error=session');
    exit;
}

if ($_GET) {
    if (isset($_GET['id'])) {
        $row = fetch_first_row('SELECT * FROM exchange_article WHERE id = ?', 'i', $_GET['id']);
        if ($row === false) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            echo "<h1 style='color: red'>" . T_("SQL 쿼리를 실행하는 도중 오류가 발생했습니다.") . "</h1>";
            exit;
        }
        else {
            if ($row['author'] != $_SESSION['ID']) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                echo "<h1 style='color: red'>" . T_("글을 쓴 사용자만이 지울 수 있습니다.") . "</h1>";
                exit;
            }
            else {
                execute_query('DELETE FROM exchange_article WHERE ID = ?', 'i', $_GET['id']);
                require('../calculate_reputation.php');
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
        }
    }
    else {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        echo "<h1 style='color: red'>" . T_("파라미터가 올바르지 않습니다!") . "</h1>";
    }
}
else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "<h1 style='color: red'>" . T_("파라미터가 올바르지 않습니다!") . "</h1>";
}
