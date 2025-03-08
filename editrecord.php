<?php

// Secure session settings
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    ini_set('session.cookie_secure', 0); // Allow HTTP sessions on localhost
} else {
    ini_set('session.cookie_secure', 1); // Enforce HTTPS on production
}

// Always enforce these security settings
ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookies
ini_set('session.use_strict_mode', 1); // Prevent session fixation attacks


session_start();

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

// Check if ID is provided
if (!isset($_POST['id'])) {
    die("Invalid request.");
}

$row_id = intval($_POST['id']);

// Fetch the record to be edited
$sql = "SELECT * FROM member_profile WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $row_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Record not found.");
}

$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    // Retrieve form inputs
    $row_id = intval($_POST['id']); // Get record ID from the form
    $date = $_POST['date'];
    $time = $_POST['time'];
    $price = $_POST['price'];
    $note = $_POST['note'];
    $mode_of_payment = $_POST['mode_of_payment'];
    $status = $_POST['status'];
    
    // Convert service array to a CSV string (if multiple services are selected)
    $service = isset($_POST['service']) ? implode(", ", $_POST['service']) : '';

    // Fetch reg_member_id for redirection
    $member_query = "SELECT reg_member_id FROM member_profile WHERE id = ?";
    $member_stmt = $conn->prepare($member_query);
    $member_stmt->bind_param("i", $row_id);
    $member_stmt->execute();
    $member_result = $member_stmt->get_result();

    if ($member_result->num_rows > 0) {
        $member_row = $member_result->fetch_assoc();
        $reg_member_id = $member_row['reg_member_id'];
    } else {
        $_SESSION['message'] = "Error: Record not found.";
        header("Location: profile.php?id=$reg_member_id&tab=history-tab");
        exit();
    }

    // Update query
    $update_sql = "UPDATE member_profile 
                   SET date = ?, time = ?, price = ?, service = ?, note = ?, mode_of_payment = ?, status = ? 
                   WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssssi", $date, $time, $price, $service, $note, $mode_of_payment, $status, $row_id);

    if ($update_stmt->execute()) {
        $_SESSION['message'] = "Record updated successfully.";
    } else {
        $_SESSION['message'] = "Error updating record: " . $conn->error;
    }

    // Redirect back to the profile page
    header("Location: profile.php?id=$reg_member_id&tab=history-tab");
    exit();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Session</title>
    <link rel="stylesheet" href="editsession.css">
</head>
<body>
    <div class="main-container">
        <h1>Update Session</h1>
        <?php if (isset($error)): ?>
            <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="editrecord.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">

            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($row['date']); ?>" required>
            </div>

            <div class="form-group">
                <label for="time">Time:</label>
                <input type="time" id="time" name="time" value="<?php echo htmlspecialchars($row['time']); ?>" required>
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" required>
            </div>

            <div class="form-group">
            <label for="service">Service:</label>
                    <select id="service" name="service[]" multiple required>
                        <option value="Regular Push">Regular Push</option>
                        <option value="Special Push">Special Push</option>
                        <option value="Supreme Push">Supreme Push</option>
                        <option value="Glow Drip">Glow Drip</option>
                        <option value="Miracle Drip">Miracle Drip</option>
                        <option value="Immune Booster Drip">Immune Booster Drip</option>
                        <option value="Metabo Drip">Metabo Drip</option>
                        <option value="Cindyrella Drip">Cindyrella Drip</option>
                        <option value="Regular Push Package">Regular Push Package</option>
                        <option value="Glow Drip Package">Glow Drip Package</option>
                        <option value="Immune Booster Drip Package">Immune Booster Drip Package</option>
                        <option value="Metabo Drip Package">Metabo Drip Package</option>
                        <option value="Cindyrella Drip Package(5+1)">Cindyrella Drip Package(5+1)</option>
                        <option value="Cindyrella Drip Package(10+2)">Cindyrella Drip Package(10+2)</option>
                        <option value="RF & Cavitation">RF & Cavitation</option>
                        <option value="V-Line Double Chin">V-Line Double Chin</option>
                        <option value="Love Handles/Back">Love Handles/Back</option>
                        <option value="Arms/Tummy/Thighs">Arms/Tummy/Thighs</option>
                        <option value="Injection Only">Injection Only</option>
                    </select>
            </div>

            <div class="form-group">
                <label for="note">Note:</label>
                <input type="text" id="note" name="note" value="<?php echo htmlspecialchars($row['note']); ?>">
            </div>

            <div class="form-group">
                <label for="payment-mode">Mode of Payment:</label>
                <select id="payment-mode" name="mode_of_payment" required>
                        <option value="Gcash">Gcash</option>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="N/A">N/A</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                        <option value="Free">Free</option>
                        <option value="N/A">N/A</option>
                </select>
            </div>

            <div class="button-group">
                <button type="submit" name="save" class="save-button">Save</button>
                <button type="button" class="save-button" onclick="window.location.href='profile.php?id=<?php echo $row['reg_member_id']; ?>&tab=history-tab';">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>


