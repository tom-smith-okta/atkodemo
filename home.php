<?php

$home = "atkotravel"; // establishes homedir in webdir

include $_SERVER['DOCUMENT_ROOT'] . "/" . $home . "/includes/includes.php";

$thisPage = new htmlPage($config);

/*************************************/

if (empty($_GET["oktaCookieSessionID"])) {

	$state = "unAuth";

	$header = getHeader();

	// $topMenu = "\n<li><a href='login.php'>Log in</a></li>";
	// $topMenu .= "\n<li><a href = 'register.php'>Register</a></li>";
}
else {

	$apiKey = $config["apiKey"];

	// in a production system I would check the oktaCookieSessionID here
	// again to make sure that someone has not messed with the GET call

	$oktaUserID = $_GET["oktaUserID"];

	$url = $config["apiHome"] . "/users/" . $oktaUserID;

	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url,
		CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
	));

	$result = curl_exec($curl);

	$user = json_decode($result, TRUE);

	$firstName = $user["profile"]["firstName"];

	$header = getAuthHeader($_GET["oktaCookieSessionID"], $firstName);

	// $topMenu = "\n<li><a href='" . $config["salesforce"] . "' target = '_blank'>Chatter</a></li>";

	// $logoutLink = "logout.php?oktaCookieSessionID=" . $_GET["oktaCookieSessionID"];

	// $topMenu .= "\n<li><a href = '" . $logoutLink . "'>Log out</a></li>";

	// $topMenu .= "\n<li><a href = '#'>Welcome, " .  $firstName . "!</a></li>";

}

/*** Manually add elements here ******/

$thisPage->setTitle("Atko Travel Agency Home");

// jquery
$thisPage->addElement("jquery");

$thisPage->addElement("mainCSS");

$thisPage->addElement("dates");

$body = file_get_contents("home.html");

$body = str_replace("%HEADER%", $header, $body);

$thisPage->addToBody($body);

$thisPage->display();