<?php

include "includes/includes.php";

$email = $_GET["email"];

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Thank you");

$thisPage->setConfigValue("email", $email);

$thisPage->setConfigValue("msg", $_GET["msg"]);

$elements = [
	"mainCSS",
	"jquery"
];

$thisPage->addElements($elements);

$thisPage->loadBody("thankYou", ["name", "webHome", "logo", "email", "msg"]);

$thisPage->display();