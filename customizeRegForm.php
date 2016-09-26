<?php

include "includes/includes.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Customize Registration Form");

$elements = [
	"oktaWidgetCSScore",
	"oktaWidgetCSStheme",
	"oktaWidgetCSSlocal",
	"mainCSS"
];

$thisPage->addElements($elements);

if (empty($_SESSION["apiKey"])) {

	$html = "<p>There is no api key available in this session.</p>";

	$html .= "<p>Please click the 'set api key' link to get things rolling.</p>";

	$thisPage->setConfigValue("allFields", $html);

	$thisPage->loadBody("customizeRegForm", ["allFields", "name"]);

	$thisPage->display();

	exit;
}

if (empty($_GET["action"])) {}
else if ($_GET["action"] == "clear") {
	$_SESSION["regFormType"] = "pwd";
	$_SESSION["regFields"] = $config["regFormType"]["pwd"];	
}
if (empty($_SESSION["regFormType"])) {
	$_SESSION["regFormType"] = "pwd";
	$_SESSION["regFields"] = $config["regFormType"]["pwd"];		
}

$regFormType = $_SESSION["regFormType"];

if ($regFormType == "custom") {
	$regFields = $_SESSION["regFields"];
	$thisRegForm = new regForm("custom", $regFields);
}
else {
	$thisRegForm = new regForm($regFormType);
}

$thisPage->setConfigValue("allFields", $thisRegForm->displayAllFields());

$thisPage->loadBody("customizeRegForm", ["allFields", "name"]);

$thisPage->display();

exit;