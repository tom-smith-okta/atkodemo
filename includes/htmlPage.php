<?php

class htmlPage {

	function __construct() {
		$this->elements["javascript"]["ext"] = ".js";
		$this->elements["javascript"]["tag"] = "<script src = '%PATH%'></script>";
		$this->elements["javascript"]["block"] = "";

		$this->elements["css"]["ext"] = ".css";
		$this->elements["css"]["tag"] = "<link rel = 'stylesheet' href = '%PATH%'/>";
		$this->elements["css"]["block"] = "";

		$this->findFiles();
	}

	function addElement($type, $path) {

		$tag = str_replace("%PATH%", $path, $this->elements[$type]["tag"]);

		if (!empty($this->elements[$type]["block"])) { 
			$this->elements[$type]["block"] .= "\n\t\t";
		}

		$this->elements[$type]["block"] .= $tag;

	}

	function display() {

		echo $this->getHTML();

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

	function addToBody($element) {
		$this->body = $this->body . "\n\t\t" . $element;
	}

	function getBody() {
		if (empty($this->body)) { $this->body = ""; }

		return "\n\t<body>" . $this->body . "\n\t</body>"; 
	}

	function getElements($element, $ext) {

		$dir = $_SERVER['DOCUMENT_ROOT'] . "/" . HOME . "/" . $element . "/";

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

	function setTitle($title) {

		$this->title = "<title>" . $title . "</title>";

	}
}