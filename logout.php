<?php
/**
 * Logout Page
 * CS 215 - Web & Database Programming
 * Winter 2025
 */

// Start session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();