<?php

// If this script is being run from
// 1) Tom's local machine
// 2) The official www.atkodemo.com server
// then it will just work

// If this script is being run from another machine
// then it will "just work" with the atkodemovm.okta.com
// tenant if you store a valid API key for this tenant at
// "/usr/local/keys/atkodemovm.txt"

// If you are running this script against another okta
// tenant then you need to set the value for
// $config["oktaOrg"]
// this will enable simple authentication only.
// To enable the registration flows, you need to:
// 1) store a valid API key at "/usr/local/keys/oktaAPI.txt"
// or some other location
// 2) create groups in your okta org to map to the reg flows

$config; // used as a global associative array to store all settings

$config["warnings"] = [];

/********** SET THE ENVIRONMENT ******************/
// First, figure out where the script is running
// 1) tom's local machine
// 2) www.atkodemo.com
// 3) atkodemo docker container
// 4) unknown

setEnv();


// The directory that this repo lives in
// important for constructing the redirect url
$config["homeDir"] = "atkodemo";

$config["oktaOrg"] = "atkodemovm";

$config["apiKeyPath"] = "/usr/local/keys/atkodemovm.txt";


setPaths();

if (file_exists($config["apiKeyPath"])) {
	$config["apiKey"] = trim(file_get_contents($config["apiKeyPath"]));
}
else {
	$config["warnings"][] = "The file " . $config["apiKeyPath"] . " does not exist.";  
}

checkAPIkey();

/********** SET THE UI THEME *******************/

// change this setting to switch to a different
// UI theme. 
$config["theme"] = "default";

loadTheme();

/********* LOAD REG FLOWS AND GROUPS**********************/

if ($config["oktaOrg"] === "tomco" || $config["oktaOrg"] === "atkodemovm") {

	include $config["includes"] . "/regFlows/" . $config["oktaOrg"] . ".php";
	include $config["includes"] . "/regFlows/" . "regDesc.php";

	// The list of apps that should be displayed in the UI.
	// This prevents "junk" apps from cluttering up the user's list of apps
	// The key is the appName from the Okta app Object (via appLinks)
	// The value is what you want to be displayed in the UI.

	$appsWhitelist["salesforce"] = "Chatter";

}

if ($config["oktaOrg"] === "atkodemovm") {

	// OIDC client ID - from Okta OIDC app
	$config["clientId"] = "KySezizDE4ScxOlsNLsX";

	// Social IDPs
	$idps[] = array("type"=>"FACEBOOK", "id"=>"0oassj82zxJdGVjjL1t6");
	$idps[] = array("type"=>"GOOGLE", "id"=>"0oasss0hkdAGnhCzF1t6");
}
else if ($config["oktaOrg"] === "tomco") {

	// OIDC client ID - from Okta OIDC app
	$config["clientId"] = "YYUAPHIAj3JPPO6yJans";

	// Social IDPs
	$idps[] = array("type"=>"FACEBOOK", "id"=>"0oa1w1pmezuPUbhoE1t6");	
	$idps[] = array("type"=>"GOOGLE", "id"=>"0oa1w8n4dlYlOLjPl1t6");
}
else {
	// Add your own values here for social auth

}

if (!empty($idps)) { $config["idps"] = json_encode($idps); }

if (!empty($appsWhitelist)) { $config["appsWhitelist"] = json_encode($appsWhitelist); }


/*********** END OF MAIN CONFIGURATION ***********************/

/*********** BEGIN UNDERLYING CONFIGURAION ******************/

// Widget version
$widgetVer = "1.7.0";

/************** Custom files *******************/

// A little hack to keep the dates current on the
// home page articles
$config["dates"]["type"] = "javascript";
$config["dates"]["location"] = "local";

// UI elements from HTML5up
$config["main"]["type"] = "javascript";
$config["main"]["location"] = "local";

$config["skel.min"]["type"] = "javascript";
$config["skel.min"]["location"] = "local";

$config["util"]["type"] = "javascript";
$config["util"]["location"] = "local";

// Okta Widget and session elements

$config["regOptionsLink"] = "";

// Display the "registration options link" only if there is a valid API key
if ($config["apiKeyIsValid"]) {
	$config["regOptionsLink"] = "<li><a href = '#menu'>Registration options</a></li>";
}

$config["renderWidgetBasic"]["type"] = "javascript";
$config["renderWidgetBasic"]["location"] = "inline";
$config["renderWidgetBasic"]["vars"] = array("redirectURL");

$config["renderWidgetOIDC"]["type"] = "javascript";
$config["renderWidgetOIDC"]["location"] = "inline";

$config["checkForSession"]["type"] = "javascript";
$config["checkForSession"]["location"] = "inline";
$config["checkForSession"]["vars"] = array("apiHome");

$config["loadWidgetBasic"]["type"] = "javascript";
$config["loadWidgetBasic"]["location"] = "inline";
$config["loadWidgetBasic"]["vars"] = array("oktaBaseURL", "logo", "redirectURL");

$config["loadWidgetOIDC"]["type"] = "javascript";
$config["loadWidgetOIDC"]["location"] = "inline";
$config["loadWidgetOIDC"]["vars"] = array("oktaBaseURL", "logo", "redirectURL", "clientId", "idps");

