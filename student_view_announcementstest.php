<?php
namespace App;
use PDO;

class Database {
    private PDO $conn;
    public function __construct(PDO $conn) { $this->conn = $conn; }
    public function getConnection(): PDO { return $this->conn; }
}

class StudentAuth {
    public static function requireLogin(): int {
        session_start();
        if (!isset($_SESSION["student_id"])) {
            self::redirect("student_login.php");
        }
        return $_SESSION["student_id"];
    }
    public static function redirect(string $url): void {
        if (defined('UNIT_TESTING') && UNIT_TESTING) {
            $_SESSION['__redirect_to'] = $url;
            return;
        }
        header("Location: $url");
        exit;
    }
}

class AnnouncementService {
    private PDO $conn;
    public function __construct(PDO $conn) { $this->conn = $conn; }

    public function checkEnrollment(int $student_id, int $course_id): bool {
        $sql = "SELECT * FROM Enrollments WHERE student_id = :student_id AND course_id = :course_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':student_id' => $student_id,
            ':course_id' => $course_id
        ]);
        return $stmt->rowCount() > 0;
    }

    public function getAnnouncements(int $course_id): array {
        $sql = "SELECT announcement_title, announcement_content, created_at 
                FROM Announcements 
                WHERE course_id = :course_id 
                ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':course_id' => $course_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

namespace Tests;
use PHPUnit\Framework\TestCase;
use PDO;
use App\AnnouncementService;

final class AnnouncementServiceTest extends TestCase {
    private PDO $conn;
    private AnnouncementService $service;

    protected function setUp(): void {
        $this->conn = new PDO('sqlite::memory:');
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->exec("
            CREATE TABLE Enrollments (
                student_id INTEGER,
                course_id INTEGER
            );
            CREATE TABLE Announcements (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                course_id INTEGER,
                announcement_title TEXT,
                announcement_content TEXT,
                created_at TEXT
            );
        ");
        $this->conn->exec("INSERT INTO Enrollments (student_id, course_id) VALUES (1, 101)");
        $this->conn->exec("
            INSERT INTO Announcements (course_id, announcement_title, announcement_content, created_at) 
            VALUES (101, 'Exam Update', 'Midterm postponed', '2025-08-23 09:00:00')
        ");
        $this->service = new AnnouncementService($this->conn);
    }

    public function test_checkEnrollment_returnsTrueForEnrolledStudent(): void {
        $this->assertTrue($this->service->checkEnrollment(1, 101));
    }

    public function test_checkEnrollment_returnsFalseForNonEnrolledStudent(): void {
        $this->assertFalse($this->service->checkEnrollment(2, 101));
    }

    public function test_getAnnouncements_returnsAnnouncements(): void {
        $announcements = $this->service->getAnnouncements(101);
        $this->assertCount(1, $announcements);
        $this->assertSame('Exam Update', $announcements[0]['announcement_title']);
    }
}
