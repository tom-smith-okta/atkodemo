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
	}

	function getHTML($flowType = "none") {

		$jsonSchema = $this->config["userSchema"];

		$schema = json_decode($jsonSchema, TRUE); // convert json schema to assoc array

		$formHTML = file_get_contents("html/regFormTemplate.html");

		$inputFieldsHTML = "";

		$fieldTemplate = file_get_contents("html/regFormFieldTemplate.html");

		foreach ($this->fields as $fieldName) {

			$type = $schema["definitions"]["base"]["properties"][$fieldName]["type"];

			if ($type == "string") { $type == "text"; }

			$placeholder = $schema["definitions"]["base"]["properties"][$fieldName]["title"];

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

// <div data-se="o-form-fieldset" class="o-form-fieldset o-form-label-top">
// 	<div data-se="o-form-input-container" class="o-form-input">
// 		<span data-se="o-form-input-username" class="okta-form-input-field input-fix o-form-control">
// 			<span class="input-tooltip icon form-help-16" data-hasqtip="0"></span>
// 			<span class="icon input-icon person-16-gray"></span>		
// 			%input%
//  		</span>
// 	</div>
// </div>

	function addField($fieldName) {
		$this->fields[] = $fieldName;
	}
}