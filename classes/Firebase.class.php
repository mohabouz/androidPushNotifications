<?php

/**
 * Author: Mohammed BOUZAID
 */


class Firebase {

    const FIREBASE_API_KEY = "";

    private $device_token;
    private $device_name;

    private static $errorArray = [];

    /**
     * @param string $device_token
     * @param string $device_name
     */
    public function __construct($device_token, $device_name) {
        $this->device_token = trim($device_token);
        $this->device_name = trim($device_name);
    }

    /**
     * @return boolean
     */
    public function addDevice() {

        self::$errorArray = [];

        if (empty($this->device_token)) {
            self::$errorArray[] = "Device token cannot be empty.";
            return false;
        }

        $table = "subscribed_devices";
        $dbHelper = new PDODbHelper();
        $params = ["token" => $this->device_token, "device_name" => $this->device_name];
        $result = $dbHelper->insert($table, $params);

        if (!$result) {
            self::$errorArray[] += $dbHelper->getErrors();
            return false;
        }

        return true;
    }

    /**
     * @return false|array
     */
    public static function getDevicesTokens() {
        self::$errorArray = [];
        $table = "subscribed_devices";
        $dbHelper = new PDODbHelper();
        $result = $dbHelper->select($table, ["token"]);
        if (!$result) {
            self::$errorArray[] += $dbHelper->getErrors();
            return false;
        }
        $devicesTokens = [];

        foreach ($result as $deviceToken) {
            $devicesTokens[] = $deviceToken['token'];
        }

        return $devicesTokens;
    }

    /**
     * @param Notification $notification
     * @return false|string
     */
    public static function sendNotification($notification) {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $response = ["error" => false];

        $devicesTokens = self::getDevicesTokens();

        if (!$devicesTokens) {
            $response['error'] = true;
            $response['message'] = implode(", ",self::$errorArray);
            echo json_encode($response);
            return false;
        }

        $fields = [
            'registration_ids' => $devicesTokens,
            'data' => json_decode($notification->toJson())
        ];

        $headers = [
            'Authorization:key = ' . self::FIREBASE_API_KEY,
            'Content-Type: application/json'
        ];

        return self::curlRequest($url, $headers, $fields, $response);

    }

    /**
     * @return array
     */
    public function getErrors() {
        return self::$errorArray;
    }

    /**
     * @param string $url
     * @param array $headers
     * @param array $fields
     * @param array $response
     * @return false|string
     */
    private static function curlRequest($url, array $headers, array $fields, array $response) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);

        if ($result === FALSE) {
            $response['error'] = true;
            $response['message'] = curl_error($ch);
        } else {
            $response['message'] = "Success.";
            $response['data'] = json_decode($result);
        }

        curl_close($ch);

        return json_encode($response);
    }

}