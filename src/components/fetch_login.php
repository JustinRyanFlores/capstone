<?php
// Start session
session_start();

// Include the database connection file
include('../configs/connection.php');

// Get the posted username and password from the form
$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Use prepared statements to avoid SQL injection
$stmt = $mysqlConn3->prepare("SELECT * FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the row
    $row = $result->fetch_assoc();

    // Verify the password
    if (password_verify($password, $row['password'])) {
        // Store user data in the session
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role']; // Store the user's role

        // Redirect to the dashboard or a secure page
        header("Location: /capstone/pages/dahsboard.php");
        exit();
    } else {
        // Invalid password: Set error message and redirect back to login
        $_SESSION['error_message'] = "Invalid username or password!";
        header("Location: /capstone/website/login/login.php");
        exit();
    }
} else {
    // No user found: Set error message and redirect back to login
    $_SESSION['error_message'] = "Invalid username or password!";
    header("Location: /capstone/website/login/login.php");
    exit();
}
?>
