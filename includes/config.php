<?php

$config; // this is an array that is used to store most values

// First, set the important environment variables

$homeDir = "atkodemo";
$oktaOrg = "tomco";
$apiKeyPath = "/usr/local/keys/oktaAPI.txt";

setEnv($homeDir, $oktaOrg, $apiKeyPath);

// name of fake company
$config["name"] = "Atko Corp";

$config["logo"] = "images/logo.png";
// $config["logo"] = "http://oauth2.atkodemo.com/images/USI/USIlogo.jpg";

$config["mainImage"] = "images/picnic.jpeg";
// $config["mainImage"] = "http://oauth2.atkodemo.com/images/USI/communityInvolvement.png";

$config["topImage"] = "images/yosemite.jpeg";
// $config["topImage"] = "http://oauth2.atkodemo.com/images/USI/retirementPlanning.png";

$config["bottomImage"] = "images/yellowstone.jpeg";
// $config["bottomImage"] = "http://oauth2.atkodemo.com/images/USI/personalRiskServices.png";

// Widget version
$widgetVer = "1.7.0";

/********************************************/
// GROUPS

if ($config["oktaOrg"] === "tomco") {

	// OIDC client ID - from your Okta OIDC app
	$config["clientId"] = "YYUAPHIAj3JPPO6yJans";

	// atkoDemoUsersBasic
	$config["regFlow"]["basic"]["groupID"] = "00gntdlmx9Favuwhp1t6"; 

	// atkoDemoUsersSFchatter
	$config["regFlow"]["sfChatter"]["groupID"] = "00goxo1ifVuBg7YKQ1t6";

	// atkoDemoUsersWithMFA
	$config["regFlow"]["withMFA"]["groupID"] = "00gnv1elhvYu03OLh1t6";

	// atkoDemoUsersWithEmail
	$config["regFlow"]["withEmail"]["groupID"] = "00gnv4sf0vkoLWiC21t6";

	// atkoDemoUsersProvisional
	$config["regFlow"]["provisional"]["groupID"] = "00guad15t26RsGWPK1t6";

	// atkodDemoUsersOktaAdmin
	$config["regFlow"]["okta"]["groupID"] = "00gnv0lbm756RjxT61t6";

	$facebook = array("type"=>"FACEBOOK", "id"=>"0oa1w1pmezuPUbhoE1t6");
	$idps[] = $facebook;

	$google = array("type"=>"GOOGLE", "id"=>"0oa1w8n4dlYlOLjPl1t6");
	$idps[] = $google;

	// The list of apps that should be displayed in the UI.
	// This prevents "junk" apps from cluttering up the user's list of apps
	// The key is the appName from the Okta app Object (via appLinks)
	// The value is what you want to be displayed in the UI.

	$appsWhitelist["salesforce"] = "Chatter";

}
else if ($config["oktaOrg"] === "atkodemovm") {

	$config["clientId"] = "KySezizDE4ScxOlsNLsX";

	// atkoDemoUsersBasic
	$config["regFlow"]["basic"]["groupID"] = "00gst60jvcizQe0No1t6";

	// atkoDemoUsersSFchatter
	$config["regFlow"]["sfChatter"]["groupID"] = "00gst7346E06ywPyc1t6";

	// atkoDemoUsersWithMFA
	$config["regFlow"]["withMFA"]["groupID"] = "00gst4ezhRw5g3phR1t6";

	// atkoDemoUsersWithEmail
	$config["regFlow"]["withEmail"]["groupID"] = "00gst60n94ZTQFRqn1t6";

	// atkoDemoUsersProvisional
	$config["regFlow"]["provisional"]["groupID"] = "";

	// atkodDemoUsersOktaAdmin
	$config["regFlow"]["okta"]["groupID"] = "00gst6j0n6PI0iLle1t6";

	$facebook = array("type"=>"FACEBOOK", "id"=>"0oassj82zxJdGVjjL1t6");
	$idps[] = $facebook;

	$google = array("type"=>"GOOGLE", "id"=>"0oasss0hkdAGnhCzF1t6");
	$idps[] = $google;

	// The list of apps that should be displayed in the UI.
	// This prevents "junk" apps from cluttering up the user's list of apps
	// The key is the appName from the Okta app Object (via appLinks)
	// The value is what you want to be displayed in the UI.

	$appsWhitelist["salesforce"] = "Chatter";

}

$config["regFlow"]["basic"]["title"] = "Basic registration flow";
$config["regFlow"]["basic"]["desc"] = "A basic user record will be created in the Okta universal directory. The user will be authenticated immediately.";

$config["regFlow"]["sfChatter"]["title"] = "Registration with Salesforce provisioning";
$config["regFlow"]["sfChatter"]["desc"] = "A user record will be created in the Okta universal directory, and the user will be provisioned to Salesforce Chatter. User will be authenticated immediately.";
$config["regFlow"]["sfChatter"]["shortDesc"] = "Provision a Salesforce Chatter user";

$config["regFlow"]["withMFA"]["title"] = "MFA registration flow";
$config["regFlow"]["withMFA"]["desc"] = "A user record will be created in the Okta universal directory. An activation email will be sent to the user. The user must use a 2nd factor when they authenticate.";
$config["regFlow"]["withMFA"]["shortDesc"] = "User must enroll in MFA";

$config["regFlow"]["withEmail"]["title"] = "Email verification user flow";
$config["regFlow"]["withEmail"]["desc"] = "A user record will be created in the Okta universal directory. The user must verify their email address before they can authenticate.";
$config["regFlow"]["withEmail"]["shortDesc"] = "User must verify their email address";

$config["regFlow"]["provisional"]["title"] = "Provisional registration";
$config["regFlow"]["provisional"]["desc"] = "A user will be created in an inactive state in the Okta universal directory. An admin must review the user record and manually activate (invite) the user.";
$config["regFlow"]["provisional"]["shortDesc"] = "User must be approved by admin";

$config["regFlow"]["okta"]["title"] = "Okta admin registration";
$config["regFlow"]["okta"]["desc"] = "An Okta employee can register and get admin access (read-only) to the demo tenant. An Okta email address is required. MFA is also enforced for authentication.";
$config["regFlow"]["okta"]["shortDesc"] = "Okta users can register as an admin";

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
$config["setMenu"]["vars"] = array("apiHome", "appsWhitelist");

$config["signout"]["type"] = "javascript";
$config["signout"]["location"] = "inline";
$config["signout"]["vars"] = array("apiHome");

$config["regOptions"] = getRegOptions();

// $config["indexUtils"]["type"] = "javascript";
// $config["indexUtils"]["location"] = "inline";

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

$config["webHomeURL"] = $config["host"] . $config["webHome"];

// Danger Will Robinson
// This value needs to match a value in the Redirect URIs list
// in your Okta tenant

$config["redirectURL"] = $config["host"] . $config["webHome"];

function setEnv($homeDir, $oktaOrg, $apiKeyPath) {
	global $config;

	if (file_exists("/usr/local/env/tomlocalhost.txt")) {
		// on Tom's local machine
	}
	else if (file_exists("/usr/local/env/atkoserver.txt")) {
		// on the www.atkodemo.com server
		$homeDir = "";
	}
	else {
		// on a virtual machine or someplace else
		$oktaOrg = "atkodemovm";
		$apiKeyPath = "/usr/local/keys/atkodemovm.txt";
	}

	$config["homeDir"] = $homeDir;
	$config["oktaOrg"] = $oktaOrg;
	$config["apiKey"] = trim(file_get_contents($apiKeyPath));
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