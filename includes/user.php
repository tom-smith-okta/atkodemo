<?php

include "demoSite.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class user {

	function __construct() {

		$regFlow = $_POST["regFlow"];

		unset($_POST["regFlow"]);

		// ************** BUILD USER DATA *****************/
		$password = "";

		if (array_key_exists("password", $_POST)) {
			$userData["credentials"]["password"]["value"] = $_POST["password"];
			$this->password = $userData["credentials"]["password"]["value"];
			unset($_POST["password"]);
		}

		$userData["profile"] = $_POST;

		if (array_key_exists("login", $_POST)) {
			$this->login = $_POST["login"];
		}
		else { 
			$userData["profile"]["login"] = $_POST["email"];

			$this->login = $userData["profile"]["login"];

		}

		/**************** ANY GROUPS? ********************/

		$groupIDs = "";

		if ($_SESSION["siteObj"]->regFlows[$regFlow]["groupIDs"]) {
			$userData["groupIds"] = $_SESSION["siteObj"]->regFlows[$regFlow]["groupIDs"];
		}

		$data = json_encode($userData);

		// echo "<p>the user object is: " . $data;

		// ************** ACTIVATE USER? *****************/

		if ($_SESSION["siteObj"]->regFlows[$regFlow]["activate"]) {
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

		// echo "<p> the json result is: " . $jsonResult;

		$result = json_decode($jsonResult, TRUE);

		if (array_key_exists("errorCode", $result)) {
			echo $jsonResult;
		}
		else {
			$this->userID = $result["id"];
		}
		curl_close();

		// exit;
	}

	function authenticate() {

		$apiKey = $_SESSION["siteObj"]->apiKey;

		$apiHome = $_SESSION["siteObj"]->apiHome;

		$curl = curl_init();

		$userData = '{
			"username": "' . $this->login . '",
			"password": "' . $this->password . '"
		}';

		$url = $apiHome . "/sessions?additionalFields=cookieToken";

		curl_setopt_array($curl, array(
			CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
			CURLOPT_POSTFIELDS => $userData
		));

		// $errorMsg = "<p>Sorry, there was an error trying to authenticate the new user:</p>";

		$jsonResult = curl_exec($curl);

		$result = json_decode($jsonResult, TRUE);

		if (array_key_exists("errorCode", $result)) {
			echo $jsonResult;
			exit;
		}
		else {
			return $result["cookieToken"];
		}
		curl_close();
	}

	function hasOktaEmailAddress() {
		if (substr($this->email, -9 ) == "@okta.com") { return true; }
		else { return false; }
	}

	function redirect($cookieToken) {

		$url = $_SESSION["siteObj"]->oktaBaseURL . "/login/sessionCookieRedirect?token=" . $cookieToken . "&redirectUrl=" . $_SESSION["siteObj"]->redirectUri;

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