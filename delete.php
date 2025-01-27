<?php
// Database connection settings
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

    // Begin a transaction to ensure data consistency
    $conn->begin_transaction();

    try {
        // Delete associated data in other tables (if any)
        // Example: deleting related records in 'related_table_name'
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
} else {
    echo "Invalid request.";
}

// Close connection
$conn->close();
?>
<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kdrip_database";

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

    // Begin a transaction to ensure data consistency
    $conn->begin_transaction();

    try {
        // Delete associated data in other tables (if any)
        // Example: deleting related records in 'related_table_name'
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
} else {
    echo "Invalid request.";
}

// Close connection
$conn->close();
?>
<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kdrip_database";

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

    // Begin a transaction to ensure data consistency
    $conn->begin_transaction();

    try {
        // Delete associated data in other tables (if any)
        // Example: deleting related records in 'related_table_name'
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
} else {
    echo "Invalid request.";
}

// Close connection
$conn->close();
?>
