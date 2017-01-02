<?php

include "../includes/demoEnv.php";
include "../includes/demoSite.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

setDemoEnv();

// if (!(array_key_exists("env", $_SESSION))) {
// 	setDemoEnv();
// }

if (array_key_exists("siteToLoad", $_GET)) {
	$_SESSION["demo"]["site"] = new demoSite($_GET["siteToLoad"]);
}
else if (empty($_SESSION["demo"]["site"])) {
	$siteName = $_SESSION["defaultSite"];

	$_SESSION["demo"]["site"] = new demoSite($siteName);
}