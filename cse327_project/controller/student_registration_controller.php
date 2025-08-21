
<?php
/**
 * Student Registration Controller
 * 
 * This controller handles the student registration process and course management.
 * It processes form submissions and manages the flow between the registration
 * view and model.
 *
 * @package VirtualExam
 * @subpackage Controller
 * 
 */

require_once '../model/student_registration_model.php';
// Initialize variables
$error = '';
$courses = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and process registration
    $success = registerStudent($conn, $_POST);
    
    if ($success) {
        // Redirect to login page on successful registration
        header('Location: ../view/student_login_view.php');
        exit;
    }
    
    // Set error message if registration fails
    $error = 'Registration failed. Please check your information and try again.';
}

// Get available courses for the registration form
$courses = getCourses($conn);

// Display the registration form
include '../view/student_registration_view.php';
