<?php
// /src/config/database.php

// MySQL Connection
$mysqlServername = "localhost";
$mysqlUsername = "mysql_user";
$mysqlPassword = "mysql_password";
$mysqlDbname = "mysql_database";

// Create MySQL connection
$mysqlConn = new mysqli($mysqlServername, $mysqlUsername, $mysqlPassword, $mysqlDbname);
if ($mysqlConn->connect_error) {
    die("MySQL Connection failed: " . $mysqlConn->connect_error);
}

// Second MySQL Connection (if needed)
$mysqlServername2 = "localhost";
$mysqlUsername2 = "mysql_user2";
$mysqlPassword2 = "mysql_password2";
$mysqlDbname2 = "mysql_database2";

// Create second MySQL connection
$mysqlConn2 = new mysqli($mysqlServername2, $mysqlUsername2, $mysqlPassword2, $mysqlDbname2);
if ($mysqlConn2->connect_error) {
    die("MySQL Connection 2 failed: " . $mysqlConn2->connect_error);
}
?>