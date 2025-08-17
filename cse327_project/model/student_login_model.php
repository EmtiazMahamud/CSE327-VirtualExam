
<?php
require_once 'conn.php';

/**
 * Authenticate a student using username and password.
 *
 * @param PDO $conn Database connection instance
 * @param string $username Student username
 * @param string $password Student password
 * @return mixed Student ID if authentication is successful, false otherwise
 */
function authenticateStudent(PDO $conn, string $username, string $password)
{
    $sql = 'SELECT student_id FROM Students WHERE username = :username AND password = :password';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    if ($stmt->rowCount() === 1) {
        return $stmt->fetchColumn();
    }
    return false;
}
