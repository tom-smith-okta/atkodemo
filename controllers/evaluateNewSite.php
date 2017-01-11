<?php

$fileName = "../sites/siteCount.txt";

$siteCount = file_get_contents($fileName);

$siteName = "site" . $siteCount;

$siteCount++;

file_put_contents($fileName, $siteCount);

$main["siteName"] = $siteName;

$main["oktaOrg"] = $_POST["oktaOrg"];

if (array_key_exists("apiKey", $_POST)) {
	if ($_POST["apiKey"]) {
		$main["apiKey"] = $_POST["apiKey"];
	}
}

if (array_key_exists("clientID", $_POST)) {
	if ($_POST["clientID"]) {
		$main["clientID"] = $_POST["clientID"];
	}
}

echo json_encode($main);

// $path = "../sites/" . $siteName . "/main.json";

$path = "../sites/" . $siteName;

// mkdir("../sites/temp");

mkdir($path);

$fullPath = $path . "/main.json"; 

file_put_contents($fullPath, json_encode($main));

// file_force_contents($path, json_encode($main));

// function file_force_contents($dir, $contents){
//     $parts = explode('/', $dir);
//     $file = array_pop($parts);
//     $dir = '';
//     foreach($parts as $part)
//         if(!is_dir($dir .= "/$part")) mkdir($dir);
//     file_put_contents("$dir/$file", $contents);
// }

// file_put_contents($path, json_encode($main));

// {
// 	"siteName": "Okta platform demo",
// 	"oktaOrg": "tomco",
// 	"clientId": "YYUAPHIAj3JPPO6yJans",
// 	"apiKeyPath": "/usr/local/keys/oktaAPI.txt",
// 	"appsBlacklist": [
// 		{ "name": "Facebook media management",
// 		"id": "auclb3c8tgEqckPeq1t6" }
// 	],
// 	"idps": [
// 		{ "type": "FACEBOOK",
// 		"id": "0oa1w1pmezuPUbhoE1t6"},
// 		{ "type": "GOOGLE",
// 		"id": "0oa1w8n4dlYlOLjPl1t6"}
// 	],
// 	"widgetVer": "1.7.0"
// }



// include "../includes/user.php";

// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// $regFlow = $_POST["regFlow"];

// $_SESSION["regFlow"] = $regFlow;

// $thisUser = new user();

// $_SESSION["user"] = $thisUser;

// if ($_SESSION["demo"]["site"]->regFlows[$regFlow]["activate"]) {

// 	$cookieToken = $thisUser->authenticate();

// 	$thisUser->redirect($cookieToken);
// }
// else {

// 	if (array_key_exists("ALLOW_ADMIN_REG", $_SESSION["demo"]["site"]->regFlows[$regFlow])) {

// 		if ($_SESSION["demo"]["site"]->regFlows[$regFlow]["ALLOW_ADMIN_REG"] === TRUE) {

// 			if ($thisUser->hasRequiredEmailAddress()) {

// 				$thisUser->setAdminRights();

// 			}
// 			else {
// 				echo "Sorry, that email address is not authorized for admin access.";
// 				exit;
// 			}
// 		}
// 	}

// 	$thisUser->sendActivationEmail();

// 	$headerString = "Location: " . $_SESSION["demo"]["site"]->webHome . "views/thankYou.php";

// 	header($headerString);
// }