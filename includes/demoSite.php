<?php

class demoSite {

	function __construct() {

	}

	// function getDesc() { return $this->desc; }
	function getHomeDir() { return $this->homeDir; }
	function getName() { return $this->name; }

	function load() {

		$siteToLoad = $this->getSiteToLoad();

		$this->configHome = $this->fsHome . "sites/" . $siteToLoad;

		$this->configFile = $this->configHome . "/config.php";

		$configFiles = ["config", "groups", "theme", "regDesc"];

		// $configFiles = ["config"];

		foreach ($configFiles as $fileName) {

			$this->loadConfigFile($fileName);

		}

		// echo json_encode($this->config);
	}

	private function getSiteToLoad() {

		if (file_exists($this->siteToLoadPath)) {
			$siteToLoad = trim(file_get_contents($this->siteToLoadPath));
		}
		else {
			$env = $this->getLocalEnv();

			if ($env == "tom") {
				$siteToLoad = "atkodemo";
			}
			else if ($env == "atkodemo") {
				$siteToLoad = "atkodemo";
			}
			else {
				$siteToLoad = "default";
			}
		}
		return $siteToLoad;
	}

	function loadConfigFile($fileName) {
		$fileName = $fileName . ".json";

		$filePath = $this->configHome . "/" . $fileName;

		if (!file_exists($filePath)) {
			$filePath = $this->defaultHome . "/" . $fileName;
		}

		$json = file_get_contents($filePath);

		echo "<p>the file path is: " . $filePath;
		echo "<p>";
		echo $json;

		$arr = json_decode($json, TRUE);

		foreach ($arr as $key => $value) {

			$this->config[$key] = $value;

		}
	}

	function setDesc($siteDesc) {
		if (empty($siteDesc)) {
			$desc["tom"] = "tom's local machine";
			$desc["atkodemo"] = "public site: www.atkodemo.com";
			$desc["docker"] = "docker container";
			$desc["unknown"] = "unknown";

			$this->desc = $desc[$this->name];
		}
		else { $this->desc = $siteDesc; }
	}

	function setHomeDir($homeDir) {
		if (empty($homeDir)) {
			if ( $this->getLocalEnv() === "atkodemo") { $homeDir = ""; }
			else { $homeDir = "atkodemo"; }
		}
		$this->homeDir = $homeDir;
	}

	function setName($siteName) {
		if (empty($siteName)) { $this->name = $this->getLocalEnv(); }
		else { $this->name = $siteName; }
	}

	private function getLocalEnv() {
		if (file_exists("/usr/local/env/tomlocalhost.txt")) {
			// on Tom's local machine
			return "tom";
		}
		else if (file_exists("/usr/local/env/atkoserver.txt")) {
			// on the www.atkodemo.com server
			return "atkodemo";
		}
		else if (file_exists("/var/www/html/dockerContainer.txt")) {
			// probably in the atkodemo docker container
			return "docker";
		}
		else {
			// somewhere in the wild; cool.
			return "unknown";
		}
	}

	function setLocalPaths() {

		// web home
		if (empty($this->homeDir)) { $this->webHome = "/"; }
		else { $this->webHome = "/" . $this->homeDir . "/"; }

		// local file space home
		$this->fsHome = $_SERVER["DOCUMENT_ROOT"] . $this->webHome;

		// include path
		$this->includePath = $this->fsHome . "includes";

		set_include_path($this->includePath);

		// host and web home url

		if ($_SERVER["SERVER_NAME"] != "localhost") {
			error_reporting(0); // turn off error reporting for "production" sites
		}

		$this->webHomeURL = $this->getWebHomeURL();

		$this->redirectURL = $this->webHomeURL;

		$this->sitesHome = $this->fsHome . "sites/";

		$this->siteToLoadPath = $this->sitesHome . "siteToLoad.txt";

		$this->defaultHome = $this->sitesHome . "default";
	}

	private function getWebHomeURL() {

		// http or https
		if ($this->isSecure()) { $protocol = "https"; }
		else { $protocol = "http"; }

		$webHomeURL = $protocol . "://" . $_SERVER["SERVER_NAME"];

		// add the port to the hostname if appropriate
		if (array_key_exists("SERVER_PORT", $_SERVER)) {
			if ($_SERVER["SERVER_PORT"] == "80") {}
			else { $webHomeURL .= ":" . $_SERVER["SERVER_PORT"]; }
		}

		return $webHomeURL;

	}

	private function isSecure() {
		return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
	}

}