# Okta Portal Generator #

tom.smith@okta.com

This demo is meant to show some of the capabilities of Okta's platform, specifically in terms of IDAAS for external users. This particular demo has a B2C slant, but it can be modified for other external use cases as well (B2B etc.).

The public version of this demo, which is very similar, is at: www.atkodemo.com

Docker version
The recommended way of using this application is through the docker image, which is available on dockerhub:

tomgsmith99/atkodemo

Non-docker instrux
The demo is written in php. It uses a custom, internal HTML generation engine, so the only dependency from a server perspective is that you have a web server running php. The code is written in php7, but may run under earlier php releases. The code makes heavy use of json.

The site expects to run in the following location:

/{{webRoot}}/atkodemo/

So, if you start in your web root dir and clone this repo you should be good to go. You *should* be able to run it from any context you want with a few tweaks, but this has not been fully tested yet.

Running
After you have cloned the repo, load http://localhost/atkodemo in a browser. The default atkodemo site will load.

Click on the server icon in the upper right to see the status page.

To add your own site
On your machine, make sure that php has the ability to write to the following directory:

/{{webRoot}}/atkodemo/mysites

Just click on the “add new site” button to add your own sites. The only required field is your Okta tenant name. But, to enable registration you need to add an api key, and to enable OIDC authentication you need to add a client ID.

When you add your own site, the OPG will load the default settings for the portal. This will give you the essential portal capabilities (authentication & registration) for your Okta tenant.

Advanced capabilities 

You can customize many aspects of your sites, including:
Text & images
Add new registration flows
Add/adjust registration fields
Attach Okta groups
Applications to show in the “my apps” list
All applications are listed by default
You can exclude apps by adding their IDs to a blacklist

All of these settings for the sites are stored in json files. When a site is created by the OPG, a directory is created for the site.

/{{webRoot}}/atkodemo/mysites/{{dirName}}/

At the moment {{dirName}} is not configurable; it just incrememts with every new site.

There are four essential configuration files for each site, which are created with each new site.

main.json: essential settings like orgname and api key 
theme.json: images and text
regFlows.json: defines registration flows (name of flow, which fields are included, groupIDs, etc.)
regFields.json: simple data definitions for reg fields. Any field that is included in a regFlow needs to be defined here.

So, if you want to change some of these settings for your site, edit the json file directly with a text editor, and then return to the OPG UI and click the "refresh" icon. This will load the new settings into your site.

You can also look at some of the more advanced settings in the other example sites included in the /sites/ directory.
