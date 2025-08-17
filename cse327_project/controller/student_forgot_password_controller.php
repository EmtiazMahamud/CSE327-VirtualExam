
<?php
require_once '../model/student_forgot_password_model.php';

/**
 * Handles the password recovery process for students.
 *
 * @author Your Name
 */
$formData = [
    'email' => '',
    'dob' => '',
    'security_question' => '',
    'new_password' => '',
    'error' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    } else {
        $formData = verifyStudentIdentity($conn, $_POST);
        extract($formData);
        include '../view/student_forgot_password_view.php';
    }
} else {
    include '../view/student_forgot_password_view.php';
}
