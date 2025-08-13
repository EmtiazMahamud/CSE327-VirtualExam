<?php
/**
 * Admin View Announcements Page
 *
 * This page allows an admin to view all announcements for a selected course,
 * delete announcements, and filter announcements by course.
 *
 * PHP version 8+
 *
 * @category Admin_Portal
 * @package  Course_Announcements
 * @author   
 * @license  MIT License
 * @link     
 */

session_start();
require_once 'conn.php';

/**
 * Check if admin is logged in
 */
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit;
}

/**
 * Fetch all courses for the dropdown
 *
 * @return PDOStatement
 */
$coursesResult = $conn->query("SELECT * FROM courses ORDER BY course_name ASC");

/**
 * Delete an announcement if the form is submitted
 */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_announcement"])) {
    $announcement_id = $_POST["announcement_id"] ?? null;

    if ($announcement_id) {
        $sql = "DELETE FROM announcements WHERE announcement_id = :announcement_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':announcement_id', $announcement_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Announcement deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to delete announcement.";
        }

        header("Location: view_announcements.php" . (isset($_GET['course_id']) ? "?course_id=" . intval($_GET['course_id']) : ""));
        exit;
    }
}

/**
 * Fetch announcements for the selected course
 *
 * @var array $announcements
 */
$announcements = [];
if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    $sql = "SELECT * FROM announcements WHERE course_id = :course_id ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Announcements - Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .announcement-card {
            border: 1px solid #ccc;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .success-message { color: green; margin-bottom: 15px; }
        .error-message { color: red; margin-bottom: 15px; }
        .announcement-cards { display: flex; flex-direction: column; }
    </style>
    <script>
        /**
         * Confirm deletion of announcement
         *
         * @returns {boolean} True if user confirms
         */
        function confirmDelete() {
            return confirm("Are you sure you want to delete this announcement?");
        }
    </script>
</head>
<body>

<div id="sidebar">
    <?php include 'slidebar.php'; ?>
</div>

<div id="content" class="wrapper">
    <h2>View Announcements</h2>

    <!-- Display success or error message -->
    <?php if (!empty($_SESSION['success_message'])): ?>
        <div class="success-message"><?= htmlspecialchars($_SESSION['success_message']); ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error_message'])): ?>
        <div class="error-message"><?= htmlspecialchars($_SESSION['error_message']); ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Course Selection Dropdown -->
    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
        <label for="course_id">Select Course:</label>
        <select id="course_id" name="course_id">
            <option value="">-- Select Course --</option>
            <?php while ($row = $coursesResult->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?= intval($row['course_id']); ?>" <?= (isset($_GET['course_id']) && $_GET['course_id'] == $row['course_id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($row['course_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Filter</button>
    </form>

    <!-- Display Announcements -->
    <h3>Existing Announcements</h3>
    <?php if (!empty($announcements)): ?>
        <div class="announcement-cards">
            <?php foreach ($announcements as $announcement): ?>
                <div class="announcement-card">
                    <h4><?= htmlspecialchars($announcement['announcement_title']); ?></h4>
                    <p><?= nl2br(htmlspecialchars($announcement['announcement_content'])); ?></p>
                    <p><em>Posted on: <?= htmlspecialchars($announcement['created_at']); ?></em></p>
                    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return confirmDelete();">
                        <input type="hidden" name="announcement_id" value="<?= intval($announcement['announcement_id']); ?>">
                        <button type="submit" name="delete_announcement">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No announcements found for this course.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php unset($conn); ?>
