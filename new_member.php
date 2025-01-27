<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Member Registration</title>
    <link rel="stylesheet" href="mainpage.css">
    <link rel="stylesheet" href="userprofile.css">
    <link rel="stylesheet" href="new_member.css">
</head>
<body>
    <div class="container">
        <div class="content-member">
            <h1>New Member Information</h1>
            <form action="database_insert.php" method="POST" class="form-container">
                <!-- Username Field 
                <div class="form-group">
                    <label for="username"><b>Username:</b></label>
                    <input type="text" id="username" name="username" required>
                </div> -->
                <!-- First Name Field -->
                <div class="form-group">
                    <label for="first_name"><b>First Name:</b></label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <!-- Last Name Field -->
                <div class="form-group">
                    <label for="last_name"><b>Last Name:</b></label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
                <!-- Contact Number Field -->
                <div class="form-group">
                    <label for="contact_number"><b>Contact Number:</b></label>
                    <input type="tel" id="contact_number" name="contact_number" required>
                </div>
                <!-- Email Field
                <div class="form-group">
                    <label for="email"><b>Email:</b></label>
                    <input type="email" id="email" name="email" required>
                </div> -->
                <!-- Birthday Field -->
                <div class="form-group">
                    <label for="birthday"><b>Birthday:</b></label>
                    <input type="date" id="birthday" name="birthday" required>
                </div>
                <!-- Age Field  -->
                <div class="form-group">
                    <label for="age"><b>Age:</b></label>
                    <input type="number" id="age" name="age" required>
                </div>
                <!-- Address Field -->
                <div class="form-group">
                    <label for="address"><b>Address:</b></label>
                    <input type="text" id="address" name="address" required>
                </div>
                <!-- Password Field
                <div class="form-group">
                    <label for="password"><b>Password:</b></label>
                    <input type="password" id="password" name="password" required>
                </div>  -->
                <!-- Form Actions -->
                <div class="form-actions">
                    <input type="submit" value="Submit" class="button">
                    <a href="index.html" class="button">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

