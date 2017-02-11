<?php

$fileName = "../mysites/siteCount.txt";

if (file_exists($fileName)) {
	$siteCount = file_get_contents($fileName);
}
else {
	$handle = fopen($fileName, 'w') or die('Cannot open file:  '. $fileName); //implicitly creates file

	$siteCount = 1;
	file_put_contents($fileName, $siteCount);
}

$dirName = "site" . $siteCount;

$siteCount++;

file_put_contents($fileName, $siteCount);

$main["dirName"] = $dirName;

$params = ["apiKey", "clientId", "oktaHost", "oktaOrg", "siteName"];

foreach ($params as $param) {
	if (array_key_exists($param, $_POST)) {
		if ($_POST[$param]) {
			$main[$param] = $_POST[$param];
		}
	}
}

$main["widgetVer"] = "1.10.0";

$destPath = "../mysites/" . $dirName;

mkdir($destPath);

$files = ["main", "regFields", "regFlows", "theme"];

foreach ($files as $file) {

	$fileName = $file . ".json";

	$fullPath = $destPath . "/" . $fileName;

	$paths[$fileName] = $fullPath;

	if ($file == "main") {
		file_put_contents($fullPath, json_encode($main));
	}
	else {
		$src = "../sites/default/" . $fileName;
		copy ($src, $fullPath);
	}
}

// make an index.html file

$index = file_get_contents("../html/list.html");

$links = "";

foreach ($paths as $fileName => $url) {
	$links .= "<li><a href = '" . $fileName . "' target = '_blank'>" . $fileName . "</a></li>\n";
}

$index = str_replace("%--links--%", $links, $index);
$index = str_replace("%--siteName--%", $main["siteName"], $index);

file_put_contents($destPath . "/index.html", $index);

$url = "../views/status.php?siteToLoad=" . $dirName;

header("Location: " . $url);

exit;