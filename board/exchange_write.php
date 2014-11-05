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
                                      "sssssi", htmlspecialchars($_POST["title"]), htmlspecialchars($_POST["category"]),
                                      htmlspecialchars($_POST["start_language"]), htmlspecialchars($_POST["end_language"]),
                                      htmlspecialchars($_POST["contents"]), $_SESSION["ID"]);
        execute_query("UPDATE users SET user_point = user_point - ? WHERE ID = ?", "ii", str_word_count(htmlspecialchars($_POST["contents"])), $_SESSION["ID"]);
        if ($query_result === true) {
            header('Location: /board/exchange.php');
            exit;
        }
    }
    else {
        $error = true;
        $reason = T_("비어 있는 칸이 있습니다.");
    }
}

$user = fetch_first_row("SELECT * FROM users WHERE ID = ?", "i", $_SESSION["ID"]);

//////////////////// HTML START ////////////////////

require_once("../header.php");
?>
<script>m=<?=$user["user_point"]?>;c=function(e){o=document.getElementById('contents');n=m-((o.value.match(/\S+/g)||'').length);document.getElementById('textarea-bg').innerHTML=n;s=document.getElementById('submit');if(n<0){s.className='button disabled';s.type='button'}else{s.className='button-primary';s.type='submit'};};onload=c</script>
<main class="narrow">
  <h1><?= T_("새 글 쓰기") ?></h1>
  <?php if ($error === true) { ?>
    <div class="message message-error"><?= $reason ?></div>
  <?php } ?>
  <form class="form-write" action="/board/exchange_write.php" method="post">
    <input type="text" placeholder="<?= T_("글 제목") ?>" name="title" id="title" />
    <textarea id="contents" name="contents" onkeyup="c()" onchange="c()" value="<?= (!empty($_POST['contents']))?$_POST['contents']:'' ?>"></textarea>
    <h1 class="textarea-bg" id="textarea-bg">...</h1>
    <div class="form-line" style="float: left; width: 70%;">
      <p class="form-line">
        <label for="start_language"><?= T_("언어:") ?></label>
        <?php
          $option = '<option value="">' . T_("언어 선택") . '</option>';
          $all_languages = fetch_all_row("SELECT * FROM language");
          foreach ($all_languages as $language) {
            $option .= '<option value="' . $language["lang_code"] . '">' . T_($language["lang_code"]) . '</option>';
          }
        ?>
        <?= sprintf(T_('<select name="start_language">%s</select>에서 <select name="end_language">%s</select>으로'), $option, $option) ?>
      </p>
      <p class="form-line">
        <label for="category"><?= T_("카테고리") ?>:</label>
        <select name="category">
          <option value=""><?= T_("카테고리 선택") ?></option>
          <?php $all_categories = fetch_all_row("SELECT * FROM category");
          foreach ($all_categories as $category) { ?>
            <option value="<?= $category['category_code'] ?>">
              <?= T_($category['category_code']) ?>
            </option>
          <?php } ?>
        </select>
      </p>
    </div>
    <p class="form-line">
      <a href="/board/exchange.php" class="button"><?= T_("목록") ?></a>
      <input id="submit" type="submit" onclick="c()" value="<?= T_("작성") ?>" class="button-primary" />
    </p>
  </form>
</main>
<?php require_once("../footer.php");
