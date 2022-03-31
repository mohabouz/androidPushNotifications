<?php

require_once("config/db.php");
include("classes/User.class.php");

$errors_array = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['confPassword'])) {
        $errors_array[] = "Inputs are not defined.";
    }

    $user = new User($_POST['username']);

    $result = $user->user_signup($link, $_POST['password'], $_POST['confPassword']);

    if (gettype($result) == "boolean") {
        $success = true;
    } else {
        $errors_array += $result;
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
    <input type="text" id="username" name="username"/><br/>
    <label for="password">Password</label>
    <input type="password" name="password" id="password"/><br/>
    <label for="confPassword">Confirm password</label>
    <input type="password" name="confPassword" id="confPassword"/><br/>
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