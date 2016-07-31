<?php

session_start();

include "includes/includes.php";

/******* check for valid email address syntax *********/
$email = trim($_POST["email"]);

if (filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE) {

	echo "sorry, " . $email . " does not appear to be a valid email address.";

	exit;
}

$firstName = trim($_POST["firstName"]);
$lastName = trim($_POST["lastName"]);
$password = trim($_POST["password"]);

$thisUser = new user($config, $email, $firstName, $lastName, $password);

$thisUser->putOktaRecord();

$thisUser->assignToOktaGroup();

$cookieToken = $thisUser->authenticate();

$thisUser->redirect($cookieToken);

exit;