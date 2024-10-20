<?php
session_start();
include('../configs/connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit();
}

// Check if the user ID is set
if (isset($_POST['id'])) {
    $userId = intval($_POST['id']);

    // Fetch the user details from the archive_user table
    $fetchSql = "SELECT * FROM archive_user WHERE user_id = ?";
    $stmt = $mysqlConn4->prepare($fetchSql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Insert the user data back into the user table
        $insertSql = "INSERT INTO user (user_id, fname, lname, contact_no, address, role, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $mysqlConn3->prepare($insertSql);
        $insertStmt->bind_param("isssssss", $user['user_id'], $user['fname'], $user['lname'], $user['contact_no'], $user['address'], $user['role'], $user['username'], $user['password']);
        
        if ($insertStmt->execute()) {
            // Optionally, delete the record from archive_user
            $deleteSql = "DELETE FROM archive_user WHERE user_id = ?";
            $deleteStmt = $mysqlConn4->prepare($deleteSql);
            $deleteStmt->bind_param("i", $userId);
            $deleteStmt->execute();

            echo "User restored successfully!";
        } else {
            echo "Failed to restore user.";
        }
    } else {
        echo "User not found in the archive.";
    }
} else {
    echo "No user ID specified.";
}
?>