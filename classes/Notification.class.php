<?php

class Notification {

    private $title;
    private $message;
    private $long_message;
    private $image_url;

    /**
     * @param string $title
     * @param string $message
     * @param string $long_message
     * @param string $image_url
     */
    public function __construct($title, $message, $long_message, $image_url) {
        $this->title = trim($title);
        $this->message = trim($message);
        $this->long_message = trim($long_message);
        $this->image_url = trim($image_url);
    }

    /**
     * @return false|string
     */
    public function toJson() {
        return json_encode([
            "title" => $this->title,
            "message" => $this->message,
            "long_message" => $this->long_message,
            "image_url" => $this->image_url
        ]);
    }

    /**
     * @return bool
     */
    public function saveToDatabase() {
        $table = "notifications";
        $params = [
            "title" => $this->title,
            "message" => $this->message,
            "long_message" => $this->long_message,
            "image_url" => $this->image_url
        ];

        $dbHelper = new PDODbHelper();
        return $dbHelper->insert($table, $params);
    }

}