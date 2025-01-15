<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="mainpage.css">
    <title>Back Office</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .main-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            background-color: #fff;
        }

        .box-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 1200px;
        }

        .box {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e5a1a1;
            color: #333;
            height: 150px;
            text-align: center;
            font-size: 1.5em;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .box:hover {
            transform: scale(1.05);
            background-color: #d18b8b;
        }

        @media screen and (max-width: 768px) {
            .box {
                font-size: 1.2em;
                height: 120px;
            }
        }

        @media screen and (max-width: 480px) {
            .box {
                font-size: 1em;
                height: 100px;
            }
        }
    </style>
</head>
<body>
<div class="banner">
        <a href="Homepage.html">K-Drip</a>
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
    <div class="container">
        <div class="box-container">
            <div class="box" onclick="location.href='all_member.php';">Member Record</div>
            <div class="box" onclick="location.href='calendar.php';">Appointment Calendar</div>
        </div>
    </div>
</body>
</html>
