<?php 
define('UPLOAD_FOLDER', 'uploads/');    #sub folder, relative to base directory, where images will be stored
define('DB_SERVER', 'localhost');       #database server
define('DB_USERNAME', '<database user name');       #database user name
define('DB_PASSWORD', '<database password>');     #database password
define('DB_NAME', '<database name');   #database name

$targetWidth = 160; #image thumbnail width, in pixels
define('THUMB_WIDTH', 160);

#the timezone of the event. Valid list is here: https://www.php.net/manual/en/timezones.php
define('TIME_ZONE', 'America/Bahia_Banderas');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
