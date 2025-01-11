<?php
session_start();

// Set the default timezone to UTC+08:00
date_default_timezone_set('Asia/Singapore');
include_once "../src/components/session_handler.php";
include("../src/configs/connection.php");

// Check if the user is logged in by verifying the user_id in the session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Check if the activity log has already been inserted for this session
    if (!isset($_SESSION['activity_log_inserted'])) {

        // Query to get the first name of the user
        $sql = "SELECT fname FROM user WHERE user_id = ?";
        $stmt = $mysqlConn3->prepare($sql);

        // Bind parameters and execute the query
        $stmt->bind_param("i", $user_id); // "i" indicates integer
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($fname);

        // Check if the query returned a result
        if ($stmt->fetch()) {
            // Get the current time for login_time and date
            $login_time = date("Y-m-d H:i:s");
            $logout_time = '0000-00-00 00:00:00'; // Default value for logout_time
            $date = date("Y-m-d");

            // Insert the user's first name into the activity_log table
            $insert_sql = "INSERT INTO activity_log (name, login_time, logout_time, date) 
                           VALUES (?, ?, ?, ?)";
            $insert_stmt = $mysqlConn3->prepare($insert_sql);

            // Bind parameters for the insert query
            $insert_stmt->bind_param("ssss", $fname, $login_time, $logout_time, $date); // "s" for string, "s" for each date and time field

            // Execute the insert query
            if ($insert_stmt->execute()) {
                // Set session variable to mark that the activity log has been inserted
                $_SESSION['activity_log_inserted'] = true;
            } else {
                // echo "Error inserting first name: " . $insert_stmt->error;
            }

            // Close the insert statement
            $insert_stmt->close();
        } else {
            // echo "User not found.";
        }

        // Close the statement and connection
        $stmt->close();
        $mysqlConn3->close();
    } else {
        // Activity log already inserted for this session
        // echo "Activity log already inserted for this session.";
    }
} else {
    // echo "User is not logged in.";
}


// Initialize search_query to prevent undefined variable warning
$search_query = "";

// Check if search query is set
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

// Query total population
$sql = "SELECT COUNT(*) AS total_population FROM residents_records";
$result = $mysqlConn->query($sql);
$total_population = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_population = $row['total_population'];
}

// Query total registered voters
$sql_voters = "SELECT COUNT(*) AS total_voters FROM residents_records WHERE voterstatus = '1'";
$result_voters = $mysqlConn->query($sql_voters);
$total_voters = 0;
if ($result_voters->num_rows > 0) {
    $row_voters = $result_voters->fetch_assoc();
    $total_voters = $row_voters['total_voters'];
}

