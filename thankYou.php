<?php

include "includes/includes.php";

$email = $_GET["email"];

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Thank you");

$thisPage->setConfigValue("email", $email);

$thisPage->setConfigValue("firstName", $_GET["firstName"]);

$elements = [
	"mainCSS",
	"jquery"
];

$thisPage->addElements($elements);

$thisPage->loadBody("thankYou", ["name", "webHome", "logo", "email", "firstName"]);

$thisPage->display();