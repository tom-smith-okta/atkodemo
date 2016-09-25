<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class regForm {

	function __construct($regType) {

		global $config;

		$this->config = $config;

		$this->fields = [];

		if (array_key_exists($regType, $config["regForm"])) {

			$this->fields = $config["regForm"][$regType];
		}

		$jsonSchema = $this->config["userSchema"];

		$this->schema = json_decode($jsonSchema, TRUE); // convert json schema to assoc array
	}

	function getHTML($flowType = "none") {

		$formHTML = file_get_contents("html/regFormTemplate.html");

		$inputFieldsHTML = "";

		$fieldTemplate = file_get_contents("html/regFormFieldTemplate.html");

		foreach ($this->fields as $fieldName) {

			$type = $this->schema["definitions"]["base"]["properties"][$fieldName]["type"];

			if ($type == "string") { $type == "text"; }

			$placeholder = $this->schema["definitions"]["base"]["properties"][$fieldName]["title"];

			$formField = $fieldTemplate;

			$input = "<input name = '" . $fieldName . "'";

			$input .= " type = '" .  $type . "'";

			$input .= " placeholder = '" . $placeholder . "'";

			$input .= ">";

			$formField = str_replace("%input%", $input, $formField);

			$inputFieldsHTML .= $formField;
		}

		$formHTML = str_replace("%regType%", $flowType, $formHTML);

		$formHTML = str_replace("%fields%", $inputFieldsHTML, $formHTML);

		return $formHTML;
	}

	function displayCurrentFields() {
		$retVal = "<table border = '1'>";
		foreach ($this->fields as $field) {
			$retVal .= "<tr><td>" . $field . "</td></tr>";
		}
		$retVal .= "</table>";
		return $retVal;
	}

	function displayAvailableFields() {

		$retVal = "<form>";

		$properties = $this->schema["definitions"]["base"]["properties"];

		foreach ($properties as $fieldName => $values) {
			// $retVal .= "<p>the field name is: " . $fieldName["title"];
			$retVal .= "<label><input type = 'checkbox' id = '" . $fieldName . "' value = '" . $fieldName . "'>" . $values["title"] . "</label>";
		}

		$retVal .= "</form>";

		return $retVal;

	}

	function addField($fieldName) {
		$this->fields[] = $fieldName;
	}
}