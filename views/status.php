<?php

include "../includes/loadDemo.php";

$thisSite = $_SESSION["site"];

$bodyMain = file_get_contents("../html/status.html");

$rows = "";

foreach ($_SESSION["allSites"] as $dirName) {

	$site = new Site($dirName);

	if ($site->dirName === $thisSite->dirName) {
		$rows .= "<tr style = 'background-color: LightGoldenRodYellow; border-bottom: 1px solid'>";
		$siteToLoad = $site->siteName;
	}
	else {
		$rows .= "<tr style = 'border-bottom: 1px solid; vertical-align: middle'>";
		$siteToLoad = "<a href = 'status.php?siteToLoad=" . $dirName . "' class = 'button big'>" . $site->siteName . "</a>";
	}

	$rows .= "<td style = 'border-left: 2px solid; border-right: 2px solid;'><a href = '" . $site->sitePath . "' target = '_blank'>" . $site->dirName . "</td>";
	$rows .= "<td>" . $siteToLoad . "</a></td>";

	$rows .= "<td><a href = '" . $site->oktaBaseURL . "'>" . $site->oktaDomain . "</a></td>";
	$rows .= "<td align = 'center'>" . $site->getIcon("authentication") . "</td>";
	$rows .= "<td align = 'center'>" . $site->getIcon("apiKey") . "</td>";
	$rows .= "<td align = 'center'>" . $site->getIcon("OIDC") . "</td>";
	$rows .= "<td align = 'center'>" . $site->getIcon("socialLogin") . "</td>";
	$rows .= "<td align = 'center' style = 'border-right: 1px solid'>" . $site->getIcon("appsBlacklist") . "</td>";

	$rows .= "</tr>\n";
}

$bodyMain = str_replace("%--rows--%", $rows, $bodyMain);
$bodyMain = str_replace("%--oktaDomain--%", $_SESSION["site"]->oktaDomain, $bodyMain);
$bodyMain = str_replace("%--siteName--%", $_SESSION["site"]->siteName, $bodyMain);

$thisSite->showPage("status", $bodyMain);