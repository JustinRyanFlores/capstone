<?php
include("../configs/connection.php"); // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $contactNumber = $_POST['contactNumber'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "Error: Passwords do not match.";
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert data into the database
    $query = "INSERT INTO user (fname, lname, contact_no, address, role, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $mysqlConn3->prepare($query)) {
        $stmt->bind_param('sssssss', $firstName, $lastName, $contactNumber, $address, $role, $username, $hashedPassword);
        
        if ($stmt->execute()) {
            echo "User added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: " . $mysqlConn3->error;
    }

    $stmt->close();
    $mysqlConn3->close();
}
?>
