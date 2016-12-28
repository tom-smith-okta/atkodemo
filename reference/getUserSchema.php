<?php

function getUserSchema() {

	global $config;

	$url = $config["apiHome"] . "/meta/schemas/user/default";

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_URL => $url
	));

	$schema = sendCurlRequest($curl, "error message", "json");

	return $schema;
}