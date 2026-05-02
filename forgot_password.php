<?php
require_once 'config.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Check if passwords match
    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // 2. Check if email exists
        $stmt = $mysqli->prepare("SELECT user_id FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // 3. Update Password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $mysqli->prepare("UPDATE Users SET password = ? WHERE email = ?");
            $update_stmt->bind_param("ss", $hashed_password, $email);
            
            if ($update_stmt->execute()) {
                // Redirect to login with success message
                header("Location: login.php?reset=success");
                exit;
            } else {
                $error = "Something went wrong. Please try again.";
            }
        } else {
            $error = "No account found with that email address.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Studiator</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="dashboard-page">
    <div class="form-page">
        <div class="form-container">
            <h2>Reset Password</h2>
            <p class="text-sm text-black mb-6 text-center">Enter your email and a new password to regain access.</p>

            <?php if ($error): ?>
                <p class="text-red-600 text-center mb-4 font-bold"><?= $error ?></p>
            <?php endif; ?>

            <form method="POST">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your registered email" required>

                <label>New Password</label>
                <input type="password" name="new_password" placeholder="Min. 6 characters" required>

                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="Repeat new password" required>

                <button type="submit" class="btn btn-primary">Reset Password</button>
                
                <a href="login.php" class="btn btn-secondary mt-4 block text-center">Back to Login</a>
            </form>
        </div>
    </div>
</body>
</html>