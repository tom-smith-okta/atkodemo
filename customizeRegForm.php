<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Customize Registration Form");

$thisUserSchema = new userSchema();

$thisRegFrom = new regForm();

exit;