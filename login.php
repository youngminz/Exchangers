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
    $reason_info = T_("회원가입이 정상적으로 처리되었습니다. 가입하신 아이디로 로그인해주세요.");
}
if (isset($_GET['logout']) && $_GET['logout'] === 'done') {
    $info = true;
    $reason_info = T_("성공적으로 로그아웃되었습니다.");
}
if (isset($_GET['error']) && $_GET['error'] == 'session') {
    $error = true;
    $reason_error = T_("세션이 만료되었습니다. 다시 로그인해주세요.");
}
if (isset($_GET['leave']) && $_GET['leave'] === 'done') {
    $info = true;
    $reason_info = T_("정상적으로 회원 탈퇴되었습니다. 이용해 주셔서 감사합니다 :)");
}

if ($_POST) {
    if (isset($_POST['user_id']) && isset($_POST['user_pass']) &&
       !empty($_POST['user_id']) && !empty($_POST['user_pass'])) {
        $row = fetch_first_row('SELECT * FROM users WHERE user_id = ? AND user_pass = ?',
                               'ss', htmlspecialchars($_POST['user_id']),
                               hash('sha512', $_POST['user_pass']));
        if ($row === false) {
            $error = true;
            $reason_error = T_("아이디 혹은 비밀번호가 일치하지 않습니다!");
        }
        else if ($row['enabled'] == 0) {
            $error = true;
            $reason_error = T_("아이디 혹은 비밀번호가 일치하지 않습니다!");
        }
        else {
            $_SESSION['ID'] = $row['ID'];
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_nickname'] = $row['user_nickname'];
            $_SESSION['user_email'] = $row['user_email'];
        }
    }
    else {
        $error = true;
        $reason_error = T_("입력하지 않은 칸이 있습니다!");
    }
}

if (isset($_SESSION['ID'])) {
    header('Location: /');
    exit;
}

//////////////////// HTML START ////////////////////

require_once('header.php');
?>
<main class="narrow">
  <form class="form-list" action="login.php" method="post">
    <h1><?= T_("로그인") ?></h1>
    <?php if ($info === true) { ?>
      <p class="message message-success"><?= $reason_info ?></p>
    <?php } if ($error === true) { ?>
      <p class="message message-error"><?= $reason_error ?></p>
    <?php } ?>
    <p class="form-line">
      <label for="user_id"><?= T_("아이디") ?></label><!--
      --><input type="text" name="user_id" required />
    </p>
    <p class="form-line">
      <label for="user_pass"><?= T_("비밀번호") ?></label><!--
      --><input type="password" name="user_pass" required />
    </p>
    <p class="form-line">
      <input type="submit" value="<?= T_("로그인") ?>" class="button button-primary right" />
      <a href="join.php" class="button"><?= T_("회원 가입") ?></a>
    </p>
  </form>
</main>

<?php require_once("footer.php");
