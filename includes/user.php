<?php

class user {

	function __construct($config, $email, $firstName, $lastName, $password) {
		$this->config = $config;

		$this->email = $email;

		$this->login = $email;

		$this->firstName = $firstName;

		$this->lastName = $lastName;

		$this->password = $password;

		$this->groupID = $this->config["groupID"];

		$this->type = "regular";

		$this->userID = "";

		$this->setType(); // regular or Okta

		$this->setPassword();

		$this->setGroup();
	}

	function assignToOktaGroup() {

		$apiKey = $this->config["apiKey"];

		$url = $this->config["apiHome"] . "/groups/" . $this->groupID . "/users/" . $this->userID;

		$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_CUSTOMREQUEST => "PUT",
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
	    	CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
		));

		$jsonResult = curl_exec($curl);

		if (curl_error($curl)) {
			echo "<p>There was a curl error: " . curl_error($curl);
			exit;
		}

		$result = json_decode($jsonResult, TRUE);

		if (array_key_exists("errorCauses", $result)) {
			// something went wrong
			echo "<p>Sorry, there was an error trying to assign that user to a group:</p>";
			
			echo "<p>" . $result["errorCauses"][0]["errorSummary"];

			exit;
		}
	}

	function sendCurlRequest($curl, $errorMsg) {
		$apiKey = $this->config["apiKey"];

		curl_setopt_array($curl, array(
	    	CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
		));

		$jsonResult = curl_exec($curl);

		if (curl_error($curl)) {
			echo "<p>There was a curl error: " . curl_error($curl);
			exit;
		}

		$result = json_decode($jsonResult, TRUE);

		if (array_key_exists("errorCauses", $result)) {
			// something went wrong
			echo "<p>" . $errorMsg . "</p>";
			
			echo "<p>" . $result["errorCauses"][0]["errorSummary"];

			exit;
		}

		return $result;

	}

	function authenticateAndRedirect() {

		$apiKey = $this->config["apiKey"];

		$curl = curl_init();

		$userData = '{
			"username": "' . $this->login . '",
			"password": "' . $this->password . '"
		}';

		$url = $this->config["apiHome"] . "/sessions?additionalFields=cookieToken";

		curl_setopt_array($curl, array(
			CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
			CURLOPT_POSTFIELDS => $userData
		));

		$jsonResult = curl_exec($curl);

		if (curl_error($curl)) {
			echo "<p>There was a curl error: " . curl_error($curl);
			exit;
		}

		// echo "<p>The JSON result is: " . $jsonResult;

		$result = json_decode($jsonResult, TRUE);

		if (array_key_exists("errorCauses", $result)) {
			// something went wrong
			echo "<p>Sorry, there was an error trying to create that user:</p>";
			
			echo "<p>" . $decodedResult["errorCauses"][0]["errorSummary"];

			exit;
		}

		$cookieToken = $result["cookieToken"];

		// }
		// else {
		// 	echo "<p>Sorry, there was an error trying to authenticate the new user:</p>";
				
		// 	echo "<p>" . $decodedResult["errorCauses"][0]["errorSummary"];
		// }

		$url = $this->config["oktaBaseURL"] . "/login/sessionCookieRedirect?token=" . $cookieToken . "&redirectUrl=" . $this->config["redirectURL"];

		$headerString = "Location: " . $url; 

		header($headerString);

		exit;

	}

	function putOktaRecord() {

		$userData = '{
			"profile": {
				"firstName": "' . $this->firstName . '",
				"lastName":  "' . $this->lastName  . '",
				"email":     "' . $this->email     . '",
				"login":     "' . $this->login     . '"
			},
			"credentials": {
				"password": {
					"value": "' . $this->password  . '"
				}
			}
		}';

		$url = $this->config["apiHome"] . "/users?activate=true";

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
	    	CURLOPT_POSTFIELDS => $userData
		));

		$errorMsg = "<p>Sorry, something went wrong with trying to create this user.";

		$result = $this->sendCurlRequest($curl, $errorMsg);

		$this->userID = $result["id"];

		// echo "<p>the userID is: " . $this->userID;

		curl_close($curl);

	}

	function setEmail($email) { $this->email = $email; }

	function setGroup() {
		if ($this->type == "okta") { $this->groupID = $this->config["oktaGroupID"]; }
	}

	function setPassword() {
		if ($this->type == "okta") { $this->password = "Atko1234!"; }
	}

	function setType() {
		$validEmailDomains = array("@okta.com", "@mailinator.com");

		foreach ($validEmailDomains as $domain) {

			$offset = 0 - strlen($domain);

			if (substr($this->email, $offset) == $domain) {
				$this->type = "okta";
				break;
			}
		}
	}
}

