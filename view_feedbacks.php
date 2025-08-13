<?php
/**
 * Admin View Feedbacks Page
 *
 * This page allows an admin to view all feedbacks submitted by users/students.
 *
 * PHP version 8+
 *
 * @category Admin_Portal
 * @package  Course_Feedbacks
 * @author   
 * @license  MIT License
 * @link     
 */

session_start();
require_once 'conn.php';

/**
 * Check if admin is logged in
 */
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("Location: admin_login.php");
    exit;
}

/**
 * Fetch all feedbacks from the database
 *
 * @return array $feedbacks List of feedback entries
 */
$sql = "SELECT * FROM feedbacks ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Close connection
unset($conn);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedbacks - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        #content {
            padding: 20px;
            max-width: 800px;
            margin-left: 250px;
        }

        #content h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 36px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        #content ul {
            list-style: none;
            padding: 0;
        }

        #content li {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        #content li:hover {
            transform: translateY(-5px);
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.2);
        }

        #content strong {
            color: #007bff;
        }

        #content p {
            color: #666;
            text-align: center;
            font-size: 18px;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #content ul,
        #content p {
            animation: fadeIn 0.5s ease;
        }
    </style>
</head>

<body>
    <div id="sidebar">
        <?php include 'slidebar.php'; ?>
    </div>

    <div id="content" class="wrapper">
        <h1>Feedbacks</h1>

        <?php if (!empty($feedbacks)) : ?>
            <ul>
                <?php foreach ($feedbacks as $feedback) : ?>
                    <li>
                        <strong>Feedback:</strong> <?= nl2br(htmlspecialchars($feedback['feedback'])); ?><br>
                        <strong>Name:</strong> <?= $feedback['anonymous'] ? 'Anonymous' : htmlspecialchars($feedback['name']); ?><br>
                        <strong>Posted At:</strong> <?= htmlspecialchars($feedback['created_at']); ?><br>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>No feedbacks found.</p>
        <?php endif; ?>
    </div>
</body>

</html>
