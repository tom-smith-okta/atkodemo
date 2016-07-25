<?php

// To be changed before PROD:

// get rid of mailinator
// change the default Okta password schema

session_start();

include "includes/includes.php";

echo "<p>The security answer is: " . $_POST["securityAnswer"];

echo "<p>The okta session id is: " . $_SESSION['oktaCookieSessionID'];