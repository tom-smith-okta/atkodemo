<?php

session_start();

include "includes/includes.php";

/******************************/

$oktaCookieSessionID = $_GET["oktaCookieSessionID"];

$url = $config["apiHome"] . "/sessions/" . $oktaCookieSessionID;

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_CUSTOMREQUEST=> "DELETE",
	CURLOPT_URL => $url,
));

$errorMsg = "<p>Sorry, something went wrong trying to kill the session.";

$jsonResult = sendCurlRequest($curl, $errorMsg, TRUE);

$result = json_decode($jsonResult, TRUE);

if (array_key_exists("errorCode", $result)) {

	if ($result["errorCode"] == "E0000007") {
		// okta session was already expired. continue.
	}
}

session_destroy();

$headerString = "Location: " . $config["webHomeURL"];

header($headerString);