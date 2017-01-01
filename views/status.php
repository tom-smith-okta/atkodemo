<?php

include "../includes/loadDemo.php";

$thisSite = $_SESSION["demo"]["site"];

$bodyMain = file_get_contents("../html/status.html");

$env = $_SESSION["env"]["name"];

$rows = "";

$configFiles = ["main", "regFlows", "theme"];

$bottomRow = file_get_contents("../html/status/bottomRow.html");

foreach ($_SESSION["demo"]["sites"] as $siteName) {

	$site = new demoSite($siteName);

	$rows .= "<tr>";
	$rows .= "<td style = 'border-left: 2px solid; border-right: 2px solid;'>" . $site->dirName . "</td>";
	$rows .= "<td>" . $site->siteName . "</td>";
	$rows .= "<td>" . $site->oktaOrg . "</td>";
	$rows .= "<td align = 'center'>" . $site->getIcon("authentication") . "</td>";
	$rows .= "<td align = 'center'>" . $site->getIcon("apiKey") . "</td>";
	$rows .= "<td align = 'center'>" . $site->getIcon("OIDC") . "</td>";
	$rows .= "<td align = 'center'>" . $site->getIcon("socialLogin") . "</td>";
	$rows .= "<td align = 'center' style = 'border-right: 1px solid'>" . $site->getIcon("appsBlacklist") . "</td>";
	$rows .= "<td align = 'center' style = 'border-right: 1px solid'>" . $site->getIcon("regFlows") . "</td>";
	$rows .= "<td align = 'center' style = 'border-right: 1px solid'>" . $site->getIcon("theme") . "</td>";

	$rows .= "</tr>\n";

	$thisRow = $bottomRow;

	foreach ($configFiles as $file) {

		$bullseye = "%--" . $file . "--%";

		$path = $site->source[$file];

		$arrow = "<a href = '" . $path . "' target = '_blank'>" . $path . "</a>";

		$thisRow = str_replace("%--$file--%", $arrow, $thisRow);

	}

	$rows .= $thisRow;

	// $rows .= "<tr style = 'border-bottom: 2px solid'>";

	// $rows .= "<td style = 'border-left: 2px solid; border-right: 2px solid;'></td><td colspan = '7' style = 'border-right: 1px solid; text-align: center;'>" . $site->source["main"] . "</td><td style = 'border-right: 1px solid; text-align: center;'>" . $site->source["regFlows"] . "</td><td style = 'border-right: 1px solid; text-align: center;'>" . $site->source["theme"] . "</td>";

	// $rows .= "</tr>\n";

}


$bodyMain = str_replace("%--rows--%", $rows, $bodyMain);
$bodyMain = str_replace("%--env--%", $env, $bodyMain);

$thisSite->showPage("status", $bodyMain);