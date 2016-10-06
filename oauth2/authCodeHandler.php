<?php

/******************************************/

// Okta OAuth2 flows
// tom.smith@okta.com

/******************************************/

// Authorization Code Grant flow

// BASIC CONFIGURATION

$oktaOrg = "tomco"; // your Okta org name

$clientID = "YYUAPHIAj3JPPO6yJans"; // the client ID from your Okta OAuth app

// Load your client secret from a location not exposed to the web
$clientSecret = trim(file_get_contents("/usr/local/keys/tomcoSecret.txt"));

// This url needs to be whitelisted in your Okta OAuth app
// It must match the redirect_uri used in the call to create
// the authorization code
$redirect_uri = "http://localhost:8888/atkodemo/oauth2/getAuthCode.html";

/******************************************/

// PULL THE AUTHORIZATION CODE FROM
// THE GET REQUEST

if (array_key_exists("code", $_GET)) {
	$authorizationCode = $_GET["code"];
}

// $authorizationCode = "aXSbOtaN-U8hSGrmB3TL"; // for debugging

/*****************************************/

// PREPARE THE CALL TO THE AUTHORIZATION SERVER

$url = "https://" . $oktaOrg . ".okta.com/oauth2/v1/token";

// Create your Basic Auth string
// ${Base64(<client_id>:<client_secret>)} 
$authString = base64_encode($clientID . ":" . $clientSecret);

// Set data parameters
$data["grant_type"] = "authorization_code";
$data["code"] = $authorizationCode;
$data["scope"] = "openid";
$data["redirect_uri"] = "http://localhost:8888/atkodemo/oauth2/getAuthCode.html";

// Put data in a form that the endpoint will like (not json)
// Might be a more standard way to do this
$postString = "";

foreach ($data as $key => $value) {
	$postString .= $key . "=" . $value . "&";
}

$postString = trim($postString, "&");

// Set up curl request

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_POST => TRUE,
	CURLOPT_POSTFIELDS => $postString,
	CURLOPT_HTTPHEADER => array("Authorization: Basic $authString", "Accept: application/json", "Content-Type: application/x-www-form-urlencoded"),
	CURLOPT_RETURNTRANSFER => TRUE,
	CURLOPT_URL => $url
));

/*****************************************/

// MAKE THE CALL TO THE AUTHORIZATION SERVER

$jsonResult = curl_exec($curl);

if (curl_error($curl)) {
	echo curl_error($curl);
}
else {
	// Success
	
	// Convert the json result into an associative array
	$result = json_decode($jsonResult, TRUE);

	$accessToken = $result["access_token"];

	// now you have the access token so you can do something
	// cool like make a call to an API gateway.

	// For the purposes of the demo, I am going to 
	// send the access token and the rest of the json back to the browser.
	// In a real site you would not do this.

	echo $jsonResult;

}
