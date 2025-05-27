<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// No getUsername() function here, assumed declared in config.php

// Get files uploaded BY the user
$stmt = $conn->prepare("SELECT * FROM files WHERE uploader_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$uploads = $stmt->get_result();

// Get files sent TO the user
$stmt = $conn->prepare("SELECT * FROM files WHERE target_user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$received = $stmt->get_result();

// Get all other users except current user
$currentUserId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id != ?");
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$recipients = $stmt->get_result();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .section { background: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #4CAF50; color: white; }
        tr:hover { background-color: #f5f5f5; }
        .success { color: green; padding: 10px; background: #e8f5e9; border-radius: 4px; }
        .error { color: red; padding: 10px; background: #ffebee; border-radius: 4px; }
        .upload-form { display: flex; gap: 10px; margin-top: 15px; }
        .upload-form select, .upload-form input[type="file"] { flex: 1; padding: 8px; }
        .upload-form button { padding: 8px 15px; background: #2196F3; color: white; border: none; cursor: pointer; }
        .logout { color: #f44336; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo ucfirst($_SESSION['role']); ?>)</h1>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <?php if (isset($_GET['message'])): ?>
        <div class="success"><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <div class="section">
        <h2>Upload New File</h2>
        <form action="upload.php" method="POST" enctype="multipart/form-data" class="upload-form">
            <input type="file" name="file" required>
            <select name="target_user" required>
                <option value="">Select Recipient</option>
                <?php while ($recipient = $recipients->fetch_assoc()): ?>
                    <option value="<?php echo $recipient['id']; ?>">
                        <?php echo ucfirst($recipient['role']); ?>: <?php echo htmlspecialchars($recipient['username']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Upload</button>
        </form>
    </div>

    <div class="section">
        <h2>Your Uploads</h2>
        <?php if ($uploads->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Filename</th>
                    <th>Recipient</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php while ($file = $uploads->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($file['filename']); ?></td>
                        <td><?php echo getUsername($file['target_user_id']); ?></td>
                        <td><?php echo $file['upload_time']; ?></td>
                        <td>
                            <a href="download.php?file_id=<?php echo $file['id']; ?>">Download</a> |
                            <a href="delete.php?file_id=<?php echo $file['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No files uploaded yet.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>Files Received</h2>
        <?php if ($received->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Filename</th>
                    <th>From</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php while ($file = $received->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($file['filename']); ?></td>
                        <td><?php echo getUsername($file['uploader_id']); ?></td>
                        <td><?php echo $file['upload_time']; ?></td>
                        <td><a href="download.php?file_id=<?php echo $file['id']; ?>">Download</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No files received yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
