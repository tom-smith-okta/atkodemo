<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "../includes/demoEnv.php";
include "../includes/demoSite.php";

if (!(array_key_exists("env", $_SESSION))) {
	setDemoEnv();
}

if (empty($_SESSION["demo"]["site"])) {
	$siteName = $_SESSION["env"]["defaultSite"];

	$_SESSION["demo"]["site"] = new demoSite($siteName);

	// $_SESSION["demo"]["site"] = $thisSite;
}

foreach($_SESSION["demo"]["sites"] as $siteName) {

	$thisSite = new demoSite($siteName);

	// echo "<p><b>" . $thisSite->siteName . "</b></p>";

	// echo "<p>the base URL is: " . $thisSite->apiHome;

	// echo "<p> the apikey is: " . $thisSite->apiKey;

	// $thisSite->showSettings();


}

$thisSite = new demoSite("tomslocalhost");


$thisSite->showPage("index");


session_destroy();