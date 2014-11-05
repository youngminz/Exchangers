<?php
session_start();
require_once("../config.php");
require_once("../function.php");

$article_id = 1;
if (isset($_GET['id'])) {
    $article_id = (int) $_GET['id'];
}

if ($_POST) {
    if (isset($_POST['contents']) && !empty($_POST['contents'])) {
        $query = "INSERT INTO exchange_article " .
                 "VALUES (NULL, ?, NULL, NULL, NULL, NULL, ?, DEFAULT, DEFAULT, DEFAULT, DEFAULT, ?)";
        execute_query($query, "isi", $article_id, htmlspecialchars($_POST['contents']), $_SESSION['ID']);
    }
    header('Location: /board/exchange_view.php?id=' . $article_id);
    exit;
}

$question = fetch_first_row("SELECT * FROM exchange_article WHERE id = ?", "i", $article_id);
$answer = fetch_all_row("SELECT * FROM exchange_article WHERE parent_id = ?", "i", $article_id);
if ($question === false) {
    header('Location: /board/exchange.php');
    exit;
}

execute_query("UPDATE exchange_article SET board_hit = board_hit + 1 WHERE ID = ?",
              "i", $article_id);

function recursive_comment($parent_article, $parent_id, $level) {
    $result = fetch_all_row("SELECT * FROM exchange_comment " .
                            "WHERE parent_article = ? " .
                            "AND (IF (ISNULL(?), parent_id IS NULL, parent_id = ?))",
                            "iii", $parent_article, $parent_id, $parent_id);
   
    if ($parent_id === NULL && isset($_SESSION['ID'])) { ?>
        <form method="post" action="/board/write_comment.php">
            <input type="text" name="text" placeholder="<?= T_("댓글을 입력하세요...") ?>" />
            <input type="hidden" name="mode" value="exchange" />
            <input type="hidden" name="parent_id" value="NULL" />
            <input type="hidden" name="parent_article" value="<?= $parent_article ?>" />
        </form>
    <?php } else if ($parent_id === NULL) { ?>
        <form>
            <input type="text" name="text" placeholder="<?= T_("권한이 없습니다.") ?> "disabled />
            <input type="hidden" name="mode" value="exchange" />
            <input type="hidden" name="parent_id" value="NULL" />
            <input type="hidden" name="parent_article" value="<?= $parent_article ?>" />
        </form>
    <?php }
    if (count($result) > 0) {
        foreach ($result as $row) {
            echo '<li style="padding-left: ' . ($level * 1.5 - 1) . 'rem;">';
            if ($row['visible'] == 1) {
                $user = fetch_first_row('SELECT * FROM users WHERE ID = ?', 'i', $row['author']);

                echo $row["content"];
                echo " - <small>";
                echo sprintf("<a href='/profile.php?id=%s'>%s</a>가 %s에", $user['ID'], $user['user_nickname'], time2str($row['date']));
                echo "</small>";
                if (isset($_SESSION['ID']) && !empty($_SESSION['ID']) && $row['author'] == $_SESSION['ID']) { ?>
                    <a href="/board/exchange_remove_comment.php?mode=exchange&comment=<?= $row['ID'] ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" class="icon icon-small">
                            <path d="M0,1h3v-1h5v1h3v1h-12z" />
                            <path d="M1,3h9v9h-1v-8h-1v7h-1v-7h-1v7h-1v-7h-1v7h-1v-7h-1v7h7v1h-8z" />
                        </svg>
                    </a>
                <?php } ?>
                <?php if (isset($_SESSION['ID']) && !empty($_SESSION['ID'])) { ?>
                    <label for="toggle-visible-comment-<?= $row['ID'] ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12"
                             class="icon icon-small">
                            <path d="M1,4v7h7m-7,0L11,1z" stroke-width="2" stroke="#000" fill="none" />
                        </svg>
                    </label>
                    <input type="checkbox" style="display: none;"
                           id="toggle-visible-comment-<?= $row['ID'] ?>" />
                        <form method="post" action="/board/write_comment.php">
                        <input type="text" name="text" placeholder="<?= T_("댓글을 입력하세요...") ?>" />
                        <input type="hidden" name="mode" value="exchange" />
                        <input type="hidden" name="parent_id" value="<?= $row['ID'] ?>" />
                        <input type="hidden" name="parent_article" value="<?= $parent_article ?>" />
                    </form>
                <?php } ?>
            <?php } else {
                echo T_("<i>[삭제된 댓글입니다]</i>");
            }
            recursive_comment($parent_article, $row['ID'], $level + 1);
        }
    }
}

$user = fetch_first_row('SELECT * FROM users WHERE id = ?', 'i', $question['author']);

//////////////////// HTML START ////////////////////

