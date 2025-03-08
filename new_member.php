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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Member Registration</title>
    <link rel="stylesheet" href="mainpage.css">
    <link rel="stylesheet" href="userprofile.css">
    <link rel="stylesheet" href="new_member.css">
</head>
<body>
    <div class="container">
        <div class="content-member">
            <h1>New Member Information</h1>
            <form action="database_insert.php" method="POST" class="form-container">
                <!-- First Name Field -->
                <div class="form-group">
                    <label for="first_name"><b>First Name:</b></label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <!-- Last Name Field -->
                <div class="form-group">
                    <label for="last_name"><b>Last Name:</b></label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
                <!-- Contact Number Field -->
                <div class="form-group">
                    <label for="contact_number"><b>Contact Number:</b></label>
                    <input type="tel" id="contact_number" name="contact_number" required>
                </div>
                <!-- Birthday Field -->
                <div class="form-group">
                    <label for="birthday"><b>Birthday:</b></label>
                    <input type="date" id="birthday" name="birthday" required>
                </div>
                <!-- Age Field  -->
                <div class="form-group">
                    <label for="age"><b>Age:</b></label>
                    <input type="number" id="age" name="age" required>
                </div>
                <!-- Address Field -->
                <div class="form-group">
                    <label for="address"><b>Address:</b></label>
                    <input type="text" id="address" name="address" required>
                </div>
                <!-- Form Actions -->
                <div class="form-actions">
                    <input type="submit" value="Submit" class="button">
                    <a href="index.html" class="button">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
