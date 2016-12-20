<?php

// include "includes/includes.php";

include "../includes/user.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// echo "the post is: ";

// echo json_encode($_POST);

if (array_key_exists("regFlow", $_POST)) {
	$regFlow = $_POST["regFlow"];
}
else {
	if (empty($_POST["regFlow"])) { $regFlow = "basic"; }
	else { $regFlow = $_POST["regFlow"]; }
}

foreach ($_POST as $key => $value) {
	if ($key != "regFlow") {
		$user[$key] = filter_var($value, FILTER_SANITIZE_STRING);
	}
}

// echo "<p>";

// echo "<p>the user object is: " . json_encode($user);

$thisUser = new user($regFlow, $user);

exit;


if ($regType == "basic" || $regType == "sfChatter") {

	$cookieToken = $thisUser->authenticate();

	$thisUser->redirect($cookieToken);
}
else {

	$msg = "<p>Thank you for registering with us, " . $thisUser->firstName . "!</p>";

	if ($regType == "provisional") {

		$msg .= "<p>You will receive an activation email after your registration has been reviewed.</p>";

	}
	else {

		$msg .= "<p>Please check your inbox for an activation email to complete your registration.</p>";

		if ($regType == "okta") {
			if ($thisUser->hasOktaEmailAddress()) {
				$thisUser->setAdminRights();
			}
		}

		$thisUser->sendActivationEmail();

	}

	$headerString = "Location: " . $config["webHomeURL"] . "thankYou.php?email=" . $thisUser->email;
	$headerString .= "&msg=" . $msg;

	header($headerString);

}