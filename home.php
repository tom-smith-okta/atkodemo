<?php

session_start();

include "includes/includes.php";

$thisPage = new htmlPage($config);

/*************************************/


// default state = unAuthenticated
// the "header" is the nav menu at the top of the page
// displays "Log In | Register" for unauth
// or something more customized when the user is authed
$_SESSION["header"] = getHeader("unAuth");

// Summary: if i've received an oktaCookieSessionID and it's valid, then store the
// okta session object in my local session.
// Same if there is already an oktaCookieSessionID in my local session: if it's
// valid, then store the okta session object in my local session.
if (isset($_GET["oktaCookieSessionID"])){
	if (oktaSessionIsValid($_GET["oktaCookieSessionID"])) {
		setSession();
	}
}
else if (isset($_SESSION["oktaCookieSessionID"])) {
	if (oktaSessionIsValid($_SESSION["oktaCookieSessionID"])) {
		setSession();
	}
}

/*** Manually add elements here ******/

$thisPage->setTitle($config["name"] . " Home");

// jquery
$thisPage->addElement("jquery");

$thisPage->addElement("mainCSS");

$thisPage->addElement("dates");

$thisPage->addElement("font-awesome");

$body = file_get_contents("home.html");

$body = str_replace("%HEADER%", $_SESSION["header"], $body);

$body = str_replace("%NAME%", $config["name"], $body);

$body = str_replace("%LOGO%", $config["logo"], $body);

$thisPage->addToBody($body);

$thisPage->display();