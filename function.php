<?php
register_shutdown_function("fatal_handler");

function format_error($errno, $errstr, $errfile, $errline) {
    $trace = print_r(debug_backtrace(false), true);

    $content  = "<table><thead bgcolor='#c8c8c8'><th>Item</th><th>Description</th></thead><tbody>";
    $content .= "<tr valign='top'><td><b>Error</b></td><td><pre>$errstr</pre></td></tr>";
    $content .= "<tr valign='top'><td><b>Errno</b></td><td><pre>$errno</pre></td></tr>";
    $content .= "<tr valign='top'><td><b>File</b></td><td>$errfile</td></tr>";
    $content .= "<tr valign='top'><td><b>Line</b></td><td>$errline</td></tr>";
    $content .= "<tr valign='top'><td><b>Trace</b></td><td><pre>$trace</pre></td></tr>";
    $content .= '</tbody></table>';

    return $content;
}

function fatal_handler() {
    $errfile = "unknown file";
    $errstr  = "shutdown";
    $errno   = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if ($error !== NULL) {
        $errno   = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr  = $error["message"];

        echo format_error ($errno, $errstr, $errfile, $errline);
    }
}

function bind_array($stmt, &$row) {
    $md = $stmt->result_metadata();
    $params = array();
    while ($field = $md->fetch_field()) {
        $params[] = &$row[$field->name];
    }
    call_user_func_array(array($stmt, 'bind_result'), $params);
}

function clone_array($arr) {
    $cloned_array = [];
    foreach ($arr as $k => $v) {
        $cloned_array[$k] = $v;
    }
    return $cloned_array;
}

// make values referenced.
function make_values_referenced($arr) {
    $refs = array();
    foreach($arr as $key => $value)
        $refs[$key] = &$arr[$key];
    return $refs;
}

// First argument is sql query, second is bind_param mode, after third is parameters.
// Return: success -> array, error -> false
function fetch_first_row() {
    $args = func_get_args();
    $args_count = func_num_args();
    if ($args_count == 1) {
        $sql = $args[0];
    }
    else {
        $sql = $args[0];
        $mode = $args[1];
    }
    
    global $db_host, $db_user, $db_pass, $db_name;
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_errno) {
        echo "<h1>데이터베이스에 연결하던 도중 오류가 발생했습니다.</h1>";
        exit;
    }
    $stmt = $conn->prepare($sql);
    if ($args_count != 1) {
        $sliced = array_slice($args, 1);
        call_user_func_array(array($stmt, "bind_param"), make_values_referenced($sliced));
    }
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        bind_array($stmt, $row);
        $stmt->fetch();
        $stmt->close();
        $conn->close();
        return $row;
    }
    else {
        $stmt->close();
        $conn->close();
        return false;
    }
}

function fetch_all_row() {
    $args = func_get_args();
    $args_count = func_num_args();
    if ($args_count == 1) {
        $sql = $args[0];
    }
    else {
        $sql = $args[0];
        $mode = $args[1];
    }
    
    global $db_host, $db_user, $db_pass, $db_name;
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_errno) {
        echo "<h1>데이터베이스에 연결하던 도중 오류가 발생했습니다.</h1>";
        exit;
    }
    $stmt = $conn->prepare($sql);
    if ($args_count != 1) {
        $sliced = array_slice($args, 1);
        call_user_func_array(array($stmt, "bind_param"), make_values_referenced($sliced));
    }
    $stmt->execute();
    $stmt->store_result();
    
    bind_array($stmt, $row);
    $array = [];
    while ($stmt->fetch()) {
        array_push($array, clone_array($row));
    }
    
    $stmt->close();
    $conn->close();
    return $array;
}

// First argument is sql query, second is bind_param mode, after third is parameters.
// Return: success -> true, error -> false
function execute_query() {
    $args = func_get_args();
    $args_count = func_num_args();
    if ($args_count == 1) {
        $sql = $args[0];
    }
    else {
        $sql = $args[0];
        $mode = $args[1];
    }
    
    global $db_host, $db_user, $db_pass, $db_name;
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_errno) {
        echo "<h1>데이터베이스에 연결하던 도중 오류가 발생했습니다.</h1>";
        exit;
    }
    $conn->query("SET time_zone = '+9:00'");
    $stmt = $conn->prepare($sql);
    if ($args_count != 1) {
        $sliced = array_slice($args, 1);
        call_user_func_array(array($stmt, "bind_param"), make_values_referenced($sliced));
    }
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->errno) {
        $stmt->close();
        $conn->close();
        return false; // error: false
    }
    else {
        $stmt->close();
        $conn->close();
        return true; // success: true
    }
}

function time2str($ts) {
    if (!ctype_digit($ts))
        $ts = strtotime($ts);

    $diff = time() - $ts;
    if ($diff == 0) {
        return '지금';
    } else if($diff > 0) {
        $day_diff = floor($diff / 86400);
        if ($day_diff == 0) {
            if ($diff < 60)
                return '방금 전';
            else if ($diff < 3600)
                return floor($diff / 60) . '분 전';
            else if ($diff < 86400)
                return floor($diff / 3600) . '시간 전';
        }
        else if ($day_diff == 1)
                return '어제';
        else if ($day_diff < 10) 
            return $day_diff . '일 전';
        #else if($day_diff < 31)
        #    return ceil($day_diff / 7) . '주 전';
        #else if($day_diff < 60)
        #    return '달 전';
        else
            return date('n월 j일 G시', $ts);
    } else {
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);
        if ($day_diff == 0) {
            if ($diff < 3600) 
                return floor($diff / 60) . '분 후';
            else if ($diff < 86400) 
                return floor($diff / 3600) . '시간 후';
        }
        else if ($day_diff == 1) 
            return '내일';
        else if ($day_diff < 4) 
            return date('l', $ts);
        else if ($day_diff < 7 + (7 - date('w'))) 
            return '다음 주';
        #else
        #    if (ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . '주 후';
        #else
        #    if (date('n', $ts) == date('n') + 1) return '다음 달';
        else return date('n월 j일 G시', $ts);
    }
    return "부엉이바위에서";
}
