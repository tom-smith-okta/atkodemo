<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

/*************************************/

/*** Manually add elements here ******/

$thisPage->setTitle($config["name"] . " - Register");

// jquery
$thisPage->addElement("jquery");

$thisPage->addElement("mainCSS");

$thisPage->addElement("oktaWidgetCSScore");

$thisPage->addElement("oktaWidgetCSStheme");

$thisPage->addElement("oktaWidgetCSSlocal");

$body = file_get_contents("register.html");

$thisPage->setBodyParam("class", "single");

$header = getHeader("blank");

$body = str_replace("%HEADER%", $header, $body);

$thisPage->addToBody($body);

$thisPage->display();