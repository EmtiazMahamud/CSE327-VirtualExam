
<?php
require_once 'conn.php';

/**
 * Verify if the provided security answer matches the stored answer for the student.
 *
 * @param PDO $conn Database connection instance
 * @param array $postData Array containing email and security answer
 * @return bool True if answer matches, false otherwise
 */
function verifySecurityAnswer(PDO $conn, array $postData): bool
{
    $stmt = $conn->prepare('SELECT security_question_answer FROM Students WHERE email = ?');
    $stmt->execute([$postData['email']]);
    $student = $stmt->fetch();
    return $student && $student['security_question_answer'] === $postData['security_question_answer'];
}

/**
 * Update the password for a student identified by email.
 *
 * @param PDO $conn Database connection instance
 * @param string $email Student email
 * @param string $newPassword New password to set
 * @return bool True on success, false on failure
 */
function updatePassword(PDO $conn, string $email, string $newPassword): bool
{
    $stmt = $conn->prepare('UPDATE Students SET password = ? WHERE email = ?');
    return $stmt->execute([$newPassword, $email]);
}

/**
 * Verify student identity using email and date of birth, and fetch security question.
 *
 * @param PDO $conn Database connection instance
 * @param array $postData Array containing email and dob
 * @return array Associative array with email, dob, security question, and error message
 */
function verifyStudentIdentity(PDO $conn, array $postData): array
{
    $stmt = $conn->prepare('SELECT security_question FROM Students WHERE email = ? AND dob = ?');
    $stmt->execute([$postData['email'], $postData['dob']]);
    $student = $stmt->fetch();
    return [
        'email' => $postData['email'],
        'dob' => $postData['dob'],
        'security_question' => $student['security_question'] ?? '',
        'error' => $student ? '' : 'Invalid credentials',
    ];
}
