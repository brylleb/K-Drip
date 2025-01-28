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
            // Start the transaction to ensure both deletes happen or neither
            $conn->begin_transaction();
            
            try {
                // SQL query to delete the member profile by member_id
                $sql_profile = "DELETE FROM reg_member WHERE id = ?";
                $stmt_profile = $conn->prepare($sql_profile);
                $stmt_profile->bind_param("i", $member_id);
                $stmt_profile->execute();

                // SQL query to delete from member_profile using reg_member_id
                $sql_member = "DELETE FROM member_profile WHERE reg_member_id = ?";
                $stmt_member = $conn->prepare($sql_member);
                $stmt_member->bind_param("i", $member_id);
                $stmt_member->execute();

                // Commit the transaction if both deletions are successful
                $conn->commit();

                // Redirect back to the member list after deletion
                header("Location: all_member.php?message=Member and profile deleted successfully");
                exit;
            } catch (Exception $e) {
                // Rollback the transaction if an error occurs
                $conn->rollback();
                echo "Error: " . $e->getMessage();
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
        echo "Invalid request! ID is missing.";
    }
} else {
    echo "Invalid request! This page should be accessed via GET method.";
}

$conn->close();
?>
