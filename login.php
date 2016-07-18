<?php

$home = "atkotravel"; // establishes homedir in webdir

include $_SERVER['DOCUMENT_ROOT'] . "/" . $home . "/includes/includes.php";

$thisPage = new htmlPage($config);

/*************************************/

$thisPage->setTitle($config["name"] . " - Log In");

$thisPage->addElement("jquery");

$thisPage->addElement("mainCSS");

$thisPage->addElement("okta-signin-widget");

$thisPage->addElement("oktaWidgetCSScore");

$thisPage->addElement("oktaWidgetCSStheme");

$thisPage->addElement("oktaSignInOIDC");

$thisPage->setBodyParam("class", "single");

$body = file_get_contents("login.html");

$header = getHeader("blank");

$body = str_replace("%HEADER%", $header, $body);

$thisPage->addToBody($body);

$thisPage->display();