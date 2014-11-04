<?php
require_once("config.php");
require_once("function.php");

$all_row = fetch_all_row("SELECT * FROM exchange_article");

$users = fetch_all_row("SELECT * FROM users");

foreach ($users as $user) {
    execute_query("UPDATE users SET user_reputation = 0 WHERE ID = ?", 'i', $user['ID']);
    //echo "Done clear user ID " . $user['ID'] . "<br />";
}

foreach ($all_row as $row) {
    execute_query("UPDATE users SET user_reputation = user_reputation + ? WHERE ID = ?",
                  'ii', $row['vote_up'] + $row['vote_down'], $row['author']);
    //echo "Done calculating article ID " . $row['ID'] . "<br />";
}
