<?php
session_start();
require_once("config.php");
require_once("function.php");
if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    header('Location: /login.php?error=session');
    exit;
}
$is_me = true;
if (isset($_GET["id"]) && $_GET['id'] != $_SESSION['ID']) {
    $is_me = false;
    $id = (int) $_GET['id'];
}
else {
    $id = (int) $_SESSION['ID'];
}

$modified = false;
$different_password = false;

if ($_POST) {
    if (isset($_POST['user_pass']) && !empty($_POST['user_pass']) &&
        isset($_POST['user_pass_twice']) && !empty($_POST['user_pass_twice'])) {
        if ($_POST['user_pass'] == $_POST['user_pass_twice']) {
            execute_query('UPDATE users SET user_pass = ? WHERE ID = ?', 'si', hash('sha512', $_POST['user_pass']), $_SESSION['ID']);
            $modified = true;
        }
        else {
            $different_password = true;
        }
    }
    if (isset($_POST['user_nickname']) && !empty($_POST['user_nickname'])) {
        execute_query('UPDATE users SET user_nickname = ? WHERE ID = ?', 'si', htmlspecialchars($_POST['user_nickname']), $_SESSION['ID']);
        $modified = true;
    }
    $my_data = fetch_first_row("SELECT * FROM users WHERE ID = ?", 'i', $_SESSION['ID']);

    $_SESSION['user_id'] = $my_data['user_id'];
    $_SESSION['user_nickname'] = $my_data['user_nickname'];
    $_SESSION['user_email'] = $my_data['user_email'];
}

$profile_data = fetch_first_row("SELECT * FROM users WHERE ID = ?", 'i', $id);

//////////////////// HTML START ////////////////////

require_once('header.php');
?>

<main>
  <header>
    <img src="//www.gravatar.com/avatar/<?= hash('md5', $profile_data['user_email']) ?>?d=identicon&size=240" />
    <div>
      <h1><?= $profile_data['user_nickname'] ?></h1>
        <?= _("사용자 #") ?><?= $id ?>
        <?php if ($is_me === true) { ?>
          <form class="form-list" action="profile.php" method="post">
          <?php if ($modified === true) { ?>
            <p class="message message-success"><?= _("정보 변경이 성공적으로 이루어졌습니다.") ?></p>
          <?php } if ($different_password === true) { ?>
            <p class="message message-error"><?= _("변경하려는 비밀번호가 일치하지 않습니다!") ?></p>
          <?php } ?>
          <p class="form-line">
            <label for="user_pass"><?= _("비밀번호") ?></label><!--
         --><input type="password" name="user_pass"  />
          </p>
          <p class="form-line">
            <label for="user_pass_twice"><?= _("재입력") ?></label><!--
         --><input type="password" name="user_pass_twice"  />
          </p>
          <p class="form-line">
            <label for="user_nickname"><?= _("닉네임") ?></label><!--
         --><input type="text" name="user_nickname" placeholder="<?= $profile_data['user_nickname'] ?>"  />
          </p>
          <p class="form-line">
            <a href="/leave_ask.php" class="button"><?= _("회원 탈퇴") ?></a>
            <input type="submit" value="<?= _("프로필 업데이트") ?>" class="button button-primary right" />
          </p>
        </form>
      <?php } ?>

      <dl>
        <dt><?= _("아이디") ?></dt>
        <dd><?= $profile_data['user_id'] ?></dd>
      </dl>
      <dl>
        <dt><?= _("이메일") ?></dt>
        <dd><?= $profile_data['user_email'] ?></dd>
      </dl>
      <dl>
        <dt><?= _("사용자 평판") ?></dt>
        <dd><?= _("(준비 중)") ?></dd>
      </dl>
    </div>
  </header>
</main>
</body>
</html>
