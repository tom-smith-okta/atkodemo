<?php

$oktaCookieSessionID = $_GET["oktaCookieSessionID"];

// pull my api key from a file not exposed to the web
$apiKey = file_get_contents("/usr/local/keys/oktaAPI.txt");

// in a production system I would check the oktaCookieSessionID here
// again to make sure that someone has not messed with the GET call

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_CUSTOMREQUEST=> "DELETE",
	CURLOPT_URL => "https://tomco.okta.com/api/v1/sessions/" . $oktaCookieSessionID,
	CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
));

$result = curl_exec($curl);

$decodedResult = json_decode($result, TRUE);

header( 'Location: http://localhost:8888/atkotravel/home.php' );