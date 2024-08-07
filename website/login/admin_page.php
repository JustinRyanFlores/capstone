<?php
session_start();

// Check if the user is logged in and if they are an admin
if (!isset($_SESSION['username']) || $_SESSION['usertype'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
</head>

<body>
    <h1>Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>This is the admin page. Here you can manage the website settings and user accounts.</p>
    <!-- Add admin-specific content here -->

    <a href="logout.php">Logout</a>
</body>

</html>