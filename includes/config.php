<?php

$config["homeDir"] = $home; // e.g.: "atkotravel"

// e.g.: "/Applications/MAMP/htdocs"
$config["fsHome"] = $_SERVER['DOCUMENT_ROOT'] . "/" . $config["homeDir"];

// Settings that would need to change for this app to run
// with a different okta tenant
// or on someone else's localhost
$config["oktaOrg"] = "tomco";

$config["name"] = "Atko Corp";

$config["localhost"] = "localhost:8888";

// store the apiKey in a file not exposed to the web
$apiKeyPath = "/usr/local/keys/oktaAPI.txt";

// you can supply a local path here or a URI
// the value will be tested with fopen()
// if fopen() fails the value will be prepended with $config["webHome"]
$logoPath = "images/logo.png"; 

// $config["bgImage"] = 

// The path to salesforce on your Okta instance
// future dev efforts might retrieve an end-user's list of apps automatically
// but this demo is optimized for showing automatic provisioning to SF
$config["salesforce"] = "/home/salesforce/0oapq5e1G3yk5Syeg1t5/46";

/****************************************/

$config["host"] = $_SERVER["SERVER_NAME"];

if (($config["host"]) == "localhost") {
	$config["host"] = $config["localhost"];
}

// Need to update this to accommodate https
$config["host"] = "http://" . $config["host"];

$config["oktaBaseURL"] = "https://" . $config["oktaOrg"] . ".okta.com";
$config["apiHome"] = $config["oktaBaseURL"] . "/api/v1";
$config["salesforce"] = $config["oktaBaseURL"] . $config["salesforce"];

// e.g.: /atkotravel
$config["webHome"] = "/" . $config["homeDir"];

if (fopen($logoPath)) { $config["logo"] = $logoPath; }
else { $config["logo"] = $config["webHome"] . "/" . $logoPath; }

// http://localhost:8888/atkotravel
$config["webHomeURL"] = $config["host"] . $config["webHome"];

$config["homePage"] = $config["webHome"] . "/" . "home.php";

$config["apiKey"] = file_get_contents($apiKeyPath);

// Danger Will Robinson
// This value needs to match a value in the Redirect URIs list
// in your Okta tenant
$config["sessionManager"] = $config["host"] . $config["webHome"] . "/index.php";

/************** Custom files *******************/

// Custom js to check for Okta session
$config["checkForSession"]["type"] = "javascript";
$config["checkForSession"]["vars"] = array("oktaBaseURL", "homePage");
$config["checkForSession"]["isInline"] = TRUE;

// A little hack to keep the dates current on the
// home page articles
$config["dates"]["type"] = "javascript";
$config["dates"]["url"] = $config["webHome"] . "/javascript/dates.js";
$config["dates"]["isInline"] = FALSE;

$config["oktaSignInOIDC"]["type"] = "javascript";
$config["oktaSignInOIDC"]["vars"] = array("oktaBaseURL", "sessionManager");
$config["oktaSignInOIDC"]["isInline"] = TRUE;

/************** Okta files *********************/

// Okta widget
$config["okta-signin-widget"]["type"] = "javascript";
$config["okta-signin-widget"]["url"] = "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/1.3.3/js/okta-sign-in-1.3.3.min.js";
$config["okta-signin-widget"]["isInline"] = FALSE;

// Okta core CSS
$config["oktaWidgetCSScore"]["type"] = "css";
$config["oktaWidgetCSScore"]["url"] = "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/1.3.3/css/okta-sign-in-1.3.3.min.css";
$config["oktaWidgetCSScore"]["isInline"] = FALSE;

// Okta customizable CSS
$config["oktaWidgetCSStheme"]["type"] = "css";
$config["oktaWidgetCSStheme"]["url"] = "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/1.3.3/css/okta-theme-1.3.3.css";
$config["oktaWidgetCSStheme"]["isInline"] = FALSE;

/***************** Design stuff ******************/
$config["mainCSS"]["type"] = "css";
$config["mainCSS"]["url"] = $config["webHome"] . "/css/main.css";
$config["mainCSS"]["isInline"] = FALSE;

/***************** Utilities *********************/

// jquery
$config["jquery"]["type"] = "javascript";
$config["jquery"]["url"] = "https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js";
$config["jquery"]["isInline"] = FALSE;