require_once('../header.php');
?>
<main id="article-view">
  <h2><?= $question['board_title'] ?></h2>
  <article class="question">
    <aside>
      <section>
        <img src="//www.gravatar.com/avatar/<?= hash('md5', $user['user_email']) ?>?d=identicon&size=56" class="right" />
        <b><?= time2str($question['date']) . ', ' . "<a href='/profile.php?id=" . $user['ID'] . "'>" . $user['user_nickname'] . "</a>" ?></b><br />
        <?= sprintf(T_("조회 <b>%s</b>회"), $question['board_hit'])?><br />
        <?php if (isset($_SESSION['ID']) && !empty($_SESSION['ID']) && $question['author'] == $_SESSION['ID']) { ?><br />
          <a href="/board/exchange_edit.php?id=<?= $question['ID'] ?>"><?= T_("수정") ?></a>
          <a href="/board/exchange_remove.php?id=<?= $question['ID'] ?>"><?= T_("삭제") ?></a>
        <?php } ?>
      </section>
    </aside>
    <div class="vote left">
      <?php if (isset($_SESSION['ID']) && !empty($_SESSION['ID']) && $question['author'] != $_SESSION['ID']) { ?>
        <a href="/board/exchange_vote.php?mode=exchange&type=up&article=<?= $question['ID'] ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 12" class="icon">
            <polygon points="12,0 24,12 23,13 12,1.41423 1,13 0,12 "/>
          </svg>
        </a>
      <?php } else { ?>
        <a>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 12" class="icon">
            <polygon points="12,0 24,12 23,13 12,1.41423 1,13 0,12 "/>
          </svg>
        </a>  
      <?php } ?>
      <?= $question['vote_up'] + $question['vote_down'] ?>
      <?php if (isset($_SESSION['ID']) && !empty($_SESSION['ID']) && $question['author'] != $_SESSION['ID']) { ?>
      <a href="/board/exchange_vote.php?mode=exchange&type=down&article=<?= $question['ID'] ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 12" class="icon">
	        <polygon points="12,12 24,0 23,-1 12,10.58577 1,-1 0,0 "/>
        </svg>
      </a>
      <?php } else { ?>
      <a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 12" class="icon">
	        <polygon points="12,12 24,0 23,-1 12,10.58577 1,-1 0,0 "/>
        </svg>
      </a>
      <?php } ?>
    </div>
    <section class="content">
      <?= nl2br($question['contents']) ?>
    </section>
    <span class="left"><?= T_("댓글") ?></span>
    <section class="comment">
      <?php recursive_comment($question['ID'], NULL, 1); ?>
    </section>
  </article>

  <?php
  if (count($answer) != 0) {
    echo sprintf("<h2>답변 %d개</h2>", count($answer));
    foreach ($answer as $answer_row) {
      $user = fetch_first_row('SELECT * FROM users WHERE id = ?', 'i', $answer_row['author']); ?>
      <article>
        <aside>
          <section>
            <img src="//www.gravatar.com/avatar/<?= hash('md5', $user['user_email']) ?>?d=identicon&size=56" class="right" />
            <b>
              <?= time2str($answer_row['date']) . ', ' . "<a href='/profile.php?id=" . $user['ID'] . "'>" . $user['user_nickname'] . "</a>" ?>
            </b><br />
            <?php if (isset($_SESSION['ID']) && !empty($_SESSION['ID']) && $answer_row['author'] == $_SESSION['ID']) { ?>
              <br />
              <a href="/board/exchange_edit.php?id=<?= $answer_row['ID'] ?>"><?= T_("수정") ?></a>
              <a href="/board/exchange_remove.php?id=<?= $answer_row['ID'] ?>"><?= T_("삭제") ?></a>
            <?php } ?>
          </section>
        </aside>
        <div class="vote left">
          <?php if (isset($_SESSION['ID']) && !empty($_SESSION['ID']) && $answer_row['author'] != $_SESSION['ID']) { ?>
        <a href="/board/exchange_vote.php?mode=exchange&type=up&article=<?= $answer_row['ID'] ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 12" class="icon">
            <polygon points="12,0 24,12 23,13 12,1.41423 1,13 0,12 "/>
          </svg>
        </a>
      <?php } else { ?>
        <a>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 12" class="icon">
            <polygon points="12,0 24,12 23,13 12,1.41423 1,13 0,12 "/>
          </svg>
        </a>  
      <?php } ?>
      <?= $answer_row['vote_up'] + $answer_row['vote_down'] ?>
      <?php if (isset($_SESSION['ID']) && !empty($_SESSION['ID']) && $answer_row['author'] != $_SESSION['ID']) { ?>
      <a href="/board/exchange_vote.php?mode=exchange&type=down&article=<?= $answer_row['ID'] ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 12" class="icon">
	        <polygon points="12,12 24,0 23,-1 12,10.58577 1,-1 0,0 "/>
        </svg>
      </a>
      <?php } else { ?>
      <a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 12" class="icon">
	        <polygon points="12,12 24,0 23,-1 12,10.58577 1,-1 0,0 "/>
        </svg>
      </a>
      <?php } ?>
        </div>
        <section class="content">
          <?= nl2br($answer_row['contents']) ?>
        </section>
        <span class="left"><?= T_("댓글") ?></span>
        <section class="comment">
          <?php recursive_comment($answer_row['ID'], NULL, 1); ?>
        </section>
      </article>
      <?php }
    }
  ?>
    <?php if (isset($_SESSION['ID']) && !empty($_SESSION['ID'])) { ?>
  <h2><?= T_("답변하기") ?></h2>
  <form class="form-write" action="/board/exchange_view.php?id=<?= $article_id ?>" method="post">
    <textarea name="contents"></textarea>
    <p class="form-line">
      <a href="/board/exchange.php" class="button"><?= T_("목록") ?></a>
      <input type="submit" value="<?= T_("작성") ?>" class="button-primary" />
    </p>
  </form>
    <?php } ?>
</main>
<?php require_once("../footer.php");
