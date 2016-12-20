<?php

include "demoSite.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class user {

	function __construct($regType, $user) {

		unset($_POST["regType"]);

		$password = "";

		if (array_key_exists("password", $_POST)) {
			$userData["credentials"]["password"]["value"] = $_POST["password"];
			unset($_POST["password"]);
		}

		foreach ($_POST as $key => $value) {
			$userData["profile"][$key] = $value;
		}

		echo "<p>the user object is: " . json_encode($userData);

		$groupIDs = "";

		if ($_SESSION["siteObj"]->regFlows[$regType]["groupIDs"]) {
			$groupIDs = $_SESSION["siteObj"]->regFlows[$regType]["groupIDs"];
		}

		$url = $_SESSION["siteObj"]->apiHome . "/users?activate=";

		echo "<p>the apiHome is: " . $url;


		if ($regType == "sfChatter" || $regType == "basic") {
			$this->password = $user["password"];

			$userData["credentials"]["password"]["value"] = $this->password;

			$url .= "true"; // activate=true
		}
		else { // $regType = withMFA || withEmail || okta
			$url .= "false"; // activate=false
		}

		$data = json_encode($userData);

		// echo $data;

		$apiKey = $config["apiKey"];

		$curl = curl_init();

		curl_setopt_array($curl, array(
    		CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json")
		));

		curl_setopt_array($curl, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
	    	CURLOPT_POSTFIELDS => $data
		));

		$jsonResult = curl_exec($curl);

		// echo "<p> the json result is: " . $jsonResult;

		$result = json_decode($jsonResult, TRUE);

		$this->userID = $result["id"];

		/*********** ADD TO GROUP ******************/
		$curl = curl_init();

		$url = $config["apiHome"] . "/groups/" . $this->groupID . "/users/" . $this->userID;

		curl_setopt_array($curl, array(
			CURLOPT_CUSTOMREQUEST => "PUT",
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
		));

		$result = sendCurlRequest($curl, $errorMsg);

	}

	function authenticate() {

		$curl = curl_init();

		$userData = '{
			"username": "' . $this->login . '",
			"password": "' . $this->password . '"
		}';

		$url = $this->config["apiHome"] . "/sessions?additionalFields=cookieToken";

		curl_setopt_array($curl, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
			CURLOPT_POSTFIELDS => $userData
		));

		$errorMsg = "<p>Sorry, there was an error trying to authenticate the new user:</p>";

		$result = sendCurlRequest($curl, $errorMsg);

		// echo "<p>the cookie token is: " . $result["cookieToken"];

		return $result["cookieToken"];
	}

	function hasOktaEmailAddress() {
		if (substr($this->email, -9 ) == "@okta.com") { return true; }
		else { return false; }
	}

	function redirect($cookieToken) {

		$url = $this->config["oktaBaseURL"] . "/login/sessionCookieRedirect?token=" . $cookieToken . "&redirectUrl=" . $this->config["redirectURL"];

		$headerString = "Location: " . $url; 

		header($headerString);

		exit;
	}

	function sendActivationEmail() {

		$url = $this->config["apiHome"] . "/users/" . $this->userID . "/lifecycle/activate?sendEmail=true";

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
		));

		$errorMsg = "<p>Sorry, something went wrong with trying to set admin rights";

		$result = sendCurlRequest($curl, $errorMsg);

	}

	function setAdminRights() {
		$url = $this->config["apiHome"] . "/users/" . $this->userID . "/roles";

		$roleData = '{ "type": "READ_ONLY_ADMIN" }';

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
			CURLOPT_POSTFIELDS => $roleData
		));

		$errorMsg = "<p>Sorry, something went wrong with trying to set admin rights";

		$result = sendCurlRequest($curl, $errorMsg);

	}
}