<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;

class ViewExamsTest extends TestCase
{
    private PDO $mockPdo;
    private PDOStatement $mockStatement;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock PDO and PDOStatement
        $this->mockPdo = $this->createMock(PDO::class);
        $this->mockStatement = $this->createMock(PDOStatement::class);
        
        // Reset global test output
        global $testOutput;
        $testOutput = '';
    }

    /**
     * Test deleteExam function with successful deletion
     */
    public function testDeleteExamSuccess(): void
    {
        // Arrange
        $examId = 123;
        
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM Exams WHERE exam_id = :exam_id')
            ->willReturn($this->mockStatement);
            
        $this->mockStatement->expects($this->once())
            ->method('bindParam')
            ->with(':exam_id', $examId, PDO::PARAM_INT);
            
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Include the functions
        require_once __DIR__ . '/../view_exams_functions.php';

        // Act
        $result = deleteExam($this->mockPdo, $examId);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test deleteExam function with failed deletion
     */
    public function testDeleteExamFailure(): void
    {
        // Arrange
        $examId = 123;
        
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM Exams WHERE exam_id = :exam_id')
            ->willReturn(false);

        // Include the functions
        require_once __DIR__ . '/../view_exams_functions.php';

        // Act
        $result = deleteExam($this->mockPdo, $examId);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test deleteExam function with execute failure
     */
    public function testDeleteExamExecuteFailure(): void
    {
        // Arrange
        $examId = 123;
        
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM Exams WHERE exam_id = :exam_id')
            ->willReturn($this->mockStatement);
            
        $this->mockStatement->expects($this->once())
            ->method('bindParam')
            ->with(':exam_id', $examId, PDO::PARAM_INT);
            
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        // Include the functions
        require_once __DIR__ . '/../view_exams_functions.php';

        // Act
        $result = deleteExam($this->mockPdo, $examId);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test changeExamStatus function with successful status change
     */
    public function testChangeExamStatusSuccess(): void
    {
        // Arrange
        $examId = 123;
        $status = 'active';
        
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE Exams SET status = :status WHERE exam_id = :exam_id')
            ->willReturn($this->mockStatement);
            
        $this->mockStatement->expects($this->exactly(2))
            ->method('bindParam')
            ->withConsecutive(
                [':exam_id', $examId, PDO::PARAM_INT],
                [':status', $status, PDO::PARAM_STR]
            );
            
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Include the functions
        require_once __DIR__ . '/../view_exams_functions.php';

        // Act
        $result = changeExamStatus($this->mockPdo, $examId, $status);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test changeExamStatus function with failed status change
     */
    public function testChangeExamStatusFailure(): void
    {
        // Arrange
        $examId = 123;
        $status = 'inactive';
        
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE Exams SET status = :status WHERE exam_id = :exam_id')
            ->willReturn(false);

        // Include the functions
        require_once __DIR__ . '/../view_exams_functions.php';

        // Act
        $result = changeExamStatus($this->mockPdo, $examId, $status);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test changeExamStatus function with execute failure
     */
    public function testChangeExamStatusExecuteFailure(): void
    {
        // Arrange
        $examId = 123;
        $status = 'active';
        
        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE Exams SET status = :status WHERE exam_id = :exam_id')
            ->willReturn($this->mockStatement);
            
        $this->mockStatement->expects($this->exactly(2))
            ->method('bindParam')
            ->withConsecutive(
                [':exam_id', $examId, PDO::PARAM_INT],
                [':status', $status, PDO::PARAM_STR]
            );
            
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        // Include the functions
        require_once __DIR__ . '/../view_exams_functions.php';

        // Act
        $result = changeExamStatus($this->mockPdo, $examId, $status);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test changeExamStatus with different status values
     */
    public function testChangeExamStatusWithDifferentValues(): void
    {
        $testCases = [
            ['active', 456],
            ['inactive', 789],
            ['pending', 101]
        ];

        foreach ($testCases as [$status, $examId]) {
            $this->mockPdo->expects($this->once())
                ->method('prepare')
                ->with('UPDATE Exams SET status = :status WHERE exam_id = :exam_id')
                ->willReturn($this->mockStatement);
                
            $this->mockStatement->expects($this->exactly(2))
                ->method('bindParam')
                ->withConsecutive(
                    [':exam_id', $examId, PDO::PARAM_INT],
                    [':status', $status, PDO::PARAM_STR]
                );
                
            $this->mockStatement->expects($this->once())
                ->method('execute')
                ->willReturn(true);

            // Include the functions
            require_once __DIR__ . '/../view_exams_functions.php';

            // Act
            $result = changeExamStatus($this->mockPdo, $examId, $status);

            // Assert
            $this->assertTrue($result, "Failed for status: $status, examId: $examId");
            
            // Reset mocks for next iteration
            $this->setUp();
        }
    }

    /**
     * Test function parameter types
     */
    public function testFunctionParameterTypes(): void
    {
        // Include the functions
        require_once __DIR__ . '/../view_exams_functions.php';

        // Test with wrong parameter types should cause type errors
        $this->expectException(\TypeError::class);
        
        // This should throw a TypeError because we're passing string instead of int
        deleteExam($this->mockPdo, "not_an_integer");
    }
}