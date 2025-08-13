<?php
/**
 * Admin Login System
 * 
 * This file handles admin authentication and login functionality.
 * It validates admin credentials and creates session upon successful login.
 * 
 * @package VirtualExam
 * @version 1.0
 */

require_once 'conn.php';
session_start();

if (isset($_SESSION["admin_loggedin"]) && $_SESSION["admin_loggedin"] === true) {
    header("location: admin_dashboard.php");
    exit;
}

$username = $password = "";
$username_err = $password_err = $login_err = "";

/**
 * Process login form submission
 * 
 * @return void
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT admin_id, username, password FROM Admins WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch();
                $id = $row["admin_id"];
                $username = $row["username"];
                $stored_password = $row["password"];
                
                if ($password === $stored_password) {
                    session_start();
                    $_SESSION["admin_loggedin"] = true;
                    $_SESSION["admin_id"] = $id;
                    $_SESSION["admin_username"] = $username;
                    header("location: admin_dashboard.php");
                    exit;
                } else {
                    $login_err = "Invalid password.";
                }
            } else {
                $login_err = "Invalid username.";
            }
        } else {
            echo "Something went wrong. Please try again.";
        }
        unset($stmt);
    }
    unset($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-container { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 400px; }
        h2 { text-align: center; color: #333; margin-bottom: 30px; font-size: 28px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        input[type="text"]:focus, input[type="password"]:focus { border-color: #667eea; outline: none; }
        .login-btn { width: 100%; padding: 12px; background: #667eea; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; transition: background 0.3s; }
        .login-btn:hover { background: #5a6fd8; }
        .error { color: #e74c3c; font-size: 14px; margin-top: 5px; }
        .success { color: #27ae60; font-size: 14px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form method="post">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Enter username">
                <span class="error"><?php echo $username_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" placeholder="Enter password">
                <span class="error"><?php echo $password_err; ?></span>
            </div>
            
            <div class="form-group">
                <button type="submit" class="login-btn">Login</button>
            </div>
            
            <div class="error"><?php echo $login_err; ?></div>
        </form>
    </div>
</body>
</html>