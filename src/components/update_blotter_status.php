<?php
include("../configs/connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $blotterId = $_POST['blotterId'];
    $status = $_POST['status'];

    // Update status in the database
    $query = "UPDATE blotter SET blotter_status = ? WHERE blotter_id = ?";
    $stmt = $mysqlConn2->prepare($query);
    $stmt->bind_param('si', $status, $blotterId);

    if ($stmt->execute()) {
        echo 'Status updated successfully.';
    } else {
        echo 'Error updating status.';
    }

    $stmt->close();
    $mysqlConn2->close();
}
?>
