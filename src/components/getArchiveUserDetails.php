<?php
include('../configs/connection.php');

if (isset($_POST['id'])) {
    $userID = $_POST['id'];

    $sql = "SELECT * FROM archive_user WHERE user_id = ?";
    $stmt = $mysqlConn4->prepare($sql); // Assuming you're using the third connection for users
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<p><strong>First Name:</strong> {$row['fname']}</p>";
        echo "<p><strong>Last Name:</strong> {$row['lname']}</p>";
        echo "<p><strong>Contact Number:</strong> {$row['contact_no']}</p>";
        echo "<p><strong>Address:</strong> {$row['address']}</p>";
        echo "<p><strong>Role:</strong> {$row['role']}</p>";
        echo "<p><strong>Username:</strong> {$row['username']}</p>";
        echo "<p><strong>Password:</strong> {$row['password']}</p>"; 
    } else {
        echo "<p>No details found for the selected record.</p>";
    }
}
?>
