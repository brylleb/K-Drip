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

// Get the user ID from the URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get the current page number for pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5; // Number of records per page
$offset = ($page - 1) * $limit;

if ($user_id > 0) {
    // Fetch user data from the database
    $sql = "SELECT first_name, last_name, contact_number, email, birthday, age, address FROM reg_member WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        die("User not found.");
    }

    // Fetch session history from the transaction table with pagination
    $history_sql = "SELECT * FROM member_profile WHERE reg_member_id = ? ORDER BY date ASC LIMIT ? OFFSET ?";
    $history_stmt = $conn->prepare($history_sql);
    $history_stmt->bind_param("iii", $user_id, $limit, $offset);
    $history_stmt->execute();
    $history_result = $history_stmt->get_result();

    // Fetch total number of sessions for pagination
    $count_sql = "SELECT COUNT(*) as total_sessions FROM member_profile WHERE reg_member_id = ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("i", $user_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_row = $count_result->fetch_assoc();
    $total_sessions = $count_row['total_sessions'];

    $stmt->close();
    $history_stmt->close();
    $count_stmt->close();
} else {
    die("Invalid User ID.");
}

// Close connection
$conn->close();

// Calculate the total number of pages
$total_pages = ceil($total_sessions / $limit);

// Get the active tab from URL or default to 'history-tab'
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'appointment-tab';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['first_name']); ?> Transaction Record</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <!-- Display success or error message -->
    <?php
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8');
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            alert('$message');
            window.history.replaceState({}, document.title, window.location.pathname + window.location.search.split('&message=')[0]);
        });
    </script>";
}
?>

    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Profile Picture -->
            <div class="profile-pic-container">
                <div class="profile-pic"></div>
            </div>
            <!-- User Info -->
            <div class="user-info-container">
                <div class="user-info">
                    <h4>First Name: <?php echo htmlspecialchars($user['first_name']); ?></h4>
                    <h4>Last Name: <?php echo htmlspecialchars($user['last_name']); ?></h4>
                    <h4>Contact: <?php echo htmlspecialchars($user['contact_number']); ?></h4>
                </div>
            </div>
            <div class="logout-button">
                <a href="all_member.php" style="text-decoration: none !important;"><b>All Member</b></a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-section">
            <h1>Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>

            <!-- Tabs -->
            <div class="tabs">
                <div class="tab <?php echo $active_tab == 'appointment-tab' ? 'active' : ''; ?>" data-target="appointment-tab">
                    Appointment
                </div>
                <div class="tab <?php echo $active_tab == 'history-tab' ? 'active' : ''; ?>" data-target="history-tab">
                    Session History
                </div>
            </div>

            <!-- Tab Content -->
            <div id="appointment-tab" class="tab-content <?php echo $active_tab == 'appointment-tab' ? 'active' : ''; ?>">
                <h2>Book Appointment</h2>

                <!-- Appointment Form -->
                <form action="appointment.php" method="POST" class="form-container">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>

                    <label for="time">Time:</label>
                    <input type="time" id="time" name="time" required>

                    <label for="price">Price:</label>
                    <input type="text" id="price" name="price" required>

                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                        <option value="Free">Free</option>
                        <option value="N/A">N/A</option>
                    </select>

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
                    <label for="note">Note:</label>
                    <input type="text" id="note" name="note">

                    <label for="payment-mode">Mode of Payment:</label>
                    <select id="payment-mode" name="payment_mode" required>
                        <option value="Gcash">Gcash</option>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="N/A">N/A</option>
                    </select>

                    <div class="button-group">
                        <button type="submit" class="paid-button">Submit</button>
                        <button type="reset" class="clear-button">Clear</button>
                    </div>
                </form>
            </div>

            <!-- Session History Tab -->
            <div id="history-tab" class="tab-content <?php echo $active_tab == 'history-tab' ? 'active' : ''; ?>">
                <h2>Drip/Push History</h2>
                <p>View your previous Drip and Push sessions here.</p>
                <div style="overflow-x: auto;">
    <table>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Price</th>
            <th>Service</th>
            <th>Note</th>
            <th>Mode of Payment</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php

// Display session history
while ($row = $history_result->fetch_assoc()) {
    $time = date("h:i A", strtotime($row['time']));  // Convert to 12-hour format with AM/PM

    echo "<tr>
            <td>" . htmlspecialchars($row['date']) . "</td>
            <td>" . $time . "</td>
            <td>" . htmlspecialchars($row['price']) . "</td>
            <td>" . nl2br(htmlspecialchars(str_replace(",", "\n\n", $row['service']))) . "</td>
            <td>" . htmlspecialchars($row['note']) . "</td>
            <td>" . htmlspecialchars($row['mode_of_payment']) . "</td>
            <td>" . htmlspecialchars($row['status']) . "</td>
            <td class='action-buttons'>
                <form style='display:inline;' action='editrecord.php' method='POST'>
                    <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                    <button class='edit-btn' type='submit'>Edit</button>
                </form>
                <form style='display:inline;' action='deleterecord.php' method='POST'>
                    <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                    <button class='delete-btn' type='submit' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</button>
                </form>
            </td>
          </tr>";
}    
        ?>
    </table>
</div>

                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?id=<?php echo $user_id; ?>&page=<?php echo $page - 1; ?>&tab=history-tab">Previous</a>
                    <?php endif; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?id=<?php echo $user_id; ?>&page=<?php echo $page + 1; ?>&tab=history-tab">Next</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(tc => tc.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.getAttribute('data-target')).classList.add('active');
                // Update URL to reflect active tab
                window.history.pushState({}, '', '?id=<?php echo $user_id; ?>&tab=' + tab.getAttribute('data-target'));
            });
        });

        document.getElementById('service').addEventListener('change', function() {
        let selectedOptions = Array.from(this.selectedOptions).map(option => option.value);
        console.log(selectedOptions);
    });
    </script>
</body>
</html>
