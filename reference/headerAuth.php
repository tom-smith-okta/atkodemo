<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

/*************************************/

$thisPage->setTitle($config["name"] . " - Home");

$elements = [
	"oktaWidgetCSScore",
	"oktaWidgetCSStheme",
	"oktaWidgetCSSlocal",
	"mainCSS",
	"jquery",
	"font-awesome",
//	"okta-signin-widget",
	"index",
	"dates"
];

$headers = getallheaders();

if (array_key_exists("username", $headers)) {

	$username = $headers["username"];
}
else {
	$username = "none";
}

if (array_key_exists("User-Agent", $headers)) {
	$userAgent = substr($headers["User-Agent"], 0, 12) . "...";
}
else {
	$userAgent = "none";
}

$thisPage->setConfigValue("username", $username);
$thisPage->setConfigValue("userAgent", $userAgent); 


$thisPage->addElements($elements);

$thisPage->loadBody("headerAuth", ["username", "userAgent", "name"]);

$thisPage->display();