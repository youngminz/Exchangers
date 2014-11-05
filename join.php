<?php
session_start();
require_once('config.php');
require_once('function.php');
$is_valid = true;
$user_id = "";
$user_nickname = "";
$user_email = "";

if ($_POST) {
    if ($_POST["user_id"] !== "" && $_POST["user_pass"] !== "" &&
        $_POST["user_pass_twice"] !== "" && $_POST["user_nickname"] !== "" &&
        $_POST["user_email"] !== "") {
        $user_id = htmlspecialchars(trim($_POST["user_id"]));
        $user_pass = hash('sha512', trim($_POST["user_pass"]));
        $user_pass_twice = hash('sha512', trim($_POST["user_pass_twice"]));
        $user_nickname = htmlspecialchars(trim($_POST["user_nickname"]));
        $user_email = htmlspecialchars(trim($_POST["user_email"]));

        $is_valid = true;
        if ($user_pass == $user_pass_twice) {
            if (fetch_first_row("SELECT user_id FROM users WHERE user_id = ?",
                                "s", $user_id) !== false
               ) {
                $is_valid = false;
                $reason = T_("이미 존재하는 아이디입니다. 다른 아이디를 사용해 주세요.");
            }
        
            if ($is_valid == true) {
                if (fetch_first_row("SELECT user_id FROM users WHERE user_email = ?",
                                    "s", $user_email) !== false
                   ) {
                    $is_valid = false;
                    $reason = T_("다른 사용자가 사용 중인 이메일입니다. 다른 이메일을 사용해 주세요.");
                }
            }
        }
        else {
            $is_valid = false;
            $reason = T_("입력하신 두 개의 비밀번호가 다릅니다. 비밀번호를 다시 입력하세요.");
        }

        if ($is_valid === true) {
            execute_query("INSERT INTO users (ID, user_id, user_pass, user_nickname, user_email) " .
                          "VALUES(NULL, ?, ?, ?, ?)",
                          "ssss", $user_id, $user_pass, $user_nickname, $user_email);
            header('Location: /login.php?join=done');
        }
    }
    else {
        $is_valid = false;
        $reason = T_("작성하지 않은 곳이 있습니다.");
    }
}

//////////////////// HTML START ////////////////////

require_once("header.php");
?>

<main class="narrow">
  <form class="form-list" action="join.php" method="post">
    <h1><?= T_("회원 가입") ?></h1>
    <?php if ($is_valid == false) { ?>
      <p class="message message-error"><?= $reason ?></p>
    <?php } ?>
    <p class="form-line">
      <label for="user_id"><?= T_("아이디") ?></label><!--
      --><input type="text" name="user_id" required
                value="<?= isset($_POST['user_id']) ? $_POST['user_id'] : '' ?>" />
    </p>
    <p class="form-line">
      <label for="user_pass"><?= T_("비밀번호") ?></label><!--
      --><input type="password" name="user_pass" required />
    </p>
    <p class="form-line">
      <label for="user_pass_twice"><?= T_("비밀번호 재입력") ?></label><!--
      --><input type="password" name="user_pass_twice" required />
    </p>
    <p class="form-line">
      <label for="user_nickname"><?= T_("닉네임") ?></label><!--
      --><input type="text" name="user_nickname" required
                value="<?= isset($_POST['user_nickname']) ? $_POST['user_nickname'] : '' ?>" />
    </p>
    <p class="form-line">
      <label for="user_email">e-mail</label><!--
      --><input type="email" name="user_email" required
                value="<?= isset($_POST['user_email']) ? $_POST['user_email'] : '' ?>" />
    </p>
    <p class="form-line">
      <input type="submit" value="<?= T_("회원가입") ?>" class="button button-primary right" />
      <a href="/login.php" class="button"><?= T_("로그인으로 되돌아가기") ?></a>
    </p>
  </form>
</main>

<?php require_once("footer.php");
