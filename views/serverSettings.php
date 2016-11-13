<?php

include "../includes/includes.php";

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Server/Host Settings");

$thisPage->addElements($config["defaultVals"]);

$thisPage->loadBody("serverSettings", ["name", "webHome", "logo", "regOptions", "menu", "serverSettings"]);

$thisPage->display();