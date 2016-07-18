<?php

$oktaCookieSessionID = $_GET["oktaCookieSessionID"];

$apiKey = $config["apiKey"];

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