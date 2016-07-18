<?php

$home = "atkotravel"; // establishes homedir in webdir

include $_SERVER['DOCUMENT_ROOT'] . "/" . $home . "/includes/includes.php";

$thisPage = new htmlPage($config);

/*************************************/

/*** Manually add elements here ******/

$thisPage->setTitle("Atko Travel Agency - Register");

// jquery
$thisPage->addElement("jquery");

$thisPage->addElement("mainCSS");

$body = file_get_contents("register.html");

$thisPage->setBodyParam("class", "single");

$header = getHeader("blank");

$body = str_replace("%HEADER%", $header, $body);

$thisPage->addToBody($body);

$thisPage->display();