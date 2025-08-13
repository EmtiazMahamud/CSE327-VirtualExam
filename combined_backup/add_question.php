<?php
/**
 * Add Questions to Exam
 * 
 * This file handles the creation and addition of multiple choice questions to an exam.
 * It provides a dynamic form interface where users can add multiple questions at once.
 * 
 * @package VirtualExam
 * @author Your Name
 * @version 1.0
 */

require_once 'conn.php';
session_start();

/**
 * Handles form submission for adding questions
 * 
 * Processes the submitted form data and inserts multiple questions into the database.
 * Each question includes text, 4 options, correct answer, and marks allocation.
 * 
 * @return void
 */
if ($_POST) {
    $exam_id = $_POST['exam_id'];
    
    $question_texts = $_POST['question_text'];
    $marks = $_POST['marks'];
    $correct_options = $_POST['correct_option'];
    
    for ($i = 0; $i < count($question_texts); $i++) {
        $sql = "INSERT INTO questions (exam_id, question_text, marks, option_1, option_2, option_3, option_4, correct_option) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $exam_id,
            $question_texts[$i],
            $marks[$i],
            $_POST['option_1'][$i],
            $_POST['option_2'][$i],
            $_POST['option_3'][$i],
            $_POST['option_4'][$i],
            $correct_options[$i]
        ]);
    }
    
    $_SESSION['success_message'] = "Questions added successfully!";
    header("location: view_questions.php?exam_id=" . $exam_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .question-box { border: 2px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; background: #f9f9f9; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #45a049; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div id="sidebar"><?php include 'slidebar.php'; ?></div>
    
    <div id="content" class="wrapper">
        <div class="container">
            <h1>Add Questions to Exam</h1>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success"><?php echo $_SESSION['success_message']; ?></div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error"><?php echo $_SESSION['error_message']; ?></div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <form method="post">
                <input type="hidden" name="exam_id" value="<?php echo $_GET['exam_id']; ?>">
                
                <div id="questionContainer">
                    <div class="question-box">
                        <h3>Question 1</h3>
                        
                        <div class="form-group">
                            <label>Question Text:</label>
                            <textarea name="question_text[]" rows="4" required placeholder="Enter your question here..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Option 1:</label>
                            <input type="text" name="option_1[0]" required placeholder="Enter option 1">
                        </div>
                        
                        <div class="form-group">
                            <label>Option 2:</label>
                            <input type="text" name="option_2[0]" required placeholder="Enter option 2">
                        </div>
                        
                        <div class="form-group">
                            <label>Option 3:</label>
                            <input type="text" name="option_3[0]" required placeholder="Enter option 3">
                        </div>
                        
                        <div class="form-group">
                            <label>Option 4:</label>
                            <input type="text" name="option_4[0]" required placeholder="Enter option 4">
                        </div>
                        
                        <div class="form-group">
                            <label>Correct Answer:</label>
                            <select name="correct_option[]">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                                <option value="4">Option 4</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Marks:</label>
                            <input type="number" name="marks[]" value="1" required min="1" max="10">
                        </div>
                    </div>
                </div>
                
                <button type="button" id="addQuestion">Add Another Question</button>
                <button type="submit">Save All Questions</button>
            </form>
        </div>
    </div>
    
    <script>
        /**
         * Dynamic question addition functionality
         * Allows users to add multiple questions dynamically without page reload
         */
        $(document).ready(function() {
            $("#addQuestion").click(function() {
                var questionCount = $(".question-box").length + 1;
                
                var newQuestion = '<div class="question-box">';
                newQuestion += '<h3>Question ' + questionCount + '</h3>';
                newQuestion += '<div class="form-group"><label>Question Text:</label><textarea name="question_text[]" rows="4" required placeholder="Enter your question here..."></textarea></div>';
                newQuestion += '<div class="form-group"><label>Option 1:</label><input type="text" name="option_1[' + (questionCount - 1) + ']" required placeholder="Enter option 1"></div>';
                newQuestion += '<div class="form-group"><label>Option 2:</label><input type="text" name="option_2[' + (questionCount - 1) + ']" required placeholder="Enter option 2"></div>';
                newQuestion += '<div class="form-group"><label>Option 3:</label><input type="text" name="option_3[' + (questionCount - 1) + ']" required placeholder="Enter option 3"></div>';
                newQuestion += '<div class="form-group"><label>Option 4:</label><input type="text" name="option_4[' + (questionCount - 1) + ']" required placeholder="Enter option 4"></div>';
                newQuestion += '<div class="form-group"><label>Correct Answer:</label><select name="correct_option[]"><option value="1">Option 1</option><option value="2">Option 2</option><option value="3">Option 3</option><option value="4">Option 4</option></select></div>';
                newQuestion += '<div class="form-group"><label>Marks:</label><input type="number" name="marks[]" value="1" required min="1" max="10"></div>';
                newQuestion += '</div>';
                
                $("#questionContainer").append(newQuestion);
            });
        });
    </script>
</body>
</html>
