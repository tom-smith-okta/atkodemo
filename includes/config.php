<?php

$config["homeDir"] = $home; // e.g.: "atkodemo"

// e.g.: "/Applications/MAMP/htdocs"
$config["fsHome"] = $_SERVER['DOCUMENT_ROOT'] . "/" . $config["homeDir"];

/*************************************************************************/
// Settings that would need to change for this app to run
// with a different okta tenant
// or on someone else's localhost
$config["oktaOrg"] = "tomco";

// name of fake company
$config["name"] = "Atko Corp";

// If your localhost is running on a specific port, indicate it here
$config["localhost"] = "localhost:8888";

 // I add all new users to a group called "externalUsers"
$config["groupID"] = "00g1yq9e5JOWsxFdu1t6";

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
$config["salesforce"] = "/home/salesforce/0oapq5e1G3yk5Syeg1t5/46";

// OIDC client ID - from your Okta social auth app
$config["clientId"] = "YYUAPHIAj3JPPO6yJans";

$facebook = array("type"=>"FACEBOOK", "id"=>"0oa1w1pmezuPUbhoE1t6");
$idps[] = $facebook;
// use those same two lines again to push another idp onto the stack

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
$config["salesforce"] = $config["oktaBaseURL"] . $config["salesforce"];

// /atkodemo
$config["webHome"] = "/" . $config["homeDir"];

if (fopen($logoPath, "r")) { $config["logo"] = $logoPath; }
else { $config["logo"] = $config["webHome"] . "/" . $logoPath; }

if (fopen($bgImagePath, "r")) { $config["bgImage"] = $bgImagePath; }
else { $config["bgImage"] = $config["webHome"] . "/" . $bgImagePath; }

// http://localhost:8888/atkodemo
$config["webHomeURL"] = $config["host"] . $config["webHome"];

$config["homePage"] = $config["webHome"] . "/" . "home.php";

$config["apiKey"] = file_get_contents($apiKeyPath);

// Danger Will Robinson
// This value needs to match a value in the Redirect URIs list
// in your Okta tenant

// http://localhost:8888/atkodemo/
// i am using index.php as my redirect target and session manager
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

/************** Okta files *********************/

$oktaWidgetBaseURL = "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/1.3.3";

// Okta widget
$config["okta-signin-widget"]["type"] = "javascript";
$config["okta-signin-widget"]["location"] = "remote";
$config["okta-signin-widget"]["url"] = $oktaWidgetBaseURL . "/js/okta-sign-in-1.3.3.min.js";

// Okta core CSS
$config["oktaWidgetCSScore"]["type"] = "css";
$config["oktaWidgetCSScore"]["location"] = "remote";
$config["oktaWidgetCSScore"]["url"] = $oktaWidgetBaseURL . "/css/okta-sign-in-1.3.3.min.css";

// Okta customizable CSS - remote
$config["oktaWidgetCSStheme"]["type"] = "css";
$config["oktaWidgetCSStheme"]["location"] = "remote";
$config["oktaWidgetCSStheme"]["url"] = $oktaWidgetBaseURL . "/css/okta-theme-1.3.3.css";

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
