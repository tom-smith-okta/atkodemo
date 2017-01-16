FROM ubuntu:14.04

RUN sudo apt-get -y update

RUN sudo apt-get -y install apache2

# COPY /Applications/MAMP/htdocs/atkodemo /var/www/html/atkodemo/

COPY ./views/ /var/www/html/atkodemo/

# docker build -t apachetest .

# docker run -i -t apachetest