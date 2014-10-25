<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="/style/core.css" />
  </head>
  <body>
    <nav>
      <div class="container">
        <ul class="nav">
          <li class="logo">
            <a href="/">Weirdorithm</a>
          </li>
          <li>
            <a>자연 속에</a>
          </li>
          <li>
            <a>개발이 있다</a>
          </li>
<?php if (isset($_SESSION['ID']) && !empty($_SESSION['ID'])) { ?>
          <li class="right">
            <a href="프로필 어딨음">Hi, <?= $_SESSION['user_nickname'] ?></a>
          </li>
<?php } else { ?>
          <li class="right">
            <a href="/login.php">로그인</a>
          </li>
<?php } ?>
        </ul>
      </div>
    </nav>