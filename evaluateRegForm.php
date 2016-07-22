<?php

include "includes/includes.php";

/****************************/

$apiKey = $config["apiKey"];

$email = trim($_POST["email"]);

if (filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE) {

	echo "sorry, " . $email . " does not appear to be a valid email address.";

	exit;
}

// First, we'll assume that this is a regular user
$userType = "regularUser";
$password = $_POST["password"];
$groupID = $config["groupID"];

// Next, we'll check to see whether this is an Okta user

/********************************************************/
// MAKE SURE YOU CHANGE THIS BEFORE GOING TO PROD
/********************************************************/

$validEmailDomains = array("@okta.com", "@mailinator.com");

foreach ($validEmailDomains as $domain) {

	$offset = 0 - strlen($domain);

	if (substr($email, $offset) == $domain) { 
		// This is an Okta user
		$userType = "oktaUser";
		$password = "Atko1234!";
		$groupID = $confg["oktaGroupID"];
	}
}

$userData = '{
	"profile": {
		"firstName": "' . $_POST["firstName"] . '",
		"lastName":  "' . $_POST["lastName"]  . '",
		"email":     "' . $email     . '",
		"login":     "' . $email     . '"
	},
	"credentials": {
		"password": {
			"value": "' . $password  . '"
		}
	}
}';

$url = $config["apiHome"] . "/users?activate=true";

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_POST => 1,
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_URL => $url,
    CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
    CURLOPT_POSTFIELDS => $userData
));

$result = curl_exec($curl);

$decodedResult = json_decode($result, TRUE);

if (array_key_exists("id", $decodedResult)) {
	// success
	$userID = $decodedResult["id"];
	$userName = $decodedResult["profile"]["login"];
}
else {
	echo "<p>Sorry, there was an error trying to create that user:</p>";
	$errorCause = $decodedResult["errorCauses"][0]["errorSummary"];

	echo "<p>" . $errorCause;

	exit;
}

/************** ASSIGN THE USER TO AN OKTA GROUP *******************/

// If it's a regular end-user then assign the user to the group "externalUsers"
// If it's an okta user then assign the user to the group "OktaAdmin"

$url = $config["apiHome"] . "/groups/" . $groupID . "/users/" . $userID;

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_CUSTOMREQUEST => "PUT"
));

$result = curl_exec($curl);

$decodedResult = json_decode($result, TRUE);

if (array_key_exists("errorCauses", $decodedResult)) {
	// something went wrong
	echo "<p>Sorry, there was an error trying to assign that user to a group:</p>";
	
	echo "<p>" . $decodedResult["errorCauses"][0]["errorSummary"];

	exit;
}

/******************* IF IT'S AN OKTA USER **********/

/******************** MAKE THEM AN ADMIN ***********/

if ($userType == "oktaUser") {
	$url = $config["apiHome"] . "/users/" . $userID . "/roles";

	$roleData = '{ "type": "READ_ONLY_ADMIN" }';

	curl_setopt_array($curl, array(
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url,
		CURLOPT_POSTFIELDS => $roleData
	));

	$result = curl_exec($curl);

	$decodedResult = json_decode($result, TRUE);

	if (array_key_exists("errorCauses", $decodedResult)) {
		// something went wrong
		echo "<p>Sorry, there was an error trying to give that user an admin role:</p>";
		
		echo "<p>" . $decodedResult["errorCauses"][0]["errorSummary"];

		exit;
	}

	/************** AND SEND THEM A RESET PASSWORD EMAIL ******/

	// {{url}}/api/v1/users/{{userId}}/credentials/forgot_password?sendEmail=false
	// $url = $config["apiHome"] . "/users/" . $userID . "/credentials/forgot_password?sendEmail=true";

	$url = $config["apiHome"] . "/authn/recovery/password";

	$userData = '{
		"username": "' . $email . '",
		"factorType": "EMAIL",
		"relayState": "' . $config["redirectURL"] . '"
	}';

	curl_setopt_array($curl, array(
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url,
		CURLOPT_POSTFIELDS => $userData
	));

	$result = curl_exec($curl);

	$decodedResult = json_decode($result, TRUE);

	if (array_key_exists("errorCauses", $decodedResult)) {
		// something went wrong
		echo "<p>Sorry, there was an error trying to send that user a reset password email:</p>";
		
		echo "<p>" . $decodedResult["errorCauses"][0]["errorSummary"];

		echo "<pre>" . print_r($decodedResult) . "</pre>";

		exit;
	}
	else {
		echo "<p>we have sent you an email to verify your okta.com email address.</p>";
		echo "<p>please verify your email and come back to the site to log in.</p>";

		exit;
	}
}
else {
	/*************** AUTHENTICATE THE USER AND REDIRECT **************/

	$userData = '{
		"username": "' . $userName . '",
		"password": "' . $password . '"
	}';

	$url = $config["apiHome"] . "/sessions?additionalFields=cookieToken";

	curl_setopt_array($curl, array(
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url,
		CURLOPT_POSTFIELDS => $userData
	));

	$result = curl_exec($curl);

	$decodedResult = json_decode($result, TRUE);

	if ($decodedResult["cookieToken"]) {

		$cookieToken = $decodedResult["cookieToken"];

	}
	else {
		echo "<p>Sorry, there was an error trying to authenticate the new user:</p>";
			
		echo "<p>" . $decodedResult["errorCauses"][0]["errorSummary"];
	}

	$url = $config["oktaBaseURL"] . "/login/sessionCookieRedirect?token=" . $cookieToken . "&redirectUrl=" . $config["redirectURL"];

	$headerString = "Location: " . $url; 

	header($headerString);

	exit;
}