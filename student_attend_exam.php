<?php
/**
 * Student Exam Taking Interface
 * 
 * This file provides the interface for students to take exams. It includes
 * question navigation, timer functionality, answer submission, and score calculation.
 * 
 * @package VirtualExam
 * @author Your Name
 * @version 1.0
 */

require_once 'conn.php';
session_start();

if (!isset($_GET['exam_id'])) {
    $_SESSION['error_message'] = "No exam selected.";
    header("location: student_exams.php");
    exit;
}

$exam_id = $_GET['exam_id'];

$sql = "SELECT * FROM Exams WHERE exam_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();

$sql = "SELECT * FROM questions WHERE exam_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$exam_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_SESSION['student_id'])) {
    $_SESSION['error_message'] = "Please login first.";
    header("location: student_login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

$sql = "SELECT * FROM Results WHERE student_id = ? AND exam_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$student_id, $exam_id]);
if ($stmt->fetch()) {
    $_SESSION['error_message'] = "You have already taken this exam.";
    header("location: student_exams.php");
    exit;
}

if ($_POST) {
    $score = 0;
    
    foreach ($_POST['answers'] as $question_id => $answer) {
        foreach ($questions as $question) {
            if ($question['question_id'] == $question_id && $question['correct_option'] == $answer) {
                $score += $question['marks'];
                break;
            }
        }
    }
    
    $sql = "INSERT INTO student_answers (student_id, exam_id, question_id, selected_option_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    foreach ($_POST['answers'] as $question_id => $answer) {
        $stmt->execute([$student_id, $exam_id, $question_id, $answer]);
    }
    
    $sql = "INSERT INTO Results (student_id, exam_id, score) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$student_id, $exam_id, $score]);
    
    $_SESSION['success_message'] = "Exam completed! Score: $score";
    header("location: student_exams.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Exam</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .exam-container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .exam-header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #eee; }
        .exam-title { color: #333; font-size: 24px; margin-bottom: 10px; }
        .timer { background: #ff6b6b; color: white; padding: 10px 20px; border-radius: 25px; font-weight: bold; display: inline-block; margin: 10px 0; }
        .question-container { display: none; margin-bottom: 30px; }
        .question-container.active { display: block; }
        .question-header { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .question-number { color: #007bff; font-weight: bold; font-size: 18px; }
        .question-text { font-size: 16px; line-height: 1.6; margin-top: 10px; }
        .options { margin-left: 20px; }
        .option { display: block; margin: 15px 0; padding: 10px; border: 2px solid #ddd; border-radius: 5px; cursor: pointer; transition: all 0.3s ease; }
        .option:hover { border-color: #007bff; background: #f8f9fa; }
        .option input[type="radio"] { margin-right: 10px; transform: scale(1.2); }
        .navigation-buttons { display: flex; justify-content: space-between; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
        .nav-btn { padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; transition: background-color 0.3s ease; }
        .prev-btn { background: #6c757d; color: white; }
        .prev-btn:hover { background: #5a6268; }
        .next-btn { background: #007bff; color: white; }
        .next-btn:hover { background: #0056b3; }
        .submit-btn { background: #28a745; color: white; }
        .submit-btn:hover { background: #218838; }
        .progress-bar { width: 100%; height: 10px; background: #e9ecef; border-radius: 5px; margin: 20px 0; overflow: hidden; }
        .progress-fill { height: 100%; background: #007bff; transition: width 0.3s ease; }
        .error-message { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="exam-container">
        <div class="exam-header">
            <h1 class="exam-title"><?php echo htmlspecialchars($exam['exam_name']); ?></h1>
            <div class="timer" id="timer">Time Remaining: Loading...</div>
            <div class="progress-bar">
                <div class="progress-fill" id="progress-fill"></div>
            </div>
        </div>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_SESSION['error_message']); ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <form method="post" id="exam-form">
            <?php foreach ($questions as $index => $question): ?>
                <div class="question-container <?php echo ($index == 0) ? 'active' : ''; ?>" id="question-<?php echo $question['question_id']; ?>">
                    <div class="question-header">
                        <div class="question-number">Question <?php echo $index + 1; ?> of <?php echo count($questions); ?></div>
                        <div class="question-text"><?php echo htmlspecialchars($question['question_text']); ?></div>
                    </div>
                    
                    <div class="options">
                        <label class="option">
                            <input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="1" required>
                            <?php echo htmlspecialchars($question['option_1']); ?>
                        </label>
                        <label class="option">
                            <input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="2" required>
                            <?php echo htmlspecialchars($question['option_2']); ?>
                        </label>
                        <label class="option">
                            <input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="3" required>
                            <?php echo htmlspecialchars($question['option_3']); ?>
                        </label>
                        <label class="option">
                            <input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="4" required>
                            <?php echo htmlspecialchars($question['option_4']); ?>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="navigation-buttons">
                <button type="button" class="nav-btn prev-btn" onclick="showPreviousQuestion()">Previous</button>
                <button type="button" class="nav-btn next-btn" onclick="showNextQuestion()">Next</button>
                <button type="submit" class="nav-btn submit-btn" id="submit-btn" style="display: none;">Submit Exam</button>
            </div>
        </form>
    </div>
    
    <?php include 'clock.php'; ?>
    
    <script>
        let currentQuestionIndex = 0;
        const totalQuestions = <?php echo count($questions); ?>;
        const questions = document.querySelectorAll('.question-container');
        const examDuration = <?php echo $exam['exam_duration'] * 60; ?>;
        let remainingTime = examDuration;
        const timerElement = document.getElementById('timer');
        const progressFill = document.getElementById('progress-fill');
        
        function showQuestion(index) {
            questions.forEach(q => q.classList.remove('active'));
            questions[index].classList.add('active');
            updateNavigationButtons();
            updateProgressBar();
        }
        
        function showNextQuestion() {
            if (currentQuestionIndex < totalQuestions - 1) {
                currentQuestionIndex++;
                showQuestion(currentQuestionIndex);
            }
        }
        
        function showPreviousQuestion() {
            if (currentQuestionIndex > 0) {
                currentQuestionIndex--;
                showQuestion(currentQuestionIndex);
            }
        }
        
        function updateNavigationButtons() {
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');
            const submitBtn = document.getElementById('submit-btn');
            
            prevBtn.style.display = currentQuestionIndex === 0 ? 'none' : 'inline-block';
            if (currentQuestionIndex === totalQuestions - 1) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'inline-block';
            } else {
                nextBtn.style.display = 'inline-block';
                submitBtn.style.display = 'none';
            }
        }
        
        function updateProgressBar() {
            const progress = ((currentQuestionIndex + 1) / totalQuestions) * 100;
            progressFill.style.width = progress + '%';
        }
        
        function updateTimer() {
            const minutes = Math.floor(remainingTime / 60);
            const seconds = remainingTime % 60;
            timerElement.textContent = `Time Remaining: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (remainingTime <= 0) {
                alert('Time is up! Submitting your exam...');
                document.getElementById('exam-form').submit();
            } else {
                remainingTime--;
            }
        }
        
        setInterval(updateTimer, 1000);
        showQuestion(0);
        
        document.addEventListener('keydown', function(event) {
            if (event.key === 'ArrowLeft') showPreviousQuestion();
            else if (event.key === 'ArrowRight') showNextQuestion();
        });
    </script>
</body>
</html>

