<?php
use PHPUnit\Framework\TestCase;

final class StudentFeedbackTest extends TestCase
{
    protected function setUp(): void
    {
        define('UNIT_TESTING', true);
        session_start();
        $_SESSION = ['student_id' => 1];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        require __DIR__ . '/fixtures/conn.php';
        $GLOBALS['conn'] = $conn;
    }

    private function includeScript(): void
    {
        include __DIR__ . '/../student_feedback.php';
    }

    public function test_redirect_if_not_logged_in(): void
    {
        unset($_SESSION['student_id']);
        $this->includeScript();
        $this->assertSame('student_login.php', $_SESSION['__redirect_to'] ?? null);
    }

    public function test_submit_feedback_success(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'feedback' => 'Great course!',
        ];

        $this->includeScript();

        $this->assertSame('student_enrolled_courses.php', $_SESSION['__redirect_to']);
        $this->assertTrue($_SESSION['__feedback_success']);

        $stmt = $GLOBALS['conn']->query("SELECT * FROM feedbacks");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertCount(1, $rows);
        $this->assertSame('Great course!', $rows[0]['feedback']);
        $this->assertSame('Alice', $rows[0]['name']);
        $this->assertSame('0', $rows[0]['anonymous']);
    }

    public function test_submit_feedback_anonymous(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'feedback' => 'Anonymous feedback!',
            'anonymous' => 'anonymous'
        ];

        $this->includeScript();

        $stmt = $GLOBALS['conn']->query("SELECT * FROM feedbacks");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame('', $rows[0]['name']);
        $this->assertSame('1', $rows[0]['anonymous']);
    }

    public function test_feedback_empty_validation(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'feedback' => '',
        ];

        ob_start();
        $this->includeScript();
        $output = ob_get_clean();
        $this->assertStringContainsString('Please enter your feedback.', $output);
    }
}
