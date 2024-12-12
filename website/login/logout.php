<?php

// Start the session to access the user_id
session_start();

// Set the default timezone to UTC+08:00
date_default_timezone_set('Asia/Singapore');

include("connection.php");

// Check if the user is logged in by verifying the user_id in the session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];


    // Query to get the fname (first name) from the user table
    $sql_get_fname = "SELECT fname FROM user WHERE user_id = ?";
    $stmt_get_fname = $mysqlConn3->prepare($sql_get_fname);
    $stmt_get_fname->bind_param("i", $user_id); // "i" indicates integer for user_id
    $stmt_get_fname->execute();
    $stmt_get_fname->store_result();
    $stmt_get_fname->bind_result($fname);

    if ($stmt_get_fname->fetch()) {
        // Get the current time for logout
        $logout_time = date("Y-m-d H:i:s");

        // Query to find the last login record for the user based on fname
        $sql = "SELECT id FROM activity_log WHERE name = ? AND logout_time = '0000-00-00 00:00:00' ORDER BY date DESC LIMIT 1";
        $stmt = $mysqlConn3->prepare($sql);
        $stmt->bind_param("s", $fname); // "s" indicates string for fname
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id);

        if ($stmt->fetch()) {
            // Update the logout time for the user's active session
            $update_sql = "UPDATE activity_log SET logout_time = ? WHERE id = ?";
            $update_stmt = $mysqlConn3->prepare($update_sql);
            $update_stmt->bind_param("si", $logout_time, $id); // "s" for string (logout_time), "i" for integer (id)

            // Execute the update query
            if ($update_stmt->execute()) {
                echo "Logout time updated successfully.";
            } else {
                echo "Error updating logout time: " . $update_stmt->error;
            }

            // Close the update statement
            $update_stmt->close();
        } else {
            echo "No active session found for the user.";
        }

        // Close the statement for fetching fname
        $stmt_get_fname->close();
    } else {
        echo "User not found.";
    }

    // Close the statement and connection
    $stmt->close();
    $mysqlConn3->close();
    
// Unset all session variables
$_SESSION = array();
    // Destroy session after logout
    session_destroy();
} else {
    echo "User is not logged in.";
}
header("Location: /system/website/login/login.php");
exit();
?>
