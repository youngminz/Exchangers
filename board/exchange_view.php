<?php
session_start();
require_once("../config.php");
require_once("../function.php");
if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    header('Location: /login.php?error=session');
    exit;
}

$article_id = 1;
if (isset($_GET['id'])) {
    $article_id = (int) $_GET['id'];
}

/*
if ($_POST) {
    if (isset($_POST['contents']) && !empty($_POST['contents'])) {
        $query = "INSERT INTO exchange_article " . 
                 "VALUES ()"
        execute_query()
    }
}
*/

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
    
    if ($parent_id === NULL) { ?>
    <form method="post" action="/board/write_comment.php">
      <input type="text" name="text" placeholder="댓글을 입력하세요..." />
      <input type="hidden" name="mode" value="exchange" />
      <input type="hidden" name="parent_id" value="NULL" />
      <input type="hidden" name="parent_article" value="<?= $parent_article ?>" />
    </form>
<?php }
    if (count($result) > 0) {
        foreach ($result as $row) {
            echo '<li style="padding-left: ' . $level*1.5 . 'rem;">';
            if ($row['visible'] == 1) {
                echo $row["content"];
                echo " - <small>" .
                  fetch_first_row("SELECT user_nickname FROM users WHERE ID = ?",
                                  "i", $row['author'])['user_nickname'] . "</small>";
                if ($row['author'] == $_SESSION['ID']) { ?>
                  <a href="/board/remove_comment.php?mode=exchange&comment=<?= $row['ID'] ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12"
                         class="icon icon-small">
                      <path d="M0,1h3v-1h5v1h3v1h-12z" />
                      <path d="M1,3h9v9h-1v-8h-1v7h-1v-7h-1v7h-1v-7h-1v7h-1v-7h-1v7h7v1h-8z" />
                    </svg></a>
                <?php } ?>
                  <label for="toggle-visible-comment-<?= $row['ID'] ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12"
                         class="icon icon-small">
                      <path d="M1,4v7h7m-7,0L11,1z" stroke-width="2" stroke="#000" fill="none" />
                    </svg>
                  </label>
                  <input type="checkbox" style="display: none;"
                         id="toggle-visible-comment-<?= $row['ID'] ?>" />
                  <form method="post" action="/board/write_comment.php">
                    <input type="text" name="text" placeholder="댓글을 입력하세요..." />
                    <input type="hidden" name="mode" value="exchange" />
                    <input type="hidden" name="parent_id" value="<?= $row['ID'] ?>" />
                    <input type="hidden" name="parent_article" value="<?= $parent_article ?>" />
                  </form>
  <?php
    } else echo "<i>[삭제된 댓글입니다]</i>";
            recursive_comment($parent_article, $row['ID'], $level + 1);
        }
    }
}

//////////////////// HTML START ////////////////////

require_once('../header.php');
?>
<main id="article-view">
  <h2><?= $question['board_title'] ?></h2>
  <aside>
    <section>
      <b><?= $question['date'] ?></b>에 올라옴<br />
      <b><?= $question['board_hit'] ?></b> 조회<br />
      <b><?= fetch_first_row('SELECT * FROM users WHERE id = ?', 'i', $question['author'])['user_nickname'] ?></b>
    </section>
  </aside>
  <article class="question">
    <div class="vote left">
      <a href="/board/exchange_vote.php?mode=exchange&type=up&article=<?= $question['ID'] ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 12" class="icon">
	        <polygon points="12,0 24,12 23,13 12,1.41423 1,13 0,12 "/>
        </svg>
      </a>
      <?= $question['vote_up'] + $question['vote_down'] ?>
      <a href="/board/exchange_vote.php?mode=exchange&type=down&article=<?= $question['ID'] ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 12" class="icon">
	        <polygon points="12,12 24,0 23,-1 12,10.58577 1,-1 0,0 "/>
        </svg>
      </a>
    </div>
    <section class="content">
      <?= $question['contents'] ?>
    </section>
    <span class="left">댓글</span>
    <section class="comment">
      <?php recursive_comment($question['ID'], NULL, 1); ?>
    </section>
  </article>
  
<?php
if (count($answer) == 0) {
  echo "답변이 없습니다. ";
} else {
  echo "<h2>답변 " . count($answer) . "개</h2>";
  foreach ($answer as $answer_row) { ?>
  <article>
    <div class="vote">
      <a href="/board/exchange_vote.php?type=up&article=<?= $answer_row['ID'] ?>">Up vote</a>
      <a href="/board/exchange_vote.php?type=down&article=<?= $answer_row['ID'] ?>">Down vote</a>
    </div>
    <div>
      <section class="content">
        <?= $answer_row['contents'] ?>
      </section>
      <section class="comment">
       <?php recursive_comment($answer_row['ID'], NULL, 1); ?>
      </section>
    </div>
    <form class="form-write" action="/board/exchange_view.php" method="post">
      <textarea name="contents"></textarea>
      <p class="form-line">
        <a href="/board/exchange.php" class="button">목록</a>
        <input type="submit" value="작성" class="button-primary" />
      </p>
    </form>
  </article>
<?php
  }
}
?>
</main>

</body>
</html>