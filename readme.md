# Okta Portal Generator

tom.smith@okta.com

This demo is meant to show some of the capabilities of Okta's platform, specifically in terms of IDAAS for external users. This particular demo has a B2C slant, but it can be modified for other external use cases as well (B2B etc.).

## Sites

- Docker
```sh
docker pull atkodemo
```
- github: https://github.com/tom-smith-okta/atkodemo
- public version: http://www.atkodemo.com

## Architecture
The OPG is built as a custom PHP app. It uses a custom, internal HTML generation engine, so the only dependency from a server perspective is that you have a web server running php. The code is written in php7, but may run under earlier php releases. The code makes heavy use of json.

The recommended way to deploy the site is through the docker image, which is built on the PHP7 Docker image.

## How-to: Docker

Install Docker.

On a Mac, create the following directory:

- /Users/{{userName}}/atkodemo/

(this site/process has not been tested on Windows yet.)

Run the following command from a terminal (make sure Docker is running first):

```sh
docker run -d -p 55:80 -v /Users/{{userName}}/atkodemo:/var/www/html/atkodemo/mysites tomgsmith99/atkodemo
```

it may take a few minutes to download the atkodemo image from dockerhub.

Once the download is complete, open a web browser and go to

http://localhost:55/atkodemo

## How-to: Github repo
The site expects to run at DocumentRoot. Use your favorite method to set up a local or web address that points to the repo as the documentRoot.

Running
After you have cloned the repo, load http://localhost/ (or whatever server you set up) in a browser. The default atkodemo site will load.

Click on the server icon in the upper right to see the status page.

To add your own site
On your machine, make sure that php has the ability to write to the following directory:

/{{webRoot}}/mysites

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

/{{webRoot}}/mysites/{{dirName}}/

At the moment {{dirName}} is not configurable; it just incrememts with every new site.

There are four essential configuration files for each site, which are created with each new site.

main.json: essential settings like orgname and api key 
theme.json: images and text
regFlows.json: defines registration flows (name of flow, which fields are included, groupIDs, etc.)
regFields.json: simple data definitions for reg fields. Any field that is included in a regFlow needs to be defined here.

So, if you want to change some of these settings for your site, edit the json file directly with a text editor, and then return to the OPG UI and click the "refresh" icon. This will load the new settings into your site.

You can also look at some of the more advanced settings in the other example sites included in the /sites/ directory.

## Version History

### 2017-02-03
- Changed root to /{{DocumentRoot}} instead of /{{DocumentRoot}}/atkodemo
- Added readme to top menu

