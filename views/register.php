<?php

include "../includes/config.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$thisSite->showPage("register");

		echo "the slogan in the session are: " . $_SESSION["siteObj"]->slogan;

