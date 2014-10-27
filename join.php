<?php
session_start();
require_once('config.php');
require_once('function.php');
$is_valid = true;
$user_id = "";
$user_nickname = "";
$user_email = "";

if ($_POST) {
    if (isset($_POST["user_id"]) && isset($_POST["user_pass"]) && isset($_POST["user_pass_twice"]) &&
        isset($_POST["user_nickname"]) && isset($_POST["user_email"])
       ) {
        $user_id = $_POST["user_id"];
        $user_pass = $_POST["user_pass"];
        $user_pass_twice = $_POST["user_pass_twice"];
        $user_nickname = $_POST["user_nickname"];
        $user_email = $_POST["user_email"];

        $is_valid = true;
        if ($user_pass == $user_pass_twice) {
            if (fetch_first_row("SELECT user_id FROM users WHERE user_id = ?", "s", $user_id) !== false) {
                $is_vaild = false;
                $reason = "이미 존재하는 아이디입니다. 다른 아이디를 사용해 주세요.";
            }

            if ($is_valid == true) {
                if (fetch_first_row("SELECT user_id FROM users WHERE user_email = ?", "s", $user_email) !== false) {
                    $is_vaild = false;
                    $reason = "다른 사용자가 사용 중인 이메일입니다. 다른 이메일을 사용해 주세요.";
                }
            }
        } 
        else {
            $is_valid = false;
            $reason = "입력하신 두 개의 비밀번호가 다릅니다. 비밀번호를 다시 입력하세요.";
        }

        if ($is_valid === true) {
            if (execute_query("INSERT INTO users VALUES(NULL, ?, ?, ?, ?, DEFAULT, DEFAULT)", "ssss", $user_id, hash('sha512', $user_pass), $user_nickname, $user_email) === false) {
                $is_vaild = false;
                $reason = "DB 삽입 오류!";
            }
            else {
                echo "<meta http-equiv='refresh' content='0; url=/login.php'>";
                exit;
            }
        }
    }
}

//////////////////// HTML START ////////////////////

require_once("header.php");
if ($is_valid === false) {
    echo "오류: " . $reason . "<br />";
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
            <td><label><input type="text" name="user_nickname"/ value="<?= $user_nickname ?>"></label></td>
        </tr>
        <tr>
            <th>Email:</th>
            <td><label><input type="email" name="user_email" value="<?= $user_email ?>"/></label></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="전송">
                <input type="button" onclick="history.back();" value="뒤로 가기" />
                <!--a href="/login.php">로그인</a-->
            </td>
        </tr>
    </table>
</form>
</body>
</html>
