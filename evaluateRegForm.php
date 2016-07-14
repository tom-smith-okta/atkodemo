<?php

$apiKey = file_get_contents("/usr/local/keys/oktaAPI.txt");

$oktaHome = "https://tomco.okta.com";

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

// 00g1yq9e5JOWsxFdu1t6

// $userData = '{
//   "id": "' . $userID . '",
//   "scope": "USER",
//   "credentials": {
//     "userName": "' . $userName . '"
//   },
//   "profile": {
//     "profile": "Chatter Free User"
//   }  
// }';

// Assign the user to a group ("externalUsers")
// Apps are assigned to the group within Okta
curl_setopt_array($curl, array(
    CURLOPT_URL => $oktaHome . '/api/v1/groups/00g1yq9e5JOWsxFdu1t6/users/' . $userID,
    CURLOPT_CUSTOMREQUEST => "PUT"
));

$result = curl_exec($curl);
$decodedResult = json_decode($result, TRUE);