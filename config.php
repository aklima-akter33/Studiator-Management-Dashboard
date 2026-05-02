<?php
// config.php
session_start();

// Change these if your MySQL root user has password
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = ''; // default XAMPP: empty
$db_name = 'studiator';

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_errno) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// set charset
$mysqli->set_charset("utf8mb4");
?>
