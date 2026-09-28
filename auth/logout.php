<?php
/**
 * User Logout Page & Logic
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

require_once __DIR__ . '/../includes/functions.php';

// Unset all session values
$_SESSION = [];

// Delete session cookie if configured
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Start fresh session just to flash logged out message
session_start();
set_flash_message('info', 'You have been signed out safely. See you on your next adventure!');

header('Location: login.php');
exit;
