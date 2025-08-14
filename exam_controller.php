<?php

/**
 * ExamController handles the flow for viewing exam results.
 */

require_once '../conn.php';
require_once '../models/exam_model.php';

/**
 * Initialize session.
 */
session_start();

/**
 * Check if admin is logged in, redirect to login page if not logged in.
 */
if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    header('Location: admin_login.php');
    exit;
}

/**
 * Check if examId parameter is set in the URL.
 */
if (!isset($_GET['examId']) || empty($_GET['examId'])) {
    header('Location: error.php');
    exit;
}

/**
 * @var int $examId The ID of the exam to fetch.
 */
$examId = $_GET['examId'];

/**
 * Initialize model and fetch data.
 */
$examModel = new ExamModel($conn);
$examDetails = $examModel->getExamDetails($examId);
$results = $examModel->getExamResults($examId);

/**
 * Close database connection.
 */
unset($conn);

/**
 * Load the view.
 */
require '../views/view_results_exam.php';
