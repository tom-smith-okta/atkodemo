<?php

$apiKey = file_get_contents("/usr/local/keys/oktaAPI.txt");

$oktaHome = "https://tomco.okta.com";

// this would probably be prettier if I built an assoc array
// and then did json_encode
$userData = '{
	"profile": {
		"firstName": "' . $_POST["firstName"] . '",
		"lastName":  "' . $_POST["lastName"]  . '",
		"email":     "' . $_POST["email"]     . '",
		"login":     "' . $_POST["login"]     . '"
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
    CURLOPT_URL => $oktaHome . '/api/v1/users?activate=true',
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

$groupID = "00g1yq9e5JOWsxFdu1t6";

$url = $oktaHome . "/api/v1/groups/" . $groupID . "/users/" . $userID;

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_CUSTOMREQUEST => "PUT"
));

$result = curl_exec($curl);

// header( 'Location: http://localhost:8888/atkotravel/login.php?userID=' . $userID ) ;

// header( 'Location: http://localhost:8888/atkotravel/login.php') ;

$password = $_POST['password'];

$userData = '{
	"username": "' . $userName . '",
	"password": "' . $password . '",
	"options": {
    	"multiOptionalFactorEnroll": false,
    	"warnBeforePasswordExpired": false
  	}  
}';

$url = $oktaHome . "/api/v1/authn";

curl_setopt_array($curl, array(
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_URL => $url,
	CURLOPT_POSTFIELDS => $userData
));

$result = curl_exec($curl);

$decodedResult = json_decode($result, TRUE);

$sessionToken = $result->sessionToken;

$url = "https://tomco.okta.com/login/sessionCookieRedirect?token=" . $sessionToken . "&redirectUrl=http://localhost:8888/atkotravel"; 

header('Location: ' . $url);

// error-checking code
// if ($decodedResult["status"] == "SUCCESS") {
// 	echo "i have a sessionToken";
// 	echo "the session token is: " . $decodedResult["sessionToken"];
// }
// elseif ($decodedResult["errorSummary"]) {
// 	echo "the error was: " . $decodedResult["errorSummary"];
// }
// else {
// 	echo curl_error($curl);
// }