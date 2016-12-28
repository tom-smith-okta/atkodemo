<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Register");

if (empty($_GET["regType"])) { $regType = "sfChatter"; }
else { $regType = $_GET["regType"]; }

$elements = [
	"oktaWidgetCSScore",
	"oktaWidgetCSStheme",
	"oktaWidgetCSSlocal",
	"mainCSS",
];

$thisPage->addElements($elements);

$thisRegForm = new regForm("custom");

$thisPage->setConfigValue("regForm", $thisRegForm->getHTML()); 

$thisPage->loadBody("customRegister", ["name", "webHome", "logo", "regForm"]);

$thisPage->display();