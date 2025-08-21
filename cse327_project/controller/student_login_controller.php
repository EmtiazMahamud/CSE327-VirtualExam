
<?php
/**
 * Student Login Controller
 * 
 * This controller manages student authentication, session handling, and
 * secure login processes. It implements security best practices and
 * proper session management.
 *
 * @package VirtualExam
 * @subpackage Controller
 * @security This file implements CSRF protection and session security
 */

require_once '../model/student_login_model.php';

// Start session securely
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Lax'
]);
// Initialize variables
$error = '';
$username = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Security validation failed. Please try again.';
    } else {
        // Sanitize and validate input
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'];

        // Validate required fields
        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            // Attempt authentication
            $studentId = authenticateStudent($conn, $username, $password);
            
            if ($studentId) {
                // Set session variables securely
                $_SESSION['student_id'] = $studentId;
                $_SESSION['last_activity'] = time();
                
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                // Redirect to dashboard
                header('Location: ../view/student_enrolled_courses_view.php');
                exit;
            } else {
                $error = 'Invalid username or password. Please try again.';
            }
        }
    }
}

// Generate CSRF token for form
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Display login form
include '../view/student_login_view.php';


