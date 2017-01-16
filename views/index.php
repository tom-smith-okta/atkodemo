<?php

$filename = "/var/www/html/atkodemo/mysites/";

$temp = file_get_contents("../sites/siteToLoad.json");

include "../includes/loadDemo.php";

$_SESSION["demo"]["site"]->showPage("index");