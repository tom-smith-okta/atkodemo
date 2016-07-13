<?php

define("HOME", "atkoTravel"); // home dir on webserver

include $_SERVER['DOCUMENT_ROOT'] . "/" . HOME . "/includes/includes.php";

$thisPage = new htmlPage();

if (empty($_COOKIE["oktaCookieSessionID"])) {
	$topMenu = "\n<li><a href='login.php'>Log in</a></li>";
	$topMenu .= "\n<li><a href = 'register.php'>Register</a></li>";
}
else {
	$oktaUserID = $_COOKIE["oktaUserID"];

	$apiKey = file_get_contents("/usr/local/keys/oktaAPI.txt");

	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => "https://tomco.okta.com/api/v1/users/" . $oktaUserID,
		CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
	));

	$result = curl_exec($curl);

	$user = json_decode($result, TRUE);

	$firstName = $user["profile"]["firstName"];

	$topMenu = "\n<li><a href='https://tomco.okta.com/home/salesforce/0oapq5e1G3yk5Syeg1t5/46'>Chatter</a></li>";
	$topMenu .= "\n<li><a href = 'register.php'>Welcome, " .  $firstName . "!</a></li>";

}

/*** Manually add elements here ******/

$thisPage->setTitle("Atko Travel Agency");

// jquery
$thisPage->addElement("javascript", "https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js");

// okta sign-in widget js
$thisPage->addElement("javascript", "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/1.3.3/js/okta-sign-in-1.3.3.min.js");

// okta sign-in widget css
$thisPage->addElement("css", "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/1.3.3/css/okta-sign-in-1.3.3.min.css");

// okta sign-in widget css - customizable
$thisPage->addElement("css", "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/1.3.3/css/okta-theme-1.3.3.css");

$body = file_get_contents("home.html");

$body = str_replace("%TOPMENU%", $topMenu, $body);

$thisPage->addToBody($body);

$thisPage->display();

