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
	"loadWidgetBasic",
	"checkForSession",
	"renderWidgetBasic",
	"setMenu",
	"signout",
	"skel.min",
	"main",
	"util"
];

$thisPage->addElements($elements);

$thisPage->loadBody("login", ["webHome", "name", "regOptions"]);

$thisPage->display();