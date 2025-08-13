<?php
/**
 * View Courses Management
 * 
 * This file displays all courses in the system with options to view
 * announcements and students for each course.
 * 
 * @package VirtualExam
 * @version 1.0
 */

require_once 'conn.php';

/**
 * Get all courses from the database
 * 
 * @param PDO $conn Database connection
 * @return array Array of course data
 */
function getAllCourses($conn) {
    $sql = "SELECT * FROM Courses ORDER BY course_name";
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$courses = getAllCourses($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Courses</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
        .courses-container { padding: 20px; }
        .courses-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
        .course-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: transform 0.3s ease; }
        .course-card:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(0,0,0,0.2); }
        .course-title { color: #333; font-size: 1.5em; margin-bottom: 20px; text-align: center; }
        .course-actions { display: flex; flex-direction: column; gap: 10px; }
        .action-btn { display: block; background: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 5px; text-decoration: none; text-align: center; transition: background 0.3s; }
        .action-btn:hover { background: #0056b3; }
        .action-btn.students { background: #28a745; }
        .action-btn.students:hover { background: #218838; }
        .no-courses { text-align: center; color: #666; font-size: 1.2em; margin-top: 50px; }
    </style>
</head>
<body>
    <div id="sidebar"><?php include 'slidebar.php'; ?></div>
    
    <div id="content" class="wrapper">
        <div class="courses-container">
            <h2>All Courses</h2>
            
            <?php if (empty($courses)): ?>
                <div class="no-courses">
                    <p>No courses found in the system.</p>
                    <p>Add your first course to get started!</p>
                </div>
            <?php else: ?>
                <div class="courses-grid">
                    <?php foreach ($courses as $course): ?>
                        <div class="course-card">
                            <h3 class="course-title"><?php echo htmlspecialchars($course['course_name']); ?></h3>
                            <div class="course-actions">
                                <a href="view_announcements.php?course_id=<?php echo $course['course_id']; ?>" class="action-btn">
                                    View Announcements
                                </a>
                                <a href="view_students_by_course.php?course_id=<?php echo $course['course_id']; ?>" class="action-btn students">
                                    View Students
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
