<?php

$home = "atkotravel"; // establishes homedir in webdir

include $_SERVER['DOCUMENT_ROOT'] . "/" . $home . "/includes/includes.php";

$thisPage = new htmlPage($config);

/*************************************/

$thisPage->setTitle("Atko Travel Agency - Log In");

$thisPage->addElement("jquery");

$thisPage->addElement("mainCSS");

$thisPage->addElement("okta-signin-widget");

$thisPage->addElement("oktaWidgetCSScore");

$thisPage->addElement("oktaWidgetCSStheme");

$thisPage->addElement("oktaSignInOIDC");

$body = file_get_contents("login.html");

$thisPage->addToBody($body);

$thisPage->display();

