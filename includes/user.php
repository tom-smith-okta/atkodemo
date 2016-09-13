<?php

class user {

	function __construct($regType, $user) {

		include "config.php";

		// user properties
		$this->firstName = $user["firstName"];
		$this->lastName = $user["lastName"];
		$this->email = $user["email"];
		$this->login = $user["email"];
		$this->regType = $regType;
		$this->groupID = $config["group"][$regType]["id"];

		// this is the object we will pass to the API call
		$userData["profile"]["firstName"] = $this->firstName;
		$userData["profile"]["lastName"] = $this->lastName;
		$userData["profile"]["email"] = $this->email;
		$userData["profile"]["login"] = $this->login;

		$url = $config["apiHome"] . "/users?activate=";

		if ($regType == "default" || $regType == "vanilla") {
			$this->password = $user["password"];

			$userData["credentials"]["password"]["value"] = $this->password;

			$url .= "true"; // activate=true
		}
		else { // $regType = withMFA || withEmail || okta
			$url .= "false"; // activate=false
		}

		$userData["groupIds"] = ["$this->groupID"];

		$data = json_encode($userData);

		// echo $data;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
	    	CURLOPT_POSTFIELDS => $data
		));

		$errorMsg = "<p>Sorry, something went wrong with trying to create this user.";

		$result = sendCurlRequest($curl, $errorMsg);

		$this->userID = $result["id"];

	}

	// function assignToOktaGroup() {

	// 	$url = $this->config["apiHome"] . "/groups/" . $this->groupID . "/users/" . $this->userID;

	// 	$curl = curl_init();

	// 	curl_setopt_array($curl, array(
	// 	    CURLOPT_CUSTOMREQUEST => "PUT",
	// 		CURLOPT_RETURNTRANSFER => 1,
	// 		CURLOPT_URL => $url
	// 	));

	// 	$errorMsg = "<p>Sorry, there was an error trying to assign that user to a group:</p>";

	// 	$result = sendCurlRequest($curl, $errorMsg);

	// }

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

	// function putOktaRecord() {

	// 	$userData = '{
	// 		"profile": {
	// 			"firstName": "' . $this->firstName . '",
	// 			"lastName":  "' . $this->lastName  . '",
	// 			"email":     "' . $this->email     . '",
	// 			"login":     "' . $this->login     . '"
	// 		},
	// 		"credentials": {
	// 			"password": {
	// 				"value": "' . $this->password  . '"
	// 			}
	// 		}
	// 	}';

	// 	$url = $this->config["apiHome"] . "/users?activate=true";

	// 	$curl = curl_init();

	// 	curl_setopt_array($curl, array(
	// 		CURLOPT_POST => TRUE,
	// 		CURLOPT_RETURNTRANSFER => TRUE,
	// 		CURLOPT_URL => $url,
	//     	CURLOPT_POSTFIELDS => $userData
	// 	));

	// 	$errorMsg = "<p>Sorry, something went wrong with trying to create this user.";

	// 	$result = sendCurlRequest($curl, $errorMsg);

	// 	$this->userID = $result["id"];
	// }

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