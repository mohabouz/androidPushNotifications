<?php

/**
 * Author: Mohammed BOUZAID
 */

class Firebase {

    const FIREBASE_API_KEY = "AAAA4iqmE_Y:APA91bHeiwb416emTo9qctPrAV7TBSsgiGo8zFIttIXBWvQAp-oI1NYgKZtgqjg3hkEGR-sl8laoUB2Trp48IsC5T2VirQMBv6tweJlLqz3147xxrPKu51h4qEYseIOb4pkpYXEGE6xs";

    private $device_token;
    private $device_name;

    /**
     * @param $device_token
     * @param $device_name
     */
    public function __construct($device_token, $device_name) {
        $this->device_token = trim($device_token);
        $this->device_name = trim($device_name);
    }

    /**
     * @param mysqli $db_link
     * @return array|bool
     */
    public function add_device($db_link) {

        $errors_array = array();

        if (empty($this->device_token)) {
            $errors_array[] = "Device token cannot be empty.";
        }

        if (count($errors_array) == 0) {
            $sql = "INSERT INTO subscribed_devices (token, device_name) VALUES (?, ?)";
            $stmt = $db_link->prepare($sql);
            $token_param = null;
            $device_name_param = null;
            $stmt->bind_param("ss", $token_param, $device_name_param);
            $token_param = $this->device_token;
            $device_name_param = $this->device_name;
            if (!$stmt->execute()) {
                $errors_array[] = "Device was not added due to an error: " . $db_link->error;
            }
        }

        if (count($errors_array) > 0) {
            return $errors_array;
        } else {
            return true;
        }
    }

    /**
     * @param mysqli $db_link
     * @return array|bool
     */
    public static function get_devices_tokens($db_link) {

        $sql = "SELECT token FROM subscribed_devices";

        $result = $db_link->query($sql);
        $temp_array = array();

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $temp_array[] = $row['token'];
            }
            $result->close();
            return $temp_array;
        }

        return false;
    }

    /**
     * @param mysqli $db_Link
     * @param Notification $notification
     * @return bool|string
     */
    public static function send_notification($db_Link, $notification) {

        $url = 'https://fcm.googleapis.com/fcm/send';

        $response = array(
            "error" => false
        );

        if (!$devices_tokens = self::get_devices_tokens($db_Link)) {
            $response['error'] = true;
            $response['message'] = "Empty devices list.";
            echo json_encode($response);
            return false;
        }

        $fields = array(
            'registration_ids' => $devices_tokens,
            'data' => json_decode($notification->toJson())
        );

        $headers = array(
            'Authorization:key = ' . self::FIREBASE_API_KEY,
            'Content-Type: application/json'
        );

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