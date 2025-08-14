<?php

/**
 * ExamModel handles database operations related to exams and their results.
 *
 * PHPDock-compatible documentation included.
 */
class ExamModel
{
    /**
     * @var PDO Database connection instance
     */
    private $conn;

    /**
     * Constructor for ExamModel.
     *
     * @param PDO $conn Database connection
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Fetch exam details for a given exam ID.
     *
     * @param int $examId Exam ID
     * @return array|null Exam details with exam_name and course_name
     */
    public function getExamDetails($examId)
    {
        $sqlExam = 'SELECT e.exam_name, c.course_name
                    FROM exams e
                    INNER JOIN courses c ON e.course_id = c.course_id
                    WHERE e.exam_id = :examId';

        $stmtExam = $this->conn->prepare($sqlExam);
        $stmtExam->bindParam(':examId', $examId);
        $stmtExam->execute();

        return $stmtExam->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all results for a given exam ID.
     *
     * @param int $examId Exam ID
     * @return array List of results with student_name, student_id, and score
     */
    public function getExamResults($examId)
    {
        $sqlResults = 'SELECT r.*, s.student_name
                       FROM Results r
                       INNER JOIN Students s ON r.student_id = s.student_id
                       WHERE r.exam_id = :examId
                       ORDER BY r.score DESC';

        $stmtResults = $this->conn->prepare($sqlResults);
        $stmtResults->bindParam(':examId', $examId);
        $stmtResults->execute();

        return $stmtResults->fetchAll(PDO::FETCH_ASSOC);
    }
}
