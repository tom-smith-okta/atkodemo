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

$path = "../sites/" . $siteName;

mkdir($path);

$fullPath = $path . "/main.json"; 

file_put_contents($fullPath, json_encode($main));

$url = "../views/status.php";

header("Location: " . $url);

exit;