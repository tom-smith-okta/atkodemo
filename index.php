<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

/*************************************/

$thisPage->setTitle($config["name"] . " - Home");

$elements = [
	"mainCSS",
	"oktaWidgetCSScore",
	"oktaWidgetCSStheme",
	"oktaWidgetCSSlocal",
	"jquery",
	"font-awesome",
	"okta-signin-widget",
	"OIDC",
	"dates"
];

$thisPage->addElements($elements);

$thisPage->loadBody("index", ["webHome", "index", "name"]);

$thisPage->display();