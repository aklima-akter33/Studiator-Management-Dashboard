<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details for the profile display
$user_query = $mysqli->prepare("SELECT username FROM Users WHERE user_id = ?");
$user_query->bind_param('i', $user_id);
$user_query->execute();
$user_data = $user_query->get_result()->fetch_assoc();
$username = $user_data['username'] ?? 'Student';
$user_query->close();

// 1. Fetch Stats for Performance Evaluation
$stats_query = $mysqli->query("SELECT 
    COUNT(*) as total, 
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as done 
    FROM Tasks WHERE user_id = $user_id");
$stats = $stats_query->fetch_assoc();
$percent = ($stats['total'] > 0) ? round(($stats['done'] / $stats['total']) * 100) : 0;

// Evaluation Label
$evaluation = "Beginner";
if($percent >= 80) $evaluation = "Expert";
elseif($percent >= 50) $evaluation = "Proactive";

// 2. Fetch all tasks
$tasks = [];
$stmt = $mysqli->prepare("SELECT *, 
    (CASE WHEN deadline < NOW() AND status = 'pending' THEN 1 ELSE 0 END) as is_overdue,
    (CASE WHEN deadline BETWEEN NOW() AND (NOW() + INTERVAL 1 DAY) AND status = 'pending' THEN 1 ELSE 0 END) as is_urgent 
    FROM Tasks WHERE user_id = ? ORDER BY deadline ASC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}
$stmt->close();
$tasks_json = json_encode($tasks);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Studiator - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css"> <style>
        :root {
            --bg-start: #000000;
            --bg-end: #1a1a1a;
        }
        .gradient-bg {
    /* This adds your image AND a dark overlay so the white text stays readable */
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                url('image/dashboard.jpg') no-repeat center center/cover fixed;
    min-height: 100vh;
    width: 100%;
}
        .card-shadow { box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        .priority-high { border-left: 5px solid #ef4444; }
        .priority-medium { border-left: 5px solid #f59e0b; }
        .priority-low { border-left: 5px solid #10b981; }
        
        /* Dashboard glass card style */
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
    </style>
</head>
<body class="min-h-full gradient-bg">
<main class="min-h-full gradient-bg">
    <header class="text-white p-6">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold mb-2">Studiator</h1>
                <p class="text-xl opacity-90">Plan your study sessions smartly</p>
            </div>
            <div class="flex items-center gap-6">
    <a href="profile.php" class="flex items-center gap-3 hover:opacity-80 transition group">
        <div class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center font-bold border-2 border-white/20 group-hover:border-white">
            <?= strtoupper(substr($username, 0, 1)) ?>
        </div>
        <div class="hidden md:block">
            <p class="text-xs opacity-70 leading-none">Logged in as</p>
            <p class="font-bold text-white"><?= htmlspecialchars($username) ?></p>
        </div>
    </a>

    <div class="flex gap-2">
        <a href="add_task.php" class="bg-white text-black px-4 py-2 rounded-lg font-bold hover:bg-gray-200">Add Task</a>
        <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700">Logout</a>
    </div>
</div>
    </header>

    <div class="max-w-6xl mx-auto px-6 pb-24">
        <div class="glass-card rounded-xl p-6 mb-8 card-shadow">
    <div class="flex justify-between items-end mb-4">
        <div>
            <h2 class="text-2xl font-bold">Progress Tracking</h2>
            <p class="opacity-70 text-sm">Evaluation: <span class="text-green-400 font-bold"><?php echo $evaluation; ?></span></p>
        </div>
        <div class="text-right">
            <span class="text-3xl font-bold"><?php echo $percent; ?>%</span>
        </div>
    </div>
    <div class="w-full bg-white/10 rounded-full h-4 overflow-hidden border border-white/5">
        <div class="bg-gradient-to-r from-blue-500 to-green-400 h-full transition-all duration-1000" 
             style="width: <?php echo $percent; ?>%"></div>
    </div>
</div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="glass-card rounded-lg p-4 card-shadow text-center">
                <div class="text-2xl mb-1">📚</div>
                <p class="text-sm opacity-70">Study</p>
                <p id="study-count" class="text-xl font-bold">0</p>
            </div>
            <div class="glass-card rounded-lg p-4 card-shadow text-center">
                <div class="text-2xl mb-1">📝</div>
                <p class="text-sm opacity-70">Assignments</p>
                <p id="assign-count" class="text-xl font-bold">0</p>
            </div>
            <div class="glass-card rounded-lg p-4 card-shadow text-center">
                <div class="text-2xl mb-1">📋</div>
                <p class="text-sm opacity-70">Exams</p>
                <p id="exam-count" class="text-xl font-bold">0</p>
            </div>
            <div class="glass-card rounded-lg p-4 card-shadow text-center">
                <div class="text-2xl mb-1">✅</div>
                <p class="text-sm opacity-70">Completed</p>
                <p id="done-count" class="text-xl font-bold">0</p>
            </div>
        </div>

        <div id="items-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            </div>

        <div id="empty-state" class="text-center py-12 hidden">
            <h3 class="text-2xl text-white opacity-50">No tasks found. Start by adding one!</h3>
        </div>
    </div>
</main>

<script>
    // 1. Inject PHP data into JS
    const studyItems = <?php echo $tasks_json; ?>;

    function renderDashboard() {
    const container = document.getElementById('items-container');
    const emptyState = document.getElementById('empty-state');
    
    if (studyItems.length === 0) {
        emptyState.classList.remove('hidden');
        return;
    }

    container.innerHTML = studyItems.map(item => {
        // --- LOGIC TO CHECK STATUS ---
        const isDone = item.status === 'completed';

        return `
        <div class="glass-card rounded-lg p-6 card-shadow priority-${item.priority} relative overflow-hidden ${isDone ? 'opacity-50' : ''}">
            
            ${isDone ? `
                <div class="absolute top-0 right-0 bg-green-600 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg">
                    ✅ COMPLETED
                </div>
            ` : item.is_overdue == 1 ? `
                <div class="absolute top-0 right-0 bg-black text-red-500 border border-red-500 text-[10px] font-bold px-3 py-1 rounded-bl-lg">
                    🚫 TIME OVER
                </div>
            ` : item.is_urgent == 1 ? `
                <div class="absolute top-0 right-0 bg-red-600 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg animate-pulse">
                    ⚠️ DUE SOON
                </div>
            ` : ''}

            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-bold ${isDone ? 'line-through opacity-70' : ''}">${item.subject}</h3>
                    <p class="text-sm opacity-80">${item.topic}</p>
                </div>
                <span class="bg-black text-white text-xs px-2 py-1 rounded">${item.type.toUpperCase()}</span>
            </div>
            
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span>📅 Deadline:</span><span>${item.deadline || item.scheduled_date}</span></div>
                <div class="flex justify-between"><span>⏱️ Duration:</span><span>${item.duration}h</span></div>
                <div class="flex justify-between"><span>📂 Category:</span><span class="text-blue-300 font-bold">${item.category || 'Study'}</span></div>
            </div>

            <div class="mt-4 pt-4 border-t border-white/10 flex justify-between items-center">
                <span class="text-xs uppercase font-bold text-blue-400">${item.difficulty || 'Normal'}</span>
                <div class="flex gap-3">
                    ${!isDone ? `
                        <button onclick="markComplete(${item.task_id})" class="text-green-400 text-xs hover:underline">Complete</button>
                    ` : '<span class="text-green-500 text-xs font-bold italic">Task Finished</span>'}
                    
                    <button onclick="deleteTask(${item.task_id})" class="text-red-400 text-xs hover:underline">Delete</button>
                </div>
            </div>
        </div>
    `}).join('');

    updateStats();
}

    function updateStats() {
        document.getElementById('study-count').innerText = studyItems.filter(i => i.type === 'study').length;
        document.getElementById('assign-count').innerText = studyItems.filter(i => i.type === 'assignment').length;
        document.getElementById('exam-count').innerText = studyItems.filter(i => i.type === 'exam' || i.type === 'ct').length;
        document.getElementById('done-count').innerText = studyItems.filter(i => i.status === 'completed').length;
    }

    function deleteTask(id) {
        if(confirm('Are you sure you want to delete this task?')) {
            window.location.href = `delete_task.php?id=${id}`;
        }
    }

    function markComplete(id) {
        if(confirm('Mark this task as completed?')) {
        // This will send the user to a script to update the status
            window.location.href = `update_status.php?id=${id}&status=completed`;
        }
    }

    // Run on load
    renderDashboard();
</script>
</body>
</html>