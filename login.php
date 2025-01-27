<?php
session_start();
// Database connection settings
$servername = "sql205.infinityfree.com";
$username = "if0_38112458";
$password = "8YH7MFDryvDx8";
$dbname = "if0_38112458_kdrip_database";

// Initialize variables
$login_error = "";

// Establish database connection
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Error: Could not connect to the database.");
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $form_email_or_username = filter_var($_POST['email'], FILTER_SANITIZE_STRING); // Accept either email or username
    $form_password = $_POST['password'];

    if (!empty($form_email_or_username) && !empty($form_password)) {
        // Query to check if the email or username exists
        $stmt = $pdo->prepare("SELECT * FROM reg_member WHERE email = :email_or_username OR username = :email_or_username");
        $stmt->execute(['email_or_username' => $form_email_or_username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($form_password, $user['password'])) {
            // Start a session and store user data
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];

            // Redirect to the all member list page
            header("Location: backoffice.php");
            exit;
        } else {
            $login_error = "Invalid email/username or password. Please try again.";
        }
    } else {
        $login_error = "Both email/username and password are required.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K-Drip Portal - Login</title>
    <link rel="stylesheet" href="mainpage.css">
    <link rel="stylesheet" href="log_in.css">
</head>
<style>
    /* Log in form style */
/* Base Styles for Form */
.form-horizontal .form-group {
    display: flex;
    flex-wrap: wrap; /* Allow elements to wrap in small screens */
    align-items: center;
    margin-bottom: 10px;
}

.form-horizontal label {
    width: 100%; /* On small screens, labels take full width */
    margin-right: 10px;
    text-align: left; /* Align labels to the left on smaller screens */
    margin-bottom: 5px; /* Add some space below labels */
}

/* Input Fields */
.form-horizontal input {
    flex: 1;
    padding: 10px;
    margin: 0;
    border: 2px solid #ddd;
    border-radius: 5px;
    font-size: 1em;
    box-sizing: border-box; /* Ensure padding doesn't affect the element's width */
}

.form-horizontal button {
    padding: 10px 20px;
    margin-top: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1.1em;
    cursor: pointer;
    transition: background-color 0.3s;
}

.form-horizontal button:hover {
    background-color: #45a049;
}

/* Mobile Responsiveness */
@media screen and (max-width: 768px) {
    .form-horizontal .form-group {
        flex-direction: column; /* Stack form elements vertically */
    }

    .form-horizontal label {
        width: 100%; /* Labels will take full width */
        text-align: left; /* Align label to left */
    }

    .form-horizontal input, .form-horizontal button {
        width: 100%; /* Make inputs and buttons full width */
        margin-bottom: 10px; /* Add margin below inputs and buttons */
    }
}

@media screen and (max-width: 480px) {
    /* Ensure the font size is smaller and padding is adjusted for small screens */
    .form-horizontal input, .form-horizontal button {
        font-size: 0.9em;
        padding: 12px;
    }

    .form-horizontal label {
        font-size: 0.9em;
    }

    .form-horizontal button {
        font-size: 1em;
    }
}

</style>
<body>
    <div class="banner">
        <a href="index.html">K-Drip</a>
        <div class="right">
            <div class="dropdown">
                <a>Contact Us</a>
                <div class="dropdown-content">
                    <p>Phone: +639065310391</p>
                    <p>Email: K-Drip@gmail.com</p>
                </div>
            </div>
            <div class="dropdown">
                <a>Services</a>
                <div class="dropdown-content">
                    <a href="push.html">Gluta Push</a>
                    <a href="drip.html">Gluta Drip</a>
                </div>
            </div>
            <div class="dropdown">
                <a>Information</a>
                <div class="dropdown-content">
                    <a href="benefits.html">Benefits</a>
                    <a href="about.html">About Us</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <h2>Login to K-Drip</h2>
        
        <!-- Display error message if login fails -->
        <?php if (!empty($login_error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($login_error); ?></p>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="login.php" method="POST" class="form-horizontal">
    <div class="form-group">
        <label for="email"><b>Email or Username:</b></label>
        <input type="text" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="password"><b>Password:</b></label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Log in</button>
</form>
    </div>
</body>
</html>
