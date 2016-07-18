<?php

$home = "atkotravel"; // establishes homedir in webdir

include $_SERVER['DOCUMENT_ROOT'] . "/" . $home . "/includes/includes.php";

/******************************/

$oktaCookieSessionID = $_GET["oktaCookieSessionID"];

$apiKey = $config["apiKey"];

$url = $config["apiHome"] . "/sessions/" . $oktaCookieSessionID;

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_CUSTOMREQUEST=> "DELETE",
	CURLOPT_URL => $url,
	CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
));

$result = curl_exec($curl);

$decodedResult = json_decode($result, TRUE);

$headerString = "Location: " . $config["webHomeURL"];

header($headerString);