<?php

class htmlPage {

	function __construct($config) {
		$this->elements["javascript"]["ext"] = ".js";
		$this->elements["javascript"]["tag"] = "<script src = '%PATH%'></script>";
		$this->elements["javascript"]["block"] = "";

		$this->elements["css"]["ext"] = ".css";
		$this->elements["css"]["tag"] = "<link rel = 'stylesheet' href = '%PATH%'/>";
		$this->elements["css"]["block"] = "";

		$this->body = "";
		
		$this->elements["body"]["class"] = "";

		// this function auto-loads files placed in certain locations.
		// not using it right now.
		// $this->findFiles();

		$this->config = $config;
	}

	// add a javascript or a css to the page.
	function addElement($elementName) {

		$type = $this->config[$elementName]["type"]; // either "javascript" or "css"

		$location = $this->config[$elementName]["location"]; // remote || local || inline

		if ($location == "local") {
			$ext = $this->elements[$type]["ext"]; // either ".js" or ".css"
			$filePath = $type . "/" . $elementName . $ext;
			$content = str_replace("%PATH%", $filePath, $this->elements[$type]["tag"]);
		}
		else if ($location == "inline") {
			$ext = $this->elements[$type]["ext"]; // either ".js" or ".css"
			$filePath = $type . "/" . $elementName . $ext;
			$content = $this->replaceVars($filePath, $elementName);
		}
		else { // $location = "remote"
			$content = str_replace("%PATH%", $this->config[$elementName]["url"], $this->elements[$type]["tag"]);
		}

		$this->addToBlock($content, $type);

	}

	function addElements($elements) {
		foreach ($elements as $element) {
			$this->addElement($element);
		}
	}

	function loadBody($name, $vars = []) {
		$body = file_get_contents("html/template.html");

		$filePath = "html/" . $name . ".html";

		// $main = file_get_contents(filename)
		// $body = file_get_contents($filePath);

		$main = file_get_contents($filePath);

		$body = str_replace("%main%", $main, $body);

		if (!empty($vars)) {
			foreach($vars as $var) {

				$target = "%" . $var . "%";

				$body = str_replace($target, $this->config[$var], $body);
			}
		}

		$this->body = $body;
	}

	function addToBlock($content, $type) {

		if (!empty($this->elements[$type]["block"])) { 
			$this->elements[$type]["block"] .= "\n\t\t";
		}

		$this->elements[$type]["block"] .= "\n" . $content . "\n";
	}

	function replaceVars($filePath, $elementName) {

		$content = file_get_contents($filePath);

		if (is_array($this->config[$elementName]["vars"])) {
			foreach ($this->config[$elementName]["vars"] as $var) {

				$bullseye = "%" . $var . "%";
				$arrow = $this->config[$var];

				$content = str_replace($bullseye, $arrow, $content);

			}			
		}

		return $content;
	}

	function findFiles() {
		foreach ($this->elements as $element => $arr) {
			$files = $this->getElements($element, $arr["ext"]);

			foreach ($files as $file) {
				$path = "/" . HOME . "/" . $element . "/" . $file;

				$this->addElement($element, $path);

			}
		}
	}

	// expects HTML w/o <body></body> tags
	function addToBody($element) {
		$this->body = $this->body . "\n\t\t" . $element;
	}


	function display() { echo $this->getHTML(); }

	function getBody() {
		if (empty($this->elements["body"]["class"])) { $bodyTag = "<body>"; }
		else { $bodyTag = "<body class = '" . $this->elements["body"]["class"] . "'>"; }

		return "\n\t" . $bodyTag . $this->body . "\n\t</body>"; 
	}

	function getElements($element, $ext) {

		$dir = $_SERVER['DOCUMENT_ROOT'] . "/" . HOME . "/" . $element . "/autoInclude/";

		$files = scandir($dir);

		foreach ($files as $file) {

			$offset = 0 - strlen($ext);

			if (substr($file, $offset) == $ext) {
				$validFiles[] = $file;
			}
		}

		return $validFiles;
	}

	function getHead() {

		$this->head = "\n\t\t<meta charset='utf-8' />";
		$this->head .= "\n\t\t<meta name='viewport' content='width=device-width, initial-scale=1' />";

		$headElements = array($this->title, $this->elements["css"]["block"], $this->elements["javascript"]["block"]);

		foreach ($headElements as $element) {
			if (!empty($element)) { $this->head .= "\n\t\t" . $element; }
		}

		return "\n\t<head>" . $this->head . "\n\t</head>";
	}

	function getHTML() {

		return "<!DOCTYPE HTML>\n<html>" . $this->getHead() . "\n" . $this->getBody() . "\n</html>";
	}

	function setBodyParam($paramType, $param) {
		$this->elements["body"][$paramType] = $param;

	}

	function setConfigValue($name, $value) {

		$this->config[$name] = $value;

	}

	function setTitle($title) {

		$this->title = "<title>" . $title . "</title>";

	}
}