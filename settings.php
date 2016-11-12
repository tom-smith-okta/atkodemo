<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

// let's not show our api key in the browser.
$config["apiKey"] = substr($config["apiKey"], 0, 5) . "...";

foreach ($config as $key => $value) {

	if (is_array($value)) {
		echo "<p>" . $key . ": " . json_encode($value) . "</p>";
	}
	else {

		echo "<xmp>" . $key . ": " . $value . "</xmp>";

	}

	echo "<br>";
}