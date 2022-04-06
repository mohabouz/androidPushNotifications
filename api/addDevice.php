<?php

include "../classes/Firebase.class.php";
include "../classes/PDODbHelper.class.php";

header('Content-Type: application/json; charset=utf-8');

$response = array(
    "error" => true,
);

if (!isset($_POST['device_token']) || !isset($_POST['device_name'])) {
    $response["message"] = "Device token and/or device name are not defined.";
    echo json_encode($response);
    exit;
}

$firebase = new Firebase($_POST['device_token'], $_POST['device_name']);
$result = $firebase->addDevice();

if ($result) {
    $response['error'] = false;
    $response['message'] = "Device added successfully.";
    $response['token'] = $_POST['device_token'];
    $response['device_name'] = $_POST['device_name'];
} else {
    $response['errors'] = true;
    $response['message'] = implode(", ", $firebase->getErrors());
}

echo json_encode($response);

