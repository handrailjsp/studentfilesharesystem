<?php
include 'config.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // DELIBERATELY VULNERABLE TO SQL INJECTION
    $sql = "SELECT id, username, password, role FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Login failed! Try: admin' --";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vulnerable Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 0 auto; padding: 20px; }
        .login-form { background: #f9f9f9; padding: 20px; border-radius: 5px; }
        .error { color: red; }
        .hint { background: #fff8e1; padding: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Vulnerable Login (For Demo)</h2>
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
        </form>
        <div class="hint">
            <h4>SQL Injection Examples:</h4>
            <p>Username: <code>admin' --</code></p>
            <p>Username: <code>' OR '1'='1</code></p>
            <p>Password: <em>(anything or empty)</em></p>
        </div>
        <p>Try <a href="login.php">secure version</a>.</p>
    </div>
</body>
</html>