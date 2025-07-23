<?php
/**
 * StudyLink Configuration File
 * Handles session management and defines base paths
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set session parameters for better security
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    
    session_start();
}

// Define base paths
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('STORAGE_PATH', BASE_PATH . '/storage');

// Define file paths for data storage
define('USERS_FILE', STORAGE_PATH . '/users.json');
define('CONTACTS_FILE', STORAGE_PATH . '/contacts.json');

// Application settings
define('APP_NAME', 'StudyLink');
define('APP_VERSION', '1.0.0');

// Initialize data files if they don't exist
if (!file_exists(USERS_FILE)) {
    file_put_contents(USERS_FILE, json_encode([], JSON_PRETTY_PRINT));
}

if (!file_exists(CONTACTS_FILE)) {
    file_put_contents(CONTACTS_FILE, json_encode([], JSON_PRETTY_PRINT));
}

// Include helper functions
require_once INCLUDES_PATH . '/functions.php';
?>
