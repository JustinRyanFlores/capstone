<?php
include('../configs/connection.php'); // Ensure correct database connections are included

if (isset($_POST['userId'])) {
    $userId = $_POST['userId'];

    // Log to verify userId is received
    error_log("User ID received: $userId");

    // Check if the user exists before proceeding
    $checkQuery = "SELECT * FROM user.user WHERE user_id = ?";
    $stmtCheck = $mysqlConn3->prepare($checkQuery);
    if (!$stmtCheck) {
        error_log("Failed to prepare check query: " . $mysqlConn3->error);
        die("Failed to prepare check query: " . $mysqlConn3->error);
    }
    $stmtCheck->bind_param('i', $userId);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();
    if ($result->num_rows === 0) {
        die("No user with user_id $userId exists.");
    }

    // Move record to the archive database
    $moveToArchiveQuery = "INSERT INTO archive.archive_user (user_id, fname, lname, contact_no, address, role, username, password)
                           SELECT user_id, fname, lname, contact_no, address, role, username, password
                           FROM user.user WHERE user_id = ?";
    
    $stmtMove = $mysqlConn4->prepare($moveToArchiveQuery);
    
    if (!$stmtMove) {
        error_log("Failed to prepare move to archive query: " . $mysqlConn4->error);
        die("Failed to prepare move to archive query: " . $mysqlConn4->error);
    }

    $stmtMove->bind_param('i', $userId);

    if (!$stmtMove->execute()) {
        error_log("Failed to execute move to archive query: " . $stmtMove->error);
        die("Failed to execute move to archive query: " . $stmtMove->error);
    } else {
        error_log("Record moved to archive successfully.");
    }

    // Delete the record from the User table
    $deleteQuery = "DELETE FROM user.user WHERE user_id = ?";
    $stmtDelete = $mysqlConn3->prepare($deleteQuery);
    
    if (!$stmtDelete) {
        error_log("Failed to prepare delete query: " . $mysqlConn3->error);
        die("Failed to prepare delete query: " . $mysqlConn3->error);
    }

    $stmtDelete->bind_param('i', $userId);

    if (!$stmtDelete->execute()) {
        error_log("Failed to execute delete query: " . $stmtDelete->error);
        die("Failed to execute delete query: " . $stmtDelete->error);
    } else {
        echo "Record successfully moved and deleted.";
        error_log("Record deleted from user table successfully.");
    }
} else {
    die("No User ID provided!");
}

?>
