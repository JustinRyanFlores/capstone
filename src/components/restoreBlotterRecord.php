<?php
session_start();
include('../configs/connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) {
        $blotterID = $_POST['id'];

        // Fetch the record from the archive table
        $fetchSql = "SELECT * FROM archive_blotter WHERE blotter_id = ?";
        $stmt = $mysqlConn4->prepare($fetchSql);
        $stmt->bind_param('i', $blotterID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $record = $result->fetch_assoc();

            // Insert the record back into the blotter table
            $insertSql = "INSERT INTO blotter (blotter_id, type_incident, blotter_status, dt_reported, dt_incident, place_incident, name_complainant, name_accused, user_in_charge, narrative) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $mysqlConn2->prepare($insertSql);
            $insertStmt->bind_param('isssssssss', $record['blotter_id'], $record['type_incident'], $record['blotter_status'], $record['dt_reported'], $record['dt_incident'], $record['place_incident'], $record['name_complainant'], $record['name_accused'], $record['user_in_charge'], $record['narrative']);
            
            if ($insertStmt->execute()) {
                // Now delete the record from the archive table
                $deleteSql = "DELETE FROM archive_blotter WHERE blotter_id = ?";
                $deleteStmt = $mysqlConn4->prepare($deleteSql);
                $deleteStmt->bind_param('i', $blotterID);
                $deleteStmt->execute();
                
                echo "Record restored successfully.";
            } else {
                echo "Failed to restore record.";
            }
        } else {
            echo "No record found for restoration.";
        }
    } else {
        echo "Invalid request.";
    }
} else {
    echo "Invalid request method.";
}
?>