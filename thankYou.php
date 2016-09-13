<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Thank you");

$elements = [
	"mainCSS",
	"jquery"
];

$thisPage->addElements($elements);

$thisPage->loadBody("thankYou", ["name", "webHome", "logo", "regForm", "regDesc"]);

$thisPage->display();