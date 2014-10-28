<?php
session_start();
require_once('config.php');
require_once('function.php');

$info = false;
$error = false;

$reason_info = '';
$reason_error = '';

if (isset($_GET['join']) && $_GET['join'] === 'done') {
    $info = true;
    $reason_info = "회원가입이 정상적으로 처리되었습니다. 가입하신 아이디로 로그인해주세요.";
}
if (isset($_GET['logout']) && $_GET['logout'] === 'done') {
    $info = true;
    $reason_info = "성공적으로 로그아웃되었습니다.";
}
if (isset($_GET['error']) && $_GET['error'] === 'session') {
    $error = true;
    $reason_info = "세션이 만료되었습니다. 다시 로그인해주세요.";
}

if ($_POST) {
    if (isset($_POST['user_id']) && isset($_POST['user_pass']) && 
       !empty($_POST['user_id']) && !empty($_POST['user_pass'])) {
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
    else {
        $error = true;
        $reason_error = "입력하지 않은 칸이 있습니다!";
    }
}

if (isset($_SESSION['ID'])) {
    header('Location: /');
    exit;
}

//////////////////// HTML START ////////////////////

require_once('header.php');
?>
<div class="narrow-container">
  <form class="form-list" action="login.php" method="post">
    <h1>로그인</h1>
<?php if ($info === true) { ?>
    <p class="message message-success"><?= $reason_info ?></p>
<?php } if ($error === true) { ?>
    <p class="message message-error"><?= $reason_error ?></p>
<?php } ?>
    <p class="form-line">
      <label for="user_id">ID</label><!--
      --><input type="text" name="user_id" required />
    </p>
    <p class="form-line">
      <label for="user_pass">Password</label><!--
      --><input type="password" name="user_pass" required />
    </p>
    <p class="form-line">
      <input type="submit" value="로그인" class="button button-primary right" />
      <a href="join.php" class="button">회원가입</a>
    </p>
  </form>
</div>
</body>
</html>
