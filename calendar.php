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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="mainpage.css">
    <link rel="stylesheet" href="calendar.css">
    <link rel="stylesheet" href="time.css">
    <style>
.message-box {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    border: 1px solid #ccc;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    padding: 20px;
    border-radius: 5px;
    z-index: 1000;
    display: none;
    max-width: 90%; /* Responsive for smaller screens */
    text-align: center;
}

        .message-box p {
            margin: 0;
            font-size: 0.9rem;
            color: #333;
        }

        /*time table CSS*/
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
}

th {
    background-color: #4CAF50;
    color: white;
}

td {
    height: 40px; /* Uniform row height */
}
    </style>
</head>
<body style="height: 100%">
        <div class="banner">
            <a href="index.html">K-Drip</a>
            <div class="right">
                <div class="dropdown">
                    <a>Contact Us</a>
                    <div class="dropdown-content">
                        <p> Phone: +639065310391</p>
                        <p> Email: K-Drip@gmail.com</p>
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
    <div class="calendarcontainer"> 
        <h1><?php echo date('Y'); ?> Calendar</h1>

        <div class="tabs">
            <?php
            $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            foreach ($months as $index => $month) {
                echo "<button class='tab" . ($index === 0 ? " active" : "") . "' data-target='month-$index'>$month</button>";
            }
            ?>
        </div>

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

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $currentYear = date('Y');

        $sql = "SELECT mp.date, rm.first_name, DATE_FORMAT(mp.time, '%h:%i %p') AS formatted_time
                FROM member_profile mp 
                JOIN reg_member rm ON mp.reg_member_id = rm.id
                WHERE YEAR(mp.date) = ? 
                ORDER BY mp.date ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $currentYear);
        $stmt->execute();
        $result = $stmt->get_result();

        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[$row['date']][] = [
                'first_name' => $row['first_name'], 
                'time' => $row['formatted_time'] // Use formatted time here
            ];
        }

        $conn->close();

        foreach ($months as $index => $month) {
            $firstDayOfMonth = strtotime("$month 1 $currentYear");
            $daysInMonth = date('t', $firstDayOfMonth);
            $startDay = date('w', $firstDayOfMonth); 

            echo "<div class='tab-content" . ($index === 0 ? " active" : "") . "' id='month-$index'>";
            echo "<div class='calendar'>";

            $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            foreach ($daysOfWeek as $day) {
                echo "<div class='day header'>$day</div>";
            }

            for ($i = 0; $i < $startDay; $i++) {
                echo "<div class='day'></div>";
            }

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = "$currentYear-" . str_pad($index + 1, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT);
            
                echo "<div class='day' data-date='$currentDate'>"; 
                echo $day;
            
                if (isset($appointments[$currentDate])) {
                    foreach ($appointments[$currentDate] as $appointment) {
                        echo "<div class='appointment'>
                                <span class='appointment-entry' data-name='{$appointment['first_name']}' data-time='{$appointment['time']}'>
                                    {$appointment['first_name']}
                                </span>
                              </div>";
                    }
                }
            
                echo "</div>";
            }
            echo "</div>";
            echo "</div>";
        }
        ?>
        <a href="backoffice.php" class="button">Back</a>
    </div>
    <div id="message-box" class="message-box"></div>
    <script>
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        const messageBox = document.getElementById('message-box');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(tc => tc.classList.remove('active'));

                tab.classList.add('active');
                document.getElementById(tab.getAttribute('data-target')).classList.add('active');
            });
        });

        function showMessage(event, name, time) {
    messageBox.innerHTML = `<p><strong>${name}</strong><br>Appointment Time: ${time}</p>`;
    messageBox.style.display = 'block';
}

