<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
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
    // Process form submission for updates
    $date = $_POST['date'];
    $time = $_POST['time'];
    $price = $_POST['price'];
    $service = $_POST['service'];
    $note = $_POST['note'];
    $mode_of_payment = $_POST['mode_of_payment'];
    $status = $_POST['status'];

    // Update query
    $update_sql = "UPDATE member_profile 
                   SET date = ?, time = ?, price = ?, service = ?, note = ?, mode_of_payment = ?, status = ? 
                   WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssssi", $date, $time, $price, $service, $note, $mode_of_payment, $status, $row_id);

    if ($update_stmt->execute()) {
        $_SESSION['message'] = "Record updated successfully.";
        header("Location: profile.php?id=" . $row['reg_member_id'] . "&tab=history-tab");
        exit();
    } else {
        $error = "Error updating record: " . $conn->error;
    }
}

// Close connection
$stmt->close();
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
        <form method="POST" action="editsession.php">
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
                <select id="service" name="service" required>
                        <option value="Regular Push">Regular Push</option>
                        <option value="Special Push">Special Push</option>
                        <option value="Supreme Push">Supreme Push</option>
                        <option value="Regular Drip">Regular Drip</option>
                        <option value="Special Drip">Special Drip</option>
                        <option value="Supreme Drip">Supreme Drip</option>
                        <option value="Slim Drip">Slim Drip</option>
                        <option value="Regular Push Package">Regular Push Package</option>
                        <option value="Regular Drip Package">Regular Drip Package</option>
                        <option value="Supreme Drip Package">Supreme Drip Package</option>
                        <option value="Slim Drip Package">Slim Drip Package</option>
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


