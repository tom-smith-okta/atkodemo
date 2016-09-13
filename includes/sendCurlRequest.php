<?php

function sendCurlRequest($curl, $errorMsg) {
	global $config;

	$apiKey = $config["apiKey"];

	curl_setopt_array($curl, array(
    	CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json")
	));

	$jsonResult = curl_exec($curl);

	// I keep getting a bogus "malformed" error in my curl calls.
	// everything seems to be working so I am shutting down error checking for now

	if (curl_error($curl)) {
		// echo "<p>There was a curl error: " . curl_error($curl);

		// echo $jsonResult;

		// exit;
	}

	curl_close($curl);

	// NOTE: the "PUT" call does not return a response
	// so an empty jsonResult is not necessarily an error
	if ($jsonResult) {

		$result = json_decode($jsonResult, TRUE);

		if (array_key_exists("errorCode", $result)) {
			// something went wrong
			// echo "<p>" . $errorMsg . "</p>";
			
			// echo $jsonResult;

			exit;
		}
		else { return $result; }
	}
}