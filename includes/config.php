<?php

// this is the home directory name on the web server
// (not the full path, just the dir name)
$config["homeDir"] = "atkodemo"; // e.g.: "atkodemo"

// name of Okta organization
// $config["oktaOrg"] = "tomco";
$config["oktaOrg"] = "atkodemovm";

// name of fake company
$config["name"] = "Atko Corp";

/*********************************************/
// API Key
// store your apiKey in a file not exposed to the web
// $apiKeyPath = "/usr/local/keys/oktaAPI.txt";

$apiKeyPath = "/usr/local/keys/atkodemovm.txt";

$config["apiKey"] = trim(file_get_contents($apiKeyPath));

// you can supply a local path here or a URI
// the value will be tested with fopen()
// if fopen() fails the value will be prepended with $config["webHome"]
$logoPath = "images/logo.png"; 

$bgImagePath = "images/bgImage.jpg";

// Widget version
$widgetVer = "1.7.0";

/********************************************/
// GROUPS

if ($config["oktaOrg"] === "tomco") {

	// OIDC client ID - from your Okta OIDC app
	$config["clientId"] = "YYUAPHIAj3JPPO6yJans";

	// atkoDemoUsersBasic
	$config["group"]["basic"]["id"] = "00gntdlmx9Favuwhp1t6";

	// atkoDemoUsersSFchatter
	$config["group"]["sfChatter"]["id"] = "00goxo1ifVuBg7YKQ1t6";

	// atkoDemoUsersWithMFA
	$config["group"]["withMFA"]["id"] = "00gnv1elhvYu03OLh1t6";

	// atkoDemoUsersWithEmail
	$config["group"]["withEmail"]["id"] = "00gnv4sf0vkoLWiC21t6";

	// atkodDemoUsersOktaAdmin
	$config["group"]["okta"]["id"] = "00gnv0lbm756RjxT61t6";

	$facebook = array("type"=>"FACEBOOK", "id"=>"0oa1w1pmezuPUbhoE1t6");
	$idps[] = $facebook;

	$google = array("type"=>"GOOGLE", "id"=>"0oa1w8n4dlYlOLjPl1t6");
	$idps[] = $google;

}
else if ($config["oktaOrg"] === "atkodemovm") {

	$config["clientId"] = "KySezizDE4ScxOlsNLsX";

	// atkoDemoUsersBasic
	$config["group"]["basic"]["id"] = "00gst60jvcizQe0No1t6";

	// atkoDemoUsersSFchatter
	$config["group"]["sfChatter"]["id"] = "00gst7346E06ywPyc1t6";

	// atkoDemoUsersWithMFA
	$config["group"]["withMFA"]["id"] = "00gst4ezhRw5g3phR1t6";

	// atkoDemoUsersWithEmail
	$config["group"]["withEmail"]["id"] = "00gst60n94ZTQFRqn1t6";

	// atkodDemoUsersOktaAdmin
	$config["group"]["okta"]["id"] = "00gst6j0n6PI0iLle1t6";

	$facebook = array("type"=>"FACEBOOK", "id"=>"0oassj82zxJdGVjjL1t6");
	$idps[] = $facebook;

	$google = array("type"=>"GOOGLE", "id"=>"0oasss0hkdAGnhCzF1t6");
	$idps[] = $google;

}




// The list of apps that should be displayed in the UI.
// This prevents "junk" apps from cluttering up the user's list of apps

$appsWhitelist = ["salesforce"];
$config["appsWhitelist"] = json_encode($appsWhitelist);

/************************************************************************/

$config["idps"] = json_encode($idps);

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

// check for a logo
if (fopen($logoPath, "r")) { $config["logo"] = $logoPath; }
else { $config["logo"] = $config["webHome"] . $logoPath; }

// check for a background image
if (fopen($bgImagePath, "r")) { $config["bgImage"] = $bgImagePath; }
else { $config["bgImage"] = $config["webHome"] . $bgImagePath; }


/************** Custom files *******************/

// A little hack to keep the dates current on the
// home page articles
$config["dates"]["type"] = "javascript";
$config["dates"]["location"] = "local";

$config["main"]["type"] = "javascript";
$config["main"]["location"] = "local";

$config["skel.min"]["type"] = "javascript";
$config["skel.min"]["location"] = "local";

$config["util"]["type"] = "javascript";
$config["util"]["location"] = "local";

$config["setMenu"]["type"] = "javascript";
$config["setMenu"]["location"] = "inline";
$config["setMenu"]["vars"] = array("apiHome", "appsWhitelist");

$config["signout"]["type"] = "javascript";
$config["signout"]["location"] = "inline";
$config["signout"]["vars"] = array("apiHome");

$config["widgetOIDC"]["type"] = "javascript";
$config["widgetOIDC"]["location"] = "inline";
$config["widgetOIDC"]["vars"] = array("oktaBaseURL", "redirectURL", "logo", "clientId", "idps", "apiHome", "appsWhitelist");

$config["widgetBasic"]["type"] = "javascript";
$config["widgetBasic"]["location"] = "inline";
$config["widgetBasic"]["vars"] = array("oktaBaseURL", "redirectURL", "logo", "clientId", "idps", "apiHome", "appsWhitelist");

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

/*************** Registration forms *************/
$config["regFormType"]["min"] = ["firstName", "lastName", "login", "email"];

$config["regFormType"]["pwd"] = $config["regFormType"]["min"];
$config["regFormType"]["pwd"][] = "password";

/**************** Hostname etc. ****************/

// the OS home dir
// e.g. /var/www/html
$config["fsHome"] = $_SERVER['DOCUMENT_ROOT'];

$config["host"] = $_SERVER["SERVER_NAME"];

if (array_key_exists("SERVER_PORT", $_SERVER)) {
	if ($_SERVER["SERVER_PORT"] != "80") {
		$config["host"] .= ":" . $_SERVER["SERVER_PORT"];
	}
}

if (($config["host"]) != "localhost") {
	error_reporting(0); // turn off error reporting for "production" sites
}

// Need to add some logic here to accommodate https
$config["host"] = "http://" . $config["host"];

// check to see if the homedir is defined as document root
// if so, we are probably on www.atkodemo.com
// otherwise we are on localhost
if (strpos($_SERVER['DOCUMENT_ROOT'], $config["homeDir"])) {
	$config["homeDir"] = "";
}
else {
	$config["fsHome"] .= "/" . $config["homeDir"];
}

// Danger Will Robinson
// This value needs to match a value in the Redirect URIs list
// in your Okta tenant

$config["redirectURL"] = $config["host"] . $config["webHome"];

$config["webHomeURL"] = $config["host"] . $config["webHome"];



