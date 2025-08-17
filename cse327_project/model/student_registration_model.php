
<?php
require_once 'conn.php';

/**
 * Register a new student and store their information in the requested_students table.
 *
 * @param PDO $conn Database connection instance
 * @param array $data Associative array of student registration data
 * @return bool True on success, false on failure
 */
function registerStudent(PDO $conn, array $data): bool
{
    try {
        $stmt = $conn->prepare('
            INSERT INTO requested_students (
                student_name, username, email, course_id, dob,
                phone_number, gender, address, security_question,
                security_question_answer, password
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $success = $stmt->execute([
            $data['student_name'],
            $data['username'],
            $data['email'],
            $data['course_id'],
            $data['dob'],
            $data['phone_number'],
            $data['gender'],
            $data['address'],
            $data['security_question'],
            $data['security_question_answer'],
            $data['password'],
        ]);
        return $success;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}

/**
 * Retrieve all courses from the Courses table.
 *
 * @param PDO $conn Database connection instance
 * @return array List of courses
 */
function getCourses(PDO $conn): array
{
    return $conn->query('SELECT * FROM Courses')->fetchAll();
}
