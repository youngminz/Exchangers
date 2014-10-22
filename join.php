<?php
session_start();
require_once('config.php');
if ($_POST) {
    if (isset($_POST['user_id']) && isset($_POST['user_pass']) && isset($_POST['user_pass_twice']) &&
        isset($_POST['user_name']) && isset($_POST['user_email'])
    ) {
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name) or die('<h1>Cannot connect to database!</h1>');
        $conn->set_charset('utf8');

        $user_id = $_POST['user_id'];
        $user_pass = $_POST['user_pass'];
        $user_pass_twice = $_POST['user_pass_twice'];
        $user_name = $_POST['user_name'];
        $user_email = $_POST['user_email'];

        $is_valid = true;
        if ($user_pass == $user_pass_twice) {
            if ($stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id=?")) {
                $stmt->bind_param('s', $user_id);
                $stmt->execute();
                $stmt->store_result();

                // 해당하는 ID가 있는 경우 처리
                if ($stmt->num_rows != 0) {
                    $is_valid = false;
                    $reason = "이미 존재하는 아이디입니다.";
                }

                $stmt->free_result();
                $stmt->close();
            }

            if ($is_valid == true) {
                $stmt = $conn->prepare("SELECT user_email FROM users WHERE user_email=?");
                $stmt->bind_param('s', $user_email);
                $stmt->execute();
                $stmt->store_result();

                // 해당하는 이메일이 있는 경우 처리
                if ($stmt->num_rows != 0) {
                    $is_valid = false;
                    $reason = "이미 존재하는 이메일입니다.";
                }

                $stmt->free_result();
                $stmt->close();
            }
        } else {
            $is_valid = false;
            $reason = "비밀번호가 다릅니다. 비밀번호를 다시 입력하세요.";
        }

        // 모든 조건을 통과하였으면
        if ($is_valid == true) {
            $password_hash = hash('sha512', $user_pass);
            if ($stmt = $conn->prepare("INSERT INTO users VALUES(NULL, ?, ?, ?, ?)")) {
                $stmt->bind_param('ssss', $user_id, $password_hash, $user_name, $user_email);
                $stmt->execute();
                $stmt->close();
            }
            $conn->close();
            echo "<meta http-equiv='refresh' content='0; url=/login.php'>";
            exit;
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>:: Join ::</title>
    <link rel="stylesheet" href="style/table.css"/>
</head>
<body>
<?php
if ($is_valid === false) {
    echo "오류: " . $reason . '<br />';
}
?>
<form action="join.php" method="post">
    <table cellspacing="0">
        <tr>
            <th>ID:</th>
            <td><label><input type="text" name="user_id" value="<?= $user_id ?>"/></label></td>
        </tr>
        <tr>
            <th>Password:</th>
            <td><label><input type="password" name="user_pass"/></label></td>
        </tr>
        <tr>
            <th>Password Again:</th>
            <td><label><input type="password" name="user_pass_twice"/></label></td>
        </tr>
        <tr>
            <th>Name:</th>
            <td><label><input type="text" name="user_name"/ value="<?= $user_name ?>"></label></td>
        </tr>
        <tr>
            <th>Email:</th>
            <td><label><input type="email" name="user_email" value="<?= $user_email ?>"/></label></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="전송">
            </td>
        </tr>
    </table>
</form>
</body>
</html>