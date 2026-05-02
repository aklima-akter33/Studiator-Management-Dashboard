<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type       = $_POST['type'];
    $category   = $_POST['category']; // Matches the new DB column
    $subject    = trim($_POST['subject']);
    $topic      = trim($_POST['topic']);
    $duration   = !empty($_POST['duration']) ? $_POST['duration'] : 0;
    $marks      = !empty($_POST['marks']) ? $_POST['marks'] : 0;
    $difficulty = $_POST['difficulty'];
    $priority   = $_POST['priority'];
    $s_date     = $_POST['scheduled_date'];
    $deadline   = !empty($_POST['deadline']) ? $_POST['deadline'] : $_POST['scheduled_date']; // Fallback to scheduled date

    if (empty($subject) || empty($topic) || empty($s_date)) {
        $errors[] = "Subject, Topic, and Scheduled Date are required.";
    }

    if (empty($errors)) {
        // Updated INSERT to include 'category' and 'deadline'
        $stmt = $mysqli->prepare("INSERT INTO Tasks (user_id, type, category, subject, topic, duration, marks, difficulty, priority, scheduled_date, deadline) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('issssdissss', $user_id, $type, $category, $subject, $topic, $duration, $marks, $difficulty, $priority, $s_date, $deadline);

        if ($stmt->execute()) {
            header("Location: dashboard.php?success=1");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Add Task — Studiator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">

<div class="form-page">
    <div class="form-container">
        <h2>Add New Task</h2>

        <?php if (!empty($errors)): ?>
        <div class="error-box">
            <ul><?php foreach ($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>

        <form method="post">
            <div class="row">
                <div class="col">
                    <label>Task Type</label>
                    <select name="type">
                        <option value="study">Study</option>
                        <option value="assignment">Assignment</option>
                        <option value="exam">Exam</option>
                    </select>
                </div>
                <div class="col">
                    <label>Category</label>
                    <select name="category">
                        <option value="Study">Regular Study</option>
                        <option value="Assignment">Assignment</option>
                        <option value="Exam">Exam / Quiz</option>
                    </select>
                </div>
            </div>

            <label>Subject</label>
            <input type="text" name="subject" placeholder="e.g. Mathematics" required>

            <label>Topic</label>
            <input type="text" name="topic" placeholder="e.g. Calculus" required>

            <div class="row">
                <div class="col">
                    <label>Duration (hrs)</label>
                    <input type="number" step="0.25" name="duration">
                </div>
                <div class="col">
                    <label>Marks</label>
                    <input type="number" name="marks">
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label>Difficulty</label>
                    <select name="difficulty">
                        <option value="easy">Easy</option>
                        <option value="medium" selected>Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>
                <div class="col">
                    <label>Priority</label>
                    <select name="priority">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>

            <label>Scheduled Date</label>
            <input type="date" name="scheduled_date" required>

            <label>Final Deadline (Date & Time)</label>
            <input type="datetime-local" name="deadline" required>

            <button class="btn btn-primary" type="submit">Submit Task</button>
            <a href="dashboard.php" class="btn btn-secondary mt-4 block text-center" style="text-decoration: none;">Back to Dashboard</a>
        </form>
    </div>
</div>

</body>
</html>