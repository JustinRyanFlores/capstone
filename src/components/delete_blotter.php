<?php
include('../configs/connection.php'); // Ensure correct database connections are included

if (isset($_POST['blotterId'])) {
    $blotterId = $_POST['blotterId'];

    // Log to verify blotterId is received
    error_log("Blotter ID received: $blotterId");

    // Move record to the archive database
    $moveToArchiveQuery = "INSERT INTO archive.archive_blotter (blotter_id, type_incident, blotter_status, dt_reported, dt_incident, place_incident, name_complainant, name_accused,user_in_charge, narrative)
                           SELECT blotter_id, type_incident, blotter_status, dt_reported, dt_incident, place_incident, name_complainant, name_accused,user_in_charge, narrative
                           FROM blotter_records.blotter WHERE blotter_id = ?";
    
    $stmtMove = $mysqlConn4->prepare($moveToArchiveQuery);
    
    if (!$stmtMove) {
        // Log any errors when preparing the statement
        error_log("Failed to prepare move to archive query: " . $mysqlConn4->error);
        die("Failed to prepare move to archive query: " . $mysqlConn4->error);
    }

    // Bind the blotter ID and execute
    $stmtMove->bind_param('i', $blotterId);

    if (!$stmtMove->execute()) {
        // Log any errors with executing the statement
        error_log("Failed to execute move to archive query: " . $stmtMove->error);
        die("Failed to execute move to archive query: " . $stmtMove->error);
    } else {
        // Log success and test if the record is moved
        error_log("Record moved to archive successfully.");
    }

    // Delete the record from the blotter table
    $deleteQuery = "DELETE FROM blotter_records.blotter WHERE blotter_id = ?";
    $stmtDelete = $mysqlConn2->prepare($deleteQuery);
    
    if (!$stmtDelete) {
        // Log any errors when preparing the delete query
        error_log("Failed to prepare delete query: " . $mysqlConn2->error);
        die("Failed to prepare delete query: " . $mysqlConn2->error);
    }

    $stmtDelete->bind_param('i', $blotterId);

    if (!$stmtDelete->execute()) {
        // Log any errors with executing the delete statement
        error_log("Failed to execute delete query: " . $stmtDelete->error);
        die("Failed to execute delete query: " . $stmtDelete->error);
    } else {
        echo "Record successfully moved and deleted.";
        error_log("Record deleted from blotter table successfully.");
    }
} else {
    die("No Blotter ID provided!");
}
?>
