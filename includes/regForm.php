<?php

class regForm {

	function __construct($regType) {

		global $config;

		$this->config = $config;

		echo "<p>the reg type is: " . $regType;

		if (array_key_exists($regType, $config["regForm"])) {

			$this->fields = $config["regForm"][$regType];
		}

		if ($regType = "custom") {
			// echo "<p>this is a custom reg form.";
		}
	}

	function addField($fieldName) {
		$this->fields[] = $fieldName;
	}
}