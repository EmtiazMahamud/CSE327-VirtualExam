
<?php
require_once '../model/student_registration_model.php';

/**
 * Handles new student registration and course retrieval.
 *
 * @author Your Name
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = registerStudent($conn, $_POST);
    if ($success) {
        header('Location: ../view/student_login_view.php');
        exit;
    }
    $error = 'Registration failed';
    $courses = getCourses($conn);
    include '../view/student_registration_view.php';
} else {
    $courses = getCourses($conn);
    include '../view/student_registration_view.php';
}
