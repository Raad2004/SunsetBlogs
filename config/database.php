<?php
$host = 'localhost';
$dbname = 'sunset_blogs';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false, // Ensures real prepared statements
        PDO::MYSQL_ATTR_FOUND_ROWS => true
    ]);
} catch(PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    die("Unable to connect to the database. Please try again later.");
}
?> 
