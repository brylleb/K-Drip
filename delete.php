<?php
session_start();
// Secure session settings
ini_set('session.cookie_secure', 1); // Ensure cookies are sent over HTTPS
ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookies
ini_set('session.use_strict_mode', 1); // Prevent session fixation attacks

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

// Check if 'id' is passed in the POST request
if (isset($_POST['id'])) {
    // Sanitize input to prevent SQL injection
    $id = (int) $_POST['id'];  // Make sure it's an integer

    // Display confirmation message using JavaScript
    echo "<script>
        var confirmation = confirm('Are you sure you want to delete this record? This action cannot be undone.');
        if (confirmation) {
            // Begin a transaction to ensure data consistency
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = window.location.href;  // This will keep the action to the current page
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'id';
            input.value = '$id';
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        } else {
            window.location.href = 'all_member.php'; // Redirect back if canceled
        }
    </script>";
} else {
    echo "Invalid request.";
}

// If the confirmation is passed, delete the record
if (isset($_POST['id'])) {
    // Begin a transaction to ensure data consistency
    $conn->begin_transaction();

    try {
        // Delete associated data in other tables (if any)
        $sql1 = "DELETE FROM member_profile WHERE id = ?";  // Make sure to use the correct table name
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("i", $id);
        $stmt1->execute();

        // Finally, delete the member from 'reg_member' table
        $sql = "DELETE FROM reg_member WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // If all queries are successful, commit the transaction
        $conn->commit();

        // Redirect back to the page after deletion with a success message
        header("Location: all_member.php?message=" . urlencode("Record deleted successfully"));
        exit();
    } catch (Exception $e) {
        // If any query fails, rollback the transaction
        $conn->rollback();
        // Redirect back with an error message
        header("Location: all_member.php?message=" . urlencode("Error deleting member"));
        exit();
    }
}

// Close connection
$conn->close();
?>
