<?php
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
