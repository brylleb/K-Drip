<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection settings
$servername = "sql205.infinityfree.com";
$username = "if0_38112458";
$password = "8YH7MFDryvDx8";
$dbname = "if0_38112458_kdrip_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $user_id = $_POST['user_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $service = $_POST['service'];
    $note = $_POST['note'];
    $payment_mode = $_POST['payment_mode'];

    // Insert data into transaction table
    $sql = "INSERT INTO member_profile (date, time, price, status, service, note, mode_of_payment, reg_member_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssssi", $date, $time, $price, $status, $service, $note, $payment_mode, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Appointment successfully recorded!";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Close connection
$conn->close();

// Redirect back to the user profile page to display the history tab with updated data
header("Location: profile.php?id=" . $user_id);
exit();
?>
