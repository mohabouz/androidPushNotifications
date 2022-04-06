<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

include "config/db.php";
include "classes/Firebase.class.php";
include "classes/Notification.class.php";

$requestJson = "";
$response = false;

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if (!isset($_POST['title']) ||
        !isset($_POST['message']) ||
        !isset($_POST['long_message']) ||
        !isset($_POST['image_url'])) {

        $response = json_encode([
            "error" => TRUE,
            "message" => "Inputs are not all defined."
        ]);

    } else {

        $notification = new Notification(
            $_POST['title'],
            $_POST['message'],
            $_POST['long_message'],
            $_POST['image_url']
        );

        $response = Firebase::sendNotification($notification);

        if ($response) {
            $notification->saveToDatabase();
        }

    }

}

?>

<html lang="en">
<head>
    <title>Firebase Push Notification System on Android</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js"></script>
    <link rel="shortcut icon" href="//www.gstatic.com/mobilesdk/160503_mobilesdk/logo/favicon.ico">
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
<div class="container">

    <div class="fl_window">
        <div>
            <img src="https://www.gstatic.com/devrel-devsite/prod/vc705ce9bd51279e80f03a51aec7c6eb1f05e56e75c958618655fc719098c9888/firebase/images/lockup.svg"
                 width="200" alt="Firebase"/>
        </div>
        <br/>
        <br/>
        <?php if ($response) { ?>
            <label><b>Response:</b></label>
            <div class="json_preview">
                <pre class="prettyprint"><?php echo $response ?></pre>
            </div>
        <?php } ?>

    </div>

    <form class="pure-form pure-form-stacked" method="POST">
        <fieldset>
            <legend>Firebase Push Notification System on Android</legend>

            <label for="title">Title</label>
            <input type="text" id="title" name="title" class="pure-input-1-2" placeholder="Enter title">

            <label for="message">Message</label>
            <input class="pure-input-1-2" name="message" id="message" placeholder="Short message"/>

            <label for="long_message">Article</label>
            <textarea class="pure-input-1-2" name="long_message" id="long_message" rows="6"
                      placeholder="Enter Full message..."></textarea>

            <label for="image_url">Image URL</label>
            <input type="text" id="image_url" name="image_url" class="pure-input-1-2" placeholder="Enter Image URL">

            <button type="submit" class="pure-button pure-button-primary btn_send">Send Notification</button>

        </fieldset>
    </form>

</div>

<script>

    const preElements = document.querySelectorAll('pre.prettyprint');

    preElements.forEach(e => {
        let obj = JSON.parse(e.innerHTML);
        e.innerHTML = JSON.stringify(obj, null, 2);
    })

</script>

</body>
</html>