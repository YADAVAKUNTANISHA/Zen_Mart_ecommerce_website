<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    header("location: index.php");
    exit;
}

require_once "config.php";

// Initialize variables
$username = $password = "";
$username_err = $password_err = $err = "";

// If the request method is POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Validate inputs
    if (empty(trim($_POST['username']))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST['username']);
    }

    if (empty(trim($_POST['password']))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST['password']);
    }

    // Check for errors before querying the database
    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;

            // Try to execute the statement
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                // Check if username exists
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind the result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        // Verify the password
                        if (password_verify($password, $hashed_password)) {
                            // Correct password, start a new session
                            session_start();

                            // Regenerate session ID to prevent session fixation
                            session_regenerate_id(true);

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            // Set a success message
                            $_SESSION["success_message"] = "Login successful!";
                            $_SESSION["login_success"] = true; // Add this line

                            // Redirect user to the index page
                            header("location: index.php");
                            exit;
                        } else {
                            // Display an error for wrong credentials
                            $err = "Invalid username or password.";
                        }
                    }
                } else {
                    // Username doesn't exist
                    $err = "No account found with that username.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close the connection
    mysqli_close($conn);
}
?>
