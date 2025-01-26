<?php
$databaseHost = 'localhost';
$databaseName = 'vlan_management'; // Updated database name
$databaseUsername = 'root';
$databasePassword = '';

// Open a new connection to the MySQL server
$mysqli = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);

// Check connection
if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
