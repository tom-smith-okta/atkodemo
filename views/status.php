<?php

include "../includes/loadDemo.php";

$thisSite = $_SESSION["demo"]["site"];

$bodyMain = file_get_contents("../html/status.html");

$rows = "";

$configFiles = $_SESSION["configFiles"];

$bottomRow = file_get_contents("../html/status/bottomRow.html");

foreach ($_SESSION["demo"]["sites"] as $dirName) {

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

	foreach ($configFiles as $file) {

		$bullseye = "%--" . $file . "--%";

		$arrow = "";

		if (array_key_exists($file, $site->source)) {
			$path = $site->source[$file]["path"];
			$dir = $site->source[$file]["dir"];

			$arrow = "<a href = '" . $path . "' target = '_blank'>" . $dir . "</a>";
		}

		$thisRow = str_replace("%--$file--%", $arrow, $thisRow);

	}
	$rows .= $thisRow;
}

$bodyMain = str_replace("%--rows--%", $rows, $bodyMain);

$thisSite->showPage("status", $bodyMain);