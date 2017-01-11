FROM php:7.0-apache

MAINTAINER Tom Smith <tom.smith@okta.com>

RUN apt-get update && apt-get install -y git

WORKDIR /var/www/html/

# RUN git clone -b tsmith/atkodemoDockerV01 --single-branch https://github.com/tom-smith-okta/atkodemo.git

####################
# Tom's testing sequence
# 1. Create a git branch specifically for the docker build
#		atkodemoDockerV01
# 2. Push the git branch to github
# 3. Build the docker container locally
#		docker build -t atkodemov01 .
# 4. Upload the container to dockerhub
# 5. Run the container
#		docker run -d -p 80:80 atkodemodockerv01

