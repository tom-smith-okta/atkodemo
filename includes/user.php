<?php

include "demoSite.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class user {

	function __construct($regType, $user) {

		unset($_POST["regFlow"]);


		// ************** BUILD USER DATA *****************/
		$password = "";

		if (array_key_exists("password", $_POST)) {
			$userData["credentials"]["password"]["value"] = $_POST["password"];
			unset($_POST["password"]);
		}

		$userData["profile"] = $_POST;

		if (array_key_exists("login", $_POST)) {}
		else { 
			$userData["profile"]["login"] = $_POST["email"];
		}


		/**************** ANY GROUPS? ********************/

		$groupIDs = "";

		if ($_SESSION["siteObj"]->regFlows[$regType]["groupIDs"]) {
			$userData["groupIds"] = $_SESSION["siteObj"]->regFlows[$regType]["groupIDs"];
		}

		$data = json_encode($userData);

		echo "<p>the user object is: " . $data;

		// ************** ACTIVATE USER? *****************/

		if ($_SESSION["siteObj"]->regFlows[$regType]["activate"]) {
			$activate = "true";
		}
		else { $activate = "false"; }

		/**************** SET URL ************************/

		$url = $_SESSION["siteObj"]->apiHome . "/users?activate=";

		$url .= $activate;

		/**************** GET API KEY *******************/

		$apiKey = $_SESSION["siteObj"]->apiKey;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
	    	CURLOPT_POSTFIELDS => $data,
	    	CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json")
		));

		$jsonResult = curl_exec($curl);

		echo "<p> the json result is: " . $jsonResult;

		$result = json_decode($jsonResult, TRUE);

		if (array_key_exists("errorCode", $result)) {
			echo $jsonResult;
		}
		else {
			$this->userID = $result["id"];
		}

		exit;
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