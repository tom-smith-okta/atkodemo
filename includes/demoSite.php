<?php

class demoSite {

	function __construct($dirName) {

		$this->dirName = $dirName;

		$this->sitePath = "../sites/" . $dirName . "/";

		if ($_SESSION["webHome"] === "/") { $this->webHome = "/"; }
		else { $this->webHome =  "/" . $_SESSION["webHome"]; }

		$this->main = $this->getConfigFile("main", TRUE);

		foreach ($this->main as $key => $value) {
			$this->$key = $value;
		}

		$this->theme = $this->getConfigFile("theme", TRUE);

		foreach ($this->theme as $key => $value) {
			$this->$key = $value;
		}

		$this->setRemotePaths();

		$this->checkAPIkey();

		$this->setSiteStatus();

		if ($this->status["registration"]) {
			$this->regFlows = $this->getConfigFile("regFlows", TRUE);
			$this->setRegOptions();
		}

		if ($this->status["appsBlacklist"]) {
			$this->appsBlacklist = json_encode($this->appsBlacklist);
		}
		else {
			$this->appsBlacklist = "none";
		}

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

	public function getStatus() {
		return $this->status;
	}

	public function getHTML($pageName) {

		$html = "";

		if ($pageName === "status") {

			$html .= "<h1>Site status</h1>";
			$html .= "<table border = '1'>\n";
			$html .= "<tr><td>Site</td><td>env</td><td>Okta org</td><td align = 'center'>AuthN</td><td align = 'center'>Reg</td><td align = 'center'>OIDC</td><td align = 'center'>Social</td><td align= 'center'>Apps BL</td></tr>";
			$html .= "<tr>";
			$html .= "<td>" . $this->dirName . "</td>";
			$html .= "<td>" . $this->env . "</td>";
			$html .= "<td>" . $this->oktaOrg . "</td>";
			$html .= "<td align = 'center'>" . $this->getIcon("authentication") . "</td>";
			$html .= "<td align = 'center'>" . $this->getIcon("registration") . "</td>";
			$html .= "<td align = 'center'>" . $this->getIcon("OIDC") . "</td>";
			$html .= "<td align = 'center'>" . $this->getIcon("socialLogin") . "</td>";
			$html .= "<td align = 'center'>" . $this->getIcon("appsBlacklist") . "</td>";

			$html .= "</tr>";
			$html .= "</table>";

			$dirs = scandir($this->sitesHome);

			foreach ($dirs as $dir) {

				if ($dir === "." || $dir === "..") {}
				else {
					$path = $this->sitesHome . $dir;

					if (is_dir($path)) {
						$html .= "<p>" . $dir . " is a directory ";


					}
				}
			}

			// $html .= "<p>List of sites: " . json_encode($sites);
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
		else if ($pageName === "register") {

			$regFlow = $_GET["regFlow"];

			$fileName = "regFields.json";

			if (file_exists($this->sitePath . $fileName)) {
				$json = file_get_contents($this->sitePath . $fileName);
			}
			else {
				$json = file_get_contents($_SESSION["defaultPath"] . $fileName);
			}

			$regFields = json_decode($json, TRUE);

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

			$thankYouMsg01 = $_SESSION["demo"]["site"]->regFlows[$regFlow]["thankYouMsg01"];
			$thankYouMsg02 = $_SESSION["demo"]["site"]->regFlows[$regFlow]["thankYouMsg02"];

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

		if ($this->status[$param]) { return "<i class='fa fa-check' aria-hidden='true' style='color:LimeGreen'></i>"; }
		else { return "<i class='fa fa-close' aria-hidden='true' style='color:Red'></i>"; }
	}

	function setSiteStatus() {

		foreach ($_SESSION["capabilities"] as $capability) {
			$this->status[$capability] = FALSE;
		}

		if ($this->oktaOrg) { $this->status["authentication"] = TRUE; }

		if ($this->apiKeyIsValid) {
			$this->status["apiKey"] = TRUE;
			$this->status["registration"] = TRUE;
		}

		if ($this->clientId) { $this->status["OIDC"] = TRUE; }
		if ($this->status["OIDC"] && $this->idps) { $this->status["socialLogin"] = TRUE; }

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

	private function getConfigFile($configFile, $getDefault = FALSE) {

		$dir = "";

		$fileName = $configFile . ".json";

		if (file_exists($this->sitePath . $fileName)) {
			$dir = $this->sitePath;
		}
		else {
			if ($getDefault) {
				$dir = $_SESSION["defaultPath"];
			}
		}

		$path = $dir . $fileName;

		$this->source[$configFile] = $path; // save the $path for error-checking purposes

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

		$redirectURI .= $this->webHome . "/views/index.php";

		return $redirectURI;
	}
}