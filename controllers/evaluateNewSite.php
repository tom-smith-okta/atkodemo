<?php

$fileName = "../mysites/siteCount.txt";

if (file_exists($fileName)) {
	$siteCount = file_get_contents($fileName);
}
else {
	$siteCount = 1;
	file_put_contents($fileName, $siteCount);
}

$dirName = "site" . $siteCount;

$siteCount++;

file_put_contents($fileName, $siteCount);

$main["dirName"] = $dirName;

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

if (array_key_exists("siteName", $_POST)) {
	if ($_POST["siteName"]) {
		$main["siteName"] = $_POST["siteName"];
	}
}

if (array_key_exists("oktaHost", $_POST)) {
	if ($_POST["oktaHost"]) {
		$main["oktaHost"] = $_POST["oktaHost"];
	}
	else { $main["oktaHost"] = "okta"; }
}

$main["widgetVer"] = "1.7.0";

$path = "../mysites/" . $dirName;

mkdir($path);

$fullPath = $path . "/main.json"; 

file_put_contents($fullPath, json_encode($main));

$url = "../views/status.php";

header("Location: " . $url);

exit;