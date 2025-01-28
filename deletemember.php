<?php
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $member_id = $_GET['id'];
        
        // Check if confirmation is received via GET
        if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
            // SQL query to delete the member by ID
            $sql = "DELETE FROM reg_member WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $member_id);
            
            if ($stmt->execute()) {
                // Redirect back to the member list after deletion
                header("Location: all_member.php?message=Member deleted successfully");
                exit;
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            // Ask for confirmation before deleting
            echo "<script>
                var confirmation = confirm('Are you sure you want to delete this member?');
                if (confirmation) {
                    window.location.href = 'deletemember.php?id=" . $member_id . "&confirm=yes';
                } else {
                    window.location.href = 'all_member.php';
                }
            </script>";
        }
    } else {
        echo "Invalid request!";
    }
}

$conn->close();
?>
