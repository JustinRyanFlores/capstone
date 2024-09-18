<?php
include("../configs/connection.php"); // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $typeIncident = $_POST['typeIncident'];
    $blotterStatus = $_POST['blotterStatus'];
    $dtReported = $_POST['dtReported'];
    $dtIncident = $_POST['dtIncident'];
    $placeIncident = $_POST['placeIncident'];
    $nameComplainant = $_POST['nameComplainant'];
    $nameAccused = $_POST['nameAccused'];
    $userInCharge = $_POST['userInCharge'];
    $narrative = $_POST['narrative'];

    // Insert data into the database
    $query = "INSERT INTO blotter (type_incident, blotter_status, dt_reported, dt_incident, place_incident, name_complainant, name_accused, user_in_charge, narrative) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $mysqlConn2->prepare($query)) {
        $stmt->bind_param('sssssssss', $typeIncident, $blotterStatus, $dtReported, $dtIncident, $placeIncident, $nameComplainant, $nameAccused, $userInCharge, $narrative);
        
        if ($stmt->execute()) {
            echo "Blotter record added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: " . $mysqlConn2->error;
    }

    $stmt->close();
    $mysqlConn2->close();
}
?>
