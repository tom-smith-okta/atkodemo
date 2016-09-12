<?php

class user {

	function __construct($config, $regType, $user) {
		$this->config = $config;

		$this->email = $user["email"];

		$this->login = $this->email;

		$this->firstName = $user["firstName"];

		$this->lastName = $user["lastName"];

		if ($regType == "default" || $regType == "vanilla") {

			$this->password = $user["password"];

			if ($regType == "default") {
				echo "<p>the type of user is default.</p>";
			}
			else {
				echo "<p>the type of user is vanilla.</p>";
			}


		}

		exit;



		$this->groupID = $this->config["group"]["default"]["id"];

		$this->type = "regular";

		$this->userID = "";

		$this->setType(); // regular or Okta

		$this->setPassword();

		$this->setGroup();
	}

	function assignToOktaGroup() {

		$url = $this->config["apiHome"] . "/groups/" . $this->groupID . "/users/" . $this->userID;

		$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_CUSTOMREQUEST => "PUT",
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url
		));

		$errorMsg = "<p>Sorry, there was an error trying to assign that user to a group:</p>";

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

		return $result["cookieToken"];
	}

	function redirect($cookieToken) {

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

		$result = sendCurlRequest($curl, $errorMsg);

		$this->userID = $result["id"];
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

	function setEmail($email) { $this->email = $email; }

	function setGroup() {
		if ($this->type == "okta") { $this->groupID = $this->config["oktaGroupID"]; }
		else if ($this->type == "mfa") { $this->groupID = $this->config["group"]["mfa"]["id"]; }
	}

	function setPassword() {
		if ($this->type == "okta") { 
			$pwd = openssl_random_pseudo_bytes(8);

			$this->password = "Aa!" . bin2hex($pwd);
		}
	}

	function setType() {

		$domain = $this->config["group"]["mfa"]["domain"];

		$email = "@" . $domain;

		$offset = 0 - strlen($email);

		if (substr($this->email, $offset) == $email) {
			$this->type = "mfa";
		}
	}
}