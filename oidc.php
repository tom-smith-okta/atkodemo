<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

/*************************************/

$thisPage->setTitle($config["name"] . " - Log In");

// scripts
$thisPage->addElement("jquery");

$thisPage->addElement("okta-signin-widget");

// $thisPage->addElement("oktaSignInOIDC");

$thisPage->addElement("OIDC");


// css
$thisPage->addElement("mainCSS");

$thisPage->addElement("oktaWidgetCSScore");

$thisPage->addElement("oktaWidgetCSStheme");


$thisPage->addElement("oktaWidgetCSSlocal");

// $thisPage->setBodyParam("class", "single");

// $thisPage->setBodyParam("class", "single");

$body = file_get_contents("id_token.html");

$header = getHeader("blank");

$body = str_replace("%HEADER%", $header, $body);

$thisPage->addToBody($body);

$thisPage->display();