<?php
use PHPUnit\Framework\TestCase;

final class PostAnnouncementTest extends TestCase
{
    protected function setUp(): void
    {
        define('UNIT_TESTING', true);
        session_start();
        $_SESSION = [];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // Include SQLite test DB
        require __DIR__ . '/fixtures/conn.php';
        $GLOBALS['conn'] = $conn;
    }

    private function includeScript(): void
    {
        include __DIR__ . '/../post_announcement.php';
    }

    public function test_redirects_if_not_logged_in(): void
    {
        unset($_SESSION['admin_loggedin']);
        $this->includeScript();
        $this->assertSame('admin_login.php', $_SESSION['__redirect_to'] ?? null);
    }

    public function test_get_courses_dropdown(): void
    {
        $_SESSION['admin_loggedin'] = true;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        ob_start();
        $this->includeScript();
        $html = ob_get_clean();
        $this->assertStringContainsString('<option value="1">CSE 331</option>', $html);
        $this->assertStringContainsString('<option value="2">CSE 115</option>', $html);
    }

    public function test_post_announcement_success(): void
    {
        $_SESSION['admin_loggedin'] = true;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'post_announcement' => '1',
            'course_id' => '1',
            'announcement_title' => 'Midterm Update',
            'announcement_content' => 'Midterm on Monday.'
        ];

        $this->includeScript();

        // Redirect and success message
        $this->assertSame('view_announcements.php', $_SESSION['__redirect_to']);
        $this->assertSame('Announcement posted successfully!', $_SESSION['success_message']);

        // DB check
        $stmt = $GLOBALS['conn']->query("SELECT * FROM announcements");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertCount(1, $rows);
        $this->assertSame('Midterm Update', $rows[0]['announcement_title']);
    }

    public function test_post_announcement_failure(): void
    {
        $_SESSION['admin_loggedin'] = true;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'post_announcement' => '1',
            'course_id' => '1',
            // Missing title should fail (NULL not allowed)
            'announcement_content' => 'Missing title should fail'
        ];

        $this->includeScript();

        $this->assertSame('view_announcements.php', $_SESSION['__redirect_to']);
        $this->assertSame('Failed to post announcement.', $_SESSION['error_message']);

        // DB should have 0 rows
        $stmt = $GLOBALS['conn']->query("SELECT * FROM announcements");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertCount(0, $rows);
    }
}
