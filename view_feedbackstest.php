<?php
/**
 * Admin View Feedbacks Page
 *
 * This page allows an admin to view all feedbacks submitted by users/students.
 *
 * PHP version 8+
 *
 * @category Admin_Portal
 * @package  Course_Feedbacks
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

class FeedbackService {
    private PDO $conn;
    public function __construct(PDO $conn) { $this->conn = $conn; }

    /**
     * Fetch all feedbacks from the database
     *
     * @return array $feedbacks List of feedback entries
     */
    public function getFeedbacks(): array {
        $sql = "SELECT * FROM feedbacks ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

namespace Tests;
use PHPUnit\Framework\TestCase;
use PDO;
use App\FeedbackService;

final class FeedbackServiceTest extends TestCase {
    private PDO $conn;
    private FeedbackService $service;

    protected function setUp(): void {
        $this->conn = new PDO('sqlite::memory:');
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->conn->exec("
            CREATE TABLE feedbacks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                feedback TEXT,
                name TEXT,
                anonymous INTEGER,
                created_at TEXT
            );
        ");

        $this->conn->exec("
            INSERT INTO feedbacks (feedback, name, anonymous, created_at) 
            VALUES 
            ('Great course!', 'Alice', 0, '2025-08-23 10:00:00'),
            ('Needs improvement.', 'Bob', 0, '2025-08-22 09:30:00'),
            ('Excellent!', '', 1, '2025-08-21 12:15:00');
        ");

        $this->service = new FeedbackService($this->conn);
    }

    public function test_getFeedbacks_returnsAllEntries(): void {
        $feedbacks = $this->service->getFeedbacks();
        $this->assertCount(3, $feedbacks);
        $this->assertSame('Great course!', $feedbacks[0]['feedback']);
        $this->assertSame('Alice', $feedbacks[0]['name']);
        $this->assertSame('Excellent!', $feedbacks[2]['feedback']);
        $this->assertSame('1', $feedbacks[2]['anonymous']);
    }
}