// Hide the message box when clicking outside
document.addEventListener('click', (event) => {
    if (!event.target.closest('.appointment span') && !event.target.closest('.message-box')) {
        messageBox.style.display = 'none';
    }
});
    </script>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const timeSlots = [
        "08:00 AM", "09:00 AM", "10:00 AM", "11:00 AM",
        "12:00 PM", "01:00 PM", "02:00 PM", "03:00 PM",
        "04:00 PM", "05:00 PM", "06:00 PM", "07:00 PM",
        "08:00 PM", "09:00 PM", "10:00 PM",
    ];

    // Create pop-up container
    const popup = document.createElement("div");
    popup.id = "appointment-popup";
    popup.style.position = "fixed";
    popup.style.top = "50%";
    popup.style.left = "50%";
    popup.style.transform = "translate(-50%, -50%)";
    popup.style.background = "#fff";
    popup.style.padding = "20px";
    popup.style.borderRadius = "10px";
    popup.style.boxShadow = "0 4px 8px rgba(0,0,0,0.2)";
    popup.style.zIndex = "1000";
    popup.style.display = "none";
    popup.style.maxWidth = "90%";
    popup.style.textAlign = "center";

    // Create pop-up content
    popup.innerHTML = `
        <h2>Appointments</h2>
        <p id="popup-date"></p>
        <table>
            <thead>
                <tr><th>Time</th><th>Name</th></tr>
            </thead>
            <tbody id="popup-appointments"></tbody>
        </table>
        <button id="close-popup" style="margin-top:10px;padding:5px 10px;border:none;background:#4CAF50;color:white;cursor:pointer;border-radius:5px;">Close</button>
    `;

    document.body.appendChild(popup);

    // Event to close the popup
    document.getElementById("close-popup").addEventListener("click", function () {
        popup.style.display = "none";
    });

    // Function to convert 12-hour format to 24-hour format for sorting
    function convertTo24HourFormat(time) {
        let [hours, minutes, period] = time.match(/(\d+):(\d+) (\w+)/).slice(1);
        hours = parseInt(hours, 10);
        minutes = parseInt(minutes, 10);

        if (period === "PM" && hours !== 12) {
            hours += 12;
        } else if (period === "AM" && hours === 12) {
            hours = 0;
        }

        return hours * 60 + minutes; // Convert to total minutes for easy sorting
    }

    // Function to filter and display sorted appointments
    function filterAppointments(date) {
        const appointmentTable = document.getElementById("popup-appointments");
        const dateDisplay = document.getElementById("popup-date");
        appointmentTable.innerHTML = ""; // Clear previous entries
        dateDisplay.textContent = `Schedule for ${date}`;

        let appointmentsArray = [];

        document.querySelectorAll(".appointment-entry").forEach(appointment => {
            const parentDay = appointment.closest(".day").getAttribute("data-date");
            if (parentDay === date) {
                const name = appointment.getAttribute("data-name");
                const time = appointment.getAttribute("data-time");
                appointmentsArray.push({ name, time, sortTime: convertTo24HourFormat(time) });
            }
        });

        // Sort appointments by time in ascending order
        appointmentsArray.sort((a, b) => a.sortTime - b.sortTime);

        // Display sorted appointments
        if (appointmentsArray.length > 0) {
            appointmentsArray.forEach(app => {
                const row = document.createElement("tr");
                row.innerHTML = `<td>${app.time}</td><td>${app.name}</td>`;
                appointmentTable.appendChild(row);
            });
        } else {
            appointmentTable.innerHTML = "<tr><td colspan='2'>No Appointments</td></tr>";
        }

        // Show the pop-up
        popup.style.display = "block";
    }

    // Attach event listener to all calendar days
    document.querySelectorAll(".day").forEach(day => {
        day.addEventListener("click", function () {
            const selectedDate = this.getAttribute("data-date");
            filterAppointments(selectedDate);
        });
    });

    // Hide the pop-up when clicking outside of it
    document.addEventListener("click", function (event) {
        if (!popup.contains(event.target) && !event.target.closest(".day")) {
            popup.style.display = "none";
        }
    });
});
</script>
</body>
</html>
