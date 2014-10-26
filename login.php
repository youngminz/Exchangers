<?php
session_start();
require_once('config.php');
require_once('function.php');

$info = false;
$error = false;

$reason_info = '';
$reason_error = '';


if (isset($_SERVER['HTTP_REFERER'])) {
    if (strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'] . "/join.php") !== false) {
        $info = true;
        $reason_info = "회원가입이 정상적으로 처리되었습니다. 가입하신 아이디로 로그인해주세요.";
    }
    if (strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'] . '/logout.php') !== false) {
        $info = true;
        $reason_info = "성공적으로 로그아웃되었습니다.";
    }
}

if ($_POST) {
    if (isset($_POST['user_id']) && isset($_POST['user_pass'])) {
        $row = fetch_first_row('SELECT * FROM users WHERE user_id = ? AND user_pass = ?', 
                               'ss', $_POST['user_id'], hash('sha512', $_POST['user_pass']));
        if ($row === false) {
            $error = true;
            $reason_error = "아이디 혹은 비밀번호가 일치하지 않습니다!";
        }
        else {
            $_SESSION['ID'] = htmlspecialchars($row['ID']);
            $_SESSION['user_id'] = htmlspecialchars($row['user_id']);
            $_SESSION['user_nickname'] = htmlspecialchars($row['user_nickname']);
            $_SESSION['user_email'] = htmlspecialchars($row['user_email']);
        }
    }
}

if (isset($_SESSION['ID'])) {
    echo "<meta http-equiv='refresh' content='0; url=/' />";
    exit;
}

require_once('header.php');
?>
<div class="container">
  <h1>로그인</h1>
<?php if ($info === true) { ?>
  <p class="message-success">
    <?= $reason_info ?>
  </p>
<?php } if ($error === true) { ?>
  <p class="message-error">
    <?= $reason_error ?>
  </p>
<?php } ?>
  <form id="form-login" action="login.php" method="post">
    <p class="form-line">
      <label for="user_id">ID</label>
      <input type="text" name="user_id" />
    </p>
    <p class="form-line">
      <label for="user_pass">Password</label>
      <input type="password" name="user_pass" />
    </p>
    <p class="form-line">
      <input type="submit" value="로그인">
      <a href="join.php">회원가입</a>
    </p>
  </form>
</div>
</body>
</html>
