<?php
// Remove this line to prevent direct output of connection success
// echo "Database successfully connected";

require_once "config.php";

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Process form data when form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Validate username
    if (empty(trim($_POST['username']))) {
        $username_err = "Username cannot be blank.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST['username']);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST['username']);
                }
            } else {
                $username_err = "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $username_err = "Error preparing statement. Please try again later.";
        }
    }

    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = "Password cannot be blank.";
    } elseif (strlen(trim($_POST['password'])) < 5) {
        $password_err = "Password must be at least 5 characters.";
    } else {
        $password = trim($_POST['password']);
    }

    // Validate confirm password
    if (empty(trim($_POST['confirm_password']))) {
        $confirm_password_err = "Please confirm your password.";
    } elseif (trim($_POST['password']) != trim($_POST['confirm_password'])) {
        $confirm_password_err = "Passwords do not match.";
    }

    // If no errors, insert into database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page after successful signup
                header("Location: login.html");
                exit(); // Ensure the script stops executing after redirection
            } else {
                $username_err = "Error inserting data. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $username_err = "Error preparing statement. Please try again later.";
        }
    }
}

// Close database connection
mysqli_close($conn);
?>

<!-- Place your HTML and jQuery code below -->

<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles/styles.css"> <!-- Link to your CSS file -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
    <style>
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('#signupForm').on('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                // Clear previous error messages
                $('#errorMessage').text('');

                let isValid = true; // Flag to check validity

                // Validate username
                const username = $('#username').val().trim();
                if (username.length === 0) {
                    $('#errorMessage').text("Username cannot be blank.");
                    isValid = false;
                }

                // Validate password
                const password = $('#password').val();
                if (password.length < 5) {
                    $('#errorMessage').text("Password must be at least 5 characters.");
                    isValid = false;
                }

                // Validate confirm password
                const confirmPassword = $('#confirm_password').val();
                if (confirmPassword !== password) {
                    $('#errorMessage').text("Passwords do not match.");
                    isValid = false;
                }

                // If valid, submit the form
                if (isValid) {
                    this.submit();
                } else {
                    $('#myModal').css('display', 'block'); // Show modal
                }
            });

            // Close the modal
            $('.close').on('click', function() {
                $('#myModal').css('display', 'none');
            });

            // Close modal when clicking outside of it
            $(window).on('click', function(event) {
                if ($(event.target).is('#myModal')) {
                    $('#myModal').css('display', 'none');
                }
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        
        <!-- Sign Up Form -->
        <form id="signupForm" action="" method="post">
            <div>
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div>
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <div>
                <button type="submit">Sign Up</button>
            </div>
        </form>
    </div>

    <!-- Modal for displaying error messages -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="errorMessage" style="color:red;"></p> <!-- Error message will be displayed here -->
        </div>
    </div>
</body>
</html>
