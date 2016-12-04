<?php

class demoSite {

	function __construct($env, $homeDir) {

		$this->env = $env;

		$this->homeDir = $homeDir;

		$this->sitesHome = "../sites/";

		$this->defaultPath = "../sites/default/";

		$this->webHome = "/";

		if(!empty($this->homeDir)) {
			$this->webHome = $this->webHome . $homeDir . "/";
		}

		$this->metaData = file_get_contents("metadata.json", FILE_USE_INCLUDE_PATH);

		$this->metaData = json_decode($this->metaData, TRUE);

		foreach($this->metaData as $key => $value) {

			$this->configFiles[] = $key;

		}

		$this->capabilities = ["authentication", "registration", "regWithMFA", "OIDC", "socialLogin", "appsWhitelist", "regWithMFA", "appProvisioning"];

		$this->importantSettings = array_merge($this->configFiles, [ "homeDir", "host", "oktaOrg", "apiKeyPath", "apiKey", "apiKeyIsValid", "clientId", "appsWhitelist", "idps"]);

	}

	private function getRegOptions() {

		foreach ($config["regFlow"] as $regFlowName => $values) {

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


	function loadJSON($varName) {
		$json = file_get_contents($varName . ".json", FILE_USE_INCLUDE_PATH);

		$arr = json_decode($json, TRUE);

		foreach($arr as $value) {
			$this->$varName = $arr[$varName];

		}
	}

	function setSite($siteName) {
		
		$this->siteName = $siteName;

		$this->sitePath = $this->sitesHome . $this->siteName . "/";

		// load just the essentials first
		// $isRequired = TRUE
		$this->loadConfigFiles(TRUE);

		$this->setRemotePaths();

		$this->checkAPIkey();

		// load the optional config files
		$this->loadConfigFiles(FALSE);

		// check to see which capabilities are ready
		// and which are not
		$this->setSiteStatus();

		if ($this->status["appsWhitelist"]) {
			$this->appsWhitelist = json_encode($this->appsWhitelist);
		}
		else {
			// do something else
		}

		$this->setOktaWidget();

		$this->setRegOptions();

		$this->setMenus();

	}

	private function setOktaWidget() {

		$this->redirectUri = $this->getRedirectURI();

		if ($this->status["OIDC"]) {

			$this->oktaSignIn = file_get_contents("../javascript/loadWidgetOIDC.js");

			$this->renderWidget = file_get_contents("../javascript/renderWidgetOIDC.js");

			$this->widgetInBody = "<div id = 'container>\n\t\t<div id = 'oktaWidget'></div>\n\t</div>";

			if ($this->status["socialLogin"]) {
				$this->idpJS = "idpDisplay: 'PRIMARY',\n\t\t";
				$this->idpJS .= "idps: " . json_encode($this->idps);
			}
			else { $this->idpJS = ""; }
		}

		$this->oktaSignIn = $this->replaceElements($this->oktaSignIn);

	}

	private function setRegOptions() {

		$this->regOptions = "";

		if ($this->status["registration"] === TRUE) {

			$retVal = "";

			foreach ($this->regFlows as $key => $values) {

				$retVal .= "<li>";
				$retVal .= "<a href = 'register.php?regType=" . $values["title"] . "'>";
				$retVal .= "<h3>" . $values["title"] . "</h3>";

				if (array_key_exists("shortDesc", $values)) {
					$retVal .= "<p>" . $values["shortDesc"] . "</p>";
				}

				$retVal .= "</a></li>";

			}
			$this->regOptions = $retVal;
		}
	}

	private function getPrefix() {
		if ($this->isSecure()) { $prefix = "https://"; }
		else { $prefix = "http://"; }
		return $prefix;
	}

	private function setMenus() {

		$this->menu = '<li class = "menu"><a class="fa-info-circle" href="status.php">Sites</a></li>';

		$this->loginAndReg = "";

		if ($this->status["authentication"]) {

			if ($this->status["OIDC"]) {

				$this->loginAndReg .= "<li><a href = '#' id = 'login' onclick = 'showWidget()'>Log in (OIDC)</a></li>";

			}
			else {
				$this->loginAndReg .= "<li><a href = 'login.php'>Log in</a></li>";
			}
		}

		if ($this->status["registration"]) {
			$this->loginAndReg .= "<li><a href = '#menu'>Registration options</a></li>";
		}

	}

	private function getHTML($pageName) {

		$html = "";

		if ($pageName === "status") {

			$html .= "<table border = '1'>\n";
			$html .= "<tr><td>Site</td><td>Okta org</td><td>Auth</td><td>Reg</td><td>reg w/MFA</td><td>OIDC</td><td>Social</td></tr>";
			$html .= "<tr>";
			$html .= "<td>" . $this->siteName . "</td>";
			$html .= "<td>" . $this->oktaOrg . "</td>";
			$html .= "<td>" . $this->getIcon("authentication") . "</td>";
			$html .= "<td>" . $this->getIcon("registration") . "</td>";
			$html .= "<td>" . $this->getIcon("regWithMFA") . "</td>";
			$html .= "<td>" . $this->getIcon("OIDC") . "</td>";
			$html .= "<td>" . $this->getIcon("socialLogin") . "</td>";
			$html .= "</tr>";
			$html .= "</table>";
		}

		else if ($pageName === "allSettings") {

			foreach ($this as $key => $value) {

				// if ($key["isHTML"] || $key["isJS"]) {}
				if ($key == "oktaSignIn" || $key == "renderWidget" || $key == "menu" || $key == "loginAndReg" || $key == "widgetInBody") {}
				else {
					$html .= "<p><b>" . $key . "</b>: ";

					if (is_array($this->$key)) {
						$html .= json_encode($this->$key);
					}
					else { $html .= $this->$key; }					
				}
			}
		}

		return $html;

	}

	private function getIcon($param) {

		// if ($this->$param) { return "<a class = 'fa-check-square-o'></a>"; }
		// else { return "<a class = 'fa-times'></a>"; }

		if ($this->status[$param]) { return "yes"; }
		else { return "no"; }

	}

	function setSiteStatus() {

		foreach ($this->capabilities as $capability) {
			$this->status[$capability] = FALSE;
		}

		if ($this->oktaOrg) { $this->status["authentication"] = TRUE; }

		if ($this->apiKeyIsValid) {
			$this->status["registration"] = TRUE;

			if (isset($this->regFlows["withMFA"]["groupID"])) { 
				$this->status["regWithMFA"] = TRUE;
			}
			if (isset($this->regFlows["appProvisioning"]["groupID"])) {
				$this->status["appProvisioning"] = TRUE;
			}

		}

		if ($this->clientId) { $this->status["OIDC"] = TRUE; }
		if ($this->status["OIDC"] && $this->idps) { $this->status["socialLogin"] = TRUE; }

		if (!empty($this->appsWhitelist)) { $this->status["appsWhitelist"] = TRUE; }

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

		if ($this->metaData[$configFile]["required"]) {
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

	public function showPage($pageName) {
		$head = file_get_contents("../html/head.html");

		$head = $this->replaceElements($head);

		$this->bodyMain = $this->getHTML($pageName);

		$body = file_get_contents("../html/body.html");

		$body = $this->replaceElements($body);

		echo "<!DOCTYPE HTML>\n<html>\n" . $head . $body . "\n</html>";
	}

	private function replaceElements($thisString) {

		$delimiter = "%";

		$arr = explode($delimiter, $thisString);

		foreach($arr as $element) {
			if (substr($element, 0, 2) == "--") {
				$target = "%" . $element . "%";

				$nameArr = explode("--", $element);

				$name = $nameArr[1];

				$thisString = str_replace($target, $this->$name, $thisString);

			}
		}

		return $thisString;
	}


	private function insertElements($thisString, $array) {

		foreach ($array as $element) {
			$target = "%" . $element . "%";

			$thisString = str_replace($target, $this->$element, $thisString);
		}

		return $thisString;

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

		echo "<p>HELLO";


	}

	private function setRemotePaths() {
		$this->oktaBaseURL = "https://" . $this->oktaOrg . ".okta.com";
		$this->apiHome = $this->oktaBaseURL . "/api/v1";


// 	// Need to add some logic here to accommodate https
// 	$config["host"] = "http://" . $config["host"];

// 	$config["webHomeURL"] = $config["host"] . $config["webHome"];

// 	// Danger Will Robinson
// 	// This value needs to match a value in the Redirect URIs list
// 	// in your Okta tenant

// 	$config["redirectURL"] = $config["host"] . $config["webHome"];
// }

		
		// $this->redirectUri = 
	}

	private function isSecure() {
		return
		    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		    || $_SERVER['SERVER_PORT'] == 443;		
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

	private function getRedirectURI() {

		// http or https
		if ($this->isSecure()) { $protocol = "https"; }
		else { $protocol = "http"; }

		$redirectURI = $protocol . "://" . $_SERVER["SERVER_NAME"];

		// add the port to the hostname if appropriate
		if (array_key_exists("SERVER_PORT", $_SERVER)) {
			if ($_SERVER["SERVER_PORT"] == "80") {}
			else { $redirectURI .= ":" . $_SERVER["SERVER_PORT"]; }
		}

		$redirectURI .= $this->webHome . "views/index.php";

		return $redirectURI;

	}
}