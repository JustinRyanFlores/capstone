<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /system/website/login/login.php");
    exit();
}

include('../configs/connection.php');

// Check if the ID is passed in the POST request
if (isset($_POST['id'])) {
    $residentID = $_POST['id'];

    // Prepare and execute the delete statement
    $sql = "DELETE FROM residents_records WHERE id = ?";
    if ($stmt = $mysqlConn4->prepare($sql)) {
        $stmt->bind_param("i", $residentID);

        // Try executing the query
        if ($stmt->execute()) {
            echo "Resident record deleted successfully.";
        } else {
            echo "Error executing query: " . $stmt->error; // Detailed error message
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $mysqlConn4->error; // Error preparing the query
    }
} else {
    echo "No ID provided.";
}
?>
