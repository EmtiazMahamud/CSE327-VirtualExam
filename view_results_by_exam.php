<?php

/**
 * Include the database connection file.
 */
require_once 'conn.php';

/**
 * Initialize session.
 */
session_start();

/**
 * Check if admin is logged in, redirect to login page if not logged in.
 */
if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    header('Location: admin_login.php');
    exit;
}

/**
 * Check if examId parameter is set in the URL.
 */
if (!isset($_GET['examId']) || empty($_GET['examId'])) {
    // Redirect to error page if examId is not provided
    header('Location: error.php');
    exit;
}

/**
 * @var int $examId The ID of the exam to fetch.
 */
$examId = $_GET['examId'];

/**
 * Fetch exam details.
 *
 * @var string $sqlExam
 * @var PDOStatement $stmtExam
 * @var array|null $examDetails
 */
$sqlExam = 'SELECT e.exam_name, c.course_name
            FROM exams e
            INNER JOIN courses c ON e.course_id = c.course_id
            WHERE e.exam_id = :examId';

$stmtExam = $conn->prepare($sqlExam);
$stmtExam->bindParam(':examId', $examId);
$stmtExam->execute();

/** @var array|null $examDetails */
$examDetails = $stmtExam->fetch(PDO::FETCH_ASSOC);

/**
 * Fetch results for the selected exam.
 *
 * @var string $sqlResults
 * @var PDOStatement $stmtResults
 * @var array $results
 */
$sqlResults = 'SELECT r.*, s.student_name
               FROM Results r
               INNER JOIN Students s ON r.student_id = s.student_id
               WHERE r.exam_id = :examId
               ORDER BY r.score DESC';

$stmtResults = $conn->prepare($sqlResults);
$stmtResults->bindParam(':examId', $examId);
$stmtResults->execute();

/** @var array $results */
$results = $stmtResults->fetchAll(PDO::FETCH_ASSOC);

/**
 * Close connection.
 */
unset($conn);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View Results by Exam</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        /* style_results_exam.css */

        .wrapper {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 36px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #666;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f9f9f9;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        p {
            color: #666;
            text-align: center;
            font-size: 18px;
        }
    </style>
</head>

<body>
    <div id="sidebar">
        <?php include 'slidebar.php'; // Include the sidebar ?>
    </div>
    <div id="content" class="wrapper">
        <h1>View Results for <?php echo htmlspecialchars($examDetails['exam_name']); ?></h1>
        <h2>Course: <?php echo htmlspecialchars($examDetails['course_name']); ?></h2>

        <?php if (!empty($results)) : ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Score</th>
                        <th>Rank</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; ?>
                    <?php foreach ($results as $result) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($result['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($result['score']); ?></td>
                            <td><?php echo $rank++; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No results found for this exam.</p>
        <?php endif; ?>
    </div>
</body>

</html>
