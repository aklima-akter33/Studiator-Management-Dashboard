<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $task_id = intval($_GET['id']);
    $status = $_GET['status']; // e.g., 'completed'
    $user_id = $_SESSION['user_id'];

    // Update the task status but ensure it belongs to the logged-in user
    $stmt = $mysqli->prepare("UPDATE Tasks SET status = ? WHERE task_id = ? AND user_id = ?");
    $stmt->bind_param("sii", $status, $task_id, $user_id);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?update=success");
    } else {
        header("Location: dashboard.php?update=error");
    }
    $stmt->close();
} else {
    header("Location: dashboard.php");
}
exit;