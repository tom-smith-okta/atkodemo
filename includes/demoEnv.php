<?php

function setDemoEnv() {

	setIncludePaths();

	// look for a marker file on the local machine
	// to indicate whether the script is running on:
	// 1) Tom's local machine
	// 2) the www.atkodemo.com web server
	// 3) a docker container
	// 4) other

	$json = file_get_contents("standardEnvs.json", FILE_USE_INCLUDE_PATH);

	$envs = json_decode($json, TRUE);

	$_SESSION["demo"]["env"]["name"] = getEnvironment($envs);

	$_SESSION["demo"]["homeDir"] = getHomeDir();

	getSites();

}

/********* Function defs *********************/

function getEnvironment($envs) {
	foreach ($envs as $name => $vals) {
		if (file_exists($vals["marker"])) {

			$_SESSION["demo"]["env"]["defaultSite"] = $vals["site"];

			return $name;
		}
	}
	$_SESSION["demo"]["env"]["defaultSite"] = "default";
	return "unknown";
}

// fixes the script's place in the filesystem
function getHomeDir() {
	$dirPathArr = explode("/", dirname(getcwd()));
	return end($dirPathArr);
}

function getSites() {

	$sitesHome = "../sites";

	$dirs = scandir($sitesHome);

	foreach ($dirs as $dir) {

		if ($dir === "." || $dir === "..") {}
		else {
			$path = $sitesHome . "/" . $dir;

			if (is_dir($path)) {
				$_SESSION["demo"]["sites"][] = $dir;
			}
		}
	}
}

function setIncludePaths() {
	$includePath = dirName(getcwd()) . "/includes";

	set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);
	set_include_path(get_include_path() . PATH_SEPARATOR . $includePath . "/config");
}