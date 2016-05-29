<?php

error_reporting(E_ALL); // (debug) Report all errors
require_once("config.php");//configuration file

/* try{
	// Create connection
	$conn = mysqli_connect(SERVER,DB_USER,DB_PASS,DB);

	// Check connection
	if(mysqli_connect_errno()){
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		
		die();
	}
	
	
	
}
catch(Exception $e){

} */




$conn = new mysqli(SERVER, DB_USER, DB_PASS) or die("Connection failed: ". $conn->connect_error);


$day_of_week = strtolower(date('l')); //get current day
//echo $day_of_week;


//query WEEKLY schedules first
$sql = "
SELECT * 
FROM  `auto` 
WHERE master_enable = '1' 
AND ". $day_of_week. "_en = '1' 
AND start_time <= CURTIME( ) 
AND end_time > CURTIME( ) 
";

$conn->select_db(DB);
if (!$result = $conn->query($sql)){
  die("Error: ". mysqli_error($conn));
}


//intialize the pins array as false so that any pin number NOT set true by the query will be turned OFF by the watchdog
for($i=0; $i<NUMBER_OF_RELAYS; $i++){
	$pins[$i] = false;
}

//echo $result->num_rows; //debug
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
		$pins[$row['relay']] = true;
    }
	$result->close();
}

mysqli_close($conn);


//If REVERSE_BIAS, then write 0 to turn relay ON, write 1 to turn relay OFF
//If not REVERSE_BIAS, then write 1 to turn relay ON, write 0 to turn relay OFF
$on = REVERSE_BIAS ? 0 : 1;
$off = REVERSE_BIAS ? 1: 0;

for($i=0; $i<NUMBER_OF_RELAYS; $i++){
	if($pins[$i]){
		system(GPIO_PATH. "/gpio write $i ". $on);
	}else{
		system(GPIO_PATH. "/gpio write $i ". $off);
	}
}

?>