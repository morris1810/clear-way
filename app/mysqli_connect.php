<?php
error_reporting(E_ERROR | E_PARSE); // Hiding the warning message

DEFINE ('DOMAIN', 'http://' . $_SERVER['SERVER_NAME'] . '/WEB2202/ClearWay'); 
DEFINE ('VERIFICATION_KEY','jhergbljkeqfbvljkaeflkjaeljkbvfliueghewo87ert'); 

// Set the database access information as constants:
DEFINE ('DB_HOST','localhost');
DEFINE ('DB_USER','root');
DEFINE ('DB_PASSWORD','');
DEFINE ('DB_NAME', 'clearway');

// Make the connection:
$dbc = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: ' . mysqli_connect_error() );

// Set the encoding...
mysqli_set_charset($dbc, 'utf8');

?>