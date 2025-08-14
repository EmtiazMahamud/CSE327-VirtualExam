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
        <?php include 'slidebar.php'; ?>
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
