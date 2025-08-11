<?php
// Include database connection
require_once 'conn.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $student_name = $_POST['student_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $course_id = $_POST['course_id'];
    $dob = $_POST['dob'];
    $phone_number = $_POST['phone_number'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $security_question = $_POST['security_question'];
    $security_question_answer = $_POST['security_question_answer'];
    $password = $_POST['password'];

    // Insert data into requested_students table
    $sql = "INSERT INTO requested_students (student_name, username, email, course_id, dob, phone_number, gender, address, security_question, security_question_answer, password) 
            VALUES (:student_name, :username, :email, :course_id, :dob, :phone_number, :gender, :address, :security_question, :security_question_answer, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':student_name', $student_name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':dob', $dob);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':security_question', $security_question);
    $stmt->bindParam(':security_question_answer', $security_question_answer);
    $stmt->bindParam(':password', $password);
    
    if ($stmt->execute()) {
        // Registration successful
        echo "<script>alert('Registration successful');</script>";
        // Redirect to index.php
        echo "<script>window.location.href = 'index.php';</script>";
        exit;
    } else {
        // Registration failed
        echo "<script>alert('Registration failed');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 60px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            transition: transform 0.3s ease;
            margin: auto;
        }
        .container:hover {
            transform: scale(1.01);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            color: #666;
            font-size: 16px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #007bff;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h1>Student Registration</h1>
            <label for="student_name">Full Name:</label>
            <input type="text" id="student_name" name="student_name" required>
            
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="course_id">Course:</label>
            <select id="course_id" name="course_id" required>
                <option value="">Select Course</option>
                <!-- Populate options from Courses table -->
                <?php
                $sql = "SELECT * FROM Courses";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['course_id']}'>{$row['course_name']}</option>";
                }
                ?>
            </select>
            
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>
            
            <label for="phone_number">Phone Number:</label>
            <input type="tel" id="phone_number" name="phone_number" required>
            
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Others">Others</option>
            </select>
            
            <label for="address">Address:</label>
            <textarea id="address" name="address" required></textarea>
            
            <label for="security_question">Security Question:</label>
            <input type="text" id="security_question" name="security_question" required>
            
            <label for="security_question_answer">Security Question Answer:</label>
            <input type="text" id="security_question_answer" name="security_question_answer" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
