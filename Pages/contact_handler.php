<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => true, 'message' => 'Please log in to submit an inquiry']);
    exit;
}

// Get POST data
$firstname = $_POST['first-name'] ?? '';
$lastname = $_POST['last-name'] ?? '';
$context = $_POST['question'] ?? '';
$user_id = $_SESSION['user_id'];

try {
    // Prepare the SQL statement
    $query = "INSERT INTO inquiry (user_id, firstname, lastname, context) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    // Execute with the form data
    $stmt->execute([$user_id, $firstname, $lastname, $context]);
    
    // Return success response
    echo json_encode(['success' => true, 'message' => 'Your inquiry has been submitted successfully']);
} catch (Exception $e) {
    // Log the error
    error_log("Error in contact_handler.php: " . $e->getMessage());
    
    // Return error response
    echo json_encode(['error' => true, 'message' => 'Failed to submit inquiry. Please try again later.']);
}
?> 