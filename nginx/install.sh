#!/bin/bash

#install nginx
sudo apt-get install -y nginx

sudo apt-get install -y php5-fpm

sudo /etc/init.d/nginx stop 

sudo cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.$(date "+%b_%d_%Y_%H.%M.%S") #backup current config file

#Copy new config file to /etc/nginx/niginx.conf
sudo rm /etc/nginx/sites-available/default
sudo cp /home/pi/pins/nginx/default /etc/nginx/sites-available/default 

sudo /etc/init.d/nginx reload

sudo /etc/init.d/nginx start


exit 0