<?php

session_start();

function setSession() {
	$userFullName = $_SESSION["oktaSessionObj"]["_links"]["user"]["name"];

	$_SESSION["oktaCookieSessionID"] = $_SESSION["oktaSessionObj"]["id"];
	$_SESSION["header"] = getHeader("auth", $_SESSION["oktaCookieSessionID"], $userFullName);
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

	$result = sendCurlRequest($curl, $errorMsg);

	if ($result["status"] == "ACTIVE") {
		$_SESSION["oktaSessionObj"] = $result;
		return TRUE;
	}
	else { return FALSE; }
}