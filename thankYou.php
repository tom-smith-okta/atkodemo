<?php

include "includes/includes.php";

$email = $_GET["email"];

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Thank you");

$thisPage->setConfigValue("email", $email);

$thisPage->setConfigValue("msg", $_GET["msg"]);

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

$thisPage->setConfigValue("regOptions", getRegOptions());

$thisPage->loadBody("thankYou", ["name", "webHome", "logo", "email", "msg", "regOptions"]);

$thisPage->display();