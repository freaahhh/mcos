<?php
// Force error reporting on for debugging - remove this later for security!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. URL Constant
if (!defined('SITEURL')) {
    define('SITEURL', 'http://localhost/mcos/');
}

// 2. Oracle Database Credentials
$db_username = "mcos";
$db_password = "mcos123";
$db_connection_string = "localhost/FREEPDB1";

// 3. Establish Oracle Connection
$conn = oci_connect($db_username, $db_password, $db_connection_string);

// 4. Check Connection
if (!$conn) {
    $e = oci_error();
    // Use die() instead of trigger_error to ensure the message actually prints on screen
    die("Oracle Connection Failed: " . $e['message']);
}
