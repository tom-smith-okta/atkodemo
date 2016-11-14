<?php

$config["warnings"] = [];

$thisSite = new demoSite();

/*********** Env Settings ***************/

// set the home directory relative to the web
// root of the server. Override this only if 
// you are serving the site from somewhere
// other than /[webRoot]/atkodemo/
// default value is "atkodemo"
$homeDir = "";
$thisSite->setHomeDir($homeDir);

$thisSite->setLocalPaths();

/******** Load Site ********************/

// Looks for a site defined in 
// /[webRoot]/[homeDir]/sites/siteToLoad.txt

$thisSite->load();

/*********** Site Name ******************/
// default value is "docker"
// the script will load a site configuration
// from /sites/$siteName/
// $siteName = "";
// $thisSite->setName($siteName);

// // optional, but useful for error-checking
// $siteDesc = "";
// $thisSite->setDesc($siteDesc);


echo "<p>home directory: " . $thisSite->getHomeDir();

echo "<p>web home: " . $thisSite->webHome;

echo "<p>local file space home: " . $thisSite->fsHome;

echo "<p>include path: " . $thisSite->includePath;

echo "<p>php include path: " . get_include_path();

echo "<p>web home URL: " . $thisSite->webHomeURL;

echo "<p>config home dir: " . $thisSite->configHome;

echo "<p>config home file: " . $thisSite->configFile;

echo "<p>default home dir: " . $thisSite->defaultHome;

