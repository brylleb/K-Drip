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
    </style>
</head>
<body style="height: 100%">
    <div class="container"> 
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

                echo "<div class='day'>"; 
                echo $day;

                if (isset($appointments[$currentDate])) {
                    foreach ($appointments[$currentDate] as $appointment) {
                        echo "<div class='appointment'>
                                <span onclick='showMessage(event, \"{$appointment['first_name']}\", \"{$appointment['time']}\")'>{$appointment['first_name']}</span>
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
</body>
</html>
