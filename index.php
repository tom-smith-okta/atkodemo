<?php

$home = "atkoTravel"; // establishes homedir in webdir

include $_SERVER['DOCUMENT_ROOT'] . "/" . $home . "/includes/includes.php";

$thisPage = new htmlPage($config);

/*** Manually add elements here ******/

$thisPage->setTitle("Atko Travel Agency - Session manager");

// okta sign-in widget js
$thisPage->addExternalElement("okta-signin-widget");

$thisPage->addInlineElement("checkForSession");

$thisPage->display();