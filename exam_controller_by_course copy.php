<?php

declare(strict_types=1);

require_once 'conn.php';
require_once 'exam_model_by_course.php';

session_start();

/**
 * Redirect to admin login if the user is not logged in as admin.
 */
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: admin_login.php");
    exit;
}

// Initialize model
$examModel = new ExamModelByCourse($conn);

// Fetch all courses
$courses = $examModel->getCourses();

// Initialize exams array
$exams = [];

// If form is submitted with selected course
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['courseId']) && $_POST['courseId'] !== '') {
    $courseId = (int) $_POST['courseId'];
    $exams = $examModel->getExamsByCourse($courseId);
}

unset($conn);

// Pass data to view
require 'view_results_by_course.php';
