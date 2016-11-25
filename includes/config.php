<?php

// find the script's parent directory
$homeDir = getHomeDir();

setIncludePaths();

include "includes.php";

// look for a marker file to indicate whether the script
// is running on:
// 1) Tom's local machine
// 2) the www.atkodemo.com web server
// 3) a docker container
// 4) other 
$host = getHost();

// Get the name of the site that should be loaded
// based on $host or the value of /sites/siteToLoad.json
$siteToLoad = getSite($host);

$thisSite = new demoSite($homeDir, $host);

$thisSite->setSite($siteToLoad);

/********* Function defs *********************/

// fixes the script's place in the filesystem
function getHomeDir() {
	$dirPathArr = explode("/", dirname(getcwd()));
	return end($dirPathArr);
}

function getHost() {
	$json = file_get_contents("hostMarkers.json", FILE_USE_INCLUDE_PATH);

	$hostMarkers = json_decode($json, TRUE);

	foreach ($hostMarkers as $name => $path) {
		if (file_exists($path)) { return $name; }
	}

	return "unknown";
}

function getSite($host) {
	$json = file_get_contents("siteNames.json", FILE_USE_INCLUDE_PATH);

	$sites = json_decode($json, TRUE);

	if (array_key_exists($host, $sites)) {
		return $sites[$host];
	}
	else { return "default"; }
}

function setIncludePaths() {
	$includePath = dirName(getcwd()) . "/includes";

	set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);
	set_include_path(get_include_path() . PATH_SEPARATOR . $includePath . "/config");
}

//////////////////////////////////////////


// $thisSite->setLocalPaths();

/************** Custom files *******************/

// Okta Widget and session elements

$config["regOptionsLink"] = "";

// Display the "registration options link" only if there is a valid API key
// if ($thisSite->apiKeyIsValid) {
// 	$config["regOptionsLink"] = "<li><a href = '#menu'>Registration options</a></li>";
// }

// $config["regOptions"] = getRegOptions();

// $a = $thisSite->getRegOptions();

// echo "<p>" . $a;

// $config["serverSettings"] = getServerSettings();

// Leave this at the bottom bc this function decides whether to
// display a warning icon in the UI. This decision is based on
// whether any warnings have accumulated in the $config["warnings"]
// object.
// $config["menu"] = getMenu();

/************** Okta files *********************/

/*************** Registration forms *************/
$config["regFormType"]["min"] = ["firstName", "lastName", "login", "email"];

$config["regFormType"]["pwd"] = $config["regFormType"]["min"];
$config["regFormType"]["pwd"][] = "password";

$config["defaultVals"] = [
	"oktaWidgetCSScore",
	"oktaWidgetCSStheme",
	"oktaWidgetCSSlocal",
	"mainCSS",
	"font-awesome",
	"jquery",
	"okta-signin-widget",
	"loadWidgetBasic",
	"checkForSession",
	"setMenu",
	"skel.min",
	"main",
	"util",
	"signout"
];

// function setLocalPaths($homeDir) {
// 	global $config;

// 	// establishes web home relative to web root
// 	// /atkodemo
// 	$config["webHome"] = "/";
// 	if(!empty($homeDir)) {
// 		$config["webHome"] = $config["webHome"] . $homeDir . "/";
// 	}

// 	$config["fsHome"] = $_SERVER["DOCUMENT_ROOT"] . $config["webHome"];

// 	$config["includes"] = $config["fsHome"] . "includes";

// 	$config["host"] = $_SERVER["SERVER_NAME"];

// 	if (($config["host"]) != "localhost") {
// 		error_reporting(0); // turn off error reporting for "production" sites
// 	}

// 	if (array_key_exists("SERVER_PORT", $_SERVER)) {
// 		if ($_SERVER["SERVER_PORT"] != "80") {
// 			$config["host"] .= ":" . $_SERVER["SERVER_PORT"];
// 		}
// 	}

// 	// Need to add some logic here to accommodate https
// 	$config["host"] = "http://" . $config["host"];

// 	$config["webHomeURL"] = $config["host"] . $config["webHome"];

// 	// Danger Will Robinson
// 	// This value needs to match a value in the Redirect URIs list
// 	// in your Okta tenant

// 	$config["redirectURL"] = $config["host"] . $config["webHome"];
// }


function getRegOptions() {
	global $config;

	$retVal = "";

	foreach ($config["regFlow"] as $regFlowName => $values) {

		$retVal .= "<li>";
		$retVal .= "<a href = 'register.php?regType=" . $regFlowName . "'>";
		$retVal .= "<h3>" . $values["title"] . "</h3>";

		if (array_key_exists("shortDesc", $values)) {
			$retVal .= "<p>" . $values["shortDesc"] . "</p>";
		}

		$retVal .= "</a></li>";

	}
	return $retVal;
}

function getMenu() {
	global $config;

	$settings = $config["fsHome"] . "html/settings.html";

	$retVal = file_get_contents($settings);

	if (!empty($config["warnings"])) {
		$warnings = $config["fsHome"] . "html/warnings.html";

		$retVal .= file_get_contents($warnings);
	}

	if ($config["apiKeyIsValid"]) {
		$regOptions = $config["fsHome"] . "html/regOptions.html";

		$retVal .= file_get_contents($regOptions);		
	}

	return $retVal;
}

// function getServerSettings() {
// 	global $config;

// 	$retVal = "";

// 	$settings = ["oktaOrg", "envLong", "apiKey", "apiKeyIsValid", "clientId"];

// 	foreach ($settings as $setting) {
// 		$retVal .= "<p>" . $setting . ": ";

// 		if ($setting === "apiKey") {
// 			$value = showAPIkey();
// 		}
// 		else { $value = $config[$setting]; }

// 		$retVal .= $value . "</p>";
// 	}

// 	return $retVal;
// }

// function showAPIkey() {
// 	global $config;

// 	if ($config["apiKey"]) {
// 		return substr($config["apiKey"], 0, 5) . "...";
// 	}
// 	else {
// 		return "NONE";
// 	}
// }