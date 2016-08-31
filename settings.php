<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

// let's not show our api key in the browser.
$config["apiKey"] = substr($config["apiKey"], 0, 5) . "...";

echo json_encode($config, JSON_PRETTY_PRINT);