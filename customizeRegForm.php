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

/* Load the Okta user schema */
if (file_exists("userSchema.txt")) { $config["userSchema"] = file_get_contents("userSchema.txt"); }
else {
	$config["userSchema"] = getUserSchema();
	file_put_contents("userSchema.txt", $config["userSchema"]);
}

if (empty($_GET["action"])) {}
else if ($_GET["action"] == "clear") {
	$_SESSION["regFormType"] = "min";
	$_SESSION["regFields"] = $config["regFormType"]["min"];	
}
else if (empty($_SESSION["regFormType"])) {
	$_SESSION["regFormType"] = "min";
	$_SESSION["regFields"] = $config["regFormType"]["min"];		
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