<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="mainpage.css">
    <link rel="stylesheet" href="calendar.css">
    <link rel="stylesheet" href="time.css">
</head>
<body>
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
        <h1><?php echo date('Y'); ?> Calendar</h1>

        <!-- Tabs for Months -->
        <div class="tabs">
            <?php
            $months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            foreach ($months as $index => $month) {
                echo "<button class='tab" . ($index === 0 ? " active" : "") . "' data-target='month-$index'>$month</button>";
            }
            ?>
        </div>

        <!-- Tab Content for Each Month -->
        <?php
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

        // Set the current year
        $currentYear = date('Y');

        // Fetch appointment data for the current year
        $sql = "SELECT mp.date, rm.first_name 
                FROM member_profile mp 
                JOIN reg_member rm ON mp.reg_member_id = rm.id
                WHERE YEAR(mp.date) = ? 
                ORDER BY mp.date ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $currentYear);
        $stmt->execute();
        $result = $stmt->get_result();

        // Create an array to store appointments by date
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[$row['date']][] = $row['first_name']; // Store multiple first names for the same date
        }

        // Close the connection
        $conn->close();

        // Render the calendar for each month
        foreach ($months as $index => $month) {
            $firstDayOfMonth = strtotime("$month 1 $currentYear");
            $daysInMonth = date('t', $firstDayOfMonth);
            $startDay = date('w', $firstDayOfMonth); // Day of the week (0 = Sunday, 6 = Saturday)

            echo "<div class='tab-content" . ($index === 0 ? " active" : "") . "' id='month-$index'>";

            // Render the calendar
            echo "<div class='calendar'>";

            // Days of the week header
            $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            foreach ($daysOfWeek as $day) {
                echo "<div class='day header'>$day</div>";
            }

            // Empty cells before the first day
            for ($i = 0; $i < $startDay; $i++) {
                echo "<div class='day'></div>";
            }

            // Days of the month
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = "$currentYear-" . str_pad($index + 1, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT);

                echo "<div class='day'>"; 
                echo $day;

                // Check if the current date exists in the $appointments array and display all first names for that date
                if (isset($appointments[$currentDate])) {
                    foreach ($appointments[$currentDate] as $firstName) {
                        echo "<div class='appointment'>$firstName</div>";
                    }
                }

                echo "</div>";
            }

            echo "</div>"; // Close calendar div
            echo "</div>"; // Close tab content div
        }
        ?>
        <a href="backoffice.php" class="button">Back</a>
    </div>

            <!-- Time Container Section -->
        <div class="timecontainer">
            <h2>Time Container</h2>
            <p>This is where you can add time-related data or other information.</p>
        </div>

    <script>
        // JavaScript to handle tab switching
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(tc => tc.classList.remove('active'));

                // Add active class to the clicked tab and its content
                tab.classList.add('active');
                document.getElementById(tab.getAttribute('data-target')).classList.add('active');
            });
        });
    </script>
</body>
</html>
