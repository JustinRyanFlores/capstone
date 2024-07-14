<?php
session_start();

// Check if the user is logged in and if they are a moderator
if (!isset($_SESSION['username']) || $_SESSION['usertype'] != 'moderator') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Moderator Page</title>
</head>
<body>
    <h1>Welcome, Moderator <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>This is the moderator page. Here you can manage content and user interactions.</p>
    <!-- Add moderator-specific content here -->

    <a href="logout.php">Logout</a>
</body>
</html>
