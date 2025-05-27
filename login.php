<?php
include 'config.php';
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Secure login with prepared statements
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Secure Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
        .login-form { background: #f9f9f9; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .error { color: red; margin-bottom: 15px; }
        input { width: 100%; padding: 8px; margin: 8px 0; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; cursor: pointer; width: 100%; }
        .register-btn { background: #2196F3; margin-top: 10px; }
        .version-links { margin-top: 20px; text-align: center; }
        .version-links a { display: inline-block; margin: 0 5px; color: #666; }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Secure Login</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div>
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            <div>
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Login</button>
            <a href="register.php" class="register-btn" style="display: block; text-align: center; padding: 10px; text-decoration: none;">Register New User</a>
        </form>
        <div class="version-links">
            <a href="login.php">Secure Login</a> | 
            <a href="login2.php">Vulnerable Login</a>
        </div>
    </div>
</body>
</html>