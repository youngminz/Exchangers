<?php
function href($uri){
    $r = ' href="' . $uri .'"';
    if($uri == $_SERVER["REQUEST_URI"]){
        $r .= ' class="current-page"';
    }
    return $r;
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="/style/core.css" />
  </head>
  <body>
    <nav>
      <div class="container">
        <ul class="nav">
          <li class="logo">
            <a <?= href("/") ?>>Weirdorithm</a>
          </li>
<?php if (isset($_SESSION['ID']) && !empty($_SESSION['ID'])) { ?>

          <li>
            <a <?= href("/board/exchange.php") ?>>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="icon">
                <path d="M2,6h4v4H2V6z M2,14h4v4H2V14z M2,22h4v4H2V22z M10,6h20v4H10V6z
                         M10,26h20v-4H10V26z M10,14v4h20v-4H10z M2,30h4v4H2V30z
                         M10,34h20v-4H10V34z"></path>
              </svg>
            </a>
          </li>
          <li>
            <a <?= href("/board/exchange_write.php") ?>>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="icon">
                <path d="M32,4l-2,2l-4-4l2-2L32,4z M11,17l-2,6l6-2L29,7l-4-4L11,17z
                         M24,15v13H4V8h13l4-4H0v28h28V11L24,15z" />
              </svg>
            </a>
          </li>
          <?php } ?>
<?php if (isset($_SESSION['ID']) && !empty($_SESSION['ID'])) { ?>
          <li class="right">
            <a <?= href("/logout.php") ?>>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="icon">
                <path d="M24,20v-4H14v-4h10V8l6,6L24,20z M22,18v8H12v6L0,26V0h22v10h-2V2H4l8,
                         4v18h8v-6H22z"/>
              </svg>
            </a>
          </li>
          <li class="right">
            <a <?= href("/profile.php") ?>><?= $_SESSION['user_nickname'] ?></a>
          </li>
<?php } else { ?>
          <li class="right">
            <a <?= href("/join.php") ?>>회원가입</a>
          </li>
          <li class="right">
            <a <?= href("/login.php") ?>>로그인</a>
          </li>
<?php } ?>
        </ul>
      </div>
    </nav>