
<?php
require_once '../model/student_login_model.php';

/**
 * Handles student login authentication and session management.
 *
 * @author Your Name
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $student_id = authenticateStudent($conn, $username, $password);
    if ($student_id) {
        session_start();
        $_SESSION['student_id'] = $student_id;
        header('Location: ../view/student_enrolled_courses_view.php');
        exit;
    }
    $error = 'Invalid credentials';
    include '../view/student_login_view.php';
} else {
    include '../view/student_login_view.php';
}
