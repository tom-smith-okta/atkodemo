<?php

include "includes/includes.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Set API key");

$elements = [
	"oktaWidgetCSScore",
	"oktaWidgetCSStheme",
	"oktaWidgetCSSlocal",
	"mainCSS"
];

$thisPage->addElements($elements);

$thisPage->loadBody("setAPIkey", ["name"]);

$thisPage->display();

exit;