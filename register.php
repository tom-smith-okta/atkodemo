<?php

define("HOME", "atkoTravel"); // home dir on webserver

include $_SERVER['DOCUMENT_ROOT'] . "/" . HOME . "/includes/includes.php";

$thisPage = new htmlPage();

/*** Manually add elements here ******/

$thisPage->setTitle("Atko Travel Agency - Register");

// jquery
$thisPage->addElement("javascript", "https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js");

$body = file_get_contents("register.html");

$thisPage->setBodyParam("class", "single");

$thisPage->addToBody($body);

$thisPage->display();

