<?php
// Logout script - clears session and cookies, redirects to login page

// Start session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Unset all session variables
$_SESSION = array();

// Delete the session cookie if it exists
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Delete the ephyto_uid cookie if it exists
if (isset($_COOKIE['ephyto_uid'])) {
    setcookie('ephyto_uid', '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page (index.php)
header("Location: index.php");
exit();
?>
