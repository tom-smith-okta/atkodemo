<?php

include "../includes/config.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$thisSite->showPage("status");