<?php

include "includes/includes.php";

if (empty($_POST["regType"])) { $regType = "default"; }
else { $regType = $_POST["regType"]; }

foreach ($_POST as $fieldName => $value) {
	if ($fieldName == "regType") {}
	else {
		$user[$fieldName] = filter_var($value, FILTER_SANITIZE_STRING);
	}	
}

$thisUser = new user($regType, $user);

if ($regType == "vanilla" || $regType == "default") {

	$cookieToken = $thisUser->authenticate();

	$thisUser->redirect($cookieToken);
}
else {
	if ($regType == "okta") {
		if ($thisUser->hasOktaEmailAddress()) {
			$thisUser->setAdminRights();
		}
	}

	$headerString = "Location: " . $config["webHomeURL"] . "/thankYou.php"; 

	header($headerString);
}