// 		$type = $this->config[$elementName]["type"]; // either "javascript" or "css"

// 		$location = $this->config[$elementName]["location"]; // remote || local || inline

// 		if ($location == "local") {
// 			$ext = $this->elements[$type]["ext"]; // either ".js" or ".css"
// 			$filePath = $this->config["webHome"] . "/" . $type . "/" . $elementName . $ext;
// 			$content = str_replace("%PATH%", $filePath, $this->elements[$type]["tag"]);
// 		}
// 		else if ($location == "inline") {
// 			$ext = $this->elements[$type]["ext"]; // either ".js" or ".css"
// 			$filePath = $this->config["fsHome"] . "/" . $type . "/" . $elementName . $ext;
// 			$content = $this->replaceVars($filePath, $elementName);
// 		}
// 		else { // $location = "remote"
// 			$content = str_replace("%PATH%", $this->config[$elementName]["url"], $this->elements[$type]["tag"]);
// 		}

// 		$this->addToBlock($content, $type);

// 	}

// 	function addToBlock($content, $type) {

// 		if (!empty($this->elements[$type]["block"])) { 
// 			$this->elements[$type]["block"] .= "\n\t\t";
// 		}

// 		$this->elements[$type]["block"] .= "\n" . $content . "\n";
// 	}

// 	function replaceVars($filePath, $elementName) {

// 		$content = file_get_contents($filePath);

// 		foreach ($this->config[$elementName]["vars"] as $var) {

// 			$bullseye = "%" . $var . "%";
// 			$arrow = $this->config[$var];

// 			$content = str_replace($bullseye, $arrow, $content);

// 		}

// 		return $content;
// 	}

// 	function display() {

// 		echo $this->getHTML();

// 	}

// 	function findFiles() {
// 		foreach ($this->elements as $element => $arr) {
// 			$files = $this->getElements($element, $arr["ext"]);

// 			foreach ($files as $file) {
// 				$path = "/" . HOME . "/" . $element . "/" . $file;

// 				$this->addElement($element, $path);

// 			}
// 		}
// 	}

// 	// expects HTML w/o <body></body> tags
// 	function addToBody($element) {
// 		$this->body = $this->body . "\n\t\t" . $element;
// 	}

// 	function getBody() {
// 		if (empty($this->elements["body"]["class"])) { $bodyTag = "<body>"; }
// 		else { $bodyTag = "<body class = '" . $this->elements["body"]["class"] . "'>"; }

// 		return "\n\t" . $bodyTag . $this->body . "\n\t</body>"; 
// 	}

// 	function getElements($element, $ext) {

// 		$dir = $_SERVER['DOCUMENT_ROOT'] . "/" . HOME . "/" . $element . "/autoInclude/";

// 		$files = scandir($dir);

// 		foreach ($files as $file) {

// 			$offset = 0 - strlen($ext);

// 			if (substr($file, $offset) == $ext) {
// 				$validFiles[] = $file;
// 			}
// 		}

// 		return $validFiles;
// 	}

// 	function getHead() {

// 		$this->head = "\n\t\t<meta charset='utf-8' />";
// 		$this->head .= "\n\t\t<meta name='viewport' content='width=device-width, initial-scale=1' />";

// 		$headElements = array($this->title, $this->elements["css"]["block"], $this->elements["javascript"]["block"]);

// 		foreach ($headElements as $element) {
// 			if (!empty($element)) { $this->head .= "\n\t\t" . $element; }
// 		}

// 		return "\n\t<head>" . $this->head . "\n\t</head>";
// 	}

// 	function getHTML() {

// 		return "<!DOCTYPE HTML>\n<html>" . $this->getHead() . "\n" . $this->getBody() . "\n</html>";
// 	}

// 	function setBodyParam($paramType, $param) {
// 		$this->elements["body"][$paramType] = $param;

// 	} 

// 	function setTitle($title) {

// 		$this->title = "<title>" . $title . "</title>";

// 	}
// }