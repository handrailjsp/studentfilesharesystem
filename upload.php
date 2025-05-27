<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $target_user_id = $_POST['target_user'];
    
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO files (uploader_id, target_user_id, filename, filepath) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $_SESSION['user_id'], $target_user_id, $_FILES["file"]["name"], $target_file);
        $stmt->execute();
        header("Location: dashboard.php?message=File+uploaded+successfully");
    } else {
        header("Location: dashboard.php?error=File+upload+failed");
    }
    exit();
}

header("Location: dashboard.php");
?>