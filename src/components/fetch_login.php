<?php
// Start session
session_start();

// Include the database connection file
include('../configs/connection.php');

// Get the posted username and password from the form
$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Sanitize inputs to prevent SQL injection
$username = $mysqlConn3->real_escape_string($username);

// Query the `user` table to find matching credentials
$sql = "SELECT * FROM user WHERE username = '$username'";
$result = $mysqlConn3->query($sql);

if ($result->num_rows > 0) {
    // Fetch the row
    $row = $result->fetch_assoc();

    // Debugging output
    echo "Stored Hash: " . htmlspecialchars($row['password']) . "<br>";
    
    // Verify the password
    if (password_verify($password, $row['password'])) {
        // Store user data in the session
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        
        // Redirect to the dashboard or a secure page
        header("Location: /capstone/pages/dahsboard.php");
        exit();
    } else {
        // Invalid password
        echo "Invalid password!";
    }
} else {
    // No user found
    echo "Invalid username!";
}
?>
