<?php

// this is the home directory name on the web server
// (not the full path, just the dir name)
$config["homeDir"] = "atkodemo"; // e.g.: "atkodemo"

/******* Next few lines should not be touched **************************/
// I'm going to re-work the next few lines. They might not
// really be necessary.

// the OS home dir
// e.g. /var/www/html
$config["fsHome"] = $_SERVER['DOCUMENT_ROOT'];

if (strpos($_SERVER['DOCUMENT_ROOT'], $config["homeDir"])) {
	// this means we are probably on www.atkodemo.com
	$config["homeDir"] = "";
}
else {
	$config["fsHome"] .= "/" . $config["homeDir"];
}

/*************************************************************************/
// MAIN CONFIGURATION BEGINS HERE
// Settings that would need to change for this app to run
// with a different okta tenant
// or on someone else's localhost
$config["oktaOrg"] = "tomco";

// name of fake company
$config["name"] = "Atko Corp";

// If your localhost is running on a specific port, indicate it here
$config["localhost"] = "localhost:8888";

 // I add all new users to an Okta group called "externalUsers"
$config["group"]["default"]["id"] = "00g1yq9e5JOWsxFdu1t6";

// For users who should be added to to a group requiring MFA
// triggered in reg flow by email address
$config["group"]["mfa"]["id"] = "00g32ude0PD5Dbcqt1t6";
$config["group"]["mfa"]["domain"] = "mailinator.net";

// I use this to add Okta admins. Optional.
$config["oktaGroupID"] = "00gqasglzEnaoUZdV1t5";

// store your apiKey in a file not exposed to the web
$apiKeyPath = "/usr/local/keys/oktaAPI.txt";

// you can supply a local path here or a URI
// the value will be tested with fopen()
// if fopen() fails the value will be prepended with $config["webHome"]
$logoPath = "images/logo.png"; 

$bgImagePath = "images/bgImage.jpg";

// The path to salesforce on your Okta instance
// future dev efforts might retrieve an end-user's list of apps automatically
// but this demo is optimized for showing automatic provisioning to SF
$salesforce = "/home/salesforce/0oapq5e1G3yk5Syeg1t5/46";

// OIDC client ID - from your Okta social auth app
$config["clientId"] = "YYUAPHIAj3JPPO6yJans";

// Widget version
$widgetVer = "1.4.0";

$facebook = array("type"=>"FACEBOOK", "id"=>"0oa1w1pmezuPUbhoE1t6");
$idps[] = $facebook;

$google = array("type"=>"GOOGLE", "id"=>"0oa1w8n4dlYlOLjPl1t6");
$idps[] = $google;

/************************************************************************/

$config["idps"] = json_encode($idps);

$config["host"] = $_SERVER["SERVER_NAME"];

// if this site is running on localhost, then use the value for localhost
// indicated above: localhost:8888
if (($config["host"]) == "localhost") {
	$config["host"] = $config["localhost"];
}
else {
	error_reporting(0); // turn off error reporting for "production" sites
}

// Need to add some logic here to accommodate https
$config["host"] = "http://" . $config["host"];

// https://tomco.okta.com
$config["oktaBaseURL"] = "https://" . $config["oktaOrg"] . ".okta.com";

// https://tomco.okta.com/api/v1
$config["apiHome"] = $config["oktaBaseURL"] . "/api/v1";

// https://tomco.okta.com/home/salesforce/0oapq5e1G3yk5Syeg1t5/46
$config["salesforce"] = $config["oktaBaseURL"] . $salesforce;

// establishes web home relative to web root
// /atkodemo
$config["webHome"] = "/" . $config["homeDir"];

// check for a logo
if (fopen($logoPath, "r")) { $config["logo"] = $logoPath; }
else { $config["logo"] = $config["webHome"] . "/" . $logoPath; }

// check for a background image
if (fopen($bgImagePath, "r")) { $config["bgImage"] = $bgImagePath; }
else { $config["bgImage"] = $config["webHome"] . "/" . $bgImagePath; }

// http://localhost:8888/atkodemo
$config["webHomeURL"] = $config["host"] . $config["webHome"];

$config["homePage"] = $config["webHome"] . "/" . "home.php";

$config["apiKey"] = file_get_contents($apiKeyPath);

// Danger Will Robinson
// This value needs to match a value in the Redirect URIs list
// in your Okta tenant

// http://localhost:8888/atkodemo
// i am using index.php as my redirect target and session manager
// $config["redirectURL"] = $config["host"] . $config["webHome"] . "/login.php";

// $config["redirectURL"] = $config["host"] . $config["webHome"] . "/index.php";

$config["redirectURL"] = $config["host"] . $config["webHome"];


/************** Custom files *******************/

// Custom js to check for Okta session
$config["checkForSession"]["type"] = "javascript";
$config["checkForSession"]["location"] = "inline";
$config["checkForSession"]["vars"] = array("oktaBaseURL", "homePage");

// A little hack to keep the dates current on the
// home page articles
$config["dates"]["type"] = "javascript";
$config["dates"]["location"] = "local";

$config["oktaSignInOIDC"]["type"] = "javascript";
$config["oktaSignInOIDC"]["location"] = "inline";
$config["oktaSignInOIDC"]["vars"] = array("oktaBaseURL", "redirectURL", "logo", "clientId", "idps");

$config["OIDC"]["type"] = "javascript";
$config["OIDC"]["location"] = "inline";
$config["OIDC"]["vars"] = array("salesforce", "oktaBaseURL", "redirectURL", "logo", "clientId", "idps", "apiHome");

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
$config["oktaWidgetCSSlocal"]["vars"] = array("bgImage"); 

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
