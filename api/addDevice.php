<?php
require("../config/db.php");
include("../classes/Firebase.class.php");

header('Content-Type: application/json; charset=utf-8');

$response = array(
    "error" => true,
    "message" => ""
);

if (!isset($_POST['device_token']) || !isset($_POST['device_name'])) {
    $response["message"] = "Device token and/or device name are not defined.";
    echo json_encode($response);
    exit;
}

$firebase = new Firebase($_POST['device_token'], $_POST['device_name']);
$result = $firebase->add_device($link);

if (gettype($result) == "boolean") {
    $response['error'] = false;
    $response['message'] = "Device added successfully.";
    $response['token'] = $_POST['device_token'];
    $response['device_name'] = $_POST['device_name'];
} else {
    $response['errors'] = $result;
}

echo json_encode($response);

