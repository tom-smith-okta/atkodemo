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

	function authenticateAndRedirect() {

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

		$cookieToken = $result["cookieToken"];

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
	}

	function setPassword() {
		if ($this->type == "okta") { 
			$pwd = openssl_random_pseudo_bytes(8);

			$this->password = "Aa!" . bin2hex($pwd);
		}
	}

	function setType() {
		$validEmailDomains = array("@okta.com");

		foreach ($validEmailDomains as $domain) {

			$offset = 0 - strlen($domain);

			if (substr($this->email, $offset) == $domain) {
				$this->type = "okta";
				break;
			}
		}
	}
}