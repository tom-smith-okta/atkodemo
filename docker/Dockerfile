FROM php:7.0-apache

MAINTAINER Tom Smith <tom.smith@okta.com>

RUN apt-get update && apt-get install -y git

WORKDIR /var/www/html/

# This is a flag that tells the script what kind of
# environment it's running on
RUN touch dockerContainer.txt

# RUN git clone https://github.com/tom-smith-okta/atkodemo.git

RUN git clone https://github.com/tom-smith-okta/atkodemo.git

# EXPOSE 80

####################

# Point dockerfile to specific git branch

