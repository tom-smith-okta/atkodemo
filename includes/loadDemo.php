<?php

include "../includes/demoEnv.php";
include "../includes/site.php";

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

setDemoEnv();

$_SESSION["site"] = new Site("atkodemoOfficial");


// if (array_key_exists("siteToLoad", $_GET)) {
// 	$_SESSION["site"] = new Site($_GET["siteToLoad"]);
// }
// else if (empty($_SESSION["site"])) {
// 	echo "<br>the session site var is empty.";
// 	$dirName = $_SESSION["defaultSite"];

// 	$_SESSION["site"] = new Site($dirName);
// }

// echo "<br>the session site is: " . $_SESSION["site"];
// echo "<br>the dirname is: " . $dirName;

// exit;