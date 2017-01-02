FROM php:7.0-apache

MAINTAINER Tom Smith <tom.smith@okta.com>

RUN apt-get update && apt-get install -y git

WORKDIR /var/www/html/

# This is a flag that tells the script what kind of
# environment it's running on
RUN touch dockerContainer.txt

# RUN git clone https://github.com/tom-smith-okta/atkodemo.git

RUN git clone -b tsmith/dockerV01 --single-branch https://github.com/tom-smith-okta/atkodemo.git

####################

# docker build -t atkodemov01 .
# docker run -d -p 80:80 atkodemodockerv01

