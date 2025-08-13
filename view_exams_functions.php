<?php

declare(strict_types=1);

/**
 * Delete an exam by its ID.
 *
 * @param PDO $conn   Database connection instance.
 * @param int $examId ID of the exam to delete.
 *
 * @return bool True if deletion was successful, false otherwise.
 */
function deleteExam(PDO $conn, int $examId): bool
{
    $sql = "DELETE FROM Exams WHERE exam_id = :exam_id";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bindParam(":exam_id", $examId, PDO::PARAM_INT);

        return $stmt->execute();
    }
    return false;
}

/**
 * Change the status of an exam.
 *
 * @param PDO    $conn   Database connection instance.
 * @param int    $examId ID of the exam.
 * @param string $status New status ('active' or 'inactive').
 *
 * @return bool True if status update was successful, false otherwise.
 */
function changeExamStatus(PDO $conn, int $examId, string $status): bool
{
    $sql = "UPDATE Exams SET status = :status WHERE exam_id = :exam_id";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bindParam(":exam_id", $examId, PDO::PARAM_INT);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);

        return $stmt->execute();
    }
    return false;
}