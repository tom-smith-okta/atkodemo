<?php

include "../includes/includes.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SESSION["regFormType"] = "custom";

foreach ($_POST as $field => $value) {

	if ($field != "submit") {
		$_SESSION["regFields"][] = $field;
	}
}

$headerString = "Location: " . $config["webHomeURL"] . "/customizeRegForm.php"; 

header($headerString);