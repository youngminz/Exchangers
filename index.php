<?php
session_start();
if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    echo "<meta http-equiv='refresh' content='0; url=/login.php'>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>:: Weirdorithm ::</title>
</head>
<body>
유저 번호: <?= $_SESSION['ID'] ?><br/>
ID: <?= $_SESSION['user_id'] ?><br/>
닉네임: <?= $_SESSION['user_nickname'] ?><br/>
email: <?= $_SESSION['user_email'] ?><br/>
<a href="board/exchange.php">Exchange Translate</a><br/>
<a href="logout.php">로그아웃</a>
</body>
</html>
