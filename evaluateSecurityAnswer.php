<?php

// To be changed before PROD:

// get rid of mailinator
// change the default Okta password schema

session_start();

include "includes/includes.php";

// this is just to make sure that there is a valid server-side session in place
// (not an okta session)
if (isset($_SESSION["nonce"])) {
	// continue
}
else {
	echo "<p>Sorry, but there does not appear to be a valid user session in place.";
	exit;
}

$securityAnswer = trim($_POST["securityAnswer"]);

$securityAnswer = filter_var($securityAnswer, FILTER_SANITIZE_STRING);

// for the love of god why can't I get json_encode to handle this properly
$data = '{
  "credentials": {
    "recovery_question": {
      "question": "What is the best word to describe Oktane?",
      "answer": "' . $securityAnswer . '"
    }
  }
}';

$userID = $_SESSION["userID"];

$url = $config["apiHome"] . "/users/" . $userID;

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_CUSTOMREQUEST => "PUT",
	CURLOPT_RETURNTRANSFER => TRUE,
	CURLOPT_URL => $url,
	CURLOPT_POSTFIELDS => $data
));

$errorMsg = "<p>Sorry, something went wrong with trying to set the security question and answer.";

$result = sendCurlRequest($curl, $errorMsg);

/************* Now send a reset password email *****************/

$curl = curl_init();

$url = $config["apiHome"] . "/users/" . $userID . "/credentials/forgot_password?sendEmail=true";

curl_setopt_array($curl, array(
	CURLOPT_POST => TRUE,
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => TRUE
));

$errorMsg = "<p>something went wrong with trying to send a password reset email";

$result = sendCurlRequest($curl, $errorMsg);

echo "<p>Thank you for registering as an administrator.</p>";
echo "<p>Please check your email to set your password and activate your account.</p>";
echo "<p>You can click <a href = '" . $config["webHomeURL"] . "'>here</a> to go to the home page.</p>";

exit;