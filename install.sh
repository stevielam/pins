#!/bin/bash


set -e


PASSWORD=test
TIMEZONE="America/Los_Angeles"

read -s -p 'Choose Password for MySQL root and PHPMyAdmin root: ' pw1
 
read -s -p 'Please repeat the password: ' pw2

# Check both passwords match
if [ $pw1 != $pw2 ]; then
    echo "Passwords do not match. Aborting" && exit 1    
else
    PASSWORD=$pw1
fi

#TODO: ask for timezone


#update and upgrade
(sudo apt-get update && sudo apt-get -y upgrade) || (echo "Upgrade Failed. Aborting..." && exit 1)  

#set the timezone
sudo echo $TIMEZONE > sudo /etc/timezone

#install git
sudo apt-get install -y git-core || (echo "Git Install Failed. Aborting..." && exit 1)

#check if wiringPi exists and delete it, TODO: pull instead of delete
if [ -d ~/wiringPi ] 
then 
	sudo echo "wiringPi exists. Deleting..."
	sudo rm -rf wiringPi || (echo "WiringPi Delete Failed. Aborting..." && exit 1) 
fi

#Install wiringPi
git clone git://git.drogon.net/wiringPi || (echo "WiringPi Clone Failed. Aborting..." && exit 1) 

#build wiringPi
cd wiringPi
#sudo ./build ||  (echo "Building wiringPi Failed. Aborting..." && exit 1)
cd ~

#installing Apache
sudo apt-get install -y apache2 apache2-utils || (echo "Apache Install Failed. Aborting..." && exit 1)
sudo chown -R www-data:www-data /var/www/ || (echo "Setting Ownership Failed. Aborting..." && exit 1)
sudo chmod g+rw -R /var/www/ || (echo "Setting Permissions Failed. Aborting..." && exit 1)
sudo chmod g+s -R /var/www/ || (echo "Setting Permissions Failed. Aborting..." && exit 1)
sudo usermod -a -G www-data pi || (echo "Adding pi to www-data Failed. Aborting..." && exit 1)

#installing mysql
echo "mysql-server mysql-server/root_password password $PASSWORD" | sudo debconf-set-selections
echo "mysql-server mysql-server/root_password_again password $PASSWORD" | sudo debconf-set-selections
sudo apt-get install -y mysql-server mysql-client || (echo "MySQL Install Failed. Aborting..." && exit 1)

#install phpmyadmin
echo "phpmyadmin phpmyadmin/dbconfig-install boolean true" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/app-password-confirm password $PASSWORD" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/admin-pass password $PASSWORD" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/app-pass password $PASSWORD" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2" | sudo debconf-set-selections
sudo apt-get install -y phpmyadmin || (echo "PHPMyAdmin Install Failed. Aborting..." && exit 1)

#create database and tables
#TODO: create database and tables

#check if pins exists and delete it, TODO: pull instead of delete
if [ -d ~/pins ] 
then 
	sudo echo "pins exists. Deleting..."
	sudo rm -rf pins || (echo "Pins Delete Failed. Aborting..." && exit 1) 
fi

#clone pins repo
git clone https://github.com/stevielam/pins.git || (echo "Pins Clone Failed. Aborting..." && exit 1)

#configure cron job
#todo: check if cron job exists, if not then create one for the init and poll scripts

exit 0
