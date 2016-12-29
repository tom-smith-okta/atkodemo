<?php

include "../includes/demoSite.php";
include "../includes/curlRequest.php";

if (session_status() === PHP_SESSION_NONE) {
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

		if ($_SESSION["demo"]["site"]->regFlows[$regFlow]["groupIDs"]) {
			$userData["groupIds"] = $_SESSION["demo"]["site"]->regFlows[$regFlow]["groupIDs"];
		}

		$data = json_encode($userData);

		// echo "<p>the user object is: " . $data;

		// ************** ACTIVATE USER? *****************/

		if ($_SESSION["demo"]["site"]->regFlows[$regFlow]["activate"]) {
			$activate = "true";
		}
		else { $activate = "false"; }

		/**************** SEND CURL REQUEST *******************/

		$path = "/users?activate=" . $activate;

		$result = curlRequest($path, $data);

		$this->userID = $result["id"];
		$this->email = $_POST["email"];

		$_SESSION["userProfile"] = $userData["profile"];
	}

	function authenticate() {

		$path = "/sessions?additionalFields=cookieToken";

		$userData = '{
			"username": "' . $this->login . '",
			"password": "' . $this->password . '"
		}';

		$result = curlRequest($path, $userData);

		return $result["cookieToken"];
	}

	public function hasRequiredEmailAddress() {

		$regFlow = $_SESSION["regFlow"];

		$substr = $_SESSION["demo"]["site"]->regFlows[$regFlow]["adminSubstring"];

		$len = strlen($substr);

		$offset = "-" . $len;

		if (substr(strtolower($this->email), $offset ) === strtolower($substr)) { return true; }
		else { return false; }
	}

	function redirect($cookieToken) {

		$url = $_SESSION["demo"]["site"]->oktaBaseURL . "/login/sessionCookieRedirect?token=" . $cookieToken . "&redirectUrl=" . $_SESSION["demo"]["site"]->redirectUri;

		$headerString = "Location: " . $url; 

		header($headerString);

		exit;
	}

	function sendActivationEmail() {

		$path = "/users/" . $this->userID . "/lifecycle/activate?sendEmail=true";

		$result = curlRequest($path, "");
	}

	public function setAdminRights() {

		$path = "/users/" . $this->userID . "/roles";

		$role = $_SESSION["demo"]["site"]->regFlows[$this->regFlow]["adminRole"];

		$postFields = '{ "type": "' . $role . '" }';

		$result = curlRequest($path, $postFields);

	}
}