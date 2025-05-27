<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $target_user_id = $_POST['target_user'];
    
    // Prevent self-uploads
    if ($_SESSION['user_id'] == $target_user_id) {
        header("Location: dashboard.php?error=You+cannot+send+files+to+yourself");
        exit();
    }
    
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    
    // Check if file already exists
    if (file_exists($target_file)) {
        header("Location: dashboard.php?error=File+already+exists");
        exit();
    }
    
    // Check file size (5MB max)
    if ($_FILES["file"]["size"] > 5000000) {
        header("Location: dashboard.php?error=File+too+large");
        exit();
    }
    
    // Try to move file
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO files (uploader_id, target_user_id, filename, filepath) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $_SESSION['user_id'], $target_user_id, $_FILES["file"]["name"], $target_file);
        
        if ($stmt->execute()) {
            header("Location: dashboard.php?message=File+uploaded+successfully");
        } else {
            unlink($target_file); // Remove the uploaded file if DB insert fails
            header("Location: dashboard.php?error=Database+error");
        }
    } else {
        header("Location: dashboard.php?error=File+upload+failed");
    }
    exit();
}

header("Location: dashboard.php");
?>