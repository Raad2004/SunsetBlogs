<?php
// Error and content setup
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Set session cookie parameters for better security
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Start session at the beginning
session_start();

// Load PDO connection
require_once '../config/database.php';

function respond($success, $data = []) {
    error_log('Response: ' . json_encode(array_merge(['success' => $success], $data)));
    echo json_encode(array_merge(['success' => $success], $data));
    exit;
}

try {
    // Parse JSON input
    $input = file_get_contents('php://input');
    error_log('Received input: ' . $input);
    
    if (!$input) {
        respond(false, ['message' => 'No input received']);
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        respond(false, ['message' => 'Invalid JSON']);
    }

    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (!$email || !$password) {
        respond(false, ['message' => 'Please fill in all fields']);
    }

    // Fetch user by email
    $stmt = $pdo->prepare("SELECT user_id, username, password_hash FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        respond(false, ['message' => 'Invalid email or password']);
    }

    // Check if user is admin
    $isAdmin = ($user['username'] === 'Admin');
    
    // Clear any existing session data
    session_unset();
    
    // Set session variables
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = $isAdmin;
    $_SESSION['last_activity'] = time();

    // Debug information
    error_log("Login successful - Username: {$user['username']}, isAdmin: " . ($isAdmin ? 'true' : 'false'));
    error_log("Session ID: " . session_id());
    error_log("Session data: " . print_r($_SESSION, true));

    respond(true, [
        'username' => $user['username'],
        'isAdmin' => $isAdmin,
        'sessionId' => session_id() // Send session ID for debugging
    ]);

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    respond(false, ['message' => 'Server error: ' . $e->getMessage()]);
}
