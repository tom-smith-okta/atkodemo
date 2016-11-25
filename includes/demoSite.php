<?php

class demoSite {

	function __construct($homeDir, $host) {

		$this->homeDir = $homeDir;

		$this->host = $host;

		$this->sitesHome = "../sites/";

		$this->defaultPath = "../sites/default/";

		$this->metaData = file_get_contents("metadata.json", FILE_USE_INCLUDE_PATH);

		$this->metaData = json_decode($this->metaData, TRUE);

		foreach($this->metaData as $key => $value) {

			$this->configFiles[] = $key;

		}

		$this->importantSettings = array_merge($this->configFiles, [ "homeDir", "host", "oktaOrg", "apiKeyPath", "apiKey", "apiKeyIsValid", "clientId", "appsWhitelist", "idps"]);

	}

	function setSite($siteName) {
		
		$this->siteName = $siteName;

		$this->sitePath = $this->sitesHome . $this->siteName . "/";

		// load just the essentials first
		// $isRequired = TRUE
		$this->loadConfigFiles("TRUE");

		$this->setRemotePaths();

		$this->checkAPIkey();

		$this->loadConfigFiles("FALSE");

	}

	private function apiKeyIsValid() {

		$curl = curl_init();

		$url = $this->apiHome . "/meta/schemas/user/default";

		$apiKey = $this->apiKey;

		curl_setopt_array($curl, array(
			CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url
		));

		$jsonResult = curl_exec($curl);

		$assocArray = json_decode($jsonResult, TRUE);

		if ($assocArray["id"]) { return TRUE; }
		else {
			$this->warnings[] = $jsonResult;
			$this->warnings[] = "User registration is not possible without an API key.";
			return FALSE;
		}
	}

	private function checkAPIkey() {

		if (empty($this->apiKey) && $this->apiKeyPath) {
			$this->apiKey = $this->getAPIkey();
		}

		if ($this->apiKey) {
			$this->apiKeyIsValid = $this->apiKeyIsValid();
		}
	}

	private function getAPIkey() {

		$apiKeyPath = $this->apiKeyPath;

		if (file_exists($apiKeyPath)) {
			return trim(file_get_contents($apiKeyPath));
		}
		else {
			$this->warnings[] = "The file " . $apiKeyPath . " does not exist.";
		}

	}

	private function getFile($configFile) {

		$fileName = $configFile . ".json";

		if (file_exists($this->sitePath . $fileName)) {
			$dir = $this->sitePath;
		}
		else {

			$dir = $this->defaultPath;
		}

		$path = $dir . $fileName;

		$this->$configFile = $path; // save the $path for error-checking purposes

		$settings = json_decode(file_get_contents($path), TRUE);

		foreach ($settings as $key => $value) {

			$this->$key = $value;
		}

	}

	// Reads json config files and stores the settings in $this object
	// If the config file is not in the site directory, then the json
	// file will be loaded from the default directory.
	private function getSettings($configFile) {

		if ($this->metaData[$configFile]["required"] == "TRUE") {
			$this->getFile($configFile);
		}
		else {
			$dependency = $this->metaData[$configFile]["dependency"];

			if ($this->$dependency) {
				$this->getFile($configFile);
			}
			else {
				$this->$configFile = "none";
			}
		}
	}

	private function loadConfigFiles($isRequired) {

		foreach ($this->configFiles as $configFile) {
			if ($this->metaData[$configFile]["required"] === $isRequired) {
				$this->getSettings($configFile);
			}
		}
	}

	public function showSettings() {
		$output = "<p><b>Settings</b></p>";

		foreach ($this->importantSettings as $key) {

			$output .= "<p><b>" . $key . "</b>: ";

			if (isset($this->$key)) {

				if (is_array($this->$key)) {
					$output .= json_encode($this->$key);
				}
				else {
					$output .= $this->$key;
				}
			}
			else {
				$output .= "[none]";
			}
		}

		echo $output;
	}


	function load() {

		$siteToLoad = $this->getSiteToLoad();

		$this->configHome = $this->fsHome . "sites/" . $siteToLoad;

		$this->configFile = $this->configHome . "/config.php";

		$configFiles = ["mainConfig", "groups", "theme", "regDesc"];

		foreach ($configFiles as $fileName) {

			$this->loadConfigFile($fileName);

		}

		$this->setRemotePaths();

		$this->lookForAPIkey();

		$this->apiKeyIsValid = $this->checkAPIkey();

		if (!empty($this->idps)) { $this->idps = json_encode($this->idps); }

		if (!empty($this->appsWhitelist)) { $this->appsWhitelist = json_encode($this->appsWhitelist); }
	}

	private function setRemotePaths() {
		$this->oktaBaseURL = "https://" . $this->oktaOrg . ".okta.com";
		$this->apiHome = $this->oktaBaseURL . "/api/v1";
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
		$fullFileName = $fileName . ".json";

		$filePath = $this->configHome . "/" . $fullFileName;

		if (!file_exists($filePath)) {
			$filePath = $this->defaultHome . "/" . $fullFileName;
		}

		$json = trim(file_get_contents($filePath));

		echo "<p>the file path is: " . $filePath;
		echo "<p>";
		echo $json;

		$arr = json_decode($json, TRUE);

		// echo "<p>The assoc array is: " . "<pre>" . var_dump($arr) . "</pre></p>";

		// $arr = json_decode($json);

		$this->$fileName = $arr;

		// $temp["basic"]["title"] = "basic reg title";
		// $temp["basic"]["desc"] = "basic description";
		// $temp["basic"]["shortDesc"] = "basic short desc";

		// $temp["mfa"]["title"] = "mfa reg title";
		// $temp["mfa"]["desc"] = "mfa description";
		// $temp["mfa"]["shortDesc"] = "mfa short desc";

		// echo "<p>this is what the json should look like: ";
		// echo json_encode($temp);
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