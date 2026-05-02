<?php
// add_task.php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Add Task — Studiator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="form-page">
    <div class="form-container">
        <!-- Logo -->
        <div class="logo-container">
            <img src="image/logo.png" class="logo" alt="Studiator Logo">
        </div>

        <!-- Page Heading -->
        <h2>Add New Task</h2>

        <!-- Error messages -->
        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($_SESSION['errors'] as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <!-- Task Form -->
        <form method="post" action="add_task_submit.php">
            <label>Type</label>
            <select name="type">
                <option value="study">Study</option>
                <option value="assignment">Assignment</option>
                <option value="exam">Exam</option>
                <option value="ct">CT</option>
            </select>

            <label>Subject</label>
            <input type="text" name="subject" required>

            <label>Topic</label>
            <input type="text" name="topic" required>

            <div class="row">
                <div class="col">
                    <label>Duration (hours)</label>
                    <input type="number" step="0.25" min="0" name="duration">
                </div>
                <div class="col">
                    <label>Marks (if applicable)</label>
                    <input type="number" min="0" name="marks">
                </div>
            </div>

            <label>Scheduled Date</label>
            <input type="date" name="scheduled_date" required>

            <label>Due Date (optional)</label>
            <input type="date" name="due_date">

            <div style="text-align:center;margin-top:12px;">
                <button class="btn btn-primary" type="submit">Add Task</button>
            </div>
        </form>

        <!-- Back to Dashboard -->
        <p class="center" style="margin-top:15px;">
            <a class="btn btn-secondary" href="dashboard.php">Back to Dashboard</a>
        </p>
    </div>
</body>
</html>
