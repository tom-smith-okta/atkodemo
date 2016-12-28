<?php

include "../includes/user.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$regFlow = $_POST["regFlow"];

$_SESSION["regFlow"] = $regFlow;

$thisUser = new user();

$_SESSION["user"] = $thisUser;

if ($_SESSION["siteObj"]->regFlows[$regFlow]["activate"]) {

	$cookieToken = $thisUser->authenticate();

	$thisUser->redirect($cookieToken);
}
else {

	if (array_key_exists("ALLOW_ADMIN_REG", $_SESSION["siteObj"]->regFlows[$regFlow])) {

		if ($_SESSION["siteObj"]->regFlows[$regFlow]["ALLOW_ADMIN_REG"] === TRUE) {

			if ($thisUser->hasRequiredEmailAddress()) {

				$thisUser->setAdminRights();

			}
			else {
				echo "Sorry, that email address is not authorized for admin access.";
				exit;
			}
		}
	}

	$thisUser->sendActivationEmail();

	$headerString = "Location: " . $_SESSION["siteObj"]->webHome . "views/thankYou.php";

	header($headerString);
}