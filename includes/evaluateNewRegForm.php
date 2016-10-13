<?php

include "../includes/includes.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SESSION["regFormType"] = "custom";

foreach ($_POST as $field => $value) {

	if ($field == "submit") {}
	else {
		if ($value == "add") {
			$_SESSION["regFields"][] = $field;
		}
		else if ($value == "remove") {

			$index = array_search($field, $_SESSION["regFields"]);

			unset($_SESSION["regFields"][$index]);

		}
	}
}

$headerString = "Location: " . $config["webHomeURL"] . "customizeRegForm.php"; 

header($headerString);