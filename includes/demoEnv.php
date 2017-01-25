<?php

function setDemoEnv() {

	setPaths();

	$_SESSION["webHome"] = getHomeDir();

	// Find which site should be loaded by default

	$_SESSION["defaultSite"] = getDefaultSite();

	unset($_SESSION["allSites"]);

	$stdSites = getSites($_SESSION["paths"]["sites"]);

	if (file_exists($_SESSION["paths"]["mysites"])) {
		$mysites = getSites($_SESSION["paths"]["mysites"]);
	}

	if (empty($mysites)) { $_SESSION["allSites"] = $stdSites; }
	else { $_SESSION["allSites"] = array_merge($stdSites, $mysites); }

}

function getDefaultSite() {

	$filename = $_SESSION["paths"]["mysites"] . "/defaultSite.json";
	if (file_exists($filename)) {
		$json = file_get_contents($filename);
		$arr = json_decode($json, TRUE);

		if (!(empty($arr["defaultSite"]))) {
			return $arr["defaultSite"];
		}
	}
	$filename = $_SESSION["paths"]["sites"] . "/defaultSite.json";
	$json = file_get_contents($filename);
	$arr = json_decode($json, TRUE);
	return $arr["defaultSite"];
}

// fixes the script's place in the filesystem
function getHomeDir() {
	$dirPathArr = explode("/", dirname(getcwd()));
	return end($dirPathArr);
}

function getSites($sitesHome) {

	$dirs = scandir($sitesHome);

	foreach ($dirs as $dir) {

		if ($dir === "." || $dir === ".." || $dir === "default") {}
		else {
			$path = $sitesHome . "/" . $dir;

			if (is_dir($path)) {
				$sites[] = $dir;
			}
		}
	}

	return $sites;
}

function setPaths() {
	$includePath = dirName(getcwd()) . "/includes";

	set_include_path(get_include_path() . PATH_SEPARATOR . $includePath);

	$_SESSION["paths"]["sites"] = "../sites";
	$_SESSION["paths"]["mysites"] = "../mysites";
	$_SESSION["paths"]["default"] = $_SESSION["paths"]["sites"] . "/default/";

}