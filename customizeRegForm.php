<?php

include "includes/includes.php";

// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Customize Registration Form");

$config["userSchema"] = getUserSchema();

// if (empty($_SESSION["userSchema"])) { $_SESSION["userSchema"] = new userSchema(); }

// if (empty($_SESSION["regForm"])) {
// 	echo "the session does not have regForm defined.";
// 	exit;

// 	$_SESSION["regForm"] = new regForm("min");
// }
// else {
// 	echo "<p>there is a regform in the session.";
// 	//echo "<p>its value is: " . $_SESSION["regForm"];
// }

$regForm = new regForm("min");

// $regForm = $_SESSION["regForm"];

echo $regForm->getHTML();

exit;