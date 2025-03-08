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

// Load environment variables
$config = parse_ini_file(__DIR__ . '/.env');

// Check if environment variables are loaded
if (!$config) {
    die("Error: Could not load configuration file.");
}
$servername = $config['DB_SERVER'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];
$dbname = $config['DB_NAME'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $note = $_POST['note'];
    $payment_mode = $_POST['payment_mode'];
    
    // Convert selected services array to a comma-separated string
    $service = isset($_POST['service']) ? implode(", ", $_POST['service']) : '';

    $sql = "INSERT INTO member_profile (reg_member_id, date, time, price, service, note, mode_of_payment, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $user_id, $date, $time, $price, $service, $note, $payment_mode, $status);

    if ($stmt->execute()) {
        header("Location: profile.php?id=$user_id&tab=history-tab&message=Appointment+added+successfully");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}

// Redirect back to the user profile page to display the history tab with updated data
header("Location: profile.php?id=" . $user_id);
exit();
?>
