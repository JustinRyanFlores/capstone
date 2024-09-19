
<?php
$testPassword = '123';

// Hash the password
$hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
echo "Hashed Password: " . $hashedPassword . "<br>";

// Verify the password
$isPasswordCorrect = password_verify($testPassword, $hashedPassword);
echo $isPasswordCorrect ? "Password is correct" : "Password is incorrect";
?>