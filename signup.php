<?php
require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($username === '' || $email === '' || $password === '' || $confirm === '') {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } else {
        // check unique username/email
        $stmt = $mysqli->prepare("SELECT user_id FROM Users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Username or email already taken.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $mysqli->prepare("INSERT INTO Users (username, email, password) VALUES (?, ?, ?)");
            $ins->bind_param('sss', $username, $email, $hash);
            if ($ins->execute()) {
                $_SESSION['user_id'] = $ins->insert_id;
                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
                exit;
            } else {
                $errors[] = "Signup failed: " . $mysqli->error;
            }
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Sign Up — Studiator</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Form Page -->
<div class="form-page">
    <div class="form-container">

        <h2>Create Your Account</h2>

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

        <!-- Signup Form -->
        <form method="post" action="signup.php">
            <input type="text" name="username" placeholder="Username" required value="<?=htmlspecialchars($_POST['username'] ?? '')?>">
            <input type="email" name="email" placeholder="Email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <h2> 
            <button class="btn btn--primary" type="submit">Sign Up</button>
            </h2>
        </form>

        <p style="margin-top:12px;">Already registered? <a href="login.php"> Login here </a>.</p>
    </div>
</div>

</body>
</html>
