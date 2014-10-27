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
            <a href="/">Weirdorithm</a>
          </li>
          <li>
            <a href="/board/exchange_view.php">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon">
                <g id="list">
                  <path d="M3,13h2v-2H3V13z M3,17h2v-2H3V17z M3,9h2V7H3V9z M7,13h14v-2H7V13z
                           M7,17h14v-2H7V17z M7,7v2h14V7H7z"></path>
                </g>
              </svg>
            </a>
          </li>
          <li>
            <a href="/board/exchange_write.php">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon">
                <g id="translate">
                  <path d="M3,17.2V21h3.8L17.8,9.9l-3.8-3.8L3,17.2z M20.7,7c0.4-0.4,0.4-1,
                           0-1.4l-2.3-2.3c-0.4-0.4-1-0.4-1.4,0l-1.8,1.8l3.8,3.8L20.7,7z
                           M12,19l-2,2h13v-2H12z"></path>
                </g>
              </svg>
            </a>
          </li>
<?php if (isset($_SESSION['ID']) && !empty($_SESSION['ID'])) { ?>
          <li class="right">
            <a href="/logout.php">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="icon">
                <path d="M384,320v-64H224v-64h160v-64l96,96L384,320z M352,288v128H192v96L0,
                         416V0h352v160h-32V32H64l128,64v288h128v-96H352z"/>
              </svg>
            </a>
          </li>
          <li class="right">
            <a href="/profile.php"><?= $_SESSION['user_nickname'] ?></a>
          </li>
<?php } else { ?>
          <li class="right">
            <a href="/login.php">로그인</a>
          </li>
<?php } ?>
        </ul>
      </div>
    </nav>