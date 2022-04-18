<?php
session_start();
// Check if the user is already logged in, if yes then redirect him to upload page
if(isset($_SESSION["loggedin"]) === TRUE){
    header("location: upload.php");
    exit;
} else {
  header("location: login.php");
}
?>