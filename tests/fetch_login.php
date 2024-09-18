<?php
// /src/components/fetch_login.php

// Include the database connection file
include("../configs/connection.php");

// Get username and password from POST request
$username = $_POST['username'];
$password = $_POST['password'];

// Prepare and execute the SQL query
$sql = "SELECT * FROM user WHERE username = ? AND password = ?";
$stmt = $mysqlConn3->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    // Start session and set session variables
    session_start();
    $_SESSION['username'] = $username;
    header("Location: /capstone/pages/dahsboard.php"); // Redirect to a welcome page or dashboard
} else {
    // Redirect back to login page with an error message
    header("Location: /capstone/website/login/login.php?error=invalid_credentials");
}

// Close connection
$stmt->close();
$mysqlConn3->close();
?>
