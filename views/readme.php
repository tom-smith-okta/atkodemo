<?php

$readme = file_get_contents("../readme.md");

$html = file_get_contents("../html/readme.html");

echo str_replace("%--readme--%", $readme, $html);