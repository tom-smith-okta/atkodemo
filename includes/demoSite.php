<?php

class demoSite {

	function __construct($siteName, $homeDir) {

		$this->siteName = $siteName;
		$this->homeDir = $homeDir;

		$reqs = file_get_contents("siteSettings.json", FILE_USE_INCLUDE_PATH);

		// echo $reqs;

		$this->reqs = json_decode($reqs, TRUE);

		// echo "<pre>" . print_r($this->reqs) . "</pre>";

		$this->loadConfigFiles();

	}

	private function loadConfigFiles() {

		$this->sitePath = "../sites/" . $this->siteName;

		$this->mainConfig = json_decode(file_get_contents($this->sitePath . "/main.json"));

		foreach ($this->mainConfig as $key => $value) {
			$this->$key = $value;
		}

		foreach ($this->reqs as $key => $values) {
			if ($values["required"] == "TRUE" && empty($this->$key)) {
				$this->$key = $values["default"];
			}
		}
	}

	public function showSettings() {
		$output = "";

		foreach ($this->reqs as $key => $values) {
			$output .= "<p>param: " . $key;
			$output .= "<p>" . json_encode($values);
			$output .= "<p>" . $this->$key;
			$output .= "<hr>";
		}

		echo $output;
	}

	// function getDesc() { return $this->desc; }
	function getHomeDir() { return $this->homeDir; }
	function getName() { return $this->name; }

	function getRegOptions() {
		$retVal = "";

		foreach ($this->regDesc as $regFlowName => $values) {

			$retVal .= "<li>";
			$retVal .= "<a href = 'register.php?regType=" . $regFlowName . "'>";
			$retVal .= "<h3>" . $values["title"] . "</h3>";

			if (array_key_exists("shortDesc", $values)) {
				$retVal .= "<p>" . $values["shortDesc"] . "</p>";
			}

			$retVal .= "</a></li>";

		}
		return $retVal;

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

	private function checkAPIkey() {

		if (empty($this->apiKey)) {
			$config["warnings"][] = "No API key found.";
			$config["warnings"][] = "User registration is not possible without an API key.";
		}
		else {
			$apiKey = $this->apiKey;

			$curl = curl_init();

			$url = $this->apiHome . "/meta/schemas/user/default";

			curl_setopt_array($curl, array(
				CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_URL => $url
			));

			$jsonResult = curl_exec($curl);

			$assocArray = json_decode($jsonResult, TRUE);

			if ($assocArray["id"]) { return TRUE; }
			else {
				$config["apiKeyIsValid"] = FALSE;
				$config["warnings"][] = $jsonResult;
				$config["warnings"][] = "User registration is not possible without an API key.";
			}
		}
	}

	private function lookForAPIkey() {

		$apiKeyPath = $this->mainConfig["apiKeyPath"];

		if (file_exists($apiKeyPath)) {
			$this->apiKey = trim(file_get_contents($apiKeyPath));
		}
		else {
			$this->warnings[] = "The file " . $apiKeyPath . " does not exist.";
		}
	}

	private function setRemotePaths() {
		$this->oktaBaseURL = "https://" . $this->mainConfig["oktaOrg"] . ".okta.com";
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