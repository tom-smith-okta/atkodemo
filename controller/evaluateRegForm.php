<?php

include "../includes/user.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$regFlow = $_POST["regFlow"];

$thisUser = new user();

if ($_SESSION["siteObj"]->regFlows[$regFlow]["activate"]) {

	$cookieToken = $thisUser->authenticate();

	$thisUser->redirect($cookieToken);

}

exit;


if ($regType == "basic" || $regType == "sfChatter") {


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