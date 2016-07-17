<?php


$config["homeDir"] = $home; // e.g.: "atkotravel"

// e.g.: "/Applications/MAMP/htdocs"
$config["fsHome"] = $_SERVER['DOCUMENT_ROOT'] . "/" . $config["homeDir"];

// $config["host"] = $_SERVER["SERVER_NAME"];


// Settings that would need to change for this app to run
// with a different okta tenant
// or on someone else's localhost
$config["oktaOrg"] = "tomco";

// store the apiKey in a file not exposed to the web
$apiKeyPath = "/usr/local/keys/oktaAPI.txt";

$config["salesforce"] = "https://tomco.okta.com/home/salesforce/0oapq5e1G3yk5Syeg1t5/46";


/****************************************/

$config["oktaBaseURL"] = "https://" . $config["oktaOrg"] . ".okta.com";
$config["apiHome"] = $config["oktaBaseURL"] . "/api/v1";

$config["webHome"] = "/" . $config["homeDir"];

$config["homePage"] = $config["webHome"] . "/" . "home.php";

$config["apiKey"] = file_get_contents($apiKeyPath);

$config["sessionManager"] = $config["webHome"];

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


