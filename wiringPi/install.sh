#!/bin/bash

#check if wiringPi exists and delete it, TODO: pull instead of delete
if [ -d /home/pi/wiringPi ] 
then 
	sudo echo "wiringPi exists. Deleting..."
	sudo rm -R /home/pi/wiringPi || (echo "WiringPi Delete Failed. Aborting..." && exit 1) 
fi

#Install wiringPi
git clone git://git.drogon.net/wiringPi /home/pi/wiringPi || (echo "WiringPi Clone Failed. Aborting..." && exit 1) 

#build wiringPi
cd /home/pi/wiringPi
sudo ./build ||  (echo "Building wiringPi Failed. Aborting..." && exit 1)
cd /home/pi

exit 0