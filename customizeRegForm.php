<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Customize Registration Form");

$thisUserSchema = new userSchema();

$thisRegForm = new regForm("min");

foreach($thisRegForm->fields as $field) {
	echo "<p>the field is: " . $field;
}



exit;