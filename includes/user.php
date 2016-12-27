<?php

include "demoSite.php";
include "curlRequest.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class user {

	function __construct() {

		$regFlow = $_POST["regFlow"];

		$_SESSION["regFlow"] = $regFlow;

		$this->regFlow = $regFlow;

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
			$this->email = $_POST["email"];

			$_SESSION["userProfile"] = $userData["profile"];
		}
		curl_close($curl);
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

		$jsonResult = curl_exec($curl);

		$result = json_decode($jsonResult, TRUE);

		if (array_key_exists("errorCode", $result)) {
			echo $jsonResult;
			exit;
		}
		else {
			return $result["cookieToken"];
		}
		curl_close($curl);
	}

	public function hasRequiredEmailAddress() {

		$regFlow = $_SESSION["regFlow"];

		$substr = $_SESSION["siteObj"]->regFlows[$regFlow]["adminSubstring"];

		$len = strlen($substr);

		$offset = "-" . $len;

		if (substr(strtolower($this->email), $offset ) === strtolower($substr)) { return true; }
		else { return false; }
	}

	function redirect($cookieToken) {

		$url = $_SESSION["siteObj"]->oktaBaseURL . "/login/sessionCookieRedirect?token=" . $cookieToken . "&redirectUrl=" . $_SESSION["siteObj"]->redirectUri;

		$headerString = "Location: " . $url; 

		header($headerString);

		exit;
	}

	function sendActivationEmail() {

		$apiKey = $_SESSION["siteObj"]->apiKey;

		$apiHome = $_SESSION["siteObj"]->apiHome;

		$url = $apiHome . "/users/" . $this->userID . "/lifecycle/activate?sendEmail=true";

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
		));

		$jsonResult = curl_exec($curl);

		$result = json_decode($jsonResult, TRUE);

		if (array_key_exists("errorCode", $result)) {
			echo $jsonResult;
			exit;
		}
		curl_close($curl);
	}

	public function setAdminRights() {

		$path = "/users/" . $this->userID . "/roles";

		$role = $_SESSION["siteObj"]->regFlows[$this->regFlow]["adminRole"];

		$postFields = '{ "type": "' . $role . '" }';

		curlRequest($path, $postFields);

	}
}