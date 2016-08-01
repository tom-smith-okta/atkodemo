<?php

if (session_id() == '' || !isset($_SESSION)) { session_start(); }

function setSession() {
	global $config;

	if (isset($_SESSION["firstName"])) {
		$firstName = $_SESSION["firstName"];
	}
	else {
		$userID = $_SESSION["oktaSessionObj"]["userId"];

		$url = $config["apiHome"] . "/users/" . $userID;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url
		));

		$errorMsg = "something went wrong with getting the user object";

		$result = sendCurlRequest($curl, $errorMsg);

		$firstName = $result["profile"]["firstName"];
	}

	$_SESSION["oktaCookieSessionID"] = $_SESSION["oktaSessionObj"]["id"];
	$_SESSION["header"] = getHeader("auth", $_SESSION["oktaCookieSessionID"], $firstName);

	$url = $config["apiHome"] . "/users/" . $_SESSION["oktaSessionObj"]["userId"] . "/roles";

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_URL => $url
	));

	$errorMsg = "something went wrong with checking the admin status";

	$result = sendCurlRequest($curl, $errorMsg);

	if ($result) {
		$_SESSION["header"] = getHeader("admin", $_SESSION["oktaCookieSessionID"], $firstName);	
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

	$result = sendCurlRequest($curl, $errorMsg);

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