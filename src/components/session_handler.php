<?php
// Define session timeout duration (in seconds)
$timeout_duration = 900; 
// Function to handle session timeout
function checkSessionTimeout($timeout_duration) {
    // Check if the session exists and has timed out
    if (isset($_SESSION['last_activity'])) {
        $elapsed_time = time() - $_SESSION['last_activity'];
        if ($elapsed_time > $timeout_duration) {
            session_unset();
            session_destroy();
            header("Location: ../website/login/login.php?sessionexpired");
            exit();
        }
    }
    // Update the last activity time
    $_SESSION['last_activity'] = time();
}

// Function to check if the user is logged in
function checkUserLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.php");
        exit();
    }
}

// Check session timeout and login status
checkSessionTimeout($timeout_duration);
checkUserLogin();
?>

<!-- Add this JavaScript to refresh the page every 2 seconds -->
<script>
    setInterval(function() {
        location.reload(); 
    }, 900000); 
</script>
