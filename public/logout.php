<?php
/**
 * EduConnect - Logout Page
 * Handles user logout and session destruction
 */

require_once '../includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirectTo('login.php');
}

// Destroy session and logout user
session_destroy();

// Redirect to login page with logout message
redirectTo('login.php?logout=1');
?>
