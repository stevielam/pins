#!/bin/bash

#sudo curl https://raw.githubusercontent.com/stevielam/pins/master/install.sh -o runme.sh && sudo bash runme.sh && sudo rm runme.sh

set -e


PASSWORD=test
TIMEZONE="America/Los_Angeles"


#password setup
read -s -p 'Choose Password for MySQL root and PHPMyAdmin root: ' pw1
printf "\n"
read -s -p 'Please repeat the password: ' pw2
printf "\n"

# Check both passwords match
if [ $pw1 != $pw2 ]; then
    echo "Passwords do not match. Aborting" && exit 1    
else
    PASSWORD=$pw1
fi


#set the timezone
#TODO: ask for timezone
sudo echo $TIMEZONE > sudo /etc/timezone


#update and upgrade
(sudo apt-get update && sudo apt-get -y upgrade) || (echo "Upgrade Failed. Aborting..." && exit 1)  


#install git
sudo apt-get install -y git-core || (echo "Git Install Failed. Aborting..." && exit 1)

#check if wiringPi exists and delete it, TODO: pull instead of delete
if [ -d /home/pi/wiringPi ] 
then 
	sudo echo "wiringPi exists. Deleting..."
	sudo rm -rf wiringPi || (echo "WiringPi Delete Failed. Aborting..." && exit 1) 
fi

#Install wiringPi
git clone git://git.drogon.net/wiringPi || (echo "WiringPi Clone Failed. Aborting..." && exit 1) 

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
sudo mysql -uroot -p$PASSWORD -e "CREATE DATABASE IF NOT EXISTS pins" || (echo "Creating Database Failed. Aborting..." && exit 1)

#create "auto" table
sudo mysql -uroot -p$PASSWORD -e 'CREATE TABLE IF NOT EXISTS `pins`.`auto` ( `id` int(11) NOT NULL AUTO_INCREMENT, `master_enable` tinyint(1) NOT NULL, `monday_en` tinyint(1) NOT NULL, `tuesday_en` tinyint(1) NOT NULL, `wednesday_en` tinyint(1) NOT NULL, `thursday_en` tinyint(1) NOT NULL, `friday_en` tinyint(1) NOT NULL, `saturday_en` tinyint(1) NOT NULL, `sunday_en` tinyint(1) NOT NULL, `start_time` time NOT NULL, `end_time` time NOT NULL, `relay` int(11) NOT NULL, `notes` varchar(255), PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1;' || (echo "Create 'auto' table Failed. Aborting..." && exit 1) 

#create "manual" table
sudo mysql -uroot -p$PASSWORD -e 'CREATE TABLE IF NOT EXISTS `pins`.`manual` ( `id` int(11) NOT NULL AUTO_INCREMENT, `mode` varchar(20) NOT NULL, `end_time` time NOT NULL, `relay` int(11) NOT NULL, `notes` varchar(255), PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1;' || (echo "Create 'manual' table Failed. Aborting..." && exit 1) 


#check if pins exists and delete it, TODO: pull instead of delete
if [ -d ~/pins ] 
then 
	sudo echo "pins exists. Deleting..."
	sudo rm -rf pins || (echo "Pins Delete Failed. Aborting..." && exit 1) 
fi

#clone pins repo
git clone https://github.com/stevielam/pins.git || (echo "Pins Clone Failed. Aborting..." && exit 1)

#update config.php with PASSWORD
#TODO: update config.php with PASSWORD
sudo sed -i "/DB_PASS/ c\define(\"DB_PASS\", \"$PASSWORD\"); ////this is the MySQL root password /" ~/pins/cron/config.php


#configure cron job
#check if cron jobs exists, if so delete the jobs, if not then create one for the init and poll scripts
if !(crontab -l 2>/dev/null) then
	crontab -
fi

crontab -l 2>/dev/null | grep -v init.php | crontab -
crontab -l 2>/dev/null | grep -v poll.php | crontab -

(
crontab -l 
cat >> 'EOF'
#initializes ALL relays to false
@reboot /usr/bin/php /home/pi/pins/cron/init.php >> /home/pi/pins/cron/init_output 2> /home/pi/pins/cron/init_errors

#checks every minute to see if relay needs to be started from schedule
* * * * * /usr/bin/php /home/pi/pins/cron/poll.php >> /home/pi/pins/cron/poll_output 2> /home/pi/pins/cron/poll_errors

) | crontab -

exit 0
