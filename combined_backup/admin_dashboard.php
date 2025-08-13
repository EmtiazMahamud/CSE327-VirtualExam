<?php
/**
 * Admin Dashboard
 * 
 * This file displays the main admin dashboard with system statistics.
 * It shows total students, courses, and active exams in a card layout.
 * 
 * @package VirtualExam
 * @version 1.0
 */

require_once 'conn.php';

/**
 * Get total number of students in the system
 * 
 * @param PDO $conn Database connection
 * @return int Total number of students
 */
function getTotalStudents($conn) {
    $sql = "SELECT COUNT(*) AS total FROM Students";
    $stmt = $conn->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

/**
 * Get total number of courses in the system
 * 
 * @param PDO $conn Database connection
 * @return int Total number of courses
 */
function getTotalCourses($conn) {
    $sql = "SELECT COUNT(*) AS total FROM Courses";
    $stmt = $conn->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

/**
 * Get total number of active exams in the system
 * 
 * @param PDO $conn Database connection
 * @return int Total number of active exams
 */
function getTotalActiveExams($conn) {
    $sql = "SELECT COUNT(*) AS total FROM Exams WHERE status = 'active'";
    $stmt = $conn->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

$totalStudents = getTotalStudents($conn);
$totalCourses = getTotalCourses($conn);
$totalActiveExams = getTotalActiveExams($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .dashboard-container { padding: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
        .stat-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; transition: transform 0.3s ease; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-number { font-size: 2.5em; font-weight: bold; color: #007bff; margin-bottom: 10px; }
        .stat-label { color: #666; font-size: 1.1em; }
        .welcome-message { text-align: center; margin-bottom: 30px; color: #333; }
    </style>
</head>
<body>
    <div id="sidebar"><?php include 'slidebar.php'; ?></div>
    
    <div id="content" class="wrapper">
        <div class="dashboard-container">
            <div class="welcome-message">
                <h1>Welcome to Admin Dashboard</h1>
                <p>Manage your virtual exam system from here</p>
            </div>
            
            <div class="stats-grid">
                <a href="view_students.php" class="card-link">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $totalStudents; ?></div>
                        <div class="stat-label">Total Students</div>
                    </div>
                </a>
                
                <a href="view_courses.php" class="card-link">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $totalCourses; ?></div>
                        <div class="stat-label">Total Courses</div>
                    </div>
                </a>
                
                <a href="view_exams.php" class="card-link">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $totalActiveExams; ?></div>
                        <div class="stat-label">Active Exams</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
