<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Portal Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login {
            font-family: 'Poppins', sans-serif;
            background: url('background.jpg') center/cover no-repeat;
            height: 100vh;
            display: grid;
            place-items: center;
        }
        .login__card {
            width: 90%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.9);
            padding: 2.5rem;
            border-radius: 1.25rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(0.5rem);
        }
        .login__title {
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .login__form {
            display: grid;
            gap: 1rem;
        }
        .login__input {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 1.25rem;
            transition: border 0.3s ease;
        }
        .login__input:focus {
            border-color: #66afe9;
            outline: none;
        }
        .login__submit {
            background: #4caf50;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 1.25rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .login__submit:hover {
            background: #3e8e41;
        }
        .login__link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #007bff;
        }
    </style>
</head>
<body class="login">
    <div class="login__card">
        <h2 class="login__title">Student Login</h2>
        <form class="login__form" method="POST" action="../controller/student_login_controller.php">
            <label for="username">
                <i class="fas fa-user"></i> Username
            </label>
            <input class="login__input" type="text" id="username" name="username" required>
            <label for="password">
                <i class="fas fa-lock"></i> Password
            </label>
            <input class="login__input" type="password" id="password" name="password" required>
            <input class="login__submit" type="submit" value="Login">
        </form>
        <a class="login__link" href="student_forgot_password_view.php">
            <i class="fas fa-key"></i> Forgot Password?
        </a>
    </div>
</body>
</html>
