<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

/*************************************/

$thisPage->setTitle($config["name"] . " - Log In");

$thisPage->addElement("jquery");

$thisPage->addElement("mainCSS");

$thisPage->addElement("okta-signin-widget");

$thisPage->addElement("oktaWidgetCSScore");

$thisPage->addElement("oktaWidgetCSStheme");

$thisPage->addElement("oktaSignInOIDC");

$thisPage->addElement("oktaWidgetCSSlocal");

$thisPage->setBodyParam("class", "single");

$body = file_get_contents("login.html");

$header = getHeader("blank");

$body = str_replace("%HEADER%", $header, $body);

$thisPage->addToBody($body);

$thisPage->display();