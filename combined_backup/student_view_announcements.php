<?php
// Start session before any output
session_start();

// Include the database connection
require_once 'conn.php';

// Check if student is logged in
if (!isset($_SESSION["student_id"])) {
    header("Location: student_login.php");
    exit;
}

// Retrieve student ID
$student_id = $_SESSION["student_id"];

// Check if course_id is provided in URL
if (!isset($_GET['course_id'])) {
    die('<div class="error">Course ID not provided.</div>');
}

$course_id = $_GET['course_id'];

// Check if the student is enrolled in the course
$sql_check_enrollment = "SELECT * FROM Enrollments WHERE student_id = :student_id AND course_id = :course_id";
$stmt_check_enrollment = $conn->prepare($sql_check_enrollment);
$stmt_check_enrollment->bindParam(':student_id', $student_id);
$stmt_check_enrollment->bindParam(':course_id', $course_id);
$stmt_check_enrollment->execute();

if ($stmt_check_enrollment->rowCount() == 0) {
    die('<div class="error">You are not enrolled in this course.</div>');
}

// Retrieve announcements
$sql_announcements = "SELECT announcement_title, announcement_content, created_at FROM Announcements WHERE course_id = :course_id ORDER BY created_at DESC";
$stmt_announcements = $conn->prepare($sql_announcements);
$stmt_announcements->bindParam(':course_id', $course_id);
$stmt_announcements->execute();

$announcements = $stmt_announcements->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Announcements</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'floating_profile_button.php'; ?>
<div id="sidebar">
    <?php include 'student_slidebar.php'; ?>
</div>
<div id="content" class="wrapper">
    <?php if (!empty($announcements)): ?>
        <h2>Announcements</h2>
        <?php foreach ($announcements as $announcement): ?>
            <div class="announcement">
                <h3><?= htmlspecialchars($announcement['announcement_title']); ?></h3>
                <p><?= nl2br(htmlspecialchars($announcement['announcement_content'])); ?></p>
                <p><em>Posted on: <?= htmlspecialchars($announcement['created_at']); ?></em></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="info">No announcements available for this course.</div>
    <?php endif; ?>
</div>
<?php include 'clock.php'; ?>
</body>
</html>
<?php unset($conn); ?>
