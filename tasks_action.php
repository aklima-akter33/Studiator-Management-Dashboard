<?php
// task_action.php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit;
}

$task_id = intval($_POST['task_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($task_id <= 0) {
    $_SESSION['errors'] = ['Invalid task.'];
    header("Location: dashboard.php");
    exit;
}

// ensure task belongs to user
$check = $mysqli->prepare("SELECT task_id FROM Tasks WHERE task_id = ? AND user_id = ? LIMIT 1");
$check->bind_param('ii', $task_id, $user_id);
$check->execute();
$check->store_result();
if ($check->num_rows === 0) {
    $_SESSION['errors'] = ['Task not found or permission denied.'];
    header("Location: dashboard.php");
    exit;
}
$check->close();

if ($action === 'delete') {
    $del = $mysqli->prepare("DELETE FROM Tasks WHERE task_id = ? AND user_id = ?");
    $del->bind_param('ii', $task_id, $user_id);
    if ($del->execute()) {
        $_SESSION['success'] = "Task deleted.";
    } else {
        $_SESSION['errors'] = ["Failed to delete task."];
    }
    $del->close();
} elseif ($action === 'complete') {
    $u = $mysqli->prepare("UPDATE Tasks SET status = 'completed' WHERE task_id = ? AND user_id = ?");
    $u->bind_param('ii', $task_id, $user_id);
    $u->execute();
    $u->close();
    $_SESSION['success'] = "Marked as completed.";
} elseif ($action === 'inprogress') {
    $u = $mysqli->prepare("UPDATE Tasks SET status = 'in-progress' WHERE task_id = ? AND user_id = ?");
    $u->bind_param('ii', $task_id, $user_id);
    $u->execute();
    $u->close();
    $_SESSION['success'] = "Marked as in-progress.";
} else {
    $_SESSION['errors'] = ['Unknown action.'];
}

header("Location: dashboard.php");
exit;
