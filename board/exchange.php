<?php
session_start();
require_once("../config.php");
require_once("../function.php");
if (!isset($_SESSION["ID"]) || empty($_SESSION["ID"])) {
    echo "<meta http-equiv='refresh' content='0; url=/login.php'>";
    exit;
}

$first_row = fetch_first_row("SELECT COUNT(*) FROM exchange_article WHERE parent_id IS NULL");
$number_of_row = $first_row["COUNT(*)"];

$page = 1;
if (isset($_GET["page"])) {
    $page = (int) $_GET["page"];
}
$total_page_count = $number_of_row % 10 != 0 ? $number_of_row / 10 + 1 : $number_of_row / 10;
if ($page < 1 || $page > $total_page_count) {
    $page = 1;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>:: Exchange Translation ::</title>
    <style>
        table, td, th {border: 1px solid black;border-collapse:collapse;}
    </style>
</head>
<body>
<?php
$result = fetch_all_row("SELECT * FROM exchange_article WHERE parent_id IS NULL ORDER BY id DESC LIMIT ?, 10", "i", ($page - 1) * 10);
?>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>글 제목</th>
        <th>카테고리</th>
        <th>번역 방향</th>
        <th>날짜</th>
        <th>조회수</th>
        <th>Up Vote</th>
        <th>Down Vote</th>
        <th>작성자</th>
    </tr>
    </thead>
    <tbody>
    <?php 
    foreach ($result as $row) { ?>
        <tr>
            <td><?= $row['ID'] ?></td>
            <td>
                <a href="/board/exchange_view.php?id=<?= $row["ID"] ?>"><?= $row['board_title'] ?></a>
            </td>
            <td><?= $row['category'] ?></td>
            <td><?= $row['lang_from'] . ' -> ' . $row['lang_to'] ?></td>
            <td><?= $row['date'] ?></td>
            <td><?= $row['board_hit'] ?></td>
            <td><?= $row['vote_up'] ?></td>
            <td><?= $row['vote_down'] ?></td>
            <td><?= fetch_first_row('SELECT * FROM users WHERE ID = ?', 'i', $row['author'])['user_nickname'] ?></td>
        </tr>
    <?php
    } ?>
    </tbody>
</table>
<?php
for ($i = 1; $i <= $total_page_count; $i++) {
    echo "<a href='/board/exchange.php?page=$i'>$i</a> ";
}
?>
</body>
</html>