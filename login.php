
<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
//    header("Location: index.php");
}

require_once("config/db.php");
include("classes/User.class.php");

$errors_array = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        $errors_array[] = "username or/and password not given.";
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $user = new User($_POST['username']);

    $login_result = $user->user_login($link, $_POST['password']);

    if(gettype($login_result) == "array") {
        $errors_array += $login_result;
    } else {
         $user->set_session();
        header("Location: index.php");
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="" method="POST">
        <label for="username">Username: </label>
        <input type="text" id="username" name="username" /><br />
        <label for="password">Password</label>
        <input type="password" name="password" id="password" /><br />
        <input type="submit" value="Submit">
        <?php

        if (count($errors_array) > 0) {
            for ($i = 0; $i < count($errors_array); $i++) {
                echo $errors_array[$i] . "<br />";
            }
        }

        ?>
    </form>
</body>

</html>