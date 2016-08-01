<?php

if (session_id() == '' || !isset($_SESSION)) { session_start(); }

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

$_SESSION["firstName"] = $firstName;

$thisUser = new user($config, $email, $firstName, $lastName, $password);

// echo "<p>creating a new user went OK.";

$thisUser->putOktaRecord();

// echo "<p>putting the okta record went OK.";

$thisUser->assignToOktaGroup();

// echo "<p>assigning to a group went OK.";

$cookieToken = $thisUser->authenticate();

// echo "<p>authentication went OK.";

$thisUser->redirect($cookieToken);

exit;