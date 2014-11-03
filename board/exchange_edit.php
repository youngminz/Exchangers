<?php
session_start();
require_once("../config.php");
require_once("../function.php");
if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    header('Location: /login.php?error=session');
    exit;
}
$error = false;
$reason = "";

function find_root_article($id) {
    $row = fetch_first_row("SELECT * FROM exchange_article WHERE ID = ?", "i", $id);
    var_dump($row['parent_id']);
    if ($row['parent_id'] == NULL) {
        return $id;
    }
    else {
        return find_root_article($row['parent_id']);
    }
}

if ($_GET && isset($_GET['id']) && !empty($_GET['id'])) {
    // Check session id match with given article
    $row = fetch_first_row("SELECT * FROM exchange_article WHERE ID = ?", "i", $_GET['id']);
    if ($row['author'] != $_SESSION['ID']) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        echo "<h1 style='color: red'>" . T_("글을 작성한 사람만이 삭제할 수 있습니다.") . "</h1>";
        exit;
    }
    if ($_POST) {
        if (isset($_POST['contents']) && !empty($_POST['contents'])) {
            $query = "UPDATE exchange_article SET contents = ? WHERE ID = ?";
            $result = execute_query($query, "si", htmlspecialchars($_POST['contents']), $_GET['id']);
            if ($result === false) {
                $error = true;
                $reason = T_("SQL 쿼리를 실행하는 도중 오류가 발생했습니다.");
            }
            else {
                // Success!
                header('Location: /board/exchange_view.php?id=' . find_root_article($_GET['id']));
                exit;
            }
        }
        else {
            $error = true;
            $reason = T_("파라미터가 올바르지 않습니다!");
        }
    }
}
else {
    header('Location: /board/exchange.php');
    exit;
}

//////////////////// HTML START ////////////////////

require_once("../header.php");
?>
<main class="narrow">
  <h1><?= T_("글 수정하기") ?></h1>
    <?php if ($error === true) { ?>
      <div class="message message-error">
      <?= $reason ?>
      </div>
    <?php } ?>
  <form class="form-write" action="/board/exchange_edit.php?id=<?= $_GET['id'] ?>" method="post">
    <textarea name="contents"><?= $row['contents'] ?></textarea>
    <p class="form-line">
      <a href="/board/exchange.php" class="button"><?= T_("목록") ?></a>
      <input type="submit" value="<?= T_("작성") ?>" class="button-primary" />
    </p>
  </form>
</main>
<?php require_once("../footer.php");
