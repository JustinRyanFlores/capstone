<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /capstone/website/login/login.php");
    exit();
}

include("../src/configs/connection.php");

$sql = "SELECT COUNT(*) AS total_population FROM residents_records";
$result = $mysqlConn->query($sql);

$sql = "SELECT COUNT(*) AS total_unsettled_cases FROM blotter WHERE blotter_status='Pending'";
$result_total_blotter = $mysqlConn2->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "" . $row['total_population'];
}

if ($result_total_blotter->num_rows > 0) {
    $row_blotter = $result_total_blotter->fetch_assoc();
    echo "" . $row_blotter['total_unsettled_cases'];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>My Web Application</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/capstone/src/css/navbar.css" />
    <link rel="stylesheet" href="/capstone/src/css/header.css" />
    <link rel="stylesheet" href="/capstone/src/css/dashboard.css" />
    <?php include '../src/components/header.php'; ?>
</head>

<body>
    <?php include '../src/components/moderator_navbar.php'; ?>
    <div class="container-fluid main-content">
        <div class="row">
            <div class="h3 col-sm-6 col-md-6 text-start h5-sm">
                Dashboard
                <div class="h6" style="font-style: italic; color: grey">
                    Home/Dashboard
                </div>
            </div>
            <div class="col-sm-6 col-md-6 d-flex justify-content-sm-between justify-content-md-end ">
                <div>
                    <?php displayDateTime(); ?>
                </div>
            </div>
        </div>

        <!-- Responsive row of three containers -->
        <div class="row">
            <div class="col-sm-4">
                <div class="custom-container" style="height: 150px;">
                    <h5>Population</h5>
                    <h1> <?php echo $row['total_population']; ?> </h1>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="custom-container" style="height: 150px;">
                    <h5>Registered Voters</h5>
                    <h1>Content</h1>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="custom-container" style="height: 150px;">
                    <h5>Unsettled Cases</h5>
                    <h1><?php echo $row_blotter['total_unsettled_cases']; ?></h1>
                </div>
            </div>
        </div>


    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>