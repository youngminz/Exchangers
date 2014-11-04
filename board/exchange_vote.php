<?php
session_start();
require_once("../config.php");
require_once("../function.php");
if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    header('Location: /login.php?error=session');
    exit;
}
if ($_GET) {
    if (isset($_GET['type']) && isset($_GET['article']) && !empty($_GET['type']) && !empty($_GET['article'])
       ) {
        $article = fetch_first_row("SELECT * FROM exchange_article WHERE ID = ?", 'i', $_GET['article']);
        if ($_GET['type'] === 'up') {
            $query = "UPDATE exchange_article SET vote_up = vote_up + 1 WHERE ID = ?";
            $query2 = "UPDATE users SET user_reputation = user_reputation + '1' WHERE ID = ?";
        }
        else {
            $query = "UPDATE exchange_article SET vote_down = vote_down - 1 WHERE ID = ?";
            $query2 = "UPDATE users SET user_reputation = user_reputation - '1' WHERE ID = ?";
        }
        $result = execute_query($query, "i", $_GET['article']);
        $result2 = execute_query($query2, "i", $article['author']);
        if ($result === false || $result2 === false) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            echo "<h1 style='color: red'>" . T_("SQL 쿼리를 실행하는 도중 오류가 발생했습니다.") . "</h1>";
            exit;
        }
        else {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
    else {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        echo T_("파라미터가 올바르지 않습니다!");
        exit;
    }
}
else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo T_("파라미터가 올바르지 않습니다!");
    exit;
}
