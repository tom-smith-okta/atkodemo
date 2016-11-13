<?php

$config["oktaOrg"] = "atkodemovm";

$config["apiKeyPath"] = "/usr/local/keys/atkodemovm.txt";

$appsWhitelist["salesforce"] = "Chatter";

// OIDC client ID - from Okta OIDC app
$config["clientId"] = "KySezizDE4ScxOlsNLsX";

// Social IDPs
$idps[] = array("type"=>"FACEBOOK", "id"=>"0oassj82zxJdGVjjL1t6");
$idps[] = array("type"=>"GOOGLE", "id"=>"0oasss0hkdAGnhCzF1t6");
