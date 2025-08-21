<?php

declare(strict_types=1);

require_once '../model/student_forgot_password_model.php';

/**
 * Handles the password recovery process for students.
 *
 * This controller manages two main actions:
 * 1. Verifying student identity through email, date of birth, and security question.
 * 2. Resetting the password after successful verification.
 * 
 * PHP version 8.0+
 *
 * @category  Authentication
 * @package   VirtualExam
 * @author    Emtiaz
 * @link      https://github.com/EmtiazMahamud/CSE327-VirtualExam
 */

// Initialize form data
$formData = [
    'email'             => '',
    'dob'               => '',
    'security_question' => '',
    'new_password'      => '',
    'error'             => '',
];

/**
 * Main handler for forgot password requests.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Handle password reset submission
    if (isset($_POST['resetPassword'])) {
        $student = verifySecurityAnswer($conn, $_POST);
        
        if ($student) {
            updatePassword($conn, $_POST['email'], $_POST['new_password']);
            header('Location: ../view/student_login_view.php');
            exit;
        }
        
        $formData['error'] = 'Verification failed';
        extract($formData);
        include '../view/student_forgot_password_view.php';
        exit;
    }

    // Handle student identity verification (before password reset)
    $formData = verifyStudentIdentity($conn, $_POST);
    extract($formData);
    include '../view/student_forgot_password_view.php';
    exit;

} else {
    // Default: show the forgot password form
    include '../view/student_forgot_password_view.php';
}