// Query total unsettled cases
$sql = "SELECT COUNT(*) AS total_unsettled_cases FROM blotter WHERE blotter_status='Pending'";
$result_total_blotter = $mysqlConn2->query($sql);
$total_unsettled_cases = 0;
if ($result_total_blotter->num_rows > 0) {
    $row_blotter = $result_total_blotter->fetch_assoc();
    $total_unsettled_cases = $row_blotter['total_unsettled_cases'];
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Kay-Anlog Sys Info | F.A.Qs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/system/src/css/navbar.css" />
    <link rel="stylesheet" href="/system/src/css/header.css" />
    <link rel="stylesheet" href="/system/src/css/dashboard.css" />
    <link rel="stylesheet" href="/system/src/css/report.css" />
    <?php include '../src/components/header.php'; ?>
    <style>
        #autoFocusBtn {
            z-index: 1000;
            background-color: #1c2455;
            color: whitesmoke;
            border-color: whitesmoke;
        }

        #autoFocusBtn:hover {
            z-index: 1000;
            background-color: whitesmoke;
            color: #1c2455;
            border-color: #1c2455;
        }

        #autoFocusBtn {
            position: absolute;
            /* Position the button absolutely */
            top: 5%;
            /* 5% from the top */
            left: 90%;
            /* 95% from the left */
            transform: translate(-50%, 0);
            /* Center horizontally */
            padding: 10px 15px;
            /* Padding for the button */
            border: none;
            /* No border */
            border-radius: 5px;
            /* Rounded corners */
            cursor: pointer;
            /* Pointer cursor on hover */
            font-size: 16px;
            /* Default font size */
            z-index: 1000;
            /* Ensure it appears above other elements */
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            #autoFocusBtn {
                font-size: 14px;
                /* Smaller font size on smaller screens */
                padding: 8px 12px;
                /* Smaller padding */
                left: 85%;
                /* Adjust left position */
            }
        }

        @media (max-width: 480px) {
            #autoFocusBtn {
                font-size: 12px;
                /* Even smaller font size */
                padding: 6px 10px;
                /* Smaller padding */
                left: 80%;
                /* Further adjust left position */
            }
        }

        .map-container {
            display: flex;
            flex-direction: column;
            /* Stack elements vertically */
            align-items: center;
            /* Center horizontally */
        }

        #map {
            height: 500px;
            width: 50%;
            margin-top: 20px;
            border: 4px solid #1c2455;
            /* Border color */
            border-radius: 10px;
            /* Rounded corners */
        }

        .leaflet-control-attribution {
            font-size: 10px;
        }

        .custom-container {
            background: #1c2455;
            /* Dark blue background */
            border: 1px solid #dee2e6;
            /* Default light border */
            border-radius: 8px;
            /* Rounded corners */
            padding: 15px;
            /* Reduced inner spacing */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
            max-width: 300px;
            /* Optional: limit max width */
            width: 100%;
            /* Ensures full width in column */
            height: auto;
            /* Let the height adjust dynamically */
            min-height: 150px;
            /* Set a reasonable minimum height */
        }

        .custom-container h5 {
            font-size: 1.2rem;
            /* Adjust for readability */
            margin-bottom: 10px;
        }

        .custom-container h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
        }


        .custom-container:hover {
            transform: scale(1.05);
            /* Scale effect on hover */
        }

        h1 {
            font-size: 2.5rem;
            /* Larger font size for numbers */
            color: #ffffff;
            /* Dark color for contrast */
        }

        h5 {
            color: #dee2e6;
            /* Muted color for headings */
        }

  

        .polygon-label {
            font-size: 10px;
            font-weight: bold;
            color: black;
            background: rgba(255, 255, 255, 0.7);
            padding: 1px 2px;
            border-radius: 3px;
        }

        .filter-popup {
            border: 5px solid #1c2455;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 300px;
            background-color: white;
        }

        .btnfilter {
            background-color: #1c2455;
            border-color: #1c2455;
            color: white;
            border-radius: 0.3rem;

        }

        .btnfilter:hover {
            background-color: #16203c;
        }

        .btnfilter2 {
            background-color: darkgrey;
            border-color: darkgrey;
            color: white;
            border-radius: 0.3rem;

        }

        .btnfilter2:hover {
            background-color: grey;
        }

        .polygon-label {
            font-size: 12px;
            /* Base size */
            transition: font-size 0.2s ease;
            /* Smooth transition */
        }

        .enlarged {
            font-size: 16px;
            /* Increased size on hover */
        }

        .btn-delete {
            background-color: #610000;
            border-color: #610000;
            color: #ffffff;
        }

        .btn-delete:hover {
            background-color: white;
            border-color: #610000;
            color: #610000;
        }
    </style>

</head>

<body>
    <?php include '../src/components/moderator_navbar.php'; ?>
    <div class="container-fluid main-content">
        <div class="row">
            <div class="h3 col-sm-6 col-md-6 text-start h5-sm">
                Dashboard
                <div class="h6" style="font-style: italic; color: grey">
                    Home / Dash
                </div>
            </div>
            <div class="col-sm-6 col-md-6 d-flex justify-content-sm-between justify-content-md-end">
                <div>
                    <?php displayDateTime(); ?>
                </div>
            </div>
        </div>

        
</body>

</html>