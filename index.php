<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

/*************************************/

$thisPage->setTitle($config["name"] . " - Home");

$elements = [
	"oktaWidgetCSScore",
	"oktaWidgetCSStheme",
	"oktaWidgetCSSlocal",
	"mainCSS",
	"jquery",
	"font-awesome",
	"okta-signin-widget",
	"widgetOIDC",
	"setMenu",
	"signout",
	"dates",
	"skel.min",
	"main",
	"util"
];

$thisPage->addElements($elements);

$thisPage->loadBody("index", ["webHome", "name"]);

$thisPage->display();