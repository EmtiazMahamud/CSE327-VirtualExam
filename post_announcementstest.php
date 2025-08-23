<?php
/**
 * Post Announcement Page - Unit-Tested Version
 *
 * PHP version 8+
 */

namespace App;
use PDO;

class Database {
    private PDO $conn;
    public function __construct(PDO $conn) { $this->conn = $conn; }
    public function getConnection(): PDO { return $this->conn; }
}

class Auth {
    public static function requireAdminLogin(): void {
        session_start();
        if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
            self::redirect("admin_login.php");
        }
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

    public function getCourses(): array {
        $sql = "SELECT * FROM courses";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function postAnnouncement(int $courseId, string $title, string $content): bool {
        $sql = "INSERT INTO announcements (course_id, announcement_title, announcement_content)
                VALUES (:course_id, :announcement_title, :announcement_content)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':course_id' => $courseId,
            ':announcement_title' => $title,
            ':announcement_content' => $content
        ]);
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
            CREATE TABLE courses (
                course_id INTEGER PRIMARY KEY AUTOINCREMENT,
                course_name TEXT
            );
            CREATE TABLE announcements (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                course_id INTEGER,
                announcement_title TEXT,
                announcement_content TEXT
            );
        ");
        $this->conn->exec("INSERT INTO courses (course_name) VALUES ('CSE 331'), ('CSE 115')");
        $this->service = new AnnouncementService($this->conn);
    }

    public function test_getCourses_returnsAllCourses(): void {
        $courses = $this->service->getCourses();
        $this->assertCount(2, $courses);
        $this->assertSame('CSE 331', $courses[0]['course_name']);
    }

    public function test_postAnnouncement_insertsRow(): void {
        $ok = $this->service->postAnnouncement(1, "Midterm Notice", "Exam on Monday");
        $this->assertTrue($ok);
        $rows = $this->conn->query("SELECT * FROM announcements")->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertCount(1, $rows);
        $this->assertSame("Midterm Notice", $rows[0]['announcement_title']);
    }
}
