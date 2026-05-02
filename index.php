<?php
require_once 'config.php';

// redirect logged in users
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Studiator — Welcome</title>
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Floating Navbar -->
<header>
    <div class="container d-flex justify-content-between align-items-center">
        
        <div class="nav-buttons">
            <a class="btn btn--primary" href="signup.php">Sign Up</a>
            <a class="btn btn--secondary" href="login.php"> Login </a>
        </div>
    </div>
</header>

<!-- Hero Section -->
<section class="hero">
    <div class="logo-container">
        <img src="image/logo.png" alt="Studiator Logo" class="logo glass-logo">
    </div>
    <div class="hero-text text-center">
        <h1>Welcome to Studiator</h1>
        <p><b>A simple place to track study sessions, assignments, and exams<br>build your best habits</b></p>
        <div class="hero-buttons">
            <a class="btn btn--primary btn-lg" href="signup.php">Get Started</a>
            <a class="btn btn--secondary btn-lg" href="login.php">Already have an account?</a>
        </div>
    </div>
</section>

</body>
</html>
