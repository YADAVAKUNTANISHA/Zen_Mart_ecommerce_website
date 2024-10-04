<?php

define('DB_SERVER', '127.0.0.1');  // or 'localhost'
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'ecommerce');

$conn = mysqli_connect(
    DB_SERVER,
    DB_USERNAME,
    DB_PASSWORD,
    DB_NAME,
    3307  // Ensure your MySQL server is using this port
);

if ($conn == false) {
    die('Error: ' . mysqli_connect_error());  // Display detailed error
} else {
    echo('Database successfully connected');
}

?>
