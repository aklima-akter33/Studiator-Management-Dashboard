<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit;
}

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// Fetch user data
$stmt = $mysqli->prepare("SELECT username, email, password FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $new_name = trim($_POST['username']);
        $upd = $mysqli->prepare("UPDATE Users SET username = ? WHERE user_id = ?");
        $upd->bind_param("si", $new_name, $user_id);
        if ($upd->execute()) {
            $success_msg = "Name updated!";
            $user['username'] = $new_name;
        }
    } elseif (isset($_POST['change_pw'])) {
        if (password_verify($_POST['curr_pw'], $user['password'])) {
            $hashed = password_hash($_POST['new_pw'], PASSWORD_DEFAULT);
            $upd_pw = $mysqli->prepare("UPDATE Users SET password = ? WHERE user_id = ?");
            $upd_pw->bind_param("si", $hashed, $user_id);
            $upd_pw->execute();
            $success_msg = "Password changed!";
        } else {
            $error_msg = "Incorrect current password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Studiator</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="dashboard-page">
    <div class="form-page">
        <div class="form-container">
            <h2>User Profile</h2>
            <?php if ($success_msg): ?><p class="text-green-700 text-center font-bold mb-4"><?= $success_msg ?></p><?php endif; ?>
            <?php if ($error_msg): ?><p class="text-red-700 text-center font-bold mb-4"><?= $error_msg ?></p><?php endif; ?>

            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-black text-white rounded-full mx-auto flex items-center justify-center text-3xl font-bold mb-2">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
                <p class="text-black font-bold"><?= htmlspecialchars($user['email']) ?></p>
            </div>

            <form method="POST" class="border-b border-black/10 pb-6 mb-6">
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                <button type="submit" name="update_profile" class="btn btn-primary">Update Name</button>
            </form>

            <form method="POST">
                <h3 class="font-bold text-black mb-4">Change Password</h3>
                <label>Current Password</label>
                <input type="password" name="curr_pw" required>
                <label>New Password</label>
                <input type="password" name="new_pw" required>
                <button type="submit" name="change_pw" class="btn btn-primary">Update Password</button>
                <a href="dashboard.php" class="btn btn-secondary mt-4 block text-center">Back to Dashboard</a>
            </form>
        </div>
    </div>
</body>
</html>