<?php
/**
 * Add Course Management
 * 
 * This file allows administrators to add new courses to the system.
 * It provides a simple form interface for course creation with validation.
 * 
 * @package VirtualExam
 * @version 1.0
 */

require_once 'conn.php';
session_start();

if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: admin_login.php");
    exit;
}

$course_name = "";
$course_name_err = "";
$success_message = "";

/**
 * Process form submission for adding a new course
 * 
 * @return void
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["course_name"]))) {
        $course_name_err = "Please enter course name.";
    } else {
        $course_name = trim($_POST["course_name"]);
    }

    if (empty($course_name_err)) {
        $sql_check = "SELECT course_id FROM Courses WHERE course_name = :course_name";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bindParam(":course_name", $course_name, PDO::PARAM_STR);
        
        if ($stmt_check->execute()) {
            if ($stmt_check->rowCount() > 0) {
                $course_name_err = "Course already exists.";
            } else {
                $sql_insert = "INSERT INTO Courses (course_name) VALUES (:course_name)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bindParam(":course_name", $course_name, PDO::PARAM_STR);
                
                if ($stmt_insert->execute()) {
                    $success_message = "Course added successfully.";
                    $course_name = "";
                } else {
                    echo "Something went wrong. Please try again.";
                }
                unset($stmt_insert);
            }
        } else {
            echo "Something went wrong. Please try again.";
        }
        unset($stmt_check);
    }
    unset($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
        h2 { color: #333; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        input[type="submit"] { background: #4CAF50; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        input[type="submit"]:hover { background: #45a049; }
        .error { color: red; font-size: 14px; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div id="sidebar"><?php include 'slidebar.php'; ?></div>
    
    <div id="content" class="wrapper">
        <div class="container">
            <h2>Add New Course</h2>
            
            <?php if (!empty($success_message)): ?>
                <div class="success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label>Course Name:</label>
                    <input type="text" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>" placeholder="Enter course name">
                    <span class="error"><?php echo $course_name_err; ?></span>
                </div>
                
                <div class="form-group">
                    <input type="submit" value="Add Course">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
