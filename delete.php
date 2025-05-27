<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['file_id'])) {
    header("Location: login.php");
    exit();
}

$file_id = $_GET['file_id'];
$stmt = $conn->prepare("SELECT * FROM files WHERE id = ? AND uploader_id = ?");
$stmt->bind_param("ii", $file_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $file = $result->fetch_assoc();
    if (file_exists($file['filepath'])) {
        unlink($file['filepath']);
    }
    $conn->query("DELETE FROM files WHERE id = $file_id");
    header("Location: dashboard.php?message=File+deleted+successfully");
} else {
    header("Location: dashboard.php?error=You+don't+have+permission+to+delete+this+file");
}
?>