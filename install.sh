#!/bin/bash
set -e

PASSWORD=test
#TODO: ask for MySQL / phpmyadmin password and update variable


#update and upgrade
(sudo apt-get update && sudo apt-get -y upgrade) || (echo "Upgrade Failed. Aborting..." && exit 1)  

#set the timezone
echo "America/Los_Angeles" > sudo /etc/timezone

#install git
sudo apt-get install -y git-core || (echo "Git Install Failed. Aborting..." && exit 1)

#clone wiringPi
#todo: check if it exists before you clone
git clone git://git.drogon.net/wiringPi #|| (echo "WiringPi Clone Failed. Aborting..." && exit 1)

#build wiringPi
cd wiringPi
sudo ./build ||  (echo "Building wiringPi Failed. Aborting..." && exit 1)
cd ~

#installing Apache
sudo apt-get install -y apache2 apache2-utils || (echo "Apache Install Failed. Aborting..." && exit 1)
sudo chown -R www-data:www-data /var/www/ || (echo "Setting Ownership Failed. Aborting..." && exit 1)
sudo chmod g+rw -R /var/www/ || (echo "Setting Permissions Failed. Aborting..." && exit 1)
sudo chmod g+s -R /var/www/ || (echo "Setting Permissions Failed. Aborting..." && exit 1)
sudo usermod -a -G www-data pi || (echo "Adding pi to www-data Failed. Aborting..." && exit 1)

#installing mysql
sudo echo 'mysql-server mysql-server/root_password password $PASSWORD' | sudo debconf-set-selections
sudo echo 'mysql-server mysql-server/root_password_again password $PASSWORD' | sudo debconf-set-selections
sudo apt-get install -y mysql-server mysql-client || (echo "MySQL Install Failed. Aborting..." && exit 1)

#install phpmyadmin
sudo echo "phpmyadmin phpmyadmin/dbconfig-install boolean true" | sudo debconf-set-selections
sudo echo "phpmyadmin phpmyadmin/app-password-confirm password $PASSWORD" | sudo debconf-set-selections
sudo echo "phpmyadmin phpmyadmin/mysql/admin-pass password $PASSWORD" | sudo debconf-set-selections
sudo echo "phpmyadmin phpmyadmin/mysql/app-pass password $PASSWORD" | sudo debconf-set-selections
sudo echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2" | sudo debconf-set-selections
sudo apt-get install -y phpmyadmin || (echo "PHPMyAdmin Install Failed. Aborting..." && exit 1)

#create database and tables
#TODO: create database and tables

#clone pins repo
git clone https://github.com/stevielam/pins.git || (echo "Pins Clone Failed. Aborting..." && exit 1)

#configure cron job
#todo: check if cron job exists, if not then create one for the init and poll scripts

exit 0