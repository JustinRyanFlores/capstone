<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /capstone/website/login/login.php");
    exit();
}

include('../configs/connection.php');

if (isset($_POST['id'])) {
    $blotterID = $_POST['id'];

    // Prepare and execute the delete statement
    $sql = "DELETE FROM archive_blotter WHERE blotter_id = ?";
    $stmt = $mysqlConn4->prepare($sql);
    $stmt->bind_param("i", $blotterID);
    
    if ($stmt->execute()) {
        echo "Blotter record deleted successfully.";
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
}
?>