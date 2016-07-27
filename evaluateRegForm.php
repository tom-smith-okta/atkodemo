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

if ($thisUser->type == "regular") {
	$thisUser->authenticateAndRedirect();
}
else if ($thisUser->type == "okta") {
	$thisUser->setAdminRights();

	$_SESSION["nonce"] = rand();

	$_SESSION["userID"] = $thisUser->userID;

	$url = $config["webHomeURL"] . "/securityQuestion.php";

	$headerString = "Location: " . $url;

	header($headerString);
}

exit;