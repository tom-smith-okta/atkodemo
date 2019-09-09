<?php

include "../includes/user.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$regFlow = $_POST["regFlow"];

$_SESSION["regFlow"] = $regFlow;

$thisUser = new user();

$_SESSION["user"] = $thisUser;

// $cookieToken = $thisUser->authenticate();

// $thisUser->redirect($cookieToken);

// exit;

if ($_SESSION["site"]->regFlows[$regFlow]["activate"]) {

	$cookieToken = $thisUser->authenticate();

	$thisUser->redirect($cookieToken);
}
else {

	if (array_key_exists("ALLOW_ADMIN_REG", $_SESSION["site"]->regFlows[$regFlow])) {

		if ($_SESSION["site"]->regFlows[$regFlow]["ALLOW_ADMIN_REG"] === TRUE) {

			if ($thisUser->hasRequiredEmailAddress()) {

				$thisUser->setAdminRights();

			}
			else {
				echo "Sorry, that email address is not authorized for admin access.";
				exit;
			}
		}
	}

	if ($_SESSION["regFlow"] == "provisional") {}
	else {
		$thisUser->sendActivationEmail();
	}


	$headerString = "Location: " . "/views/thankYou.php";

	header($headerString);
}