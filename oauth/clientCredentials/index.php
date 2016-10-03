<?php

echo "<p>in the access token receiver page.";

echo "<p>The GET is: " . json_encode($_GET);

echo "<p>The POST is: " . json_encode($_POST);

// now get the access token

$url = "https://tomco.okta.com/oauth2/v1/token";
$data["grant_type"] = "client_credentials";
$data["response_type"] = "token";
$data["client_id"] = "YYUAPHIAj3JPPO6yJans";
$data["client_secret"] = "Oz-Opjp-22_5HPwnzXRbL6_fn-fD3errWP7LTfWc";

// $data["scope"] = "open_id";
// $data["redirect_uri"] = "http://localhost:8888/atkodemo/oauth/authCode/receiveAccessToken.php";

$jsonData = json_encode($data);

// $url = "http://tomco.okta.com/"

$curl = curl_init();
 
curl_setopt_array($curl, array(
	// CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
	CURLOPT_POST => TRUE,
	CURLOPT_POSTFIELDS => $jsonData,
	CURLOPT_RETURNTRANSFER => TRUE,
	CURLOPT_URL => $url
));

$jsonResult = curl_exec($curl);