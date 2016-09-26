<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class regForm {

	function __construct($regFormType) {

		global $config;

		$this->config = $config;

		$this->fields = [];

		if (array_key_exists($regFormType, $config["regFormType"])) {

			$this->fields = $config["regFormType"][$regFormType];
		}
		else if ($regFormType == "custom") {
			$this->fields = $_SESSION["regFields"];
		}

		if (file_exists("userSchema.txt")) { $config["userSchema"] = file_get_contents("userSchema.txt"); }
		else {
			$config["userSchema"] = getUserSchema();
			file_put_contents("userSchema.txt", $config["userSchema"]);
		}

		$jsonSchema = $config["userSchema"];

		$this->schema = json_decode($jsonSchema, TRUE); // convert json schema to assoc array
	}

	function getHTML($flowType = "none") {

		$formHTML = file_get_contents("html/regFormTemplate.html");

		$inputFieldsHTML = "";

		$fieldTemplate = file_get_contents("html/regFormFieldTemplate.html");

		foreach ($this->fields as $fieldName) {

			if ($fieldName == "password") {
				$type = "password";
				$placeholder = "password";
			}
			else {
				$type = $this->schema["definitions"]["base"]["properties"][$fieldName]["type"];

				if ($type == "string") { $type = "text"; }

				$placeholder = $this->schema["definitions"]["base"]["properties"][$fieldName]["title"];
			}

			$formField = $fieldTemplate;

			$input = "<input name = '" . $fieldName . "'";

			$input .= " type = '" .  $type . "'";

			$input .= " placeholder = '" . $placeholder . "'";

			$input .= ">";

			$formField = str_replace("%input%", $input, $formField);

			$inputFieldsHTML .= $formField;
		}

		$formHTML = str_replace("%flowType%", $flowType, $formHTML);

		$formHTML = str_replace("%fields%", $inputFieldsHTML, $formHTML);

		return $formHTML;
	}

	function displayAllFields() {

		$retVal = "<form action = 'includes/evaluateNewRegForm.php' method = 'post'>";

		$retVal .= "<table border = '0'>";

		$retVal .= "<tr><td>Current fields</td><td>Available fields</td></tr>";

		$retVal .= "<tr>";

		$retVal .= "<td>";

		$retVal .= $this->getCurrentFields();

		$retVal .= "</td>";

		$retVal .= "<td>";

		$retVal .= $this->getAvailableFields();

		$retVal .= "</td>";

		$retVal .= "</tr>";

		$retVal .= "</table>";

		$retVal .= "<input type = 'submit' value = 'submit' name = 'submit'>";

		$retVal .= "</form>";

		return $retVal;

	}

	function getCurrentFields() {
		$retVal = "<table border = '1'>";
		foreach ($_SESSION["regFields"] as $field) {

			if (in_array($field, $this->config["regFormType"]["pwd"])) {

				$retVal .= "<label>" . $field . "</label>\n";
			}
			else {
				$retVal .= "<label><input type = 'checkbox' name = '" . $field . "' value = 'remove'> " . $field . "</label>\n";
			}
		}
		$retVal .= "</table>";
		return $retVal;
	}

	function getAvailableFields() {

		$retVal = "";

		$properties = $this->schema["definitions"]["base"]["properties"];

		foreach ($properties as $fieldName => $values) {

			if (in_array($fieldName, $_SESSION["regFields"])) {}

			else if (in_array($fieldName, $this->config["regFormType"]["min"])) {}
			else {
				$retVal .= "<label><input type = 'checkbox' name = '" . $fieldName . "' value = 'add'> " . $values["title"] . "</label>\n";
			}
		}

		return $retVal;
	}
}