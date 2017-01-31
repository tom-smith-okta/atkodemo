<?php

include "../includes/loadDemo.php";

$thisSite = $_SESSION["site"];

$bodyMain = file_get_contents("../html/status.html");

$rows = "";

$bottomRow = file_get_contents("../html/status/bottomRow.html");

foreach ($_SESSION["allSites"] as $dirName) {

	$site = new Site($dirName);

	if ($site->dirName === $thisSite->dirName) { 
		$rows .= "<tr style = 'background-color: LightGoldenRodYellow;'>";
	}
	else { $rows .= "<tr>"; }

	$rows .= "<td style = 'border-left: 2px solid; border-right: 2px solid;'>" . $site->dirName . "</td>";
	$rows .= "<td>" . $site->siteName . "</td>";
	$rows .= "<td>" . $site->oktaOrg . "</td>";
	$rows .= "<td align = 'center'>" . $site->getIcon("authentication") . "</td>";
	$rows .= "<td align = 'center'>" . $site->getIcon("apiKey") . "</td>";
	$rows .= "<td align = 'center'>" . $site->getIcon("OIDC") . "</td>";
	$rows .= "<td align = 'center'>" . $site->getIcon("socialLogin") . "</td>";
	$rows .= "<td align = 'center' style = 'border-right: 1px solid'>" . $site->getIcon("appsBlacklist") . "</td>";
	$rows .= "<td align = 'center' style = 'border-right: 1px solid'>" . $site->getIcon("regFlows") . "</td>";
	$rows .= "<td align = 'center' style = 'border-right: 1px solid'>" . $site->getIcon("regFields") . "</td>";
	$rows .= "<td align = 'center' style = 'border-right: 1px solid'>" . $site->getIcon("theme") . "</td>";

	$rows .= "</tr>\n";

	$thisRow = $bottomRow;

	if ($site->dirName === $thisSite->dirName) { $siteToLoad = ""; }
	else {
		$siteToLoad = "<a href = 'status.php?siteToLoad=" . $dirName . "' class = 'button big'>Load</a>";
	}

	$thisRow = str_replace("%--siteToLoad--%", $siteToLoad, $thisRow);

	foreach ($site->configFiles as $key => $value) {

		$bullseye = "%--" . $key . "--%";

		$arrow = "";

		if (array_key_exists($key, $site->source)) {
			$path = $site->source[$key]["path"];
			$dir = $site->source[$key]["dir"];

			$arrow = "<a href = '" . $path . "' target = '_blank'>" . $dir . "</a>";
		}

		$thisRow = str_replace("%--$key--%", $arrow, $thisRow);

	}
	$rows .= $thisRow;
}

$bodyMain = str_replace("%--rows--%", $rows, $bodyMain);
$bodyMain = str_replace("%--oktaOrg--%", $_SESSION["site"]->oktaOrg, $bodyMain);
$bodyMain = str_replace("%--siteName--%", $_SESSION["site"]->siteName, $bodyMain);



$thisSite->showPage("status", $bodyMain);