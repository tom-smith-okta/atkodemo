<?php

include "../includes/includes.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

echo "<p>the value for regFormType in the session is: " . $_SESSION["regFormType"];

foreach($_SESSION["regFields"] as $field) { echo "<p>existing reg field is: " . $field; }


echo "<p>reached the evaluate New Reg Form script.";

echo "<p>";

echo json_encode($_POST);

foreach ($_POST as $field => $value) {

	// echo "<p>the value submitted was: " . 
}

// if (empty($_POST["regType"])) { $regType = "default"; }
// else { $regType = $_POST["regType"]; }

// foreach ($_POST as $fieldName => $value) {
// 	if ($fieldName == "regType") {}
// 	else {
// 		$user[$fieldName] = filter_var($value, FILTER_SANITIZE_STRING);
// 	}	
// }

// $thisUser = new user($regType, $user);

// if ($regType == "vanilla" || $regType == "default") {

// 	$cookieToken = $thisUser->authenticate();

// 	$thisUser->redirect($cookieToken);
// }
// else {
// 	if ($regType == "okta") {
// 		if ($thisUser->hasOktaEmailAddress()) {
// 			$thisUser->setAdminRights();
// 		}
// 	}

// 	$thisUser->sendActivationEmail();

// 	$headerString = "Location: " . $config["webHomeURL"] . "/thankYou.php?email=" . $thisUser->email; 

// 	header($headerString);
// }