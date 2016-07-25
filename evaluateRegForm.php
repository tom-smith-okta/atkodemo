<?php

// To be changed before PROD:

// get rid of mailinator
// change the default Okta password schema

include "includes/includes.php";

/****************************/


/******* check for valid email address syntax *********/
$email = trim($_POST["email"]);

if (filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE) {

	echo "sorry, " . $email . " does not appear to be a valid email address.";

	exit;
}

$firstName = trim($_POST["firstName"]);
$lastName = trim($_POST["lastName"]);
$password = trim($_POST["password"]);

$thisUser = new user($config, $email, $firstName, $lastName, $password);

$thisUser->putOktaRecord();

exit;

$thisUser->assignToOktaGroup();

if ($thisUser->type == "regular") {
	$thisUser->authenticateAndRedirect();
}
else if ($thisUser->type == "okta") {
	$thisUser->giveAdminRights();



}


exit;


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