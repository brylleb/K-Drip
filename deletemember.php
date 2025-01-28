<?php
session_start();

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
