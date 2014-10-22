<?php
require_once('config.php');
session_start();

function bind_array($stmt, &$row)
{
    $md = $stmt->result_metadata();
    $params = array();
    while ($field = $md->fetch_field()) {
        $params[] = &$row[$field->name];
    }
    call_user_func_array(array($stmt, 'bind_result'), $params);
}

if ($_POST) {
    if (isset($_POST['user_id']) && isset($_POST['user_pass'])) {
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name) or die('<h1>Cannot connect to database!</h1>');
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

            $_SESSION['user_id'] = htmlspecialchars($info['user_id']);
            $_SESSION['user_nickname'] = htmlspecialchars($info['user_nickname']);
            $_SESSION['user_email'] = htmlspecialchars($info['user_email']);
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
if (strpos($_SERVER['HTTP_REFERER'], "weirdorithm.youngminz.kr/join.php") !== false) {
    echo "회원가입이 정상적으로 처리되었습니다. <br />" .
        "회원가입 하신 아이디로 로그인 해 주세요!";
}
if (strpos($_SERVER['HTTP_REFERER'], 'weirdorithm.youngminz.kr/logout.php') !== false) {
    echo "성공적으로 로그아웃되었습니다!";
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
