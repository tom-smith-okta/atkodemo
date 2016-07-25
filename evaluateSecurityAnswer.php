<?php

// To be changed before PROD:

// get rid of mailinator
// change the default Okta password schema

session_start();

include "includes/includes.php";

if (isset($_SESSION['oktaCookieSessionID']) && oktaSessionIsValid($_SESSION['oktaCookieSessionID'])) {
	//continue
}
else {
	echo "<p>Sorry, there does not seem to be a user logged in.";
	exit;
}

$securityAnswer = trim($_POST["securityAnswer"]);

$securityAnswer = filter_var($securityAnswer, FILTER_SANITIZE_STRING);

$data["credentials"]["recovery_question"]["question"] = "What is the best word to describe Oktane?";
$data["credentials"]["recovery_question"]["answer"] = $securityAnswer;

$data = json_encode($data);

// for the love of god why doesn't json_encode work in this context
$data = '{
  "credentials": {
    "recovery_question": {
      "question": "What is the best word to describe Oktane?",
      "answer": "' . $securityAnswer . '"
    }
  }
}';

echo $data;

$userID = $_SESSION["oktaSessionObj"]["userId"];

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

// {{url}}/api/v1/users/00u2jc1r88cDiiQWH1t6/credentials/forgot_password?sendEmail=true

$curl = curl_init();

$url = $config["apiHome"] . "/users/" . $userID . "/credentials/forgot_password?sendEmail=true";

curl_setopt_array($curl, array(
	CURLOPT_POST => TRUE,
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => TRUE
));

$errorMsg = "<p>something went wrong with trying to send a password reset email";

$result = sendCurlRequest($curl, $errorMsg);

echo "<p>Thank you. Please check your email to set your password and activate your account.";

exit;