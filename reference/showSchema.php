<?php

include "includes/includes.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION["userSchema"])) {
	$_SESSION["userSchema"] = new userSchema();
}
$userSchema = $_SESSION["userSchema"]; 

$userSchema->display();

exit;