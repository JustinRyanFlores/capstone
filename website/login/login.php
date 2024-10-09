<?php
session_start();

// Cache control headers to prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Set a past date to ensure the page expires

// If the user is already logged in, redirect to the dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: /capstone/pages/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kay-Anlog Sys Info | Login Page</title>
    <link rel="stylesheet" href="/capstone/src/css/login.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="/capstone/src/assets/kayanlog-logo-removebg-preview.png" alt="Barangay Logo">
        </div>
        <div class="title-container">
            <h2>Barangay Kay-Anlog</h2>
            <h3>Management Information System</h3>
        </div>
    </div>

    <div class="login-box">
        <h2>Sign in to your account</h2>
        <p>Enter your username & password to login</p>

        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<p style="color:red;">' . $_SESSION['error_message'] . '</p>';
            unset($_SESSION['error_message']); // Clear the message after displaying
        }
        ?>

        <form action="/capstone/src/components/fetch_login.php" method="POST">
            <div class="input-group">
                <input type="text" id="username" name="username" required placeholder="Username">
            </div>
            <div class="input-group">
                <input type="password" id="password" name="password" required placeholder="Password">
                <span class="material-symbols-outlined toggle-password" onclick="togglePassword()" id="toggleIcon">visibility_off</span>
            </div>
            <button type="submit">Log In</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2024</p>
    </footer>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var toggleIcon = document.getElementById("toggleIcon");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.textContent = "visibility"; // Change to "visibility" when password is shown
            } else {
                passwordField.type = "password";
                toggleIcon.textContent = "visibility_off"; // Change back to "visibility_off" when password is hidden
            }
        }
    </script>
</body>
</html>
