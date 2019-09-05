<?php

class Site {

	function __construct($dirName) {

		$this->dirName = $dirName;

		$this->sitePath = $this->getSitePath();

		$this->apiKey = "";

		$this->apiKey = file_get_contents("../apiKey.txt");

// echo "the api key is: " . $this->apiKey;

// exit;

		$this->capabilities = ["authentication", "apiKey", "registration", "OIDC", "socialLogin", "appsBlacklist"];

		$this->configFiles = ["main" => TRUE, "theme" => TRUE, "regFlows" => FALSE, "regFields" => FALSE];

		foreach ($this->configFiles as $file => $isRequired) {
			$this->status[$file] = FALSE;
			if ($isRequired) {
				$this->$file = $this->getConfigFile($file, TRUE);
				foreach ($this->$file as $key => $value) {
					$this->$key = $value;
				}
			}
		}

		$this->setRemotePaths();

		$this->checkAPIkey();

		$this->setSiteStatus();

		$this->regFlows = "";
		$this->regFields = "";

		if ($this->status["registration"]) {
			$this->regFlows = $this->getConfigFile("regFlows", TRUE);
			$this->regFields = $this->getConfigFile("regFields", TRUE);
			$this->setRegOptions();
		}

		if ($this->status["appsBlacklist"]) {
			$this->appsBlacklist = json_encode($this->appsBlacklist);
		}
		else {
			$this->appsBlacklist = "[]";
		}
	}

	private function getPrefix() {
		if ($this->isSecure()) { $prefix = "https://"; }
		else { $prefix = "http://"; }
		return $prefix;
	}

	private function getSitePath() {
		$sitePath = "../mysites/";
		$officialSites = ["atkodemoOfficial", "atkodemoShared", "default"];

		foreach ($officialSites as $site) {
			if ($this->dirName === $site) {
				$sitePath = "../sites/";
				break;
			}
		}
		return $sitePath . $this->dirName . "/";
	}

