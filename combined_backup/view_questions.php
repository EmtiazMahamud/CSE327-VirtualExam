<?php
/**
 * View and Manage Exam Questions
 * 
 * This file provides an interface for viewing, updating, and deleting questions
 * for a specific exam. It displays questions in a card-based layout with
 * inline editing capabilities.
 * 
 * @package VirtualExam
 * @author Your Name
 * @version 1.0
 */

require_once 'conn.php';
session_start();

if (!isset($_GET['exam_id'])) {
    $_SESSION['error_message'] = "No exam selected.";
    header("location: view_exams.php");
    exit;
}

$exam_id = $_GET['exam_id'];

$sql = "SELECT exam_name FROM Exams WHERE exam_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();

$sql = "SELECT * FROM questions WHERE exam_id = ? ORDER BY question_id";
$stmt = $conn->prepare($sql);
$stmt->execute([$exam_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

/**
 * Handles question deletion and updates
 * 
 * Processes form submissions for deleting or updating questions.
 * Validates input and provides user feedback through session messages.
 * 
 * @return void
 */
if ($_POST) {
    if (isset($_POST['delete_question'])) {
        $question_id = $_POST['question_id'];
        $sql = "DELETE FROM questions WHERE question_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([$question_id])) {
            $_SESSION['success_message'] = "Question deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to delete question.";
        }
    }
    
    if (isset($_POST['update_question'])) {
        $question_id = $_POST['question_id'];
        $sql = "UPDATE questions SET question_text = ?, marks = ?, option_1 = ?, option_2 = ?, option_3 = ?, option_4 = ?, correct_option = ? WHERE question_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([
            $_POST['question_text'],
            $_POST['marks'],
            $_POST['option_1'],
            $_POST['option_2'],
            $_POST['option_3'],
            $_POST['option_4'],
            $_POST['correct_option'],
            $question_id
        ])) {
            $_SESSION['success_message'] = "Question updated successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to update question.";
        }
    }
    
    header("location: view_questions.php?exam_id=$exam_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Questions</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .page-header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #eee; }
        .exam-title { color: #333; font-size: 24px; margin-bottom: 10px; }
        .question-count { color: #666; font-size: 16px; }
        .question-card { border: 2px solid #ddd; margin: 20px 0; padding: 20px; border-radius: 8px; background: #f9f9f9; }
        .question-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #ddd; }
        .question-number { font-size: 18px; font-weight: bold; color: #007bff; }
        .question-actions { display: flex; gap: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #333; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        textarea { resize: vertical; min-height: 80px; }
        .options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 15px 0; }
        .option-item { display: flex; align-items: center; gap: 10px; }
        .option-item input[type="radio"] { width: auto; margin: 0; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; transition: background-color 0.3s ease; }
        .btn-update { background: #007bff; color: white; }
        .btn-update:hover { background: #0056b3; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-delete:hover { background: #c82333; }
        .btn-back { background: #6c757d; color: white; text-decoration: none; display: inline-block; margin-top: 20px; }
        .btn-back:hover { background: #5a6268; }
        .success { color: #155724; background: #d4edda; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .error { color: #721c24; background: #f8d7da; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .no-questions { text-align: center; padding: 40px; color: #666; font-size: 18px; }
    </style>
</head>
<body>
    <div id="sidebar"><?php include 'slidebar.php'; ?></div>
    
    <div id="content" class="wrapper">
        <div class="container">
            <div class="page-header">
                <h1 class="exam-title"><?php echo htmlspecialchars($exam['exam_name']); ?></h1>
                <p class="question-count">Total Questions: <?php echo count($questions); ?></p>
            </div>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success"><?php echo htmlspecialchars($_SESSION['success_message']); ?></div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error"><?php echo htmlspecialchars($_SESSION['error_message']); ?></div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <?php if ($questions): ?>
                <?php foreach ($questions as $index => $question): ?>
                    <div class="question-card">
                        <div class="question-header">
                            <span class="question-number">Question <?php echo $index + 1; ?></span>
                            <div class="question-actions">
                                <button type="submit" form="form-<?php echo $question['question_id']; ?>" name="update_question" class="btn btn-update">Update</button>
                                <button type="submit" form="form-<?php echo $question['question_id']; ?>" name="delete_question" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this question?')">Delete</button>
                            </div>
                        </div>
                        
                        <form id="form-<?php echo $question['question_id']; ?>" method="post">
                            <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
                            
                            <div class="form-group">
                                <label>Question Text:</label>
                                <textarea name="question_text" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Marks:</label>
                                <input type="number" name="marks" value="<?php echo $question['marks']; ?>" min="1" max="10" required>
                            </div>
                            
                            <div class="options-grid">
                                <div class="option-item">
                                    <input type="radio" name="correct_option" value="1" <?php echo $question['correct_option'] == 1 ? 'checked' : ''; ?> required>
                                    <input type="text" name="option_1" value="<?php echo htmlspecialchars($question['option_1']); ?>" placeholder="Option 1" required>
                                </div>
                                
                                <div class="option-item">
                                    <input type="radio" name="correct_option" value="2" <?php echo $question['correct_option'] == 2 ? 'checked' : ''; ?> required>
                                    <input type="text" name="option_2" value="<?php echo htmlspecialchars($question['option_2']); ?>" placeholder="Option 2" required>
                                </div>
                                
                                <div class="option-item">
                                    <input type="radio" name="correct_option" value="3" <?php echo $question['correct_option'] == 3 ? 'checked' : ''; ?> required>
                                    <input type="text" name="option_3" value="<?php echo htmlspecialchars($question['option_3']); ?>" placeholder="Option 3" required>
                                </div>
                                
                                <div class="option-item">
                                    <input type="radio" name="correct_option" value="4" <?php echo $question['correct_option'] == 4 ? 'checked' : ''; ?> required>
                                    <input type="text" name="option_4" value="<?php echo htmlspecialchars($question['option_4']); ?>" placeholder="Option 4" required>
                                </div>
                            </div>
                            
                            <small style="color: #666;">Select the radio button next to the correct answer.</small>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-questions">
                    <p>No questions found for this exam.</p>
                    <p>Click "Add Question" to create questions for this exam.</p>
                </div>
            <?php endif; ?>
            
            <a href="view_exams.php" class="btn btn-back">← Back to Exams</a>
            <a href="add_question.php?exam_id=<?php echo $exam_id; ?>" class="btn btn-update" style="margin-left: 10px;">+ Add New Question</a>
        </div>
    </div>
</body>
</html>
