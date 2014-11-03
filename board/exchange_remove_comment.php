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
    echo "<h1 style='color: red'>" . T_("레퍼러가 존재하지 않습니다.") . "</h1>";
}
if (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === false) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "<h1 style='color: red'>" . T_("올바르지 않은 레퍼러입니다.") . "</h1>";
}

if ($_GET) {
    if (isset($_GET['mode']) && isset($_GET['comment'])) {
        if ($_GET['mode'] === 'exchange') {
            $row = fetch_first_row('SELECT * FROM exchange_comment WHERE id = ?', 'i', $_GET['comment']);
            if ($row === false) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                echo "<h1 style='color: red'>" . T_("SQL 쿼리를 실행하던 도중 오류가 발생했습니다.") . "</h1>";
                exit;
            }
            else {
                if ($row['author'] != $_SESSION['ID']) {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                    echo "<h1 style='color: red'>" . T_("댓글을 쓴 사용자만이 지울 수 있습니다.") . "</h1>";
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
            echo "<h1 style='color: red'>" . T_("지원되지 않는 모드입니다.") . "</h1>";
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
