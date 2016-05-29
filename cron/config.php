<?php

error_reporting(E_ALL); // (debug) Report all errors
define("GPIO_PATH", "/usr/local/bin");

define("NUMBER_OF_RELAYS", 8); //this will keep track of how many relays there are on your relay board

define("REVERSE_BIAS", true); //this will keep track if the board is reverse bias of not. The Sainsmart relay boards are usually reverse bias meaning that you must send a logic true to turn them OFF and a logic false to turn them ON.

define("SERVER", "localhost"); //the MySQL server IP or hostname
define("DB", "pins"); //this is the name of your MySQL database
define("DB_USER", "root"); //this is your MySQL user for the database
define("DB_PASS", "test"); //this is the MySQL root password 

define("POLL_DELAY", 10); //the delay (in seconds) before polling the database (lower is faster but less stable)

?>