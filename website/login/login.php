<?php
// Start the session
session_start();

// Database connection settings
$servername = "localhost";
$dbname = "accounts";
$username = "root"; // Change if you have a different username
$password = ""; // Change if you have a password

try {
    // Create connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = $_POST['username'];
        $pass = $_POST['password'];

        // Prepare the SQL statement
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $user);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the password
        if ($result && password_verify($pass, $result['password'])) {
            // Password is correct
            $_SESSION['username'] = $user;
            $_SESSION['usertype'] = $result['usertype']; // Store usertype in session

            // Redirect based on usertype
            if ($result['usertype'] == 'admin') {
                header("Location: admin_page.php");
            } elseif ($result['usertype'] == 'moderator') {
                header("Location: moderator_page.php");
            } else {
                // Default action if usertype is not recognized
                header("Location: user_page.php");
            }
            exit(); // Ensure script termination after redirect
        } else {
            // Invalid credentials
            echo "Invalid username or password.";
        }
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Login</title>
     <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <h2>Barangay Kay-anlog</h2>
    <h3>Management Information System</h3>
    <form action="login.php" method="post">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required /><br /><br />
      <label for="password">Password:</label>
      <input
        type="password"
        id="password"
        name="password"
        required
      /><br /><br />
      <input type="submit" value="Log in" />
    </form>
  </body>
</html>

