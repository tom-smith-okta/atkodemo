<?php

include "../includes/loadDemo.php";

$thisSite = $_SESSION["site"];

$bodyMain = file_get_contents("../html/docs.html");

$thisSite->showPage("docs", $bodyMain);