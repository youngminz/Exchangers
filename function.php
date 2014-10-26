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


