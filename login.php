<?php
session_start();
if ($_POST) {
    if (isset($_POST['user_id']) && isset($_POST['user_pass'])) {
        $conn = mysql_connect('localhost', 'weirdorithm', 'FVB2Tjc8eKMW') or die('<h1>Cannot connect to database!</h1>');
        mysql_query('set names utf8');
        mysql_select_db('weirdorithm', $conn);

        $user_id = mysql_real_escape_string($_POST['user_id']);
        $user_pass = mysql_real_escape_string($_POST['user_pass']);

        $result = mysql_query("SELECT * FROM users WHERE user_id='$user_id' AND user_pass='$user_pass'");

        $row = mysql_fetch_assoc($result);
        if ($row != false) {
            $_SESSION['user_id'] = htmlspecialchars($row['user_id']);
            $_SESSION['user_name'] = htmlspecialchars($row['user_name']);
            $_SESSION['user_email'] = htmlspecialchars($row['user_email']);
        }
    }
}
if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {
    echo "<meta http-equiv='refresh' content='0; url=/'>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>:: Weirdorithm Login ::</title>
    <style>
        table a:link {
            color: #666;
            font-weight: bold;
            text-decoration: none;
        }

        table a:visited {
            color: #999999;
            font-weight: bold;
            text-decoration: none;
        }

        table a:active,
        table a:hover {
            color: #bd5a35;
            text-decoration: underline;
        }

        table {
            font-family: Arial, Helvetica, sans-serif;
            color: #666;
            font-size: 12px;
            text-shadow: 1px 1px 0 #fff;
            background: #eaebec;
            margin: 20px;
            border: #ccc 1px solid;

            -moz-border-radius: 3px;
            -webkit-border-radius: 3px;
            border-radius: 3px;

            -moz-box-shadow: 0 1px 2px #d1d1d1;
            -webkit-box-shadow: 0 1px 2px #d1d1d1;
            box-shadow: 0 1px 2px #d1d1d1;
        }

        table th {
            padding: 21px 25px 22px 25px;
            border-top: 1px solid #fafafa;
            border-bottom: 1px solid #e0e0e0;

            background: #ededed;
            background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
            background: -moz-linear-gradient(top, #ededed, #ebebeb);
        }

        table th:first-child {
            text-align: left;
            padding-left: 20px;
        }

        table tr:first-child th:first-child {
            -moz-border-radius-topleft: 3px;
            -webkit-border-top-left-radius: 3px;
            border-top-left-radius: 3px;
        }

        table tr:first-child th:last-child {
            -moz-border-radius-topright: 3px;
            -webkit-border-top-right-radius: 3px;
            border-top-right-radius: 3px;
        }

        table tr {
            text-align: center;
            padding-left: 20px;
        }

        table td:first-child {
            text-align: left;
            padding-left: 20px;
            border-left: 0;
        }

        table td {
            padding: 18px;
            border-top: 1px solid #ffffff;
            border-bottom: 1px solid #e0e0e0;
            border-left: 1px solid #e0e0e0;

            background: #fafafa;
            background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
            background: -moz-linear-gradient(top, #fbfbfb, #fafafa);
        }

        table tr.even td {
            background: #f6f6f6;
            background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
            background: -moz-linear-gradient(top, #f8f8f8, #f6f6f6);
        }

        table tr:last-child td {
            border-bottom: 0;
        }

        table tr:last-child td:first-child {
            -moz-border-radius-bottomleft: 3px;
            -webkit-border-bottom-left-radius: 3px;
            border-bottom-left-radius: 3px;
        }

        table tr:last-child td:last-child {
            -moz-border-radius-bottomright: 3px;
            -webkit-border-bottom-right-radius: 3px;
            border-bottom-right-radius: 3px;
        }

        table tr:hover td {
            background: #f2f2f2;
            background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
            background: -moz-linear-gradient(top, #f2f2f2, #f0f0f0);
        }
    </style>
</head>
<body>
<form action="login.php" method="post">
    <table cellspacing="0">
        <tr>
            <th>ID:</th>
            <td><label><input type="text" name="user_id"/></label></td>
        </tr>
        <tr>
            <th>Password:</th>
            <td><label><input type="text" name="user_pass"/></label></td>
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