	private function setMenus() {

		$this->menu = "\t" . '<li class = "menu"><a class="fa-refresh" href="resetSession.php?dirName=';
		$this->menu .= $this->dirName . '">refresh site</a></li>' . "\n\t\t\t\t";

		$this->menu .= "\t" . '<li class = "menu"><a class="fa-file-text" href="docs.php">docs</a></li>' . "\n\t\t\t\t";

		$this->menu .= "\t" . '<li class = "menu"><a class="fa-server" href="status.php">Site Status</a></li>' . "\n\t\t\t\t";

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

	private function setOktaWidget() {

		$arr = explode("?", basename($_SERVER['REQUEST_URI']));

		$pageName = $arr[0];

		$this->redirectUri = $this->getRedirectURI();

		$path = "../javascript/widget/";

		if ($this->status["OIDC"] && $pageName != "login.php") {
			$path .= "OIDC/";

			if ($this->status["socialLogin"]) {
				$this->idpJS = "idpDisplay: 'PRIMARY',\n\t\t";

				$this->idpJS .= "idps: " . json_encode($this->idps);
			}
			else { $this->idpJS = ""; }
		}
		else {
			$path .= "basic/";
		}

		if ($pageName == "register.php") {
			$this->oktaSignIn = "";
			$this->renderWidget = "";
		}

		else {

			$this->oktaSignIn = file_get_contents($path . "loadWidget.js");

			$this->renderWidget = file_get_contents($path . "renderWidget.js");

			$this->oktaSignIn = $this->replaceElements($this->oktaSignIn);

			$this->renderWidget = $this->replaceElements($this->renderWidget);
		}
	}

	public function getHTML($pageName) {

		$html = "";

		if ($pageName === "allSettings") {

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
		else if ($pageName === "register") {

			$regFlow = $_GET["regFlow"];

			$regFields = $this->regFields;

			$template = file_get_contents("../html/regFormFieldTemplate.html");

			$fields = "";

			foreach ($this->regFlows[$regFlow]["fields"] as $fieldName) {

				$type = $regFields[$fieldName]["type"];

				if ($type === "hidden") {

					$value = $this->regFlows[$regFlow][$fieldName];

					$fields .= "\t\t\t\t\t";

					$fields .= "<input name = '" . $fieldName . "' type = 'hidden' value = '" . $value . "'>";
				}

				else {

					$fields .= $this->getFormFieldHTML($template, $fieldName, $regFields[$fieldName]);
				}

				$fields .= "\n";
			}

			$this->fields = $fields;

			$this->regDesc = $this->regFlows[$regFlow]["desc"];

			$this->regTitle = $this->regFlows[$regFlow]["title"];

			$this->regFlow = $regFlow;

			$filePath = "../html/" . $pageName . ".html";

			$html = file_get_contents($filePath);

			$html = $this->replaceElements($html);

		}
		else if ($pageName === "thankYou") {

			$filePath = "../html/" . $pageName . ".html";

			$html = file_get_contents($filePath);

			$regFlow = $_SESSION["regFlow"];

			$thankYouMsg01 = $_SESSION["site"]->regFlows[$regFlow]["thankYouMsg01"];
			$thankYouMsg02 = $_SESSION["site"]->regFlows[$regFlow]["thankYouMsg02"];

			$email = $_SESSION["userProfile"]["email"];

			$html = str_replace("%--thankYouMsg01--%", $thankYouMsg01, $html);
			$html = str_replace("%--thankYouMsg02--%", $thankYouMsg02, $html);
			$html = str_replace("%--email--%", $email, $html);

			$html = $this->replaceElements($html);

		}
		else {

			$filePath = "../html/" . $pageName . ".html";

			$html = file_get_contents($filePath);

			$html = $this->replaceElements($html);

		}
		return $html;
	}

	private function getFormFieldHTML($template, $name, $properties) {

		$input = "<input name = '" . $name . "'";

		foreach ($properties as $key => $value) {

			$input .= " " . $key . " = '" . $value . "'";
		}

		$input .= ">";

		return str_replace("%--input--%", $input, $template);
	}

	public function getIcon($param) {

		$redX = "<i class='fa fa-close' aria-hidden='true' style='color:Red'></i>";
		$greenCheck = "<i class='fa fa-check' aria-hidden='true' style='color:LimeGreen'></i>";

		if ($param === "apiKey") { 
			if ($this->status["apiKey"]["exists"]) {
				if ($this->status["apiKey"]["isValid"]) {
					return "<p style='color:LimeGreen'>" . $this->showAPIkey() . "</p>";
				}
				else {
					return "<p style='color:Red'>" . $this->showAPIkey() . "</p>";
				}
			}
			else {
				return $redX;
			}
		}
		else {
			if ($this->status[$param]) { return $greenCheck; }
			else { return $redX; }
		}
	}

	private function setRegOptions() {

		$this->regOptions = "";

		if ($this->status["registration"] === TRUE) {

			$retVal = "";

			foreach ($this->regFlows as $key => $values) {

				$retVal .= "<li>";
				$retVal .= "<a href = 'register.php?regFlow=" . $key . "'>";

				$retVal .= "<h3>" . $values["title"] . "</h3>";

				if (array_key_exists("shortDesc", $values)) {
					$retVal .= "<p>" . $values["shortDesc"] . "</p>";
				}

				$retVal .= "</a></li>\n\t\t\t\t";

			}
			$this->regOptions = trim($retVal);
		}
	}

	function setSiteStatus() {

		foreach ($this->capabilities as $capability) {
			$this->status[$capability] = FALSE;
		}

		if ($this->oktaOrg) { $this->status["authentication"] = TRUE; }

		if ($this->apiKey) {
			$this->status["apiKey"]["exists"] = TRUE;

			if ($this->apiKeyIsValid) {
				$this->status["apiKey"]["isValid"] = TRUE;
				$this->status["registration"] = TRUE;
			}
			else { $this->status["apiKey"]["isValid"] = FALSE; }

		}
		else {
			$this->status["apiKey"]["exists"] = FALSE;
		}

		if (property_exists($this, "clientId")) {
			if ($this->clientId) { $this->status["OIDC"] = TRUE; }
		}

		if ($this->status["OIDC"])
			if (property_exists($this, "idps")) {
				if ($this->idps) {
					$this->status["socialLogin"] = TRUE;
				}
			}

		if (!empty($this->appsBlacklist)) { $this->status["appsBlacklist"] = TRUE; }

	}

	private function showAPIkey() {
		if ($this->apiKey) {
			return substr($this->apiKey, 0, 5) . "...";
		}
		else { return "NONE"; }
	}

	public function showSettings() {
		echo json_encode($this);
	}

	private function apiKeyIsValid() {

		$curl = curl_init();

		$url = $this->apiHome . "/meta/schemas/user/default";

		$apiKey = $this->apiKey;

		curl_setopt_array($curl, array(
			CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json"),
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url
		));

		$jsonResult = curl_exec($curl);

		if (curl_error($curl)) { $this->warnings[] = curl_error($curl); }

		curl_close($curl);

		$assocArray = json_decode($jsonResult, TRUE);

		if (array_key_exists("id", $assocArray)) { return TRUE; }
		else {
			$this->warnings[] = $jsonResult;
			$this->warnings[] = "User registration is not possible without an API key.";
			return FALSE;
		}
	}

	private function checkAPIkey() {
		/* Find an apiKey and then check to see
		if it is valid */

		if (empty($this->apiKey)) {
			if (property_exists($this, "apiKeyPath")) {
				if (!(empty($this->apiKeyPath))) {
					$this->apiKey = $this->getAPIkey();
				}
			}
			else {
				$apiKeysFile = "../mysites/apiKeys.json";
				if (file_exists($apiKeysFile)) {

					$json = file_get_contents($apiKeysFile);
					$arr = json_decode($json, TRUE);

					if (array_key_exists($this->oktaOrg, $arr)) {
						$this->apiKey = $arr[$this->oktaOrg];
					}
				}
			}
		}

		if ($this->apiKey) {
			$this->apiKeyIsValid = $this->apiKeyIsValid();

			// echo "the api key is valid: " . $this->apiKeyIsValid;
			// exit;
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

	private function getConfigFile($configFile, $getDefault = FALSE) {

		$dir = "";

		$fileName = $configFile . ".json";

		if (file_exists($this->sitePath . $fileName)) {
			$dir = $this->sitePath;
		}
		else {
			if ($getDefault) {
				$dir = $_SESSION["paths"]["default"];
			}
		}

		$path = $dir . $fileName;

		$this->source[$configFile]["path"] = $path; // save the $path for error-checking purposes

		$this->source[$configFile]["dir"] = $dir; // save the $dir for error-checking purposes

		if (file_exists($path)) {
			$this->status[$configFile] = TRUE;

			return json_decode(file_get_contents($path), TRUE);
		}
		else {
			$this->warnings[] = "could not find file " . $path;
			return "";
		}
	}

	public function showPage($pageName, $bodyMain = "") {

		$this->setOktaWidget($pageName);

		$this->setMenus();

		if ($pageName === "index") { $displayName = "home"; }
		else { $displayName = $pageName; }

		$this->title = $this->siteName . ": " . $displayName;

		$head = file_get_contents("../html/head.html");

		$head = $this->replaceElements($head);

		if (empty($bodyMain)) {
			$this->bodyMain = $this->getHTML($pageName);
		}
		else {
			$this->bodyMain = $bodyMain;
		}

		$body = file_get_contents("../html/body.html");

		$body = $this->replaceElements($body);

		echo "<!DOCTYPE HTML>\n<html>\n" . $head . "\n\n" . $body . "\n</html>";
	}

	private function replaceElements($thisString) {
		/* Looks for all %--elements--% in the input string and replaces
		them with $this->element */

		$delimiter = "%";

		$arr = explode($delimiter, $thisString);

		foreach ($arr as $element) {
			if (substr($element, 0, 2) == "--") {
				$target = "%" . $element . "%";

				$nameArr = explode("--", $element);

				$name = $nameArr[1];

				if (property_exists($this, $name)) {
					$thisString = str_replace($target, $this->$name, $thisString);
				}
				else {
					$thisString = str_replace($target, "", $thisString);
				}

			}
		}
		return $thisString;
	}

	private function setRemotePaths() {
		if (property_exists($this, "oktaHost")) {
			if (empty($this->oktaHost)) { $this->oktaHost = "okta"; }
		}
		else { $this->oktaHost = "okta"; }

		$this->oktaDomain = $this->oktaOrg . "." . $this->oktaHost . ".com";

		$this->oktaBaseURL = "https://" . $this->oktaDomain;

		$this->apiHome = $this->oktaBaseURL . "/api/v1";
	}

	private function isSecure() {
		return
			(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
			|| $_SERVER['SERVER_PORT'] == 443;
	}

	private function getRedirectURI() {

		$redirectURI = file_get_contents("../redirectURI.txt");

		// // http or https
		// if ($this->isSecure()) { $protocol = "https"; }
		// else { $protocol = "http"; }

		// $redirectURI = $protocol . "://" . $_SERVER["SERVER_NAME"];

		// // add the port to the hostname if appropriate
		// if (array_key_exists("SERVER_PORT", $_SERVER)) {
		// 	if ($_SERVER["SERVER_PORT"] == "80") {}
		// 	else { $redirectURI .= ":" . $_SERVER["SERVER_PORT"]; }
		// }

		// // $redirectURI .= "";

		return $redirectURI;
	}
}