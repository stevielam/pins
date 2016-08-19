<?php
error_reporting(E_ALL); // (debug) Report all errors

require_once("config.php");//configuration file

//initialize the pin results (This code should only run at first boot)
$off = REVERSE_BIAS ? 1: 0; //TODO: see if REVERSE BIAS can be derived by each relay from DB instead of all at once
for($i=0; $i<NUMBER_OF_RELAYS; $i++){
	system(GPIO_PATH. "/gpio mode $i out");
	system(GPIO_PATH. "/gpio write $i ". $off);
}


/*
#initializes ALL relays to false
@reboot /usr/bin/php /home/pi/pins/cron/init.php >> /home/pi/pins/cron/init_output 2> /home/pi/pins/cron/init_errors
*/

?>