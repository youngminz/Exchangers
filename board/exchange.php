<?php
session_start();
require_once("../config.php");
require_once("../function.php");

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
<main id="exchange-list">
  <div>
    <?php foreach ($result as $row) { ?>
      <article>
        <section class="status">
          <span class="views">
            <big><?= $row['board_hit'] ?></big>
              <?= T_("조회") ?>
          </span><!--
       --><span class="responds">
            <big><?= fetch_first_row("SELECT COUNT(*) FROM exchange_article WHERE parent_id = ?", "i", $row['ID'])['COUNT(*)']; ?></big>
                <?= T_("답변") ?>

          </span><!--
       --><span class="votes">
            <big><?= $row['vote_up'] + $row['vote_down'] ?></big>
              <?= T_("평가") ?>
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
              <?= T_("카테고리") ?>
              <mark>
                <?= T_($row['category']) ?><!--
           --></mark>,
            </span>
            <span class="lang">
            <?php
              $str = sprintf(T_('<mark>%s</mark>에서 <mark>%s</mark>로,'), T_($row['lang_from']), T_($row['lang_to']));
              echo $str;
            ?>
            </span>
            <span>
              <?= time2str($row['date']) ?>
              <?php
                $user = fetch_first_row('SELECT * FROM users WHERE ID = ?', 'i', $row['author']);
                echo "<a href='/profile.php?id=" . $user['ID'] . "'>" . $user['user_nickname'] . "</a>" ?>
            </span>
          </div>
        </section>
      </article>
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
<?php require_once("../footer.php");
