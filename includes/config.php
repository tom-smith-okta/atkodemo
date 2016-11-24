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

$thisSite = new demoSite($homeDir, $host);

// Get the name of the site that should be loaded
// based on $host or the value of /sites/siteToLoad.json
$siteToLoad = getSite($host);

$thisSite->setSite($siteToLoad);

echo $thisSite->showSettings();

// echo json_encode($thisSite, JSON_PRETTY_PRINT);

exit;


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

/*********** BEGIN UNDERLYING CONFIGURAION ******************/

// Widget version
// $widgetVer = "1.7.0";

/************** Custom files *******************/

// A little hack to keep the dates current on the
// home page articles
// $config["dates"]["type"] = "javascript";
// $config["dates"]["location"] = "local";

// // UI elements from HTML5up
// $config["main"]["type"] = "javascript";
// $config["main"]["location"] = "local";

// $config["skel.min"]["type"] = "javascript";
// $config["skel.min"]["location"] = "local";

// $config["util"]["type"] = "javascript";
// $config["util"]["location"] = "local";

// Okta Widget and session elements

$config["regOptionsLink"] = "";

// Display the "registration options link" only if there is a valid API key
if ($thisSite->apiKeyIsValid) {
	$config["regOptionsLink"] = "<li><a href = '#menu'>Registration options</a></li>";
}

// $config["regOptions"] = getRegOptions();

$a = $thisSite->getRegOptions();

echo "<p>" . $a;

$config["serverSettings"] = getServerSettings();

// Leave this at the bottom bc this function decides whether to
// display a warning icon in the UI. This decision is based on
// whether any warnings have accumulated in the $config["warnings"]
// object.
$config["menu"] = getMenu();

/************** Okta files *********************/

// $oktaWidgetBaseURL = "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/" . $widgetVer;

// // Okta widget
// $config["okta-signin-widget"]["type"] = "javascript";
// $config["okta-signin-widget"]["location"] = "remote";
// $config["okta-signin-widget"]["url"] = $oktaWidgetBaseURL . "/js/okta-sign-in.min.js";

// // Okta core CSS
// $config["oktaWidgetCSScore"]["type"] = "css";
// $config["oktaWidgetCSScore"]["location"] = "remote";
// $config["oktaWidgetCSScore"]["url"] = $oktaWidgetBaseURL . "/css/okta-sign-in.min.css";

// // Okta customizable CSS - remote
// $config["oktaWidgetCSStheme"]["type"] = "css";
// $config["oktaWidgetCSStheme"]["location"] = "remote";
// $config["oktaWidgetCSStheme"]["url"] = $oktaWidgetBaseURL . "/css/okta-theme.css";

// // Okta customizable CSS - local
// $config["oktaWidgetCSSlocal"]["type"] = "css";
// $config["oktaWidgetCSSlocal"]["location"] = "inline";
// $config["oktaWidgetCSSlocal"]["vars"] = array("bgImage"); 

/***************** Design stuff ******************/
$config["mainCSS"]["type"] = "css";
$config["mainCSS"]["location"] = "local";

/***************** Utilities *********************/

// jquery
$config["jquery"]["type"] = "javascript";
$config["jquery"]["location"] = "remote";
$config["jquery"]["url"] = "https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js";

$config["font-awesome"]["type"] = "javascript";
$config["font-awesome"]["location"] = "remote";
$config["font-awesome"]["url"] = "https://use.fontawesome.com/dc4e4e9270.js";

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


// function loadSite($site) {

// 	global $config;

// 	$sitePath = $config["fsHome"] . "/sites/" . $site; 

// 	include $sitePath . "/config.php";

// 	if (file_exists($sitePath . "/groups.php")) {
// 		include $sitePath . "/groups.php";
// 	}

// 	if (file_exists($sitePath . "/theme.php")) {
// 		include $sitePath . "/theme.php";
// 	}
// 	else {
// 		include $config["fsHome"] . "/sites/default/theme.php";
// 	}

// 	if (file_exists($sitePath . "/regDesc.php")) {
// 		include $sitePath . "regDesc.php";
// 	}
// 	else {
// 		include $config["fsHome"] . "/sites/default/regDesc.php";
// 	}

// }

// function setRemotePaths() {
// 	global $config;

// 	// https://tomco.okta.com
// 	$config["oktaBaseURL"] = "https://" . $config["oktaOrg"] . ".okta.com";

// 	// https://tomco.okta.com/api/v1
// 	$config["apiHome"] = $config["oktaBaseURL"] . "/api/v1";

// }

// function setEnv() {
// 	global $config;

// 	if (file_exists("/usr/local/env/tomlocalhost.txt")) {
// 		// on Tom's local machine
// 		$config["env"] = "tom";
// 		$config["envLong"] = "Tom's local machine";
// 		$config["oktaOrg"] = "tomco";
// 		$config["apiKeyPath"] = "/usr/local/keys/oktaAPI.txt";
// 	}
// 	else if (file_exists("/usr/local/env/atkoserver.txt")) {
// 		// on the www.atkodemo.com server
// 		$config["env"] = "atkodemo";
// 		$config["envLong"] = "Public site: www.atkodemo.com";
// 		$config["homeDir"] = "";
// 		$config["oktaOrg"] = "tomco";
// 		$config["apiKeyPath"] = "/usr/local/keys/oktaAPI.txt";
// 	}
// 	else if (file_exists("/var/www/html/dockerContainer.txt")) {
// 		// probably in the atkodemo docker container
// 		$config["env"] = "docker";
// 		$config["envLong"] = "Atkodemo docker container";
// 	}
// 	else {
// 		$config["env"] = "unknown";
// 		$config["envLong"] = "unknown";
// 	}
// }

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

function getServerSettings() {
	global $config;

	$retVal = "";

	$settings = ["oktaOrg", "envLong", "apiKey", "apiKeyIsValid", "clientId"];

	foreach ($settings as $setting) {
		$retVal .= "<p>" . $setting . ": ";

		if ($setting === "apiKey") {
			$value = showAPIkey();
		}
		else { $value = $config[$setting]; }

		$retVal .= $value . "</p>";
	}

	return $retVal;
}

function showAPIkey() {
	global $config;

	if ($config["apiKey"]) {
		return substr($config["apiKey"], 0, 5) . "...";
	}
	else {
		return "NONE";
	}
}