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
	"loadWidgetOIDC",
	"checkForSession",
	"renderWidgetOIDC",
	"signout",
	"dates",
	"skel.min",
	"main",
	"util",
	"setMenu",
	"menu"
];

$thisPage->addElements($elements);

$thisPage->loadBody("index", ["webHome", "name", "logo", "topImage", "bottomImage", "mainImage", "regOptions", "menu"]);

$thisPage->display();