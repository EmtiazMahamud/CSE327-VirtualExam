<?php
// Include database connection
require_once 'conn.php';

// Initialize variables
$email = "";
$dob = "";
$securityQuestion = "";
$securityQuestionAnswer = "";
$newPassword = "";
$error = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input data
    $email = $_POST["email"];
    $dob = $_POST["dob"];

    // Fetch student details based on email and dob
    $sql = "SELECT * FROM Students WHERE email = :email AND dob = :dob";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['email' => $email, 'dob' => $dob]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if student exists
    if ($student) {
        // Student found, set security question and answer
        $securityQuestion = $student['security_question'];
        $securityQuestionAnswer = $student['security_question_answer'];
    } else {
        // Student not found, set error message
        $error = "Invalid email or date of birth.";
    }
}

// Check if form is submitted for password reset
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['resetPassword'])) {
    // Validate security question answer
    if ($_POST['security_question_answer'] == $securityQuestionAnswer) {
        try {
            // Reset password
            $newPassword = $_POST['new_password'];
        
            // Update password in database
            $sql = "UPDATE Students SET password = :password WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['password' => $newPassword, 'email' => $email]);
        
            // Redirect to login page
            header("Location: student_login.php");
            exit();
        } catch (PDOException $e) {
            // Handle database error
            $error = "Error updating password: " . $e->getMessage();
        }
        
    } else {
        // Invalid security question answer
        $error = "Invalid security question answer.";
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #F5F5F5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #FFFFFF;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 400px;
            animation: fadeIn 0.5s ease;
        }

        h1 {
            text-align: center;
            color: #333333;
            margin-bottom: 30px;
        }

        form {
            text-align: center;
        }

        input[type="email"],
        input[type="date"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #CCCCCC;
            border-radius: 4px;
            transition: border-color 0.3s ease;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #45A049;
        }

        p.error {
            color: #FF0000;
            text-align: center;
            margin-top: 20px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        <?php if ($securityQuestion) : ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <p><?php echo $securityQuestion; ?></p>
                <input type="hidden" name="email" value="<?php echo $email; ?>">
                <input type="hidden" name="dob" value="<?php echo $dob; ?>">
                <label for="security_question_answer">Your Answer:</label>
                <input type="text" id="security_question_answer" name="security_question_answer" required><br><br>
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required><br><br>
                <input type="submit" name="resetPassword" value="Reset Password">
            </form>
        <?php else : ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" required><br><br>
                <input type="submit" value="Submit">
            </form>
        <?php endif; ?>
        <?php if ($error) : ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>