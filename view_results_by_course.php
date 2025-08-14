<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Results by Course</title>
    <link rel="stylesheet" href="style_results.css">
</head>
<body>
    <div id="sidebar">
        <?php include 'slidebar.php'; ?>
    </div>
    <div id="content" class="wrapper">
        <h1>View Results by Exams</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="courseId">Select Course:</label>
            <select name="courseId" id="courseId" onchange="this.form.submit()">
                <option value="">Select Course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo (int) $course['course_id']; ?>">
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php if (!empty($exams)): ?>
            <h2>Exams</h2>
            <ul>
                <?php foreach ($exams as $exam): ?>
                    <li>
                        <?php echo htmlspecialchars($exam['exam_name']); ?>
                        <a href="view_results_by_exam.php?examId=<?php echo (int) $exam['exam_id']; ?>">
                            View Results
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
