<?php
// cron.php running every X seconds (set by POLL_DELAY in config.php)
// you need to setup this cron run every minute using `crontab` (crontab -e)

error_reporting(E_ALL); // (debug) Report all errors
require_once("config.php");//configuration file

$script_expire = time() + 60; //the time at which the script will expire (cron will start a new one)

//Try to connect to DB
$c=mysqli_connect(SERVER,DB_USER,DB_PASS,DB);

// Check connection
if (mysqli_connect_errno()){
	die(date("M d, Y h:i:sa"). " - Failed to connect to MySQL: " . mysqli_connect_error());
}


while(time() < $script_expire){
	sleep(POLL_DELAY);
	
	//set pins array to false
	for($i=0; $i<NUMBER_OF_RELAYS; $i++){
		$pins[$i] = false;
	}
	
	//Make sure pi's time is not reset
	if(date('Y') < 2000){
		//if the year is before 2000, the time has been reset and the script should be aborted
		die("Time Error: System Time is wrong (System Time: ". date("M d, Y h:i:sa"). ") Aborting Script");
	}
	
	
	//Ping DB, reconnect if needed
	if (mysqli_ping($c)){
		//query auto
			$day_of_week = strtolower(date('l')); //get current day
			
			//query AUTO schedules first
			$q_auto = "
				SELECT * 
				FROM  `auto` 
				WHERE master_enable = '1' 
				AND ". $day_of_week. "_en = '1' 
				AND start_time <= CURTIME( ) 
				AND end_time > CURTIME( ) 
			";
			
			//run query
			if(!$result=mysqli_query($c,$q_auto)){
				die(date("M d, Y h:i:sa"). " - Auto Query Error: ". mysqli_error($c));
			}else{
				$rows = mysqli_fetch_assoc($result);
			}
			
			//update the pins array
			if (mysqli_num_rows($result) > 0) {
				foreach($rows as $row){
					$pins[$row['relay']] = true;
				}
			}
			
			// Free result set
			mysqli_free_result($result);
			
			//construct manual query
			$q_man = "
				SELECT * 
				FROM `manual` 
				WHERE end_time > CURTIME( )
			";
			
			//run query
			if(!$result=mysqli_query($c,$q_auto)){
			die(date("M d, Y h:i:sa"). " - Manual Query Error: ". mysqli_error($c));
			}else{
				$rows = mysqli_fetch_assoc($result);
			}
			
			//update the pins array
			if (mysqli_num_rows($result) > 0) {
				foreach($rows as $row){
					switch ( trim($row['mode']) ) {
						case 'on': 
							$pins[$row['relay']] = true;
							break;
						case 'off':
							$pins[$row['relay']] = false;
							break;
						default:
							break;
					}
				}
			}

			// Free result set
			mysqli_free_result($result);
			
			
			//construct ovveride ON query
			$q_override = "
				SELECT *
				FROM `relays` 
			";
			
			//run query
			if(!$result=mysqli_query($c,$q_auto)){
			die(date("M d, Y h:i:sa"). " - Override Query Error: ". mysqli_error($c));
			}else{
				$rows = mysqli_fetch_assoc($result);
			}
			
			if (mysqli_num_rows($result) > 0) {
				foreach($rows as $row){
					switch ( trim($row['mode']) ) {
						case 'on': 
							$pins[$row['number']] = true;
							break;
						case 'off':
							$pins[$row['number']] = false;
							break;
						default:
							break;
					}
				}
			}
			
	}else{
		die(date("M d, Y h:i:sa"). " - Failed to ping MySQL Server: " . mysqli_connect_error());
	}
		
	//If REVERSE_BIAS, then write 0 to turn relay ON, write 1 to turn relay OFF
	//If not REVERSE_BIAS, then write 1 to turn relay ON, write 0 to turn relay OFF
	$on 	= REVERSE_BIAS ? 0 : 1;
	$off 	= REVERSE_BIAS ? 1 : 0;

	for($i=0; $i<NUMBER_OF_RELAYS; $i++){
		if($pins[$i]){
			system(GPIO_PATH. "/gpio write $i ". $on);
		}else{
			system(GPIO_PATH. "/gpio write $i ". $off);
		}
	}
}

//close the connection
mysqli_close($c);


/*
#checks every minute to see if relay needs to be started from schedule
* * * * * /usr/bin/php /home/pi/pins/cron/poll.php >> /home/pi/pins/cron/poll_output 2> /home/pi/pins/cron/poll_errors
*/
?>