Okta Platform Demo

tom.smith@okta.com

This demo is meant to show some of the capabilities of Okta's platform, specifically in terms of IDAAS for external users. This particular demo has a B2C slant, but it can be modified for other external use cases as well (B2B etc.).

The public version of this demo is at: www.atkodemo.com

The demo is written in php. It uses a custom, internal HTML generation engine, so the only dependency from a server perspective is that you have a web server running php. My workflow is to write code and test on localhost, and then do a git pull down to the www.atkodemo.com server, and it's good to go. So, you should be able to run fine on localhost as well as a public server.

The approach is that you can make changes to just a single file

/atkodemo/includes/config.php

and then you will be able to run the demo against your own okta tenant.

To run the demo, you need to have your own okta tenant set up, and you need to set a number of things up in the tenant in order for the demo to work. Future work may include documenting this setup, but it is out of scope at the moment. (But the documentation is available on the Okta web site.)

The roadmap for the demo is here: https://okta.box.com/s/x0451iegibayq84sedbudcd2czn86nse
