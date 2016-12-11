<?php

class demoSite {

	function __construct($env, $homeDir) {

		$this->env = $env; // name of the local server environment

		$this->setLocalPaths($homeDir);

		$this->metaData = $this->getConfig("metadata");

		foreach($this->metaData as $key => $value) {

			$this->configFiles[] = $key;

		}

		$this->capabilities = ["authentication", "registration", "regWithMFA", "OIDC", "socialLogin", "appsWhitelist", "regWithMFA", "appProvisioning"];

		$this->importantSettings = array_merge($this->configFiles, [ "homeDir", "host", "oktaOrg", "apiKeyPath", "apiKey", "apiKeyIsValid", "clientId", "appsWhitelist", "idps"]);

		$this->pages = [];

	}

	private function getConfig($varName) {
		$json = file_get_contents($varName . ".json", FILE_USE_INCLUDE_PATH);

		$assocArray = json_decode($json, TRUE);
		return $assocArray;
	}

	private function setLocalPaths($homeDir) {

		$this->homeDir = $homeDir;

		$this->sitesHome = "../sites/";

		$this->defaultPath = "../sites/default/";

		$this->webHome = "/";

		if(!empty($this->homeDir)) {
			$this->webHome = $this->webHome . $homeDir . "/";
		}		
	}

	public function setSite($siteDir) {
		
		$this->siteDir = $siteDir;

		$this->sitePath = $this->sitesHome . $this->siteDir . "/";

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

		$this->setRegOptions();

	}

	private function setOktaWidget($pageName = "") {

		$this->redirectUri = $this->getRedirectURI();

		$path = "../javascript/widget/";

		if ($pageName === "login") {
			$path .= "basic/";
		}
		else if ($this->status["OIDC"]) {

			$path .= "OIDC/";

			if ($this->status["socialLogin"]) {
				$this->idpJS = "idpDisplay: 'PRIMARY',\n\t\t";
				$this->idpJS .= "idps: " . json_encode($this->idps);
			}
			else { $this->idpJS = ""; }
		}

		$this->oktaSignIn = file_get_contents($path . "loadWidget.js");

		$this->renderWidget = file_get_contents($path . "renderWidget.js");

		$this->oktaSignIn = $this->replaceElements($this->oktaSignIn);

		$this->renderWidget = $this->replaceElements($this->renderWidget);

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

				$retVal .= "</a></li>\n\t\t\t\t";

			}
			$this->regOptions = trim($retVal);
		}
	}

	private function getPrefix() {
		if ($this->isSecure()) { $prefix = "https://"; }
		else { $prefix = "http://"; }
		return $prefix;
	}

	private function setMenus() {

		$this->menu = "\t" . '<li class = "menu"><a class="fa-server" href="status.php">Site Status</a></li>' . "\n\t\t\t\t";

		$this->menu .= '<li class = "menu"><a class="fa-info-circle" href="allSettings.php">Settings</a></li>' . "\n\t\t\t\t";

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
			$this->menu .= '<li class = "menu"><a class="fa-bars" href = "#menu">Menu</a></li>';
		}

	}

	private function getHTML($pageName) {

		$html = "";

		if ($pageName === "status") {

			$html .= "<h1>Site status</h1>";
			$html .= "<table border = '1'>\n";
			$html .= "<tr><td>Site</td><td>env</td><td>Okta org</td><td align = 'center'>AuthN</td><td align = 'center'>Reg</td><td align = 'center'>reg w/MFA</td><td align = 'center'>OIDC</td><td align = 'center'>Social</td></tr>";
			$html .= "<tr>";
			$html .= "<td>" . $this->siteName . "</td>";
			$html .= "<td>" . $this->env . "</td>";
			$html .= "<td>" . $this->oktaOrg . "</td>";
			$html .= "<td align = 'center'>" . $this->getIcon("authentication") . "</td>";
			$html .= "<td align = 'center'>" . $this->getIcon("registration") . "</td>";
			$html .= "<td align = 'center'>" . $this->getIcon("regWithMFA") . "</td>";
			$html .= "<td align = 'center'>" . $this->getIcon("OIDC") . "</td>";
			$html .= "<td align = 'center'>" . $this->getIcon("socialLogin") . "</td>";
			$html .= "</tr>";
			$html .= "</table>";
		}

		else if ($pageName === "allSettings") {

			$html .= "<h1>Settings</h1>";

			foreach ($this as $key => $value) {

				// Need to clean this up
				// these values are HTML and they bork up the display page
				if ($key == "oktaSignIn" || $key == "renderWidget" || $key == "menu" || $key == "loginAndReg" || $key == "widgetInBody") {}
				else {
					$html .= "<p><b>" . $key . "</b>: ";

					if ($key === "apiKey") {
						$html .= $this->showAPIkey();
					}
					else {
						if (is_array($this->$key)) {
							$html .= json_encode($this->$key);
						}
						else {
							$html .= $this->$key;
						}
					}
				}
			}
		}

		else {

			$filePath = "../html/" . $pageName . ".html";

			$html = file_get_contents($filePath);

			$html = $this->replaceElements($html);

		}

		return $html;

	}

	private function getIcon($param) {

		if ($this->status[$param]) { return "<i class='fa fa-check' aria-hidden='true' style='color:LimeGreen'></i>"; }
		else { return "<i class='fa fa-close' aria-hidden='true' style='color:Red'></i>"; }
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

	private function showAPIkey() {

		if ($this->apiKey) {
			return substr($this->apiKey, 0, 5) . "...";
		}
		else { return "NONE"; }

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
		/* Find an apiKey and then check to see
		if it is valid */

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

	private function getFile($configFile, $getDefault = FALSE) {

		$fileName = $configFile . ".json";

		if (file_exists($this->sitePath . $fileName)) {
			$dir = $this->sitePath;
		}
		else {
			if ($getDefault) {
				$dir = $this->defaultPath;
			}
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
			$this->getFile($configFile, $this->metaData[$configFile]["required"]);
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

		$this->setOktaWidget($pageName);

		$this->setMenus();

		if ($pageName === "index") { $displayName = "home"; }
		else { $displayName = $pageName; }

		$this->title = $this->siteName . ": " . $displayName;

		$head = file_get_contents("../html/head.html");

		$head = $this->replaceElements($head);

		$this->bodyMain = $this->getHTML($pageName);

		$body = file_get_contents("../html/body.html");

		$body = $this->replaceElements($body);

		echo "<!DOCTYPE HTML>\n<html>\n" . $head . "\n\n" . $body . "\n</html>";
	}

	private function replaceElements($thisString) {
		/* Looks for all %--elements--% in the input string and replaces
		them with $this->element */

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

	private function setRemotePaths() {
		$this->oktaBaseURL = "https://" . $this->oktaOrg . ".okta.com";
		$this->apiHome = $this->oktaBaseURL . "/api/v1";
	}

	private function isSecure() {
		return
		    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		    || $_SERVER['SERVER_PORT'] == 443;		
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