<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize message variable
$message = '';

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

// Create a database connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data and sanitize it
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $contact_number = htmlspecialchars(trim($_POST['contact_number']));
    $birthday = htmlspecialchars(trim($_POST['birthday']));
    $age = htmlspecialchars(trim($_POST['age']));
    $address = htmlspecialchars(trim($_POST['address']));
    // $password = htmlspecialchars(trim($_POST['password'])); // Assuming the password field is there in the form

    // Check if any required field is empty
    if (empty($first_name) || empty($last_name) || empty($contact_number) || empty($birthday) || empty($age) || empty($address)) {
        $message = "All fields are required!";
    } else {
        // Hash the password using password_hash()
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // If you want to store the hashed password

        // Prepare the SQL query to insert data using parameterized queries
        $stmt = $conn->prepare("INSERT INTO reg_member (first_name, last_name, contact_number, birthday, age, address) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $contact_number, $birthday, $age, $address);

        // Execute the query and check for success
        if ($stmt->execute()) {
            $message = "New member has been successfully registered!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Member Registration</title>

    <style>
        /* Styles for the popup modal */
        /* Modal Wrapper */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Default width */
            max-width: 400px; /* Max width for larger screens */
            border-radius: 10px;
            text-align: center;
            box-sizing: border-box; /* Ensure padding doesn't affect width */
        }

        /* Modal Buttons */
        .modal button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .modal button:hover {
            background-color: #45a049;
        }

        /* Responsive Styling for Small Screens */
        @media screen and (max-width: 768px) {
            .modal-content {
                width: 90%; /* Increase width on smaller screens */
                margin: 20% auto; /* Adjust vertical margin for better centering */
            }

            .modal button {
                font-size: 14px; /* Smaller font size for mobile */
                padding: 8px 16px; /* Adjust button padding */
            }
        }

        @media screen and (max-width: 480px) {
            .modal-content {
                width: 95%; /* Make the modal take up more width on very small screens */
                padding: 15px; /* Adjust padding */
                margin: 25% auto; /* Adjust vertical margin for smaller screens */
            }

            .modal button {
                font-size: 14px; /* Ensure button text is readable */
                padding: 10px 18px; /* Adjust padding for buttons */
            }
        }
    </style>

</head>
<body>

    <!-- Modal HTML -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <p id="modalMessage">Your message here</p>
            <button id="closeModalBtn">Close</button>
        </div>
    </div>

    <script>
        // Show the modal when a message is set (for demonstration, this is manually triggered)
        window.onload = function() {
            var modal = document.getElementById('messageModal');
            var message = document.getElementById('modalMessage');

            // Example: Set a message dynamically and show the modal
            message.textContent = "Success! Member registered.";
            modal.style.display = "block";
        };

        // Close the modal when the close button is clicked and redirect
        document.getElementById('closeModalBtn').onclick = function() {
            var modal = document.getElementById('messageModal');
            modal.style.display = "none";
            window.location.href = "index.html"; // Replace with your desired URL
        }

        // Close the modal if the user clicks anywhere outside of it and redirect
        window.onclick = function(event) {
            var modal = document.getElementById('messageModal');
            if (event.target == modal) {
                modal.style.display = "none";
                window.location.href = "index.html"; // Replace with your desired URL
            }
        }
    </script>

</body>
</html>
