<?php

require("config.php");

$link = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}




