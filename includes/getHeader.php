<?php

function getHeader($type = "unAuth") {

	global $config;

	$header = file_get_contents($config["fsHome"] . "/html/header.html");

	if ($type == "unAuth") {
		$links = "\n<li><a href='login.php'>Log in</a></li>";
		$links .= "\n<li><a href = 'register.php'>Register</a></li>";
	}
	if ($type == "blank") {
		$links = "";
	}

	$header = str_replace("%LINKS%", $links, $header);

	return $header;
}

function getAuthHeader($sessionID, $firstName) {

	global $config;

	$header = file_get_contents($config["fsHome"] . "/html/header.html");

	$links = "\n<li><a href='" . $config["salesforce"] . "' target = '_blank'>Chatter</a></li>";

	$logoutLink = "logout.php?oktaCookieSessionID=" . $sessionID;

	$links .= "\n<li><a href = '" . $logoutLink . "'>Log out</a></li>";

	$links .= "\n<li><a href = '#'>Welcome, " .  $firstName . "!</a></li>";

	$header = str_replace("%LINKS%", $links, $header);

	return $header;

}