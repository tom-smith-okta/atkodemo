<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

setIncludePaths();

include "includes.php";

// look for a marker file to indicate whether the script
// is running on:
// 1) Tom's local machine
// 2) the www.atkodemo.com web server
// 3) a docker container
// 4) other 
$env = getEnvironment();

// find the script's parent directory
$homeDir = getHomeDir();

$thisSite = new demoSite($env, $homeDir);

// Get the name of the site that should be loaded
// based on $host or the value of /sites/siteToLoad.json
$siteToLoad = getSite($env);

$thisSite->setSite($siteToLoad);

$_SESSION["siteObj"] = $thisSite;

/********* Function defs *********************/

function getEnvironment() {
	$json = file_get_contents("envMarkers.json", FILE_USE_INCLUDE_PATH);

	$envMarkers = json_decode($json, TRUE);

	foreach ($envMarkers as $name => $path) {
		if (file_exists($path)) { return $name; }
	}
	return "unknown";
}

// fixes the script's place in the filesystem
function getHomeDir() {
	$dirPathArr = explode("/", dirname(getcwd()));
	return end($dirPathArr);
}

function getSite($host) {
	$json = file_get_contents("siteNames.json", FILE_USE_INCLUDE_PATH);

	$sites = json_decode($json, TRUE);

	if (array_key_exists($host, $sites)) {
		return $sites[$host];
	}
	else { return "default"; }
}

function setIncludePaths() {
	$includePath = dirName(getcwd()) . "/includes";

	set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);
	set_include_path(get_include_path() . PATH_SEPARATOR . $includePath . "/config");
}