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
<form action="logout.php">
    <input type="submit" value="로그아웃" style="display: inline">
</form>
</body>
</html>