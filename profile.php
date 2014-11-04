<?php
session_start();
require_once("config.php");
require_once("function.php");
if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    header('Location: /login.php?error=session');
    exit;
}
$is_me = true;
if (isset($_GET["id"]) && $_GET['id'] != $_SESSION['ID']) {
    $is_me = false;
    $id = (int) $_GET['id'];
}
else {
    $id = (int) $_SESSION['ID'];
}

$modified = false;
$different_password = false;

if ($_POST) {
    if (isset($_POST['user_pass']) && !empty($_POST['user_pass']) &&
        isset($_POST['user_pass_twice']) && !empty($_POST['user_pass_twice'])) {
        if ($_POST['user_pass'] == $_POST['user_pass_twice']) {
            execute_query('UPDATE users SET user_pass = ? WHERE ID = ?', 'si', hash('sha512', $_POST['user_pass']), $_SESSION['ID']);
            $modified = true;
        }
        else {
            $different_password = true;
        }
    }
    if (isset($_POST['user_nickname']) && !empty($_POST['user_nickname'])) {
        execute_query('UPDATE users SET user_nickname = ? WHERE ID = ?', 'si', htmlspecialchars($_POST['user_nickname']), $_SESSION['ID']);
        $modified = true;
    }
    $my_data = fetch_first_row("SELECT * FROM users WHERE ID = ?", 'i', $_SESSION['ID']);

    $_SESSION['user_id'] = $my_data['user_id'];
    $_SESSION['user_nickname'] = $my_data['user_nickname'];
    $_SESSION['user_email'] = $my_data['user_email'];
}

$profile_data = fetch_first_row("SELECT * FROM users WHERE ID = ?", 'i', $id);
$recent_question = fetch_all_row("SELECT * FROM exchange_article WHERE author = ? AND parent_id IS NULL ORDER BY date DESC LIMIT 10", 'i', $id);
$recent_answer = fetch_all_row("SELECT * FROM exchange_article WHERE author = ? AND parent_id IS NOT NULL ORDER BY date DESC LIMIT 10", 'i', $id);

function find_root_article($article_id) {
    $result = fetch_first_row("SELECT * FROM exchange_article WHERE ID = ?", 'i', $article_id);
    if ($result['parent_id'] == NULL) {
        return $result;
    }
    else {
        return find_root_article($result['parent_id']);
    }
}

//////////////////// HTML START ////////////////////

require_once('header.php');
?>

<main>
  <header>
    <img src="//www.gravatar.com/avatar/<?= hash('md5', $profile_data['user_email']) ?>?d=identicon&size=240" />
    <div>
      <h1><?= $profile_data['user_nickname'] ?></h1>
        <?= T_("사용자 #") ?><?= $id ?>
        <?php if ($is_me === true) { ?>
          <form class="form-list" action="profile.php" method="post">
          <?php if ($modified === true) { ?>
            <p class="message message-success"><?= T_("정보 변경이 성공적으로 이루어졌습니다.") ?></p>
          <?php } if ($different_password === true) { ?>
            <p class="message message-error"><?= T_("변경하려는 비밀번호가 일치하지 않습니다!") ?></p>
          <?php } ?>
          <p class="form-line">
            <label for="user_pass"><?= T_("비밀번호") ?></label><!--
         --><input type="password" name="user_pass"  />
          </p>
          <p class="form-line">
            <label for="user_pass_twice"><?= T_("재입력") ?></label><!--
         --><input type="password" name="user_pass_twice"  />
          </p>
          <p class="form-line">
            <label for="user_nickname"><?= T_("닉네임") ?></label><!--
         --><input type="text" name="user_nickname" placeholder="<?= $profile_data['user_nickname'] ?>"  />
          </p>
          <p class="form-line">
            <a href="/leave_ask.php" class="button"><?= T_("회원 탈퇴") ?></a>
            <input type="submit" value="<?= T_("프로필 업데이트") ?>" class="button button-primary right" />
          </p>
        </form>
      <?php } ?>

      <dl>
        <dt><?= T_("아이디") ?></dt>
        <dd><?= $profile_data['user_id'] ?></dd>
      </dl>
      <dl>
        <dt><?= T_("이메일") ?></dt>
        <dd><?= $profile_data['user_email'] ?></dd>
      </dl>
      <dl>
        <dt><?= T_("사용자 평판") ?></dt>
        <dd><?= $profile_data['user_reputation'] ?></dd>
      </dl>
    </div>
  </header>
</main>
  <main id="exchange-list">
    <?= T_("최근 질문") ?>
    <div>
        <?php if (count($recent_question) == 0) {
            echo T_("<p>글이 없습니다.</p>");
        } ?>
      <?php foreach ($recent_question as $row) { ?>
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
              <mark><?= T_($row['category']) ?></mark>,
            </span>
            <span class="lang">
            <?php
            $str = sprintf(T_('<mark>%s</mark>에서 <mark>%s</mark>로'), T_($row['lang_from']), T_($row['lang_to']));
            echo $str;
            ?>
            </span>
            </div>
          </section>
        </article>
      <?php } ?>
    </div>
  </main>

  <main id="exchange-list">
  <?= T_("최근 답변") ?>
  <div>
      
  <?php if (count($recent_answer) == 0) {
    echo T_("<p>글이 없습니다.</p>");
  } ?>
    <?php foreach ($recent_answer as $row) { ?>
      <?php $root = find_root_article($row['ID']); ?>
      <article>
        <section class="status"><!--
       --><span class="views">
            <big><?= $row['vote_up'] + $row['vote_down'] ?></big>
            <?= T_("평가") ?>
          </span><!--
       --><span class="responds">
            <big title="<?= T_($root['lang_from']) ?>"><?= substr($root['lang_from'], 0, 2) ?></big>
              <?= T_("시작") ?>

          </span><!--
       --><span class="votes">
            <big title="<?= T_($root['lang_to']) ?>"><?= substr($root['lang_to'], 0, 2) ?></big>
              <?= T_("도착") ?>
          </span>
        </section><!--
     --><section class="question">
          <div class="summary">
            <h3>
              <a href="/board/exchange_view.php?id=<?= $root["ID"] ?>">
                <?= $root['board_title'] ?>
              </a>
            </h3>
          </div>
          <div class="info">
            <span class="category">
              <?= T_("카테고리") ?>
              <mark>
                <?= T_($root['category']) ?><!--
           --></mark>,
            </span>
            
            <span>
              <?php
              $user = fetch_first_row('SELECT * FROM users WHERE ID = ?', 'i', $root['author']);
              echo sprintf("%s <a href='/profile.php?id=%s'>%s</a>가 질문함, ", time2str($root['date']), $user['ID'], $user['user_nickname']);
              $user = fetch_first_row('SELECT * FROM users WHERE ID = ?', 'i', $row['author']);
              echo sprintf(T_("%s에 답변함"), time2str($row['date']));
              ?>
            </span>
          </div>
        </section>
      </article>
    <?php } ?>
  </div>
</main>
<?php require_once("footer.php");
