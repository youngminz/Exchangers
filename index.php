<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    echo "<meta http-equiv='refresh' content='0; url=login.php'>";
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
ID: <?= $_SESSION['user_id'] ?><br/>
이름: <?= $_SESSION['user_name'] ?><br/>
email: <?= $_SESSION['user_email'] ?>
<a href="logout.php">로그아웃</a>
</body>
</html>