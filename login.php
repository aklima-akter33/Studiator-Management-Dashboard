<?php
require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_username = trim($_POST['email_or_username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email_or_username === '' || $password === '') {
        $errors[] = "All fields are required.";
    } else {
        $stmt = $mysqli->prepare("SELECT user_id, username, password FROM Users WHERE email = ? OR username = ? LIMIT 1");
        $stmt->bind_param('ss', $email_or_username, $email_or_username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $username, $hash);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
                exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No account found with that email/username.";
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Login — Studiator</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Form Page -->
<div class="form-page">
    <div class="form-container">

        <h2>Login to Your Account</h2>

        <!-- Error messages -->
        <?php if (!empty($errors)): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?=htmlspecialchars($e)?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="post" action="login.php">
    <label>Email or Username</label>
    <input type="text" name="email_or_username" placeholder="Email or Username" required value="<?=htmlspecialchars($_POST['email_or_username'] ?? '')?>">
    
    <label>Password</label>
    <input type="password" name="password" placeholder="Password" required>

    <div class="flex-row-end">
        <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
    </div>

    <button class="btn btn--primary" type="submit">Login</button>
</form>

<p style="margin-top:20px; text-align:center;">
    Don't have an account? <a href="signup.php" style="font-weight:bold; text-decoration:underline;">Sign up here</a>.
</p>
    </div>
</div>

</body>
</html>
