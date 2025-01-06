<?php
session_start();

// Destroy the session
session_destroy();

// Clear session variables
$_SESSION = array();

// Delete session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Redirect to login page
header("Location: login.php");
exit(); 