<?php

include "includes/includes.php";

$email = $_GET["email"];

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Thank you");

$thisPage->setConfigValue("email", $email);

$elements = [
	"mainCSS",
	"jquery"
];

$thisPage->addElements($elements);

$thisPage->loadBody("thankYou", ["name", "webHome", "logo", "email"]);

$thisPage->display();