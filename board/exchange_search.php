<?php
session_start();
require_once("../config.php");
require_once("../function.php");
if (!isset($_SESSION["ID"]) || empty($_SESSION["ID"])) {
    header('Location: /login.php?error=session');
    exit;
}

if (!$_GET || !isset($_GET['q']) || empty($_GET['q'])) {
    echo "<script>history.back();</script>";
    exit;
}
$q = htmlspecialchars($_GET['q']);

$first_row = fetch_first_row("SELECT COUNT(*)
                              FROM exchange_article
                              WHERE parent_id IS NULL AND board_title LIKE '%$q%'");
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
                        "WHERE parent_id IS NULL AND board_title LIKE '%$q%' 
                        ORDER BY id DESC LIMIT ?, 10", "i", ($page - 1) * 10);


//////////////////// HTML START ////////////////////

require_once("../header.php"); ?>
<main id="exchange-list">
  <div>
      <h2>검색 결과 <?=$number_of_row?>개 표시 중</h2>
<?php foreach ($result as $row) { ?>
    <article>
      <section class="status">
        <span class="views">
          <big><?= $row['board_hit'] ?></big>
          조회
        </span><!--
     --><span class="responds">
          <big><?= fetch_first_row("SELECT COUNT(*) FROM exchange_article WHERE parent_id = ?",
                                   "i", $row['ID'])['COUNT(*)']; ?></big>
          답변
        </span><!--
     --><span class="votes">
          <big><?= $row['vote_up'] + $row['vote_down'] ?></big>
          평가
        </span>
      </section><!--
   --><section class="question">
        <div class="summary">
          <h3>
            <a href="/board/exchange_view.php?id=<?= $row["ID"] ?>">
              <?= $row['board_title'] ?>
            </a>
          </h3>
        </div>
        <div class="info">
          <span class="category">
            카테고리
            <mark>
              <?= fetch_first_row('SELECT * FROM category WHERE category_code = ?',
                                  's', $row['category'])['korean'] ?><!--
         --></mark>,
          </span>
          <span class="lang">
            <mark>
              <?= fetch_first_row('SELECT * FROM language WHERE lang_code = ?',
                                  's', $row['lang_from'])['korean'] ?><!--
         --></mark>에서
            <mark>
              <?= fetch_first_row('SELECT * FROM language WHERE lang_code = ?',
                                  's', $row['lang_to'])['korean'] ?><!--
         --></mark>로,
          </span>
          <span>
            <?= time2str($row['date']) ?>
            <?php
              $user = fetch_first_row('SELECT * FROM users WHERE ID = ?', 'i', $row['author']); 
              echo "<a href='/profile.php?id=" . $user['ID'] . "'>" . $user['user_nickname'] . "</a>" ?>가
          </span>
        </div>
      </section>
    </article>
<?php } ?>
  </div>
  <ul class="pagination">
<?php for ($i = 1; $i <= $total_page_count; $i++) { ?>
    <li>
      <a href="/board/exchange_search.php?q=<?= $_GET['q'] ?>&page=<?= $i ?>">
        <?= $i ?>
      </a>
    </li>
<?php } ?>
  </ul>
</main>
</body>
</html>