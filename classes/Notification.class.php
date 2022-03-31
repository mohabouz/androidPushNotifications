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

    public function toJson() {
        $arr = array(
            "title" => $this->title,
            "message" => $this->message,
            "long_message" => $this->long_message,
            "image_url" => $this->image_url
        );
        return json_encode($arr);
    }

    /**
     * @param mysqli $db_link
     * @return boolean
     */
    public function save_to_database($db_link) {
        $sql = "INSERT INTO notifications (title, message, long_message, image_url) VALUES (?, ?, ?, ?)";
        $stmt = $db_link->prepare($sql);
        $stmt->bind_param(
            "ssss",
            $title_param,
            $message_param,
            $long_message_param,
            $image_url_param
        );

        $title_param = $this->title;
        $message_param = $this->message;
        $long_message_param = $this->long_message;
        $image_url_param = $this->image_url;

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }


}