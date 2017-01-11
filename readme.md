# Okta Platform Demo #

# Okta Portal Generator #

tom.smith@okta.com

This demo is meant to show some of the capabilities of Okta's platform, specifically in terms of IDAAS for external users. This particular demo has a B2C slant, but it can be modified for other external use cases as well (B2B etc.).

The public version of this demo is at: www.atkodemo.com

Installing
The demo is written in php. It uses a custom, internal HTML generation engine, so the only dependency from a server perspective is that you have a web server running php. The code is written in php7, but may run under earlier php releases. The code makes heavy use of json.

A dockerized version is in the works.

The site expects to run in the following location:

/{{webRoot}}/atkodemo/

So, if you start in your web root dir and clone this repo you should be good to go. You *should* be able to run it from any context you want with a few tweaks, but this has not been fully tested yet.

Running
After you have cloned the repo, go to localhost/atkodemo in a browser. The default atkodemo site will load.

Click on the server icon in the upper right to see the status page.

To add your own site
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

/{{webRoot}}/atkodemo/sites/{{siteName}}/

At the moment {{siteName}} is not configurable; it just incrememts with every new site.

There are four essential configuration files for each site. If OPG cannot find one of these files in the site’s directory, it will just load the default version of the file.

main.json: essential settings like orgname and api key 
theme.json: images and text
regFlows.json: defines registration flows (name of flow, which fields are included, groupIDs, etc.)
regFields.json: simple data definitions for reg fields. Any field that is included in a regFlow needs to be defined here.

So, if you want to change some of these settings for your site, copy the entire default file to the directory for your site, and change the local copy of the file.

The default versions of the files are located at:

/{{webRoot}}/atkodemo/sites/default/

You can also look at some of the more advanced settings in the other example sites included in the /sites/ directory.
