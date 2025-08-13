<?php
require_once 'conn.php';
session_start();

/**
 * Check if admin is logged in.
 * Redirect to login page if not.
 */
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: admin_login.php");
    exit;
}

/** @var PDOStatement $coursesResult Fetch all courses from database */
$sql = "SELECT * FROM courses";
$coursesResult = $conn->query($sql);

/**
 * Handle form submission to post a new announcement.
 */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["post_announcement"])) {
    /** @var string $announcement_title Title of the announcement */
    $announcement_title = $_POST["announcement_title"];
    /** @var string $announcement_content Content of the announcement */
    $announcement_content = $_POST["announcement_content"];
    /** @var int $course_id ID of the course related to the announcement */
    $course_id = $_POST["course_id"];

    /** @var string $sql SQL query to insert new announcement */
    $sql = "INSERT INTO announcements (course_id, announcement_title, announcement_content) 
            VALUES (:course_id, :announcement_title, :announcement_content)";
    
    /** @var PDOStatement $stmt Prepared statement for inserting announcement */
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':announcement_title', $announcement_title);
    $stmt->bindParam(':announcement_content', $announcement_content);

    // Execute the prepared statement
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Announcement posted successfully!";
        header("location: view_announcements.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Failed to post announcement.";
        header("location: view_announcements.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Announcement</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div id="sidebar">
        <?php include 'slidebar.php'; ?> 
    </div>

    <div id="content" class="wrapper">
        <h2>Post Announcement</h2>

        <!-- Display success or error messages -->
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div style="color: green;"><?php echo $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div style="color: red;"><?php echo $_SESSION['error_message']; ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Form to post announcement -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="course_id">Course:</label>
            <select id="course_id" name="course_id">
                <?php while ($row = $coursesResult->fetch(PDO::FETCH_ASSOC)) : ?>
                    <option value="<?php echo $row['course_id']; ?>"><?php echo $row['course_name']; ?></option>
                <?php endwhile; ?>
            </select><br>

            <label for="announcement_title">Title:</label><br>
            <input type="text" id="announcement_title" name="announcement_title" required><br>

            <label for="announcement_content">Content:</label><br>
            <textarea id="announcement_content" name="announcement_content" rows="4" required></textarea><br>

            <button type="submit" name="post_announcement">Post Announcement</button>
        </form>
    </div>
</body>
</html>
