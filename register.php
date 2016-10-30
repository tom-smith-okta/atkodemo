<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Register");

if (empty($_GET["regType"])) { $regType = "basic"; }
else { $regType = $_GET["regType"]; }

$elements = [
	"oktaWidgetCSScore",
	"oktaWidgetCSStheme",
	"oktaWidgetCSSlocal",
	"mainCSS",
	"font-awesome",
	"jquery",
	"okta-signin-widget",
	"loadWidgetBasic",
	"checkForSession",
	"setMenu",
	"skel.min",
	"main",
	"util"
];

$thisPage->addElements($elements);

$thisPage->setConfigValue("regDesc", getRegDesc($regType));

$thisPage->setConfigValue("regForm", getRegForm($regType));

$thisPage->loadBody("register", ["name", "webHome", "logo", "regForm", "regDesc", "regOptions"]);

$thisPage->display();

function getRegDesc($regType) {
	global $config;

	$returnVal = "<b>" . $config["regFlow"][$regType]["title"] . "</b>\n";
	$returnVal .= "<p>" . $config["regFlow"][$regType]["desc"] . "</p>\n";

	return $returnVal;

}

function getRegForm($regType) {

	$regForm = file_get_contents("html/regFormTemplate.html");

	$formFieldTemplate = file_get_contents("html/regFormFieldTemplate.html");

	$fieldsHTML = "";

	// First name
	$fields["firstName"]["type"] = "text";
	$fields["firstName"]["placeholder"] = "First name";

	// Last name
	$fields["lastName"]["type"] = "text";
	$fields["lastName"]["placeholder"] = "Last name";

	// email
	$fields["email"]["type"] = "text";
	$fields["email"]["placeholder"] = "email";

	if ($regType == "basic" || $regType == "sfChatter") {
		// password
		$fields["password"]["type"] = "password";
		$fields["password"]["placeholder"] = "password";		
	}

	foreach ($fields as $fieldName => $properties) {

		$formField = $formFieldTemplate;

		$input = "<input name = '" . $fieldName . "'";

		foreach ($properties as $propertyName => $value) {
			$input .= " " . $propertyName . " = '" . $value . "'";

			if ($value == "hidden") {
				$input .= " style='display:none'"; 
			}
		}

		$input .= ">";

		$formField = str_replace("%input%", $input, $formField);

		$fieldsHTML .= $formField;
	}

	$regForm = str_replace("%flowType%", $regType, $regForm);

	$regForm = str_replace("%fields%", $fieldsHTML, $regForm);

	return $regForm;

}