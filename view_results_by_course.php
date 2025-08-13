<?php

declare(strict_types=1);

require_once 'conn.php';

session_start();

/**
 * Redirect to admin login if the user is not logged in as admin.
 */
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: admin_login.php");
    exit;
}

/**
 * Fetch all courses from the database.
 *
 * @param PDO $conn Database connection instance.
 *
 * @return array<int, array<string, mixed>> List of courses with IDs and names.
 */
function getCourses(PDO $conn): array
{
    $sql = "SELECT course_id, course_name FROM courses";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch all exams for a given course.
 *
 * @param PDO $conn      Database connection instance.
 * @param int $courseId  ID of the selected course.
 *
 * @return array<int, array<string, mixed>> List of exams with IDs and names.
 */
function getExamsByCourse(PDO $conn, int $courseId): array
{
    $sql = "SELECT exam_id, exam_name FROM exams WHERE course_id = :course_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch list of courses
$courses = getCourses($conn);

// Initialize exams array
$exams = [];

// If form is submitted with selected course
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['course_id']) && $_POST['course_id'] !== '') {
    $courseId = (int) $_POST['course_id'];
    $exams = getExamsByCourse($conn, $courseId);
}

unset($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Results by Exams</title>
    <link rel="stylesheet" href="style_results.css">
</head>
<body>
    <div id="sidebar">
        <?php include 'slidebar.php'; ?>
    </div>
    <div id="content" class="wrapper">
        <h1>View Results by Exams</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="course_id">Select Course:</label>
            <select name="course_id" id="course_id" onchange="this.form.submit()">
                <option value="">Select Course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo (int) $course['course_id']; ?>">
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php if (!empty($exams)): ?>
            <h2>Exams</h2>
            <ul>
                <?php foreach ($exams as $exam): ?>
                    <li>
                        <?php echo htmlspecialchars($exam['exam_name']); ?>
                        <a href="view_results_by_exam.php?exam_id=<?php echo (int) $exam['exam_id']; ?>">
                            View Results
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
