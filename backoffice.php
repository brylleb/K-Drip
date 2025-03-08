<?php

// Secure session settings
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    ini_set('session.cookie_secure', 0); // Allow HTTP sessions on localhost
} else {
    ini_set('session.cookie_secure', 1); // Enforce HTTPS on production
}

// Always enforce these security settings
ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookies
ini_set('session.use_strict_mode', 1); // Prevent session fixation attacks


session_start();

// Set the session timeout duration (e.g., 15 minutes = 900 seconds)
$timeout_duration = 900;

// Check session expiration
if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    if ($elapsed_time > $timeout_duration) {
        session_unset();     // Unset all session variables
        session_destroy();   // Destroy the session
        header("Location: login.php?message=session_expired"); // Redirect to login with an error message
        exit();
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Regenerate session ID periodically to prevent fixation
if (!isset($_SESSION['regenerated'])) {
    session_regenerate_id(true); // Regenerate session ID
    $_SESSION['regenerated'] = true; // Mark as regenerated
}
        // Check if the user is not logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php"); // Redirect to login page if not logged in
            exit();
        }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="mainpage.css">
    <title>Back Office</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .main-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            background-color: #fff;
        }

        .box-container {
            background-color: #f4f4f4;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 1200px;
            padding: 30px;
            border-radius: 10px;
        }

        .box {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e5a1a1;
            color: #333;
            height: 150px;
            text-align: center;
            font-size: 1.5em;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.3s ease, background-color 0.3s ease;
            padding: 10px;
        }

        .box:hover {
            transform: scale(1.05);
            background-color: #d18b8b;
        }

        @media screen and (max-width: 768px) {
            .box {
                font-size: 1.2em;
                height: 120px;
            }
        }

        @media screen and (max-width: 480px) {
            .box {
                font-size: 1em;
                height: 100px;
            }
        }
    </style>
</head>
<body>
<div class="banner">
        <a href="index.html">K-Drip</a>
        <div class="right">
            <div class="dropdown">
                <a>Contact Us</a>
                <div class="dropdown-content">
                    <p> Phone: +639065310391</p>
                    <p> Email: K-Drip@gmail.com</p>
                </div>
            </div>
            <div class="dropdown">
                <a>Services</a>
                <div class="dropdown-content">
                    <a href="push.html">Gluta Push</a>
                    <a href="drip.html">Gluta Drip</a>
                </div>
            </div>
            <div class="dropdown">
                <a>Information</a>
                <div class="dropdown-content">
                    <a href="benefits.html">Benefits</a>
                    <a href="about.html">About Us</a>
                </div>
            </div>
        </div>
    </div>
        <div class="box-container">
            <div class="box" onclick="location.href='all_member.php';">Member Record</div>
            <div class="box" onclick="location.href='calendar.php';">Appointment Calendar</div>
        </div>
        <a href="logout.php" class="button">Log-out</a>
</body>
</html>
