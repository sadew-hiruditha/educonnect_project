<?php
/**
 * EduConnect Helper Functions
 * Contains utility functions used throughout the application
 */

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirect to specified page
 * @param string $page
 */
function redirectTo($page) {
    header("Location: $page");
    exit();
}

/**
 * Sanitize input data
 * @param string $data
 * @return string
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate email format
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Load users from JSON file
 * @return array
 */
function loadUsers() {
    if (!file_exists(USERS_FILE)) {
        return [];
    }
    $data = file_get_contents(USERS_FILE);
    return json_decode($data, true) ?: [];
}

/**
 * Save users to JSON file
 * @param array $users
 * @return bool
 */
function saveUsers($users) {
    return file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Load contacts from JSON file
 * @return array
 */
function loadContacts() {
    if (!file_exists(CONTACTS_FILE)) {
        return [];
    }
    $data = file_get_contents(CONTACTS_FILE);
    return json_decode($data, true) ?: [];
}

/**
 * Save contacts to JSON file
 * @param array $contacts
 * @return bool
 */
function saveContacts($contacts) {
    return file_put_contents(CONTACTS_FILE, json_encode($contacts, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Check if user exists by email
 * @param string $email
 * @return bool
 */
function userExists($email) {
    $users = loadUsers();
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return true;
        }
    }
    return false;
}

/**
 * Get user by email
 * @param string $email
 * @return array|null
 */
function getUserByEmail($email) {
    $users = loadUsers();
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return $user;
        }
    }
    return null;
}

/**
 * Register new user
 * @param string $name
 * @param string $email
 * @param string $password
 * @return bool
 */
function registerUser($name, $email, $password) {
    if (userExists($email)) {
        return false;
    }
    
    $users = loadUsers();
    $newUser = [
        'id' => uniqid(),
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $users[] = $newUser;
    return saveUsers($users);
}

/**
 * Verify user login
 * @param string $email
 * @param string $password
 * @return array|false
 */
function verifyLogin($email, $password) {
    $user = getUserByEmail($email);
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

/**
 * Save contact form submission
 * @param string $name
 * @param string $email
 * @param string $subject
 * @param string $message
 * @param int $rating
 * @return bool
 */
function saveContact($name, $email, $subject, $message, $rating) {
    $contacts = loadContacts();
    $newContact = [
        'id' => uniqid(),
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'rating' => $rating,
        'submitted_at' => date('Y-m-d H:i:s')
    ];
    
    $contacts[] = $newContact;
    return saveContacts($contacts);
}

/**
 * Get current page name for navigation highlighting
 * @return string
 */
function getCurrentPage() {
    return basename($_SERVER['PHP_SELF'], '.php');
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
        // Generate a 32-byte random token and convert to hexadecimal
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    
    if (empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}
?>
