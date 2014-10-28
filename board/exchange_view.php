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
    
    if ($parent_id === NULL) {
        echo '<form method="post" action="/board/write_comment.php">';
        echo '  <input type="text" name="contents" />';
        echo '  <input type="hidden" name="mode" value="exchange" />';
        echo '  <input type="hidden" name="parent_id" value="NULL" />';
        echo '  <input type="hidden" name="parent_article" value="' . $parent_article . '" />';
        echo '</form>';
        echo '</li>';
    }
    if (count($result) > 0) {
        foreach ($result as $row) {
            echo '<li style="margin-left: ' . $level . 'rem;">';
            if ($row['visible'] == 1) {
                echo $row["content"];
                echo " - <b>" . fetch_first_row("SELECT user_nickname " . 
                                                "FROM users WHERE ID = ?",
                                                "i", $row['author'])['user_nickname'] . "</b>";
                if ($row['author'] == $_SESSION['ID']) {
                    echo "<a href='/board/remove_comment.php?mode=exchange&comment=" . $row['ID'] . "'>";
                    echo "삭제";
                    echo "</a>";
                }
                echo $row['author'];
            }
            else
                echo "<i>[댓글 삭제되었습니다]</i>";
            echo '<form method="post" action="/board/write_comment.php">';
            echo '  <input type="text" name="contents" />';
            echo '  <input type="hidden" name="mode" value="exchange" />';
            echo '  <input type="hidden" name="parent_id" value="' . $row['ID'] . '" />';
            echo '  <input type="hidden" name="parent_article" value="' . $parent_article . '" />';
            echo '</form>';
            echo '</li>';
            recursive_comment($parent_article, $row['ID'], $level + 1);
        }
    }
}

//////////////////// HTML START ////////////////////

require_once('../header.php');
?>
<main id="article-view">
  <h2><?= htmlspecialchars($question['board_title']) ?></h2>
  <aside>
    <b><?= $question['date'] ?></b>에 올라옴<br />
    <?= 0 ?> 조회
  </aside>
  <div class="question-detail">
    <div class="vote">
      <a href="/board/exchange_vote.php?mode=exchange&type=up&article=<?= $question['ID'] ?>">Up vote(<?= $question['vote_up'] ?>)</a>
      <a href="/board/exchange_vote.php?mode=exchange&type=down&article=<?= $question['ID'] ?>">Down vote(<?= $question['vote_down'] ?>)</a>
    </div>
    <div class="content">
      <pre>
        <?= htmlspecialchars($question['contents']) ?>
      </pre>
    </div>
    <div class="comment">
      <?php recursive_comment($question['ID'], NULL, 1); ?>
    </div>
  </div>
  
  답변:
<?php
if (count($answer) == 0) {
  echo "아직 달린 답변이 없습니다. ";
} else foreach ($answer as $answer_row) {?>
  <div class="question-detail">
    <hr>
    <div class="vote">
      <a href="/board/exchange_vote.php?type=up&article=<?= $answer_row['ID'] ?>">Up vote</a>
      <a href="/board/exchange_vote.php?type=down&article=<?= $answer_row['ID'] ?>">Down vote</a>
    </div>
    <div class="contents">
      <pre>
        <?= htmlspecialchars($answer_row['contents']) ?>
      </pre>
    </div>
    <div class="comment">
      <?php recursive_comment($answer_row['ID'], NULL, 1); ?>
    </div>
  </div>
<?php } ?>
</main>

</body>
</html>