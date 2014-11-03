<?php
session_start();
require_once("config.php");
require_once("function.php");
if (!isset($_SESSION["ID"]) || empty($_SESSION["ID"])) {
    header('Location: /login.php?error=session');
    exit;
}

//////////////////// HTML START ////////////////////

require_once('header.php');
?>

<main>
  <h1><?= T_("정말 탈퇴하시게요?") ?></h1>
  <p><?= T_("탈퇴하시면, 다시는 해당하는 아이디로 아이디를 만드시거나 로그인을 하실 수 없습니다. 회원님이 작성하셨던 글은 삭제되지 않습니다.") ?></p>
  <a class="button" href="/leave.php"><?= T_("네, 그래도 회원탈퇴를 하겠습니다.") ?></a>
  <input type="button" class="button" value="<?= T_("탈퇴하지 않고 그대로 있겠습니다.") ?>" onclick="history.go(-1);">
</main>
