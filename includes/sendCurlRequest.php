<?php

function sendCurlRequest($curl, $errorMsg) {
	global $config;

	$apiKey = $config["apiKey"];

	curl_setopt_array($curl, array(
    	CURLOPT_HTTPHEADER => array("Authorization: SSWS $apiKey ", "Accept: application/json", "Content-Type: application/json")
	));

	$jsonResult = curl_exec($curl);

	if (curl_error($curl)) {
		echo "<p>There was a curl error: " . curl_error($curl);
		exit;
	}

	if ($jsonResult) {
		$result = json_decode($jsonResult, TRUE);

		if (array_key_exists("errorCauses", $result)) {
			// something went wrong
			echo "<p>" . $errorMsg . "</p>";
			
			echo "<p>" . $result["errorCauses"][0]["errorSummary"];

			exit;
		}
	}

	curl_close($curl);

	return $result;

}