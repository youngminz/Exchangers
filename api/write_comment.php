<?php
session_start();
require_once("../config.php");
require_once("../function.php");
header('Content-Type: application/json');

if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    $json = array(
        'status' => 'error',
        'message' => 'Session user data not found'
    );
    echo json_encode($json);
    exit;
}
if ($_POST) {
    if (isset($_POST['mode']) && isset($_POST['parent_id']) && 
        isset($_POST['contents']) && isset($_POST['parent_article']) &&
        !empty($_POST['mode']) && !empty($_POST['parent_id']) && 
        !empty($_POST['contents']) && !empty($_POST['parent_article']) && 
        $_POST['mode'] === 'exchange'
       ) {
        if ($_POST['parent_id'] == 'NULL') {
            $_POST['parent_id'] = NULL;
        }
        $query = "INSERT INTO exchange_comment VALUES (NULL, ?, DEFAULT, ?, 1, ?, ?)";
        $result = execute_query($query, "siii", $_POST['contents'], $_SESSION['ID'], $_POST['parent_id'], $_POST['parent_article']);
        if ($result === false) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            $json = array(
                'status'  =>'error',
                'message' => 'Error processing SQL query'
            );
            echo json_encode($json);
            exit;
        }
        else {
            $json = array(
                'status' => 'success'
            );
            echo json_encode($json);
            exit;
        }
    }
}
else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    $json = array(
        'status' => 'error',
        'messange' => 'Incorrent parameters'
    );
    echo json_encode($json);
    exit;
}
// post parameter: mode=exchange, parent_id, contents, parent_article, 