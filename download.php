<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['file_id'])) {
    header("Location: login.php");
    exit();
}

$file_id = $_GET['file_id'];
$stmt = $conn->prepare("SELECT * FROM files WHERE id = ? AND (uploader_id = ? OR target_user_id = ?)");
$stmt->bind_param("iii", $file_id, $_SESSION['user_id'], $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $file = $result->fetch_assoc();
    $filepath = $file['filepath'];
    
    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: ' . mime_content_type($filepath));
        header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
}

header("Location: dashboard.php?error=File+not+found+or+no+permission");
?>