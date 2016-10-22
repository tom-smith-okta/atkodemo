<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

/*************************************/

$thisPage->setTitle($config["name"] . " - Login (basic)");

$elements = [
	"oktaWidgetCSScore",
	"oktaWidgetCSStheme",
	"oktaWidgetCSSlocal",
	"mainCSS",
	"jquery",
	"font-awesome",
	"okta-signin-widget",
//	"index",
	"widgetBasic",
	"setMenu",
	"dates"
];

$thisPage->addElements($elements);

$thisPage->loadBody("login", ["webHome", "name"]);

$thisPage->display();