<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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

// Get all teachers for the dropdown
$teachers = $conn->query("SELECT id, username FROM users WHERE role = 'teacher'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .section { background: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['role']; ?>)</h1>
    <p><a href="logout.php">Logout</a></p>

    <?php if (isset($_GET['message'])): ?>
        <p class="success"><?php echo $_GET['message']; ?></p>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <p class="error"><?php echo $_GET['error']; ?></p>
    <?php endif; ?>

    <div class="section">
        <h2>Upload New File</h2>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <select name="target_user" required>
                <option value="">Select Teacher</option>
                <?php while ($teacher = $teachers->fetch_assoc()): ?>
                    <option value="<?php echo $teacher['id']; ?>"><?php echo $teacher['username']; ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Upload</button>
        </form>
    </div>

    <div class="section">
        <h2>Your Uploads</h2>
        <table>
            <tr><th>Filename</th><th>Sent To</th><th>Date</th><th>Action</th></tr>
            <?php while ($file = $uploads->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $file['filename']; ?></td>
                    <td><?php echo getUsername($file['target_user_id']); ?></td>
                    <td><?php echo $file['upload_time']; ?></td>
                    <td>
                        <a href="download.php?file_id=<?php echo $file['id']; ?>">Download</a> |
                        <a href="delete.php?file_id=<?php echo $file['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="section">
        <h2>Files Received</h2>
        <table>
            <tr><th>Filename</th><th>From</th><th>Date</th><th>Action</th></tr>
            <?php while ($file = $received->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $file['filename']; ?></td>
                    <td><?php echo getUsername($file['uploader_id']); ?></td>
                    <td><?php echo $file['upload_time']; ?></td>
                    <td><a href="download.php?file_id=<?php echo $file['id']; ?>">Download</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>