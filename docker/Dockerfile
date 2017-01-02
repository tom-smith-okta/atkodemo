FROM php:7.0-apache

MAINTAINER Tom Smith <tom.smith@okta.com>

RUN apt-get update && apt-get install -y git

WORKDIR /var/www/html/

RUN git clone -b tsmith/dockerV02 --single-branch https://github.com/tom-smith-okta/atkodemo.git

####################

# docker build -t atkodemov01 .
# docker run -d -p 80:80 atkodemodockerv01

