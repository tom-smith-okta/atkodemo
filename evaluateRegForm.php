<?php

include "includes/includes.php";

/****************************/

$apiKey = $config["apiKey"];

$email = trim($_POST["email"]);

if (filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE) {

	echo "sorry, " . $email . " does not appear to be a valid email address.";

	exit;
}

// Is this an okta user?
if (substr($email, -9) == "@okta.com") { $userType = "oktaUser"; }
else { $userType = "endUser"; }

$userData = '{
	"profile": {
		"firstName": "' . $_POST["firstName"] . '",
		"lastName":  "' . $_POST["lastName"]  . '",
		"email":     "' . $email     . '",
		"login":     "' . $email     . '"
	},
	"credentials": {
		"password": {
			"value": "' . $_POST["password"]  . '"
		}
	}
}';

$url = $config["apiHome"] . "/users?activate=";

if ($userType == "oktaUser") {
	$url .= "false"; // do not automatically activate the user
	$groupID = $confg["oktaGroupID"];
}
else {
	$url .= "true";
	$groupID = $config["groupID"];
}

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

/************* IF IT'S AN OKTA USER, MAKE THEM AN ADMIN ***********/

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
}

exit;


/*************** AUTHENTICATE THE USER AND REDIRECT **************/

$password = $_POST['password'];

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
	// echo curl_error($curl);
}

$url = $config["oktaBaseURL"] . "/login/sessionCookieRedirect?token=" . $cookieToken . "&redirectUrl=" . $config["redirectURL"];

$headerString = "Location: " . $url; 

header($headerString);

exit;