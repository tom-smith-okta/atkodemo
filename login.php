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

$thisPage->addElement("oktaWidgetCSSlocal");

$thisPage->setBodyParam("class", "single");

$body = file_get_contents("login.html");

$header = getHeader("blank");

$body = str_replace("%HEADER%", $header, $body);

$thisPage->addToBody($body);

// $content = 

// $thisPage->addToBlock($content, $type) {

// 		if (!empty($this->elements[$type]["block"])) { 
// 			$this->elements[$type]["block"] .= "\n\t\t";
// 		}

// 		$this->elements[$type]["block"] .= "\n" . $content . "\n";
// 	}


$thisPage->display();