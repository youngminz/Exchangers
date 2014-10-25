<?php
session_start();
require_once('../config.php');
require_once('../function.php');
if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    echo "<meta http-equiv='refresh' content='0; url=/login.php'>";
    exit;
}
$page = 1;
if (isset($_GET['page'])) {
    $page = (int) $_GET['page'];
    if (!is_int($page)) {
        $page = 1;
    }
}

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_errno) {
    echo "<h1>데이터베이스에 연결하던 도중 오류가 발생했습니다.</h1>";
    exit;
}

$first_row = fetch_first_row("SELECT COUNT(*) FROM exchange_article WHERE parent_id IS NULL");
$number_of_row = $first_row['COUNT(*)'];
if ($number_of_row % 10 != 0) {
    $total_page_count = $number_of_row / 10 + 1;
}
else {
    $total_page_count = $number_of_row / 10;
}

if ($page > $total_page_count || $page < 1) {
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
$stmt = $conn->prepare("SELECT * FROM exchange_article WHERE parent_id IS NULL ORDER BY id DESC LIMIT ?, 10");
$start_num = ($page - 1) * 10;
$stmt->bind_param('i', $start_num);
$stmt->execute();
$stmt->store_result();
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
    if ($stmt->num_rows > 0) {
        bind_array($stmt, $row);
        while ($stmt->fetch()) { ?>
            <tr>
                <td><?= $row['ID'] ?></td>
                <td><?= $row['board_title'] ?></td>
                <td><?= $row['category'] ?></td>
                <td><?= $row['lang_from'] . ' -> ' . $row['lang_to'] ?></td>
                <td><?= $row['date'] ?></td>
                <td><?= $row['board_hit'] ?></td>
                <td><?= $row['vote_up'] ?></td>
                <td><?= $row['vote_down'] ?></td>
                <td><?= fetch_first_row('SELECT * FROM users WHERE ID=?', 'i', $row['author'])['user_nickname'] ?></td>
            </tr>
  <?php }
    } ?>
    </tbody>
</table>
<?php
$stmt->close();
$conn->close();
?>
<?php
for ($i = 1; $i <= $total_page_count; $i++) {
    echo "<a href='/board/exchange.php?page=$i'>$i</a> ";
}
?>
</body>
</html>