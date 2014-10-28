<?php
session_start();
require_once("../config.php");
require_once("../function.php");
if (!isset($_SESSION["ID"]) || empty($_SESSION["ID"])) {
    header('Location: /login.php?reason=session_expired');
    exit;
}

$error = false;
$reason = "";
if ($_POST) {
    if (isset($_POST["title"]) && isset($_POST["contents"]) && isset($_POST["start_language"]) && 
        isset($_POST["end_language"]) && isset($_POST["category"]) &&
        !empty($_POST["title"]) && !empty($_POST["contents"]) && !empty($_POST["start_language"]) &&
        !empty($_POST["end_language"]) && !empty($_POST["category"])) {
        $query_result = execute_query("INSERT INTO exchange_article " . 
                                      "VALUES (NULL, NULL, ?, ?, ?, ?, ?, DEFAULT, 0, 0, 0, ?)", 
                                      "sssssi", $_POST["title"], $_POST["category"],
                                      $_POST["start_language"], $_POST["end_language"], 
                                      $_POST["contents"], $_SESSION["ID"]);
        if ($query_result === true) {
            header('Location: /board/exchange.php');  
            exit;
        }
    }
    else {
        $error = true;
        $reason = "비어 있는 칸이 있습니다.";
    }
}

//////////////////// HTML START ////////////////////

require_once("../header.php");
?>
<main class="narrow">
  <h1>새 글 쓰기</h1>
<?php if ($error === true) { ?>
  <div class="message message-error">
    <?= $reason ?>
  </div>
<?php } ?>
  <form class="form-write" action="/board/exchange_write.php" method="post">
    <input type="text" placeholder="글 제목" name="title" id="title" />
    <textarea name="contents"></textarea>
    <div class="form-line" style="float: left; width: 70%;">
      <p class="form-line">
        <label for="start_language">언어:</label>
        <select name="start_language">
          <option value="">언어 선택</option>
          <?php
            $all_languages = fetch_all_row("SELECT * FROM language");
            foreach ($all_languages as $language) {
          ?>
          <option value="<?= $language['lang_code'] ?>">
            <?= $language['korean']; ?>
          </option>
          <?php } ?>
        </select>
        에서
        <select name="end_language">
          <option value="">언어 선택</option>
          <?php
            $all_languages = fetch_all_row("SELECT * FROM language");
            foreach ($all_languages as $language) {
          ?>
          <option value="<?= $language['lang_code'] ?>">
            <?= $language['korean']; ?>
          </option>
          <?php } ?>
        </select>
        으로
      </p>
      <p class="form-line">
        <label for="category">카테고리:</label>
        <select name="category">
          <option value="">카테고리 선택</option>
    <?php $all_categories = fetch_all_row("SELECT * FROM category");
          foreach ($all_categories as $category) { ?>
            <option value="<?= $category['category_code'] ?>">
              <?= $category['korean']; ?>
            </option>
    <?php } ?>
        </select>
      </p>
    </div>
    <p class="form-line">
      <a href="/board/exchange.php" class="button">목록</a>
      <input type="submit" value="작성" class="button-primary" />
    </p>
  </form>
</main>
</body>
</html>