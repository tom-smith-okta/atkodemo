<?php


$config["homeDir"] = $home; // e.g.: "atkotravel"

// e.g.: "/Applications/MAMP/htdocs"
$config["fsHome"] = $_SERVER['DOCUMENT_ROOT'] . "/" . $config["homeDir"];

// $config["host"] = $_SERVER["SERVER_NAME"];

// $config["okta-signin-widget"] = "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/1.3.3/js/okta-sign-in-1.3.3.min.js";

$config["oktaOrg"] = "tomco";

$config["oktaBaseURL"] = "https://" . $config["oktaOrg"] . ".okta.com";

$config["homePage"] = "/" . $config["homeDir"] . "/" . "home.php";

$config["homePageWithSession"] = $config["homePage"] . "?oktaCookieSessionID=";
// $config["homePageWithSession"] .= "\" + res.id + \"&oktaUserID=\" + res.userId";

$config["checkForSession"]["type"] = "javascript";
$config["checkForSession"]["vars"] = array("oktaBaseURL", "homePage", "homePageWithSession");
$config["checkForSession"]["isInline"] = TRUE;

$config["okta-signin-widget"]["type"] = "javascript";
$config["okta-signin-widget"]["url"] = "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/1.3.3/js/okta-sign-in-1.3.3.min.js";
$config["okta-signin-widget"]["isInline"] = FALSE;
