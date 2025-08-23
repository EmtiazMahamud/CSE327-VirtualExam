<?php
use PHPUnit\Framework\TestCase;

final class ViewAnnouncementsTest extends TestCase
{
    protected function setUp(): void
    {
        define('UNIT_TESTING', true);
        session_start();
        $_SESSION = ['admin_loggedin' => true];
        $_POST = [];
        $_GET = [];

        require __DIR__ . '/fixtures/conn.php';
        $GLOBALS['conn'] = $conn;
    }

    private function includeScript(): void
    {
        include __DIR__ . '/../view_announcements.php';
    }

    public function test_fetch_announcements_for_course(): void
    {
        $_GET['course_id'] = 1;
        ob_start();
        $this->includeScript();
        $html = ob_get_clean();

        $this->assertStringContainsString('Exam Update', $html);
        $this->assertStringContainsString('Lab Update', $html);
        $this->assertStringNotContainsString('Project Reminder', $html);
    }

    public function test_delete_announcement_success(): void
    {
        $_POST['delete_announcement'] = '1';
        $_POST['announcement_id'] = 1;
        $_GET['course_id'] = 1;

        $this->includeScript();

        $this->assertSame('view_announcements.php?course_id=1', $_SESSION['__redirect_to']);
        $this->assertSame('Announcement deleted successfully!', $_SESSION['__success_message']);

        $stmt = $GLOBALS['conn']->query("SELECT * FROM announcements WHERE announcement_id = 1");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertCount(0, $rows);
    }

    public function test_delete_announcement_invalid_id(): void
    {
        $_POST['delete_announcement'] = '1';
        $_POST['announcement_id'] = 999; // invalid ID

        $this->includeScript();

        $this->assertSame('Failed to delete announcement.', $_SESSION['error_message']);
    }

    public function test_redirect_if_not_logged_in(): void
    {
        unset($_SESSION['admin_loggedin']);
        $this->includeScript();
        $this->assertSame('admin_login.php', $_SESSION['__redirect_to'] ?? null);
    }
}
