# Okta Platform Demo #

tom.smith@okta.com

This demo is meant to show some of the capabilities of Okta's platform, specifically in terms of IDAAS for external users. This particular demo has a B2C slant, but it can be modified for other external use cases as well (B2B etc.).

The public version of this demo is at: www.atkodemo.com

The demo is written in php. It uses a custom, internal HTML generation engine, so the only dependency from a server perspective is that you have a web server running php. My workflow is to write code and test on localhost, and then do a git pull down to the www.atkodemo.com server, and it's good to go. So, you should be able to run fine on localhost as well as a public server.

The approach is that you can make changes to just a single file

/atkodemo/includes/config.php

and then you will be able to run the demo against your own okta tenant.

To run the demo, you need to have your own okta tenant set up, and you need to set a number of things up in the tenant in order for the demo to work. Future work may include documenting this setup, but it is out of scope at the moment. (But the documentation is available on the Okta web site.)

The roadmap for the demo is here: https://okta.box.com/s/x0451iegibayq84sedbudcd2czn86nse

## How the HTML generator works ##

Note: You do not need to mess with any of this if you just want to get a demo up and running.

This documentation is meant for those who want to do extensive configuration and extension.

The HTML generator generates HTML on-the-fly. You can create an empty HTML page by calling the class constructor:

$thisPage = new htmlPage($config);

the class constructor will probably complain if it doesn't get a value for $config, but you should be able to immediately display an empty html page with the display() method:

$thisPage->display()

this will basically output <html><head></head><body></body></html> to the screen.

Inserting content into the HTML page is broken down into two main sections, HEAD and BODY.

The body is the easier part. Use the

addToBody() method to add HTML to the body. This method expects HTML as a parameter.

You can call this method as many times as you want to add new blocks of HTML to the body.

To add a new component to <HEAD>, you have two options:

1) the addToBlock($content, $type) method is a little more freeform.
$type must be "javascript" || "css"
$content should be html

2) the addElement($elementName) is more structured, and good for creating elements that need to remain consistent across pages.

$elementName will be a value in the $config array. $elementName can be an arbitrary string value.

$config[$elementName] must have several properties.

$config[$elementName]["type"] must be either "javascript" or "css"

$config[$elementName]["location"] must be  "remote" || "local" || "inline"

*** remote ***
"remote" essentially means "fully qualified URL"
the additional required value where location == "remote" is

$config[$elementName]["url"] which is a string value containing the full URL of the resource.

example:
$config["jquery"]["type"] = "javascript"
$config["jquery"]["location"] = "remote"
$config["jquery"]["url"] = "https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"

results in:

<script src = 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js'></script>

*** local ***
"local" essentially means "a local file"
there are no other required values for "local" assets (not even URL). BUT the script will expect the local file to reside in the following path:

/$config["homeDir"]/$type/$elementName.$ext

example:
$config["mainCSS"]["type"] = "css";
$config["mainCSS"]["location"] = "local";

the following file must be in place:

/atkodemo/css/mainCSS.js

and this will result in the following tag to be added to <head>:

<link rel = 'stylesheet' href = '/atkodemo/css/mainCSS.css'/>

*** inline ***
"inline" is a special version of "local".
"inline" means that the script will:

1. open a local file into a string
filename requirements are same as those defined for "local"

2. look for placeholders ( %placeholder% ) inside the string
the value for "placeholder" = some $elementName in $config

3. replace the %placeholders% with a value from $config

4. add the file to the <head> as either a <script> or <css>

example:

$config["checkForSession"]["type"] = "javascript";
$config["checkForSession"]["location"] = "inline";
$config["checkForSession"]["vars"] = array("oktaBaseURL", "homePage");

the following file must be in place:

/atkodemo/javascript/checkForSession.js

the script will open this file into a string.

the script will look in the string for "%oktaBaseURL%" and replace it with $config["oktaBaseURL"]
the script will look in the string for "%homePage%" and replace it with $config["homePage"]

the script will paste the string into the head as follows:

<head>

<script>

[the string]

</script>

...
