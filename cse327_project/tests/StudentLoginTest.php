<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../model/student_login_model.php';

class StudentLoginTest extends TestCase
{
    /**
     * @var PDO
     */
    private $conn;

    protected function setUp(): void
    {
        // Use SQLite in-memory for testing
        $this->conn = new PDO('sqlite::memory:');
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->exec('CREATE TABLE Students (student_id INTEGER PRIMARY KEY, username TEXT, password TEXT)');
        $this->conn->exec("INSERT INTO Students (username, password) VALUES ('testuser', 'testpass')");
    }

    public function testAuthenticateStudentSuccess()
    {
        $result = authenticateStudent($this->conn, 'testuser', 'testpass');
        $this->assertEquals(1, $result);
    }

    public function testAuthenticateStudentFailure()
    {
        $result = authenticateStudent($this->conn, 'wronguser', 'wrongpass');
        $this->assertFalse($result);
    }

    public function testAuthenticateStudentWrongPassword()
    {
        $result = authenticateStudent($this->conn, 'testuser', 'wrongpass');
        $this->assertFalse($result);
    }
}
