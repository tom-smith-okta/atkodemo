<?php

include "../includes/loadDemo.php";

$thisSite = $_SESSION["demo"]["site"];

$bodyMain = file_get_contents("../html/status.html");

$env = $_SESSION["env"]["name"];

foreach ($_SESSION["demo"]["sites"] as $siteName) {

	$site = new demoSite($siteName);

	$row = "<tr>";
	$row .= "<td>" . $site->siteName . "</td>";
	$row .= "<td>" . $site->dirName . "</td>";
	$row .= "<td>" . $site->oktaOrg . "</td>";
	$row .= "<td align = 'center'>" . $site->getIcon("authentication") . "</td>";
	$row .= "<td align = 'center'>" . $site->getIcon("registration") . "</td>";
	$row .= "<td align = 'center'>" . $site->getIcon("OIDC") . "</td>";
	$row .= "<td align = 'center'>" . $site->getIcon("socialLogin") . "</td>";
	$row .= "<td align = 'center'>" . $site->getIcon("appsBlacklist") . "</td>";
	$row .= "</tr>\n";

	$rows .= $row;
}


$bodyMain = str_replace("%--rows--%", $rows, $bodyMain);
$bodyMain = str_replace("%--env--%", $env, $bodyMain);

$thisSite->showPage("status", $bodyMain);