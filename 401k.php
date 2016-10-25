<?php

include "includes/includes.php";

// $email = $_GET["email"];

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - 401k balance");

// $thisPage->setConfigValue("email", $email);

// $thisPage->setConfigValue("firstName", $_GET["firstName"]);

$elements = [
	"mainCSS",
	"jquery"
];

$thisPage->addElements($elements);

// $thisPage->loadBody("thankYou", ["name", "webHome", "logo", "email", "firstName"]);

$thisPage->loadBody("401k", ["name", "webHome", "logo"]);

$thisPage->display();