<?php

include "includes/includes.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION["apiKey"]);
unset($_SESSION["oktaOrg"]);

$headerString = "Location: " . $config["webHomeURL"] . "/customizeRegForm.php"; 

header($headerString);

exit;