$config["setMenu"]["type"] = "javascript";
$config["setMenu"]["location"] = "inline";
$config["setMenu"]["vars"] = array("apiHome", "appsWhitelist", "regOptionsLink");

$config["signout"]["type"] = "javascript";
$config["signout"]["location"] = "inline";
$config["signout"]["vars"] = array("apiHome");

$config["regOptions"] = getRegOptions();

$config["serverSettings"] = getServerSettings();

// Leave this at the bottom bc this function decides whether to
// display a warning icon in the UI. This decision is based on
// whether any warnings have accumulated in the $config["warnings"]
// object.
$config["menu"] = getMenu();



/************** Okta files *********************/

$oktaWidgetBaseURL = "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/" . $widgetVer;

// Okta widget
$config["okta-signin-widget"]["type"] = "javascript";
$config["okta-signin-widget"]["location"] = "remote";
$config["okta-signin-widget"]["url"] = $oktaWidgetBaseURL . "/js/okta-sign-in.min.js";

// Okta core CSS
$config["oktaWidgetCSScore"]["type"] = "css";
$config["oktaWidgetCSScore"]["location"] = "remote";
$config["oktaWidgetCSScore"]["url"] = $oktaWidgetBaseURL . "/css/okta-sign-in.min.css";

// Okta customizable CSS - remote
$config["oktaWidgetCSStheme"]["type"] = "css";
$config["oktaWidgetCSStheme"]["location"] = "remote";
$config["oktaWidgetCSStheme"]["url"] = $oktaWidgetBaseURL . "/css/okta-theme.css";

// Okta customizable CSS - local
$config["oktaWidgetCSSlocal"]["type"] = "css";
$config["oktaWidgetCSSlocal"]["location"] = "inline";
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

function checkAPIkey() {

	global $config;

	if (empty($config["apiKey"])) {
		$config["warnings"][] = "No API key found.";
		$config["warnings"][] = "User registration is not possible without an API key.";
	}
	else {
		$apiKey = $config["apiKey"];

		$curl = curl_init();

		$url = $config["apiHome"] . "/meta/schemas/user/default";

		curl_setopt_array($curl, array(
			CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url
		));

		$jsonResult = curl_exec($curl);

		$assocArray = json_decode($jsonResult, TRUE);

		if ($assocArray["id"]) { $config["apiKeyIsValid"] = TRUE; }
		else {
			$config["apiKeyIsValid"] = FALSE;
			$config["warnings"][] = $jsonResult;
			$config["warnings"][] = "User registration is not possible without an API key.";
		}
	}
}

function setPaths() {
	global $config;

	// https://tomco.okta.com
	$config["oktaBaseURL"] = "https://" . $config["oktaOrg"] . ".okta.com";

	// https://tomco.okta.com/api/v1
	$config["apiHome"] = $config["oktaBaseURL"] . "/api/v1";

	// establishes web home relative to web root
	// /atkodemo
	$config["webHome"] = "/";
	if(!empty($config["homeDir"])) {
		$config["webHome"] = $config["webHome"] . $config["homeDir"] . "/";
	}

	$config["fsHome"] = $_SERVER["DOCUMENT_ROOT"] . $config["webHome"];

	$config["includes"] = $config["fsHome"] . "includes";

	$config["host"] = $_SERVER["SERVER_NAME"];

	if (($config["host"]) != "localhost") {
		error_reporting(0); // turn off error reporting for "production" sites
	}

	if (array_key_exists("SERVER_PORT", $_SERVER)) {
		if ($_SERVER["SERVER_PORT"] != "80") {
			$config["host"] .= ":" . $_SERVER["SERVER_PORT"];
		}
	}

	// Need to add some logic here to accommodate https
	$config["host"] = "http://" . $config["host"];

	$config["webHomeURL"] = $config["host"] . $config["webHome"];

	// Danger Will Robinson
	// This value needs to match a value in the Redirect URIs list
	// in your Okta tenant

	$config["redirectURL"] = $config["host"] . $config["webHome"];

}

function loadTheme() {
	global $config;

	include $config["includes"] . "/themes/" . $config["theme"] . ".php";
}

function setEnv() {
	global $config;

	if (file_exists("/usr/local/env/tomlocalhost.txt")) {
		// on Tom's local machine
		$config["env"] = "tom";
		$config["envLong"] = "Tom's local machine";
		$config["oktaOrg"] = "tomco";
		$config["apiKeyPath"] = "/usr/local/keys/oktaAPI.txt";
	}
	else if (file_exists("/usr/local/env/atkoserver.txt")) {
		// on the www.atkodemo.com server
		$config["env"] = "atkodemo";
		$config["envLong"] = "Public site: www.atkodemo.com";
		$config["homeDir"] = "";
		$config["oktaOrg"] = "tomco";
		$config["apiKeyPath"] = "/usr/local/keys/oktaAPI.txt";
	}
	else if (file_exists("/var/www/html/dockerContainer.txt")) {
		// probably in the atkodemo docker container
		$config["env"] = "docker";
		$config["envLong"] = "Atkodemo docker container";
	}
	else {
		$config["env"] = "unknown";
		$config["envLong"] = "unknown";
	}
}

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