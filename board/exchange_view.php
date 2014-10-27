<?php
session_start();
require_once("../config.php");
require_once("../function.php");
if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    echo "<meta http-equiv='refresh' content='0; url=/login.php'>";
    exit;
}

function recursive_comment($parent_article, $parent_id, $level) {
    $result = fetch_all_row("SELECT * FROM exchange_comment " . 
                            "WHERE parent_article = ? " .
                            "AND (IF (ISNULL(?), parent_id IS NULL, parent_id = ?))", 
                            "iii", $parent_article, $parent_id, $parent_id);
    if (count($result) > 0) {
        foreach ($result as $row) {
            echo "<li style='margin-left: " . $level . "rem'>";
            echo $row["content"];
            echo " - <b>" . fetch_first_row("SELECT user_nickname " . 
                                            "FROM users WHERE ID = ?",
                                            "i", $row['author'])['user_nickname'] . "</b>";
            echo "</li>";
            recursive_comment($parent_article, $row['ID'], $level + 1);
        }
    }
}

$article_id = 1;
if (isset($_GET['id'])) {
    $article_id = (int) $_GET['id'];
}

$question = fetch_first_row("SELECT * FROM exchange_article WHERE id = ?", "i", $article_id);
$answer = fetch_all_row("SELECT * FROM exchange_article WHERE parent_id = ?", "i", $article_id);
if ($question === false) {
    echo "<meta http-equiv='refresh' content='0; url=/board/exchange.php'>";
    exit;
}

execute_query("UPDATE exchange_article SET board_hit = board_hit + 1 WHERE ID = ?", 
              "i", $article_id);

//////////////////// HTML START ////////////////////

require_once('../header.php');
?>
질문: 
<table>
    <tr>
        <th>글 제목</th>
        <td><?= $question['board_title'] ?></td>
    </tr>
    <tr>
        <th>글쓴이</th>
        <td><?= fetch_first_row("SELECT user_nickname FROM users WHERE ID = ?", "i", 
                                $question['author'])['user_nickname'] ?></td>
    </tr>
    <tr>
        <th>글 내용</th>
        <td><?= $question['contents'] ?></td>
    </tr>
    <tr>
        <th>댓글</th>
        <td>
            <ul><?php recursive_comment($question['ID'], NULL, 1); ?></ul>
        </td>
    </tr>
</table>
답변:
<?php
if (count($answer) == 0) {
    echo "아직 달린 답변이 없습니다. ";
}
else {
    foreach ($answer as $answer_row) {?>
        <table>
            <tr>
                <th>글 제목</th>
                <td><?= $answer_row['board_title'] ?></td>
            </tr>
            <tr>
                <th>글쓴이</th>
                <td><?= fetch_first_row("SELECT user_nickname FROM users WHERE ID = ?", 
                                        "i", $answer_row['author'])['user_nickname'] ?></td>
            </tr>
            <tr>
                <th>글 내용</th>
                <td><?= $answer_row['contents'] ?></td>
            </tr>
            <tr>
                <th>댓글</th>
                <td>
                    <ul><?php recursive_comment($answer_row['ID'], NULL, 1); ?></ul>
                </td>
        </table>
<?php }
} ?>
</body>
</html>