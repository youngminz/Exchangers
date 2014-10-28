<?php
session_start();
require_once("../config.php");
require_once("../function.php");
if (!isset($_SESSION["ID"]) || empty($_SESSION["ID"])) {
    header('Location: /login.php?error=session');
    exit;
}

$first_row = fetch_first_row("SELECT COUNT(*) FROM exchange_article WHERE parent_id IS NULL");
$number_of_row = $first_row["COUNT(*)"];

$page = 1;
if (isset($_GET["page"])) {
    $page = (int) $_GET["page"];
}
$total_page_count = $number_of_row % 10 != 0 ? $number_of_row / 10 + 1 : $number_of_row / 10;
if ($page < 1 || $page > $total_page_count) {
    $page = 1;
}

$result = fetch_all_row("SELECT * FROM exchange_article " . 
                        "WHERE parent_id IS NULL ORDER BY id DESC LIMIT ?, 10",
                        "i", ($page - 1) * 10);

//////////////////// HTML START ////////////////////

require_once("../header.php"); ?>
<main>
  <div class="question-summary">
<?php foreach ($result as $row) { ?>
    <div class="status">
      <span><?= $row['board_hit'] ?> 조회</span> /
      <span>
        <?php echo fetch_first_row("SELECT COUNT(*) FROM exchange_article " .
                                   "WHERE parent_id = ?",
                                   "i", $row['ID'])['COUNT(*)']; ?> 답변
      </span> / 
      <span><?= $row['vote_up'] + $row['vote_down'] ?> 평가</span>
    </div>
    <div class="question">
      <div class="summary">
        <h3>
          <a href="/board/exchange_view.php?id=<?= $row["ID"] ?>">
            <?= $row['board_title'] ?>
          </a>
        </h3>
      </div>
      <div class="info">
        <span><?= fetch_first_row('SELECT * FROM category WHERE category_code = ?', 's', $row['category'])['korean'] ?> 카테고리</span>, 
        <span><?= fetch_first_row('SELECT * FROM language WHERE lang_code = ?',
                                  's', $row['lang_from'])['korean'] ?>에서
              <?= fetch_first_row('SELECT * FROM language WHERE lang_code = ?',
                                  's', $row['lang_to'])['korean'] ?>로
          </span>,
          <span><?= $row['date'] . '에' ?></span>,
          <span><?= fetch_first_row('SELECT * FROM users WHERE ID = ?',
                                    'i', $row['author'])['user_nickname'] . '가' ?></span>
        </div>
      </div>
<?php } ?>
  </div>
  <ul class="pagination">
<?php for ($i = 1; $i <= $total_page_count; $i++) { ?>
    <li>
      <a href="/board/exchange.php?page=<?= $i ?>">
        <?= $i ?>
      </a>
    </li>
<?php } ?>
  </ul>
</main>
</body>
</html>