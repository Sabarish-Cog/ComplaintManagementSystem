<?php
// Bootstrap file for PHPUnit tests

// Start session for testing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the application bootstrap
require_once __DIR__ . '/../app/bootstrap.php';

// Test configuration
define('TEST_DB_HOST', 'localhost');
define('TEST_DB_USER', 'root');
define('TEST_DB_PASS', 'root');
define('TEST_DB_NAME', 'complaint_db_test'); // Use separate test database

// Helper functions for testing
function createTestSession($userId = 1, $userName = 'Test User', $userEmail = 'test@example.com') {
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $userName;
    $_SESSION['user_email'] = $userEmail;
}

function clearTestSession() {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
}
