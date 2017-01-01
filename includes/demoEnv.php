<?php

function setDemoEnv() {

	setIncludePaths();

	// look for a marker file on the local machine
	// to indicate whether the script is running on:
	// 1) Tom's local machine
	// 2) the www.atkodemo.com web server
	// 3) a docker container
	// 4) other

	setEnvVars();

	$_SESSION["demo"]["homeDir"] = getHomeDir();

	getSites();

	$_SESSION["capabilities"] = ["authentication", "apiKey", "registration", "OIDC", "socialLogin", "appsBlacklist"];

	$_SESSION["defaultPath"] = "../sites/default/";

}

function setEnvVars() {
	$_SESSION["env"]["name"] = "unknown";
	$_SESSION["env"]["defaultDir"] = "default";
	$_SESSION["env"]["webHome"] = "atkodemo";

	$json = file_get_contents("standardEnvs.json", FILE_USE_INCLUDE_PATH);

	$envs = json_decode($json, TRUE);

	foreach ($envs as $name => $vals) {
		if (file_exists($vals["marker"])) {
			$_SESSION["env"]["name"] = $name;
			$_SESSION["env"]["defaultDir"] = $vals["dir"];
			$_SESSION["env"]["webHome"] = $vals["webHome"];
			break;
		}
	}
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