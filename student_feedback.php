<?php
require_once 'conn.php';
session_start();

if (!isset($_SESSION["student_id"])) {
    header("location: student_login.php");
    exit;
}

$studentId = $_SESSION["student_id"];
$sqlStudentName = "SELECT student_name FROM Students WHERE student_id = :student_id";
$stmtStudentName = $conn->prepare($sqlStudentName);
$stmtStudentName->bindParam(':student_id', $studentId);
$stmtStudentName->execute();
$studentName = $stmtStudentName->fetchColumn();

$feedback = "";
$name = $studentName;
$anonymous = 0;
$feedbackErr = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty(trim($_POST["feedback"]))) {
        $feedbackErr = "Please enter your feedback.";
    } else {
        $feedback = trim($_POST["feedback"]);
    }

    if (isset($_POST["anonymous"])) {
        $anonymous = 1;
        $name = "";
    }

    if (empty($feedbackErr)) {
        $sql = "INSERT INTO feedbacks (student_id, feedback, name, anonymous) 
                VALUES (:student_id, :feedback, :name, :anonymous)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bindParam(":student_id", $studentId, PDO::PARAM_INT);
            $stmt->bindParam(":feedback", $feedback, PDO::PARAM_STR);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":anonymous", $anonymous, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo '<script>alert("Feedback submitted successfully!"); 
                      window.location.href = "student_enrolled_courses.php";</script>';
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }

            unset($stmt);
        }
    }

    unset($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedback</title>
</head>
<body>
    <?php include 'floating_profile_button.php'; ?>
    <div id="sidebar">
        <?php include 'student_slidebar.php'; ?>
    </div>
    <div id="content" class="wrapper">
        <h1>Post Feedback</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Feedback:</label>
                <textarea name="feedback" rows="5" cols="50"><?php echo $feedback; ?></textarea>
                <span><?php echo $feedbackErr; ?></span>
            </div>
            <div>
                <label>Name (optional):</label>
                <input type="text" name="name" value="<?php echo $name; ?>" readonly>
            </div>
            <div>
                <input type="checkbox" id="anonymous" name="anonymous" value="anonymous">
                <label for="anonymous">Submit anonymously</label>
            </div>
            <div>
                <input type="submit" value="Submit">
            </div>
        </form>
    </div>
    <?php include 'clock.php'; ?>
</body>
</html>
