<?php

$config["warnings"] = [];

$thisSite = new demoSite();

/*********** Env Settings ***************/

// set the home directory relative to the web
// root of the server. Override this only if 
// you are serving the site from somewhere
// other than /[webRoot]/atkodemo/
// default value is "atkodemo"
$homeDir = "";
$thisSite->setHomeDir($homeDir);

$thisSite->setLocalPaths();

/******** Load Site ********************/

// Looks for a site defined in 
// /[webRoot]/[homeDir]/sites/siteToLoad.txt

$thisSite->load();

echo "<p>The okta org is: " . $thisSite->mainConfig["oktaOrg"];


/*********** Site Name ******************/
// default value is "docker"
// the script will load a site configuration
// from /sites/$siteName/
// $siteName = "";
// $thisSite->setName($siteName);

// // optional, but useful for error-checking
// $siteDesc = "";
// $thisSite->setDesc($siteDesc);


echo "<p>home directory: " . $thisSite->getHomeDir();

echo "<p>web home: " . $thisSite->webHome;

echo "<p>local file space home: " . $thisSite->fsHome;

echo "<p>include path: " . $thisSite->includePath;

echo "<p>php include path: " . get_include_path();

echo "<p>web home URL: " . $thisSite->webHomeURL;

echo "<p>config home dir: " . $thisSite->configHome;

echo "<p>config home file: " . $thisSite->configFile;

echo "<p>default home dir: " . $thisSite->defaultHome;

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
if ($thisSite->apiKeyIsValid) {
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

function getHomeDir($site) {
	if ($site === "atkodemo") {
		return "";
	}
	else { return "atkodemo"; }
}

function loadSite($site) {

	global $config;

	$sitePath = $config["fsHome"] . "/sites/" . $site; 

	include $sitePath . "/config.php";

	if (file_exists($sitePath . "/groups.php")) {
		include $sitePath . "/groups.php";
	}

	if (file_exists($sitePath . "/theme.php")) {
		include $sitePath . "/theme.php";
	}
	else {
		include $config["fsHome"] . "/sites/default/theme.php";
	}

	if (file_exists($sitePath . "/regDesc.php")) {
		include $sitePath . "regDesc.php";
	}
	else {
		include $config["fsHome"] . "/sites/default/regDesc.php";
	}

}

function setRemotePaths() {
	global $config;

	// https://tomco.okta.com
	$config["oktaBaseURL"] = "https://" . $config["oktaOrg"] . ".okta.com";

	// https://tomco.okta.com/api/v1
	$config["apiHome"] = $config["oktaBaseURL"] . "/api/v1";

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

function setLocalPaths($homeDir) {
	global $config;

	// establishes web home relative to web root
	// /atkodemo
	$config["webHome"] = "/";
	if(!empty($homeDir)) {
		$config["webHome"] = $config["webHome"] . $homeDir . "/";
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