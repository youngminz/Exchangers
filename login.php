<?php
session_start();
require_once('config.php');

function bind_array($stmt, &$row) {
    $md = $stmt->result_metadata();
    $params = array();
    while ($field = $md->fetch_field()) {
        $params[] = &$row[$field->name];
    }
    call_user_func_array(array($stmt, 'bind_result'), $params);
}

$info = false;
$error = false;

$reason_info = '';
$reason_error = '';

if (isset($_SERVER['HTTP_REFERER'])) {
    if (strpos($_SERVER['HTTP_REFERER'], "weirdorithm.youngminz.kr/join.php") !== false) {
        $info = true;
        $reason_info = "회원가입이 정상적으로 처리되었습니다. <br />" .
            "회원가입 하신 아이디로 로그인 해 주세요! <br />";
    }
    if (strpos($_SERVER['HTTP_REFERER'], 'weirdorithm.youngminz.kr/logout.php') !== false) {
        $info = true;
        $reason_info = "성공적으로 로그아웃되었습니다! <br />";
    }
}

if ($_POST) {
    if (isset($_POST['user_id']) && isset($_POST['user_pass'])) {
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if ($conn->connect_errno) {
            echo "<h1>데이터베이스에 연결하던 도중 오류가 발생했습니다.</h1>";
        }
        $conn->set_charset('utf8');

        $user_id = $_POST['user_id'];
        $user_pass = $_POST['user_pass'];
        $user_pass_hash = hash('sha512', $user_pass);

        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id=? AND user_pass=?");
        $stmt->bind_param('ss', $user_id, $user_pass_hash);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows != 0) {
            bind_array($stmt, $info);
            $stmt->fetch();

            $_SESSION['ID'] = htmlspecialchars($info['ID']);
            $_SESSION['user_id'] = htmlspecialchars($info['user_id']);
            $_SESSION['user_nickname'] = htmlspecialchars($info['user_nickname']);
            $_SESSION['user_email'] = htmlspecialchars($info['user_email']);
        } else {
            $error = true;
            $reason_error = '아이디 혹은 패스워드가 올바르지 않습니다!';
        }

        $stmt->free_result();
        $stmt->close();
    }
}

if (isset($_SESSION['user_id']) && isset($_SESSION['user_nickname']) && isset($_SESSION['user_email'])) {
    echo "<meta http-equiv='refresh' content='0; url=/'>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>:: Weirdorithm Login ::</title>
    <link rel="stylesheet" href="style/table.css"/>
</head>
<body>
<?php
if ($info === true) {
    echo $reason_info;
}
if ($error === true) {
    echo $reason_error;
}
?>
<form action="login.php" method="post">
    <table cellspacing="0">
        <tr>
            <th>ID:</th>
            <td><label><input type="text" name="user_id"/></label></td>
        </tr>
        <tr>
            <th>Password:</th>
            <td><label><input type="password" name="user_pass"/></label></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="전송">
                <a href="join.php">회원가입</a>
            </td>
        </tr>
    </table>
</form>
</body>
</html>
