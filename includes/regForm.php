<?php

class regForm {

	function __construct($regType, $fields) {

		global $config;

		$this->config = $config;

		if ($regType = "custom") {
			// echo "<p>this is a custom reg form.";
		}
	}
}