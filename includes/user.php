<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class user {

	function __construct($regType, $user) {

		global $config;

		$this->config = $config;

		if ($regType == "none") {

			$this->password = $user["password"];
			$this->username = $user["login"];

			if (empty($_SESSION["apiKey"])) {
				echo "<p>Sorry, can't find an apiKey.";
				exit;
			}

			$apiKey = $_SESSION["apiKey"];

			$oktaOrg = $_SESSION["oktaOrg"];

			if (array_key_exists("password", $user)) {
				$userData["credentials"]["password"]["value"] = $user["password"];
				unset($user["password"]);
			}

			$userData["profile"] = $user;

			$url = "https://" . $oktaOrg . ".okta.com/api/v1/users?activate=true";

			$jsonData = json_encode($userData);

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json"),
				CURLOPT_POST => TRUE,
	    		CURLOPT_POSTFIELDS => $jsonData,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_URL => $url
			));

			$jsonResult = curl_exec($curl);

			// echo "<p>" . $jsonResult;

			echo "<p>Thank you for registering!";

			$url = "https://" . $oktaOrg . ".okta.com";

			echo "<p>Please visit <a href = '" . $url . "'>" . $url . "</a> to log in!</p>";

			exit;

		}

		// user properties
		$this->firstName = $user["firstName"];
		$this->lastName = $user["lastName"];
		$this->email = $user["email"];
		$this->login = $user["email"];
		$this->regType = $regType;
		$this->groupID = $config["regFlow"][$regType]["groupID"];

		// this is the object we will pass to the API call
		$userData["profile"]["firstName"] = $this->firstName;
		$userData["profile"]["lastName"] = $this->lastName;
		$userData["profile"]["email"] = $this->email;
		$userData["profile"]["login"] = $this->login;

		// echo "<p>the user data is: " . json_encode($userData);

		$url = $config["apiHome"] . "/users?activate=";

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