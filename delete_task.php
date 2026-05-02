<?php
require_once 'config.php';
if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $stmt = $mysqli->prepare("DELETE FROM Tasks WHERE task_id = ? AND user_id = ?");
    $stmt->bind_param('ii', $_GET['id'], $_SESSION['user_id']);
    $stmt->execute();
}
header("Location: dashboard.php");
exit;