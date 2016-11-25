<?php



$testString = "this is a bunch of text. There are some %--special--% strings in here that I want to %--pull--% out and replace.";

$success = preg_match("/%.*%/", $testString, $results);

echo "the results are: " . json_encode($results);

echo "the first match is: " . $results[1];

$results = explode("%", $testString);

echo "<p>the explosion results are: " . json_encode($results);

foreach($results as $result) {

	// echo "<p>" . $result;

	// echo "<p>" . substr($result, 0, 2);

	if (substr($result, 0, 2) == "--") {
		echo "<p>" . $result;
	}
}