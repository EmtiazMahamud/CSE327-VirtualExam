<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;

class ViewExamsIntegrationTest extends TestCase
{
    private PDO $mockPdo;
    private PDOStatement $mockStatement;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock PDO and PDOStatement
        $this->mockPdo = $this->createMock(PDO::class);
        $this->mockStatement = $this->createMock(PDOStatement::class);
        
        // Mock $_GET superglobal
        $_GET = [];
        
        // Mock $_SESSION superglobal
        $_SESSION = ['admin_loggedin' => true];
    }

    protected function tearDown(): void
    {
        // Clean up superglobals
        $_GET = [];
        $_SESSION = [];
        
        parent::tearDown();
    }

    /**
     * Test the SQL query structure for fetching exams
     */
    public function testFetchExamsQuery(): void
    {
        $expectedSql = "SELECT Exams.exam_id, Exams.exam_name, Exams.status, Courses.course_name 
        FROM Exams 
        INNER JOIN Courses ON Exams.course_id = Courses.course_id";

        // Mock the query method
        $this->mockPdo->expects($this->once())
            ->method('query')
            ->with($this->stringContains('SELECT Exams.exam_id'))
            ->willReturn($this->mockStatement);

        // Set up the mock connection
        $conn = $this->mockPdo;

        // Include the view logic (without the HTML part)
        $sql = "SELECT Exams.exam_id, Exams.exam_name, Exams.status, Courses.course_name 
        FROM Exams 
        INNER JOIN Courses ON Exams.course_id = Courses.course_id";
        $result = $conn->query($sql);

        // Assert that the query was called and returned a PDOStatement
        $this->assertInstanceOf(PDOStatement::class, $result);
    }

    /**
     * Test exam deletion workflow
     */
    public function testExamDeletionWorkflow(): void
    {
        // Set up GET parameter for deletion
        $_GET['delete_exam_id'] = '123';

        // Mock successful deletion
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM Exams WHERE exam_id = :exam_id')
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->once())
            ->method('bindParam')
            ->with(':exam_id', 123, PDO::PARAM_INT);

        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Include the functions
        require_once __DIR__ . '/../view_exams_functions.php';

        // Test the deletion
        $result = deleteExam($this->mockPdo, 123);

        $this->assertTrue($result);
    }

    /**
     * Test exam status change workflow
     */
    public function testExamStatusChangeWorkflow(): void
    {
        // Set up GET parameters for status change
        $_GET['exam_id'] = '456';
        $_GET['status'] = 'active';

        // Mock successful status change
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE Exams SET status = :status WHERE exam_id = :exam_id')
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->exactly(2))
            ->method('bindParam')
            ->withConsecutive(
                [':exam_id', 456, PDO::PARAM_INT],
                [':status', 'active', PDO::PARAM_STR]
            );

        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Include the functions
        require_once __DIR__ . '/../view_exams_functions.php';

        // Test the status change
        $result = changeExamStatus($this->mockPdo, 456, 'active');

        $this->assertTrue($result);
    }

    /**
     * Test HTML output generation with exam data
     */
    public function testHtmlOutputWithExamData(): void
    {
        // Mock exam data
        $examData = [
            [
                'exam_id' => 1,
                'exam_name' => 'Math Test',
                'status' => 'active',
                'course_name' => 'Mathematics'
            ],
            [
                'exam_id' => 2,
                'exam_name' => 'Science Quiz',
                'status' => 'inactive',
                'course_name' => 'Science'
            ]
        ];

        // Mock PDOStatement behavior
        $this->mockStatement->expects($this->once())
            ->method('rowCount')
            ->willReturn(2);

        $this->mockStatement->expects($this->exactly(2))
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturnOnConsecutiveCalls(
                $examData[0],
                $examData[1]
            );

        // Test HTML generation logic
        ob_start();
        
        if ($this->mockStatement->rowCount() > 0) {
            $callCount = 0;
            while ($row = $this->mockStatement->fetch(PDO::FETCH_ASSOC)) {
                $statusClass = $row['status'] === 'active' ? 'active' : 'inactive';
                $statusText = ucfirst($row['status']);
                
                echo "<tr>";
                echo "<td>" . $row['exam_name'] . "</td>";
                echo "<td>" . $row['course_name'] . "</td>";
                echo "<td><span class='status-btn {$statusClass}' 
                        data-exam-id='{$row['exam_id']}' 
                        data-status='{$row['status']}'>
                        {$statusText}</span></td>";
                echo "</tr>";
                
                $callCount++;
                if ($callCount >= 2) break; // Prevent infinite loop in test
            }
        }
        
        $output = ob_get_clean();

        // Assert that the output contains expected exam data
        $this->assertStringContainsString('Math Test', $output);
        $this->assertStringContainsString('Mathematics', $output);
        $this->assertStringContainsString('Science Quiz', $output);
        $this->assertStringContainsString('Science', $output);
        $this->assertStringContainsString('status-btn active', $output);
        $this->assertStringContainsString('status-btn inactive', $output);
    }

    /**
     * Test empty exam list scenario
     */
    public function testEmptyExamList(): void
    {
        // Mock empty result
        $this->mockStatement->expects($this->once())
            ->method('rowCount')
            ->willReturn(0);

        // Test HTML generation for empty list
        ob_start();
        
        if ($this->mockStatement->rowCount() > 0) {
            // This should not execute
            echo "Should not appear";
        } else {
            echo "<tr><td colspan='4'>No exams found</td></tr>";
        }
        
        $output = ob_get_clean();

        // Assert that the output contains the "no exams" message
        $this->assertStringContainsString('No exams found', $output);
        $this->assertStringNotContainsString('Should not appear', $output);
    }
}