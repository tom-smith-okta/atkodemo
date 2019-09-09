<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function curlRequest($path, $postFields) {

	$apiKey = $_SESSION["site"]->apiKey;

	$apiHome = $_SESSION["site"]->apiHome;

	$url = $apiHome . $path;

	$curl = curl_init();

	echo "<br>the path is: " . $url;

	echo "<br>the postFields are: " . $postFields;

	echo "<br>the api key is: " . $apiKey;

	curl_setopt_array($curl, array(
		CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
		CURLOPT_POST => TRUE,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_URL => $url,
		CURLOPT_POSTFIELDS => $postFields
	));

	$jsonResult = curl_exec($curl);

	echo "<br>json result: " . $jsonResult;

	if (curl_error($curl)) {
		echo curl_error($curl);
		exit;
	}

	curl_close($curl);

	$result = json_decode($jsonResult, TRUE);

	if (array_key_exists("errorCode", $result)) {
		echo $jsonResult;
		exit;
	}
	return $result;
}