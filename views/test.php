<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "../includes/demoEnv.php";

if (!(array_key_exists("demo", $_SESSION))) {
	setDemoEnv();
}

if (empty($_SESSION["demo"]["siteToLoad"])) {
	$_SESSION["demo"]["siteToLoad"] = $_SESSION["demo"]["env"]["defaultSite"];
}

include "../includes/demoSite.php";

echo "<p>the include path is: " . get_include_path();

echo "<p>the env name is: " . $_SESSION["demo"]["env"]["name"];

echo "<p>the home dir is: " . $_SESSION["demo"]["homeDir"];

echo "<p>the default site is: " . $_SESSION["demo"]["env"]["defaultSite"];

echo "<p>the list of sites is: " . json_encode($_SESSION["demo"]["sites"]);

foreach($_SESSION["demo"]["sites"] as $siteName) {

	$thisSite = new demoSite($siteName);

}

session_destroy();