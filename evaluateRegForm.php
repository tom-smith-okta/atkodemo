<?php

include "includes/includes.php";

if (empty($_POST["regType"])) { $regType = "default"; }
else { $regType = $_POST["regType"]; }

foreach($_POST as $fieldName => $value) {
	if ($fieldName == "regType") {}
	else {
		$user[$fieldName] = filter_var($value, FILTER_SANITIZE_STRING);
	}	
}

// $thisUser = new user($config, $regType, $user);

$thisUser = new user($regType, $user);

exit;

// $thisUser = new user($config, $email, $firstName, $lastName, $password);

$thisUser->putOktaRecord();

$thisUser->assignToOktaGroup();

$cookieToken = $thisUser->authenticate();

$thisUser->redirect($cookieToken);

exit;