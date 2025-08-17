<?php
/**
 * Student registration handler
 * 
 * Processes new student registrations and stores them in pending approval table
 * @package Registration
 */
require_once 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $conn->prepare("
            INSERT INTO requested_students (
                student_name, username, email, course_id, dob, 
                phone_number, gender, address, security_question, 
                security_question_answer, password
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $success = $stmt->execute([
            $_POST['student_name'],
            $_POST['username'],
            $_POST['email'],
            $_POST['course_id'],
            $_POST['dob'],
            $_POST['phone_number'],
            $_POST['gender'],
            $_POST['address'],
            $_POST['security_question'],
            $_POST['security_question_answer'],
            $_POST['password']
        ]);

        if ($success) {
            header("Refresh:0; url=index.php");
            exit;
        }
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
    }
}

$courses = $conn->query("SELECT * FROM Courses")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Student Registration</title>
    <style>
        .registration {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            min-height: 100vh;
            display: grid;
            place-items: center;
        }

        .registration__card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 90%;
            max-width: 400px;
        }

        .registration__title {
            color: #333;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .registration__form {
            display: grid;
            gap: 0.75rem;
        }

        .registration__label {
            color: #666;
        }

        .registration__input {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            width: 100%;
        }

        .registration__submit {
            background: #007bff;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body class="registration">
    <div class="registration__card">
        <h1 class="registration__title">Student Registration</h1>
        <form class="registration__form" method="POST">
            <label class="registration__label" for="student_name">Full Name</label>
            <input class="registration__input" type="text" id="student_name" name="student_name" required>

            <!-- Other form fields follow same pattern -->
            
            <label class="registration__label" for="course_id">Course</label>
            <select class="registration__input" id="course_id" name="course_id" required>
                <option value="">Select Course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['course_id'] ?>">
                        <?= htmlspecialchars($course['course_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="registration__submit" type="submit">
                Complete Registration
            </button>
        </form>
    </div>
</body>
</html>