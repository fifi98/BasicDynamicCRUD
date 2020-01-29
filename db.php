<?php

$hostname="";
$username="";
$password="";
$database="";

$conn=new mysqli($hostname, $username, $password, $database);

if($conn->connect_error) die("Cannot connect to the database!");

?>
