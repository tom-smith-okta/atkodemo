<?php

define("HOME", "atkoTravel"); // home dir on webserver

include $_SERVER['DOCUMENT_ROOT'] . "/" . HOME . "/includes/includes.php";

$thisPage = new htmlPage();

/*** Manually add elements here ******/

$thisPage->setTitle("Atko Travel Agency - Log In");

// jquery
$thisPage->addElement("javascript", "https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js");

// okta sign-in widget js
$thisPage->addElement("javascript", "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/1.3.3/js/okta-sign-in-1.3.3.min.js");

// okta sign-in widget css
$thisPage->addElement("css", "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/1.3.3/css/okta-sign-in-1.3.3.min.css");

// okta sign-in widget css - customizable
$thisPage->addElement("css", "https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/1.3.3/css/okta-theme-1.3.3.css");

$thisPage->addElement("javascript", "/" . HOME . "/javascript/optional/oktaSignin.js");

$body = file_get_contents("login.html");

$thisPage->addToBody($body);

$thisPage->display();

