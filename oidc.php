<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

/*************************************/

$thisPage->setTitle($config["name"] . " - Log In");

// css
$thisPage->addElement("mainCSS");

$thisPage->addElement("oktaWidgetCSScore");

$thisPage->addElement("oktaWidgetCSStheme");

$thisPage->addElement("oktaWidgetCSSlocal");

// javascript
$thisPage->addElement("jquery");

$thisPage->addElement("okta-signin-widget");

$thisPage->addElement("OIDC");

$thisPage->addElement("dates");

// body
$body = file_get_contents("id_token.html");

$thisPage->addToBody($body);

// display
$thisPage->display();