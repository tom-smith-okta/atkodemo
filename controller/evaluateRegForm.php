<?php

include "../includes/user.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$regFlow = $_POST["regFlow"];

$thisUser = new user();

$_SESSION["user"] = $thisUser;

if ($_SESSION["siteObj"]->regFlows[$regFlow]["activate"]) {

	$cookieToken = $thisUser->authenticate();

	$thisUser->redirect($cookieToken);
}
else {

	// $msg = "<p>Thank you for registering with us, " . $thisUser->firstName . "!</p>";

	if ($regFlow == "provisional") {

		$msg .= "<p>You will receive an activation email after your registration has been reviewed.</p>";

	}
	else {

		// $msg .= "<p>Please check your inbox for an activation email to complete your registration.</p>";

		// if ($regType == "okta") {
		// 	if ($thisUser->hasOktaEmailAddress()) {
		// 		$thisUser->setAdminRights();
		// 	}
		// }

		$thisUser->sendActivationEmail();

	}

	$headerString = "Location: " . $_SESSION["siteObj"]->webHome . "views/thankYou.php";

	// echo "<p>The header string is: " . $headerString;

	// $headerString = "Location: " . $config["webHomeURL"] . "thankYou.php?email=" . $thisUser->email;
	// $headerString .= "&msg=" . $msg;

	header($headerString);

}