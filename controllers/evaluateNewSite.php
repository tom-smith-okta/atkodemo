<?php

// $fileName = "../sites/siteCount.txt";

$fileName = "../mysites/siteCount.txt";

if (file_exists($fileName)) {
	$siteCount = file_get_contents($fileName);
}
else {
	$siteCount = 1;
	file_put_contents($fileName, $siteCount);
}

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

$main["widgetVer"] = "1.7.0";

// $path = "../sites/" . $siteName;

$path = "../mysites/" . $siteName;

// echo "<p>scan dir is: " . json_encode(scandir("../mysites/"));

// echo "<p>trying to make directory: " . $path;

mkdir($path);

$fullPath = $path . "/main.json"; 

file_put_contents($fullPath, json_encode($main));

$url = "../views/status.php";

header("Location: " . $url);

exit;