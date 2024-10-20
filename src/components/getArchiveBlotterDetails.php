<?php
include('../configs/connection.php'); // Adjust this path as necessary

if (isset($_POST['id'])) {
    $blotterID = $_POST['id'];

    // Use the correct database connection
    $sql = "SELECT * FROM archive_blotter WHERE blotter_id = ?";
    $stmt = $mysqlConn4->prepare($sql); // Use $mysqlConn4 instead of $conn
    if ($stmt) { // Check if preparation was successful
        $stmt->bind_param("i", $blotterID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "<p><strong>Incident:</strong> {$row['type_incident']}</p>";
            echo "<p><strong>Status:</strong> {$row['blotter_status']}</p>";
            echo "<p><strong>Date Reported:</strong> {$row['dt_reported']}</p>";
            echo "<p><strong>Date of Incident:</strong> {$row['dt_incident']}</p>";
            echo "<p><strong>Place of Incident:</strong> {$row['place_incident']}</p>";
            echo "<p><strong>Complainant Name:</strong> {$row['name_complainant']}</p>";
            echo "<p><strong>Accused Name:</strong> {$row['name_accused']}</p>";
            echo "<p><strong>User in Charge:</strong> {$row['user_in_charge']}</p>";
            echo "<p><strong>Narrative:</strong> {$row['narrative']}</p>";
        } else {
            echo "<p>No details found for the selected record.</p>";
        }
    } else {
        echo "<p>Error preparing statement: " . $mysqlConn4->error . "</p>";
    }
}
?>
