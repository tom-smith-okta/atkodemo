<?php

echo "<p>in the access token receiver page.";

echo "<p>The GET is: " . json_encode($_GET);

echo "<p>The POST is: " . json_encode($_POST);

$code = $_GET["code"];

// now get the access token

$url = "https://tomco.okta.com/oauth2/v1/token/";

$grantType = "authorization_code";

$scope = "open_id";

$redirect_uri = "http://localhost:8888/atkodemo/oauth/authCode/receiveAccessToken.php";

