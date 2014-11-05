<?php
session_start();
require_once("config.php");
require_once("function.php");

$language = fetch_all_row("SELECT * FROM ui_language");

require_once("header.php");
?>
<main class="narrow">
  <h2>Change Language</h2>
  <ul class="lang-list">
  <?php
    foreach ($language as $row) {
      echo "<li onclick=\"document.cookie='lang=" . $row["lang_code"] . "; path=/'; location.href='" . $_SERVER["HTTP_REFERER"] . "'\">" . $row["english"] . "</a></li>";
    }
  ?>
  </ul>
</main>

<?php
require_once("footer.php");
?>
