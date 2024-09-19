<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="/capstone/src/css/login.css"> <!-- Link to the CSS file -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="/capstone/src/assets/kayanlog-logo.png" alt="Barangay Logo">
        </div>
        <div class="title-container">
            <h2>Barangay Kay-Anlog</h2>
            <h3>Management Information System</h3>
        </div>
        <div class="login-box">
            <h2>Sign in to your account</h2>
            <p>Enter your username & password to login</p>
            <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_credentials') : ?>
                <p style="color: red;">Invalid username or password. Please try again.</p>
            <?php endif; ?>
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
    </div>

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
