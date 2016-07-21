<?php

include "includes/includes.php";

/****************************/

$apiKey = $config["apiKey"];

// this might be prettier if I built an assoc array
// and then did json_encode
$userData = '{
	"profile": {
		"firstName": "' . $_POST["firstName"] . '",
		"lastName":  "' . $_POST["lastName"]  . '",
		"email":     "' . $_POST["email"]     . '",
		"login":     "' . $_POST["email"]     . '"
	},
	"credentials": {
		"password": {
			"value": "' . $_POST["password"]  . '"
		}
	}
}';

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_POST => 1,
	CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $config["apiHome"] . "/users?activate=true",
    CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
    CURLOPT_POSTFIELDS => $userData
    ));
$result = curl_exec($curl);

$decodedResult = json_decode($result, TRUE);

if ($decodedResult["id"]) {
	$userID = $decodedResult["id"];
	$userName = $decodedResult["profile"]["login"];
}
else {
	echo "something went wrong with trying to create a user.";
	exit;
}

// Now let's assign this user to the group "externalUsers"
// Use the Okta dashboard to assign apps to the group

$url = $config["apiHome"] . "/groups/" . $config["groupID"] . "/users/" . $userID;

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_CUSTOMREQUEST => "PUT"
));

$result = curl_exec($curl);

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