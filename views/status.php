<?php

include "../includes/loadDemo.php";

$thisSite = $_SESSION["site"];

$bodyMain = file_get_contents("../html/status.html");

$rows = "";

foreach ($_SESSION["allSites"] as $dirName) {

	$site = new Site($dirName);

	if ($site->dirName === $thisSite->dirName) {
		$siteToLoad = $site->siteName;
		$backgroundColor = "LightGoldenRodYellow";
	}
	else {
		$siteToLoad = "<a href = 'status.php?siteToLoad=" . $dirName . "' class = 'button big'>" . $site->siteName . "</a>";
		$backgroundColor = "";
	}

	$row = file_get_contents("../html/status/statusRow.html");

	$row = str_replace("%--backgroundColor--%", $backgroundColor, $row);
	$row = str_replace("%--siteToLoad--%", $siteToLoad, $row);
	$row = str_replace("%--dirName--%", $dirName, $row);
	$row = str_replace("%--oktaBaseURL--%", $site->oktaBaseURL, $row);
	$row = str_replace("%--sitePath--%", $site->sitePath, $row);
	$row = str_replace("%--oktaDomain--%", $site->oktaDomain, $row);

	$vals = ["authentication", "apiKey", "OIDC", "socialLogin", "appsBlacklist"];

	foreach ($vals as $val) {
		$row = str_replace("%--$val--%", $site->getIcon($val), $row);
	}

	$rows .= $row;
}

$bodyMain = str_replace("%--rows--%", $rows, $bodyMain);
$bodyMain = str_replace("%--oktaDomain--%", $_SESSION["site"]->oktaDomain, $bodyMain);
$bodyMain = str_replace("%--siteName--%", $_SESSION["site"]->siteName, $bodyMain);

$thisSite->showPage("status", $bodyMain);