<?php
session_start();
if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    echo "<meta http-equiv='refresh' content='0; url=/login.php'>";
    exit;
}
if (isset($_SERVER["id"])) {
    $id = (int) $_SERVER['id'];
}
else {
    $id = $_SESSION['ID'];
}
require_once('header.php');
?>
<img src="http://www.gravatar.com/avatar/<?= hash('md5', $_SESSION['user_email']) ?>?s=240&d=identicon" />
닉네임: <?= $_SESSION['user_nickname'] ?><br/>
email: <?= $_SESSION['user_email'] ?><br/>
</body>
</html>