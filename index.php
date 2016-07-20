<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

/*** Manually add elements here ******/

$thisPage->setTitle($config["name"] . " - Redirect URL");

// okta sign-in widget js
$thisPage->addElement("okta-signin-widget");

// local js logic to check to see whether an okta session exists
$thisPage->addElement("checkForSession");

$thisPage->display();