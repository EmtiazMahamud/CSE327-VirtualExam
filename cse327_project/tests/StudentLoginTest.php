<?php
/**
 * Student Login Test Suite
 * 
 * This test suite validates the authentication functionality
 * of the student login system, including security measures
 * and edge cases.
 *
 * @package VirtualExam
 * @subpackage Tests
 * @author Team-5
 * 
 */

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

require_once __DIR__ . '/../model/student_login_model.php';

class StudentLoginTest extends TestCase
{
    /**
     * Database connection instance
     * 
     * @var PDO
     */
    private PDO $conn;
    
    /**
     * Test data for valid users
     * 
     * @var array
     */
    private array $testUsers;

    /**
     * Set up test environment before each test
     *
     * Creates an in-memory SQLite database and populates it
     * with test data using proper password hashing.
     */
    protected function setUp(): void
    {
        // Create in-memory test database
        $this->conn = new PDO('sqlite::memory:');
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create students table with proper structure
        $this->conn->exec('
            CREATE TABLE Students (
                student_id INTEGER PRIMARY KEY,
                username TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                is_active INTEGER DEFAULT 1,
                last_login DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
        
        // Prepare test data with proper password hashing
        // test for @#$%^^, _(allowed)
        $this->testUsers = [
            [
                'username' => 'testuser1',
                'password' => 'ValidPass123!',
                'is_active' => 1
            ],
            [
                'username' => 'inactive_user',
                'password' => 'ValidPass456!',
                'is_active' => 0
            ],
            [
                'username' => 'testUser',
                'password' => 'validpass',
                'is_active' => 0
            ],
            [
                'username' => 'qwert1234',
                'password' => '123456789',
                'is_active' => 0
            ]
        ];
        
        // Insert test users
        $stmt = $this->conn->prepare('
            INSERT INTO Students (username, password_hash, is_active)
            VALUES (:username, :password_hash, :is_active)
        ');
        
        foreach ($this->testUsers as $user) {
            $stmt->execute([
                'username' => $user['username'],
                'password_hash' => password_hash($user['password'], PASSWORD_DEFAULT),
                'is_active' => $user['is_active']
            ]);
        }
    }
    
    /**
     * Test cases for valid login attempts
     */
    public function testSuccessfulAuthentication(): void
    {
        $result = authenticateStudent(
            $this->conn,
            $this->testUsers[0]['username'],
            $this->testUsers[0]['password']
        );
        
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
        
        // Verify last_login was updated
        $stmt = $this->conn->prepare('SELECT last_login FROM Students WHERE student_id = ?');
        $stmt->execute([$result]);
        $lastLogin = $stmt->fetchColumn();
        $this->assertNotNull($lastLogin);
    }
    
    /**
     * Test cases for invalid login attempts
     */
    public function testFailedAuthentication(): void
    {
        // Test wrong password
        $result1 = authenticateStudent(
            $this->conn,
            $this->testUsers[0]['username'],
            'WrongPass123!'
        );
        $this->assertNull($result1);
        
        // Test non-existent user
        $result2 = authenticateStudent(
            $this->conn,
            '',
            'AnyPass123!'
        );
        $this->assertNull($result2);
        
        // Test inactive user
        $result3 = authenticateStudent(
            $this->conn,
            $this->testUsers[1]['username'],
            $this->testUsers[1]['password']
        );
        $this->assertNull($result3);
    }
    
    /**
     * Test SQL injection prevention
     */
    public function testSQLInjectionPrevention(): void
    {
        $result = authenticateStudent(
            $this->conn,
            "' OR '1'='1",
            "' OR '1'='1"
        );
        $this->assertNull($result);
    }
    
    /**
     * Clean up test environment after each test
     */
    protected function tearDown()
    {
        $this->conn = null;
        $this->assertFalse($result);
    }

    public function testAuthenticateStudentWrongPassword()
    {
        $result = authenticateStudent($this->conn, 'testuser', 'wrongpass');
        $this->assertFalse($result);
    }
}





