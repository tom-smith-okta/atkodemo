<?php

include "includes.php";

global $config; // not sure this is necessary

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$apiKey = trim($_POST["apiKey"]);
$oktaOrg = trim($_POST["oktaOrg"]);

$url = "https://" . $oktaOrg . ".okta.com/api/v1/meta/schemas/user/default";

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
	CURLOPT_RETURNTRANSFER => TRUE,
	CURLOPT_URL => $url
));

$jsonResult = curl_exec($curl);

$assocArray = json_decode($jsonResult, TRUE);

if (array_key_exists("errorCode", $assocArray)) {
	echo "<p>sorry, there was an error: ";

	echo "<p>" . $jsonResult;
}
else {
	$config["userSchema"] = $jsonResult;

	file_put_contents("../userSchema.txt", $config["userSchema"]);

	$_SESSION["apiKey"] = $apiKey;
	$_SESSION["oktaOrg"] = $oktaOrg;

	$headerString = "Location: " . $config["webHomeURL"] . "/customizeRegForm.php"; 

	header($headerString);
}