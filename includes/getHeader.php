<?php

function getHeader($type, $sessionID = "", $firstName = "") {

	global $config;

	$header = file_get_contents($config["fsHome"] . "/html/header.html");

	$header = str_replace("%webHome%", $config["webHome"], $header);

	$header = str_replace("%name%", $config["name"], $header);

	if ($type == "blank") {
		$links = "";
	}
	elseif ($type == "unAuth") {
		$links = "\n<li><a href='login.php'>Log in</a></li>";
		$links .= "\n<li><a href = 'register.php'>Register</a></li>";
	}
	elseif ($type == "auth") {
		$links = "\n<li><a href='" . $config["salesforce"] . "' target = '_blank'>Chatter</a></li>";

		$logoutLink = "logout.php?oktaCookieSessionID=" . $sessionID;

		$links .= "\n<li><a href = '" . $logoutLink . "'>Log out</a></li>";

		$links .= "\n<li><a href = '#'>Welcome, " .  $firstName . "!</a></li>";
	}

	$header = str_replace("%LINKS%", $links, $header);

	return $header;
}