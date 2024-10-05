<?php
// Include the connection from the external file
include('../configs/connection.php'); // Ensure correct path

if (isset($_POST['residentId'])) {
    $residentId = intval($_POST['residentId']); // Ensure it's an integer

    // Log to verify residentId is received
    error_log("Resident ID received: $residentId");

    // Check if the connections are valid before proceeding
    if (!$mysqlConn) {
        error_log("Main database connection failed.");
        echo "Main database connection error.";
        exit();
    }

    if (!$mysqlConn4) {
        error_log("Archive database connection failed.");
        echo "Archive database connection error.";
        exit();
    }

    // Move to archive
    $moveToArchiveQuery = "INSERT INTO archive.residents_records 
        (first_name, middle_name, last_name, dob, age, gender, contact_number, religion, philhealth, voterstatus, street_address, house_number, subdivision, barangay, city, province, region, zip_code, mother_first_name, mother_middle_name, mother_last_name, father_first_name, father_middle_name, father_last_name, osy, als, educational_attainment, current_school, illness, medication, disability, immunization, pwd, teen_pregnancy, type_of_delivery, assisted_by, organization, cases_violated, years_of_stay, business_owner, residents_img)
        SELECT first_name, middle_name, last_name, dob, age, gender, contact_number, religion, philhealth, voterstatus, street_address, house_number, subdivision, barangay, city, province, region, zip_code, mother_first_name, mother_middle_name, mother_last_name, father_first_name, father_middle_name, father_last_name, osy, als, educational_attainment, current_school, illness, medication, disability, immunization, pwd, teen_pregnancy, type_of_delivery, assisted_by, organization, cases_violated, years_of_stay, business_owner, residents_img
        FROM residents_db.residents_records 
        WHERE id = ?";

    // Prepare statement for moving the resident record to archive
    if ($stmtMove = $mysqlConn4->prepare($moveToArchiveQuery)) {
        // Bind the resident ID and execute
        $stmtMove->bind_param('i', $residentId);

        if ($stmtMove->execute()) {
            // Now delete the record from the residents table
            $deleteQuery = "DELETE FROM residents_db.residents_records WHERE id = ?";
            if ($stmtDelete = $mysqlConn->prepare($deleteQuery)) {
                // Bind the resident ID and execute the delete statement
                $stmtDelete->bind_param('i', $residentId);

                if ($stmtDelete->execute()) {
                    echo "Resident record successfully deleted.";
                    error_log("Resident record moved to archive and deleted successfully.");
                } else {
                    error_log("Failed to execute delete query: " . $stmtDelete->error);
                    echo "An error occurred while deleting the resident record.";
                }
                $stmtDelete->close();
            } else {
                error_log("Failed to prepare delete query: " . $mysqlConn->error);
                echo "An error occurred while preparing the delete query.";
            }
        } else {
            error_log("Failed to execute move to archive query: " . $stmtMove->error);
            echo "An error occurred while moving the resident record to the archive.";
        }
        $stmtMove->close();
    } else {
        error_log("Failed to prepare move to archive query: " . $mysqlConn4->error);
        echo "An error occurred while preparing the move to archive query.";
    }

    // Close the connections
    $mysqlConn->close();
    $mysqlConn4->close();
} else {
    die("No Resident ID provided!");
}
