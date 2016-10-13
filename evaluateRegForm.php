<?php

include "includes/includes.php";

// echo "the post is: ";

// echo json_encode($_POST);

if (array_key_exists("flowType", $_POST)) {
	$regType = $_POST["flowType"];
}
else {
	if (empty($_POST["regType"])) { $regType = "basic"; }
	else { $regType = $_POST["regType"]; }
}

foreach ($_POST as $fieldName => $value) {
	if ($fieldName == "basic" || $fieldName == "flowType") {}
	else {
		$user[$fieldName] = filter_var($value, FILTER_SANITIZE_STRING);
	}
}

// echo "<p>";

// echo "<p>the user object is: " . json_encode($user);

$thisUser = new user($regType, $user);

if ($regType == "basic" || $regType == "sfChatter") {

	$cookieToken = $thisUser->authenticate();

	$thisUser->redirect($cookieToken);
}
else {
	if ($regType == "okta") {
		if ($thisUser->hasOktaEmailAddress()) {
			$thisUser->setAdminRights();
		}
	}

	$thisUser->sendActivationEmail();

	$headerString = "Location: " . $config["webHomeURL"] . "thankYou.php?email=" . $thisUser->email;
	$headerString .= "&firstName=" . $thisUser->firstName;

	header($headerString);
}