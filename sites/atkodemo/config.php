<?php

$config["oktaOrg"] = "tomco";

$config["apiKeyPath"] = "/usr/local/keys/oktaAPI.txt";

$appsWhitelist["salesforce"] = "Chatter";

// OIDC client ID - from Okta OIDC app
$config["clientId"] = "YYUAPHIAj3JPPO6yJans";

// Social IDPs
$idps[] = array("type"=>"FACEBOOK", "id"=>"0oa1w1pmezuPUbhoE1t6");	
$idps[] = array("type"=>"GOOGLE", "id"=>"0oa1w8n4dlYlOLjPl1t6");