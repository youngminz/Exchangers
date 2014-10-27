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
            echo "<script>alert('글 쓰기가 정상적으로 처리되었습니다!');</script>";
            echo "<meta http-equiv='refresh' content='0; url=/login.php'>";
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
<div class="container">
  <h1>새 글 쓰기</h1>
<?php if ($error === true) { ?>
  <div class="message message-error">
    <?= $reason ?>
  </div>
<?php } ?>
  <form action="/board/exchange_write.php" method="post">
    <div id="title">
      <label for="title">글 제목</label>
      <input type="text" placeholder="제목을 입력하세요" name="title">
    </div>
    <div id="contents">
      <label for="container"></label>
      <textarea name="contents"></textarea>
    </div>
    <div id="language">
      언어:
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
      <label for="start_language">에서</label>
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
      <label for="end_language">으로</label>
    </div>
    <div id="category">
      <label for="category">카테고리 선택</label>
        <select name="category">
          <option value="">카테고리 선택</option>
          <?php
            $all_categories = fetch_all_row("SELECT * FROM category");
            foreach ($all_categories as $category) {
          ?>
          <option value="<?= $language['lang_code'] ?>">
            <?= $language['korean']; ?>
          </option>
          <?php } ?>
        </select>
      </div>
    <input type="submit" value="전송" class="button-primary" />
  </form>
</div>
</body>
</html>