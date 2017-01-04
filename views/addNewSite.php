<?php

include "../includes/loadDemo.php";

$thisSite = $_SESSION["demo"]["site"];

$bodyMain = file_get_contents("../html/addNewSite.html");

// $bodyMain = str_replace("%--rows--%", $rows, $bodyMain);

$thisSite->showPage("addNewSite");