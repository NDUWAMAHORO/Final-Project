<?php

$host   = 'localhost';
$user   = 'root';
$pass   = '';
$dbname = 'esmart';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// FIXED LINE
$conn->set_charset("utf8");

?>