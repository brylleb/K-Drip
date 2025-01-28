<?php
// Check if 'id' parameter is set in the URL for deletion
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    // Redirect to the member list page if no ID is provided
    header("Location: all_member.php");
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

// Handle deletion after confirmation
if (isset($_POST['delete']) && $_POST['delete'] == 'yes') {
    $id = $_POST['id'];

    // SQL query to delete the member with the given ID
    $sql = "DELETE FROM reg_member WHERE id = ?";

    // Prepare the statement to prevent SQL injection
    if ($stmt = $conn->prepare($sql)) {
        // Bind the 'id' parameter to the query
        $stmt->bind_param("i", $id);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to the member list page after successful deletion
            header("Location: all_member.php");
            exit(); // Ensure the script stops executing here after redirection
        } else {
            echo "Error deleting record: " . $conn->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Error preparing the SQL statement.";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Member Confirmation</title>
    <style>
        /* Modal Container */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }

        /* Button Styling */
        .modal-button {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .yes-btn {
            background-color: #f44336;
            color: white;
        }

        .no-btn {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>

<!-- Modal HTML -->
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <h2>Are you sure you want to delete this member?</h2>
        <form action="deletemember.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <input type="hidden" name="delete" value="yes">
            <button type="submit" class="modal-button yes-btn">Yes</button>
        </form>
        <button id="confirmNo" class="modal-button no-btn">No</button>
    </div>
</div>

<!-- JavaScript for handling confirmation -->
<script>
    // Show the modal when the page loads
    document.getElementById("confirmationModal").style.display = "block";

    // If No is clicked, redirect back to the member list
    document.getElementById("confirmNo").onclick = function () {
        window.location.href = "all_member.php"; // Redirect back to the member list
    };
</script>

</body>
</html>
