<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

echo json_encode($config["warnings"], JSON_PRETTY_PRINT);