<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /capstone/website/login/login.php");
    exit();
}

include('../configs/connection.php');

if (isset($_POST['id'])) {
    $userID = $_POST['id'];
    error_log("User ID to delete: " . $userID); // Log the user ID

    $sql = "DELETE FROM archive_user WHERE user_id = ?";
    $stmt = $mysqlConn4->prepare($sql);
    $stmt->bind_param("i", $userID);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "User record deleted successfully.";
        } else {
            echo "No record found with that ID.";
        }
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
} else {
    echo "No ID received.";
}

?>
