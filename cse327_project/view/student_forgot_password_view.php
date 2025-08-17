<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Recovery</title>
    <style>
        .password-recovery {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: grid;
            place-items: center;
        }
        .password-recovery__card {
            background: white;
            border-radius: 0.625rem;
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            width: 90%;
            max-width: 400px;
        }
        .password-recovery__title {
            color: #333;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .password-recovery__form {
            display: grid;
            gap: 1rem;
        }
        .password-recovery__input {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            width: 100%;
        }
        .password-recovery__submit {
            background: #4caf50;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
        }
        .password-recovery__error {
            color: #f44336;
            text-align: center;
        }
    </style>
</head>
<body class="password-recovery">
    <div class="password-recovery__card">
        <h1 class="password-recovery__title">Reset Password</h1>
        <?php if (isset($security_question) && $security_question): ?>
            <form class="password-recovery__form" method="POST" action="../controller/student_forgot_password_controller.php">
                <p><?= htmlspecialchars($security_question) ?></p>
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <input type="hidden" name="dob" value="<?= htmlspecialchars($dob) ?>">
                <input class="password-recovery__input" type="text" name="security_question_answer" placeholder="Your answer" required>
                <input class="password-recovery__input" type="password" name="new_password" placeholder="New password" required>
                <button class="password-recovery__submit" type="submit" name="resetPassword">Reset Password</button>
            </form>
        <?php else: ?>
            <form class="password-recovery__form" method="POST" action="../controller/student_forgot_password_controller.php">
                <input class="password-recovery__input" type="email" name="email" placeholder="Your email" required>
                <input class="password-recovery__input" type="date" name="dob" required>
                <button class="password-recovery__submit" type="submit">Verify Identity</button>
            </form>
        <?php endif; ?>
        <?php if (isset($error) && $error): ?>
            <p class="password-recovery__error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
