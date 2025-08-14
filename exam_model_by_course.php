<?php

declare(strict_types=1);

/**
 * Class ExamModelByCourse
 *
 * Handles fetching courses and exams by course.
 */
class ExamModelByCourse
{
    private PDO $conn;

    /**
     * ExamModelByCourse constructor.
     *
     * @param PDO $conn Database connection instance.
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Fetch all courses from the database.
     *
     * @return array<int, array<string, mixed>> List of courses.
     */
    public function getCourses(): array
    {
        $sql = "SELECT course_id, course_name FROM courses";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all exams for a given course.
     *
     * @param int $courseId ID of the selected course.
     *
     * @return array<int, array<string, mixed>> List of exams.
     */
    public function getExamsByCourse(int $courseId): array
    {
        $sql = "SELECT exam_id, exam_name FROM exams WHERE course_id = :courseId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
