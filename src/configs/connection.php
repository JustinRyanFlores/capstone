<?php
// /src/config/database.php

// MySQL Connection
$mysqlServername = "localhost";
$mysqlUsername = "root";
$mysqlPassword = "";
$mysqlDbname = "resident_records";

// Create MySQL connection
$mysqlConn = new mysqli($mysqlServername, $mysqlUsername, $mysqlPassword, $mysqlDbname);
if ($mysqlConn->connect_error) {
    die("MySQL Connection failed: " . $mysqlConn->connect_error);
}

// Second MySQL Connection (if needed)
$mysqlServername2 = "localhost";
$mysqlUsername2 = "root";
$mysqlPassword2 = "";
$mysqlDbname2 = "blotter_records";

// Create second MySQL connection
$mysqlConn2 = new mysqli($mysqlServername2, $mysqlUsername2, $mysqlPassword2, $mysqlDbname2);
if ($mysqlConn2->connect_error) {
    die("MySQL Connection 2 failed: " . $mysqlConn2->connect_error);
}

// Third MySQL Connection (if needed)
$mysqlServername3 = "localhost";
$mysqlUsername3 = "root";
$mysqlPassword3 = "";
$mysqlDbname3 = "user";

// Create third MySQL connection
$mysqlConn3 = new mysqli($mysqlServername3, $mysqlUsername3, $mysqlPassword3, $mysqlDbname3);
if ($mysqlConn3->connect_error) {
    die("MySQL Connection 3 failed: " . $mysqlConn3->connect_error);
}


