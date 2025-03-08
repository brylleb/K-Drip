<?php

// Secure session settings
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    ini_set('session.cookie_secure', 0);
} else {
    ini_set('session.cookie_secure', 1);
}

ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

session_start();

$timeout_duration = 900;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header("Location: login.php?message=session_expired");
    exit();
}
$_SESSION['last_activity'] = time();

if (!isset($_SESSION['regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['regenerated'] = true;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$config = parse_ini_file(__DIR__ . '/.env');
if (!$config) {
    die("Error: Could not load configuration file.");
}

$servername = $config['DB_SERVER'];
$username = $config['DB_USERNAME'];
$password = $config['DB_PASSWORD'];
$dbname = $config['DB_NAME'];

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['id'])) {
        die("Error: Missing required parameters.");
    }

    $id = (int) $_POST['id'];

    $conn->begin_transaction();

    try {
        // Get the current profile ID (reg_member_id) before deleting
        $profileQuery = "SELECT reg_member_id FROM member_profile WHERE id = ?";
        $profileStmt = $conn->prepare($profileQuery);
        $profileStmt->bind_param("i", $id);
        $profileStmt->execute();
        $result = $profileStmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $reg_member_id = $row['reg_member_id'];  // Store current profile ID
        } else {
            throw new Exception("Record not found.");
        }

        // Delete the record
        $sql1 = "DELETE FROM member_profile WHERE id = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("i", $id);
        $stmt1->execute();

        $conn->commit();

        // Redirect to the profile page of the same member
        header("Location: profile.php?id=" . $reg_member_id . "&message=" . urlencode("Record deleted successfully"));
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: profile.php?id=" . $id . "&message=" . urlencode("Error deleting record"));
        exit();
    }
}

$conn->close();
?>
