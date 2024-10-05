<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /capstone/website/login/login.php");
    exit();
}

include("../src/configs/connection.php");

// Query total population
$sql = "SELECT COUNT(*) AS total_population FROM residents_records";
$result = $mysqlConn->query($sql);
$total_population = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_population = $row['total_population'];
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
    <title>My Web Application</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/capstone/src/css/navbar.css" />
    <link rel="stylesheet" href="/capstone/src/css/header.css" />
    <link rel="stylesheet" href="/capstone/src/css/dashboard.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 600px;
            width: 100%;
            margin-top: 20px;
            border: 4px solid #1c2455;
            /* Border color */
            border-radius: 10px;
            /* Rounded corners */
        }

        .custom-container {
            border-radius: 5px;
            /* Rounded corners for containers */
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            display: flex;
            /* Enable Flexbox */
            flex-direction: column;
            /* Stack children vertically */
            justify-content: center;
            /* Center vertically */
            align-items: center;
            /* Center horizontally */
            height: 100%;
            /* Ensure full height is used */
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

        .main-content {
            margin-top: 20px;
            /* Space between the header and content */
        }
    </style>
    <?php include '../src/components/header.php'; ?>
</head>

<body>
    <?php include '../src/components/moderator_navbar.php'; ?>
    <div class="container-fluid main-content">
        <div class="row mb-3">
            <div class="h3 col-md-6 text-start">
                Dashboard
                <div class="h6" style="font-style: italic; color: grey">
                    Home / Dashboard
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end">
                <div>
                    <?php displayDateTime(); ?>
                </div>
            </div>
        </div>

        <!-- Responsive row of three containers -->
        <div class="row mb-4">
            <div class="col-sm-4 mb-3">
                <a href="report.php#demographicsSection" style="text-decoration: none;">
                    <div class="custom-container">
                        <h5>Total Population</h5>
                        <h1><?php echo $row['total_population']; ?></h1>
                    </div>
                </a>
            </div>
            <div class="col-sm-4 mb-3">
                <div class="custom-container">
                    <h5>Registered Voters</h5>
                    <h1>Content</h1>
                </div>
            </div>
            <div class="col-sm-4 mb-3">
                <div class="custom-container">
                    <h5>Unsettled Cases</h5>
                    <h1><?php echo $row_blotter['total_unsettled_cases']; ?></h1>
                </div>
            </div>
        </div>
        <div id="map" class="mb-5"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        var map = L.map('map').setView([14.162525303855341, 121.11590938129102], 15);

        // Esri Satellite Layer
        L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
            maxZoom: 18
        }).addTo(map);

        // Esri Labels Layer
        L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Labels &copy; Esri',
            maxZoom: 18
        }).addTo(map);

        // Add a polygon for a sector
        var sector1 = L.polygon([
            [14.156987501471228, 121.10225239305838],
            [14.161133939499035, 121.10488677002489],
            [14.164108131116082, 121.10880224811896],
            [14.162096182226602, 121.11273576989086],
            [14.153628306303592, 121.11352969171638],
            [14.156760057532859, 121.10806245732698],
            [14.156932411052473, 121.10805708007629]
        ], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5
        }).addTo(map);

        // Handle click events
        sector1.on('click', function() {
            alert('Sector 1 clicked!');
        });
    </script>
</body>

</html>