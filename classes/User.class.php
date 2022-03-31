<?php 

class User {

    /**
     * @var string
     */
    private $username;

    /**
     * @param string $username
     */
    public function __construct($username) {
        $this->username = trim($username);
    }

    /**
     * @param mysqli $db_link
     * @param string $password
     * @param string $conf_password
     * @return array|bool
     */
    public function user_signup($db_link, $password, $conf_password) {

        $errors_array = array();

        $password = trim($password);
        $conf_password = trim($conf_password);
    
        if ($password != $conf_password) {
            $errors_array[] = "Password and confirmation password do not match.";
        }

        if (count($errors_array) == 0) {
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = $db_link->prepare($sql);
            $username_param = null;
            $password_param = null;
            $stmt->bind_param("ss", $username_param, $password_param);
            $username_param = $this->username;
            $password_param = password_hash($password, PASSWORD_DEFAULT);
            if (!$stmt->execute()) {
                $errors_array[] = "User creation error: " . $db_link->error;
            }
        }

        if (count($errors_array) > 0) {
            return $errors_array;
        } else {
            return true;
        }

    }

    /**
     * @param $db_link
     * @param $password
     * @return array|bool
     */
    public function user_login($db_link, $password) {
        $errors_array = array();

        if (empty($this->username) || empty($password)) {
            $errors_array[] = "Username and/or password are empty.";
        }

        if (count($errors_array) == 0) {
            $sql = "SELECT password FROM users WHERE username = ?";
            $stmt = $db_link->prepare($sql);
            $username_param = null;
            $stmt->bind_param("s", $username_param);
            $username_param = $this->username;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows() == 1) {
                    $db_password = null;
                    $stmt->bind_result($db_password);
                    $stmt->fetch();
                    if (password_verify($password, $db_password)) {
                        return true;
                    } else {
                        $errors_array[] = "Username or/and password are not correct.";
                    }
                } else {
                    // array_push($errors_array, "Username or/and password are not correct.");
                    $errors_array[] = "Username not found.";
                }
            }
        }

        return $errors_array;
    }

    /**
     * @return void
     */
    public function set_session() {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $this->username;
    }

}
