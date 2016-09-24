<?php

class userSchema {

	function __construct() {

		global $config;

		$this->config = $config;

		$url = $config["apiHome"] . "/meta/schemas/user/default";

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url
		));

		$this->schema = sendCurlRequest($curl, "error message");

		$this->definitions = $this->schema["definitions"];

		$this->base = $this->definitions["base"];

		$this->custom = $this->definitions["custom"];
	}

	function display() {
		// Main user schema
		echo "<p><b>Root schema</b></p>";
		foreach($this->schema as $property => $value) {
			echo "<p>" . $property . ": " . $value;
		}

		echo "<hr>";

		// Definitions
		echo "<p><b>Definitions</b></p>";
		foreach($this->definitions as $property => $value) {
			echo "<p>" . $property . ": " . $value;
		}

		echo "<hr>";

		// Base
		echo "<p><b>Base</b></p>";
		foreach ($this->base as $property => $value) {
			echo "<p>" . $property . ": " . $value;
		}

		echo "<hr>";

		// Base properties
		echo "<p><b>Base properties</b></p>";
		foreach ($this->base["properties"] as $property => $value) {
			echo "<p>" . $property . ": " . json_encode($value);
		}

		// Custom
		echo "<p><b>Custom</b></p>";
		foreach ($this->custom as $property => $value) {
			echo "<p>" . $property . ": " . $value;
		}

		// Custom properties
		echo "<p><b>Custom properties</b></p>";
		foreach ($this->custom["properties"] as $property => $value) {
			echo "<p>" . $property . ": " . json_encode($value);
		}

	}
}