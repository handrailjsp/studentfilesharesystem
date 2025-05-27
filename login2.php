<?php
include 'config.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // For demo purposes: slightly escape quotes to prevent crash, still vulnerable
    $username = str_replace("'", "''", $username);
    $password = str_replace("'", "''", $password);

    $sql = "SELECT id, username, password, role FROM users WHERE username = '$username' AND password = '$password' LIMIT 1";

    // Uncomment to show raw query in source for instructor only
    // echo "<!-- SQL Query: $sql -->";

    $result = @$conn->query($sql); // suppress error with @

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Login failed!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
        .login-form { background: #f9f9f9; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .error { color: red; margin-bottom: 15px; }
        input { width: 100%; padding: 8px; margin: 8px 0; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; cursor: pointer; width: 100%; }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Login</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
<?php
include 'config.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // This is deliberately vulnerable: raw input inside single quotes
    $sql = "SELECT id, username, password, role FROM users WHERE username = '$username' AND password = '$password' LIMIT 1";

    try {
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Login failed!";
        }
    } catch (mysqli_sql_exception $e) {
        $error = "SQL Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vulnerable Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
        .login-form { background: #f9f9f9; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .error { color: red; margin-bottom: 15px; }
        input { width: 100%; padding: 8px; margin: 8px 0; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; cursor: pointer; width: 100%; }
        a { display: block; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Login</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <p>Try the secure login <a href="login.php">here</a>.</p>
    </div>
</body>
</html>
