<?php

session_start();

function setSession() {
	global $config;

	$userFullName = $_SESSION["oktaSessionObj"]["_links"]["user"]["name"];

	$_SESSION["oktaCookieSessionID"] = $_SESSION["oktaSessionObj"]["id"];
	$_SESSION["header"] = getHeader("auth", $_SESSION["oktaCookieSessionID"], $userFullName);

	$url = $config["apiHome"] . "/users/" . $_SESSION["oktaSessionObj"]["userId"] . "/roles";

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_URL => $url
	));

	$jsonResult = sendCurlRequest($curl, $errorMsg, TRUE);

	$result = json_decode($jsonResult, TRUE);

	if ($result) {
		$_SESSION["header"] = getHeader("admin", $_SESSION["oktaCookieSessionID"], $userFullName);	
	}
}

function oktaSessionIsValid($oktaCookieSessionID) {
	global $config;

	$url = $config["apiHome"] . "/sessions/" . $oktaCookieSessionID;

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_URL => $url
	));

	$errorMsg = "<p>something went wrong with checking the session";

	$jsonResult = sendCurlRequest($curl, $errorMsg, TRUE);

	$result = json_decode($jsonResult, TRUE);

	if (array_key_exists("errorCode", $result)) {
		session_destroy();
		return FALSE;
	}
	else if ($result["status"] == "ACTIVE" || $result["status"] == "MFA_REQUIRED") {
		$_SESSION["oktaSessionObj"] = $result;
		return TRUE;
	}
	else { return FALSE; }
}