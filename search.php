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

// Get the search query
$searchQuery = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';

// Prepare SQL query
$sql = "SELECT id, first_name, last_name, contact_number, email, birthday, age, address 
        FROM reg_member 
        WHERE first_name LIKE '%$searchQuery%' 
        ORDER BY first_name ASC";

$result = $conn->query($sql);

// Fetch results as an array
$rows = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}

// Return results in JSON format
header('Content-Type: application/json');
echo json_encode($rows);

// Close connection
$conn->close();
?>
