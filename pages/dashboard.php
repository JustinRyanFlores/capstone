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
        #autoFocusBtn {
            position: absolute;
            bottom: 1%;
            /* Adjust as necessary */
            right: 3vh;
            /* Adjust as necessary */
            z-index: 1000;
            /* Ensure it's above the map */
            background-color: #1c2455;
            color: whitesmoke;
            border-color:whitesmoke;
        }

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

        .polygon-label {
            font-size: 10px;
            font-weight: bold;
            color: black;
            background: rgba(255, 255, 255, 0.7);
            padding: 1px 2px;
            border-radius: 3px;
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
        <div class="row mb-4 justify-content-center">
            <div class="col-sm-4 mb-3 d-flex justify-content-center">
                <a href="report.php#demographicsSection" style="text-decoration: none;">
                    <div class="custom-container text-center">
                        <h5>Total Population</h5>
                        <h1><?php echo $row['total_population']; ?></h1>
                    </div>
                </a>
            </div>
            <div class="col-sm-4 mb-3 d-flex justify-content-center">
                <div class="custom-container text-center">
                    <h5>Registered Voters</h5>
                    <h1>Content</h1>
                </div>
            </div>
            <div class="col-sm-4 mb-3 d-flex justify-content-center">
                <div class="custom-container text-center">
                    <h5>Unsettled Cases</h5>
                    <h1><?php echo $row_blotter['total_unsettled_cases']; ?></h1>
                </div>
            </div>
        </div>
        <div id="map" class="mb-5 text-center">
            <button id="autoFocusBtn" class="btn btn-primary" style="margin-bottom: 20px;">
                <i class="fas fa-crosshairs"></i> <!-- Compass icon -->
            </button>
        </div>
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

        // Sectors
        var sv = L.polygon([
            [14.16281008863725, 121.10695135075427],
            [14.158996892409405, 121.10317297117919],
            [14.154131410300558, 121.10257159703275],
            [14.153039079691384, 121.1059350868183],
            [14.158106245905019, 121.10900876206654],
            [14.159287195375994, 121.10948691956729]
        ], {
            color: 'blue',
            fillColor: '#0d6efd', // Improved visibility (light blue)
            fillOpacity: 0.3
        }).addTo(map).bindTooltip("South Ville", {
            permanent: true,
            direction: "center",
            className: "polygon-label"
        }).openTooltip();


        var p1 = L.polygon([
            [14.163232083344555, 121.1151981878794],
            [14.157590125374346, 121.1089005000371],
            [14.154970054428317, 121.10965537649142],
            [14.153833822703815, 121.11239785663794],
            [14.159241960976798, 121.12009221686279]
        ], {
            color: 'green',
            fillColor: '#198754', // Improved visibility (green)
            fillOpacity: 0.3
        }).addTo(map).bindTooltip("Purok-1", {
            permanent: true,
            direction: "center",
            className: "polygon-label"
        }).openTooltip();

        var p4 = L.polygon([
            [14.162601006954068, 121.10725697964726],
            [14.166922291676991, 121.11262497380848],
            [14.166362453759954, 121.11618860018439],
            [14.165408976629193, 121.11669382316428],
            [14.1640268650545, 121.11457369101659],
            [14.163327058516783, 121.11460075653335],
            [14.159609300060731, 121.10981015992907]
        ], {
            color: 'orange',
            fillColor: '#fd7e14', // Improved visibility (orange)
            fillOpacity: 0.3
        }).addTo(map).bindTooltip("Purok-4", {
            permanent: true,
            direction: "center",
            className: "polygon-label"
        }).openTooltip();

        var mvv = L.polygon([
            [14.163272568088269, 121.12055037931074],
            [14.163523080107609, 121.12120107848669],
            [14.163383906797655, 121.12192195110315],
            [14.162963293609048, 121.12189324378659],
            [14.16222103314012, 121.1227002383528],
            [14.161336502905604, 121.12122978579198]
        ], {
            color: 'red',
            fillColor: '#dc3545', // Improved visibility (red)
            fillOpacity: 0.3
        }).addTo(map).bindTooltip("Mother Ignacia<br>Villa Javier<br>Villa Andrea", {
            permanent: true,
            direction: "center",
            className: "polygon-label"
        }).openTooltip();

        var cv5 = L.polygon([
            [14.164407737288329, 121.12010936398895],
            [14.165275718428274, 121.12201355848433],
            [14.164941031178447, 121.12232839542281],
            [14.163502973754056, 121.12192631451343],
            [14.163587565619409, 121.12120181023332],
            [14.163377924851897, 121.1206062753015]
        ], {
            color: 'purple',
            fillColor: '#6f42c1', // Improved visibility (purple)
            fillOpacity: 0.3
        }).addTo(map).bindTooltip("Calamba Ville 5", {
            permanent: true,
            direction: "center",
            className: "polygon-label"
        }).openTooltip();

        var p2 = L.polygon([
            [14.165724130696342, 121.11822029916682],
            [14.163797706074321, 121.11549928167246],
            [14.161289362304672, 121.11824557014786],
            [14.163179817512763, 121.12055184555264],
            [14.164511218154459, 121.12005114102396],
            [14.164261121391938, 121.11914076915367]
        ], {
            color: 'yellow',
            fillColor: '#ffc107', // Improved visibility (yellow)
            fillOpacity: 0.3
        }).addTo(map).bindTooltip("Purok-2", {
            permanent: true,
            direction: "center",
            className: "polygon-label"
        }).openTooltip();

        var vb = L.polygon([
            [14.165467752056568, 121.12207596942218],
            [14.164383057888017, 121.119134849839],
            [14.165852642289895, 121.11837701534517],
            [14.165625207230885, 121.11774548660033],
            [14.16739219669703, 121.11659069118117],
            [14.168511866070535, 121.1190626751253],
            [14.166937329436303, 121.12007312111707],
            [14.167304722294615, 121.12115574182253],
            [14.168266938867083, 121.1210655234304],
            [14.168057001053729, 121.12202183838691]
        ], {
            color: 'cyan',
            fillColor: '#17a2b8', // Improved visibility (cyan)
            fillOpacity: 0.3
        }).addTo(map).bindTooltip("Valley-Breeze", {
            permanent: true,
            direction: "center",
            className: "polygon-label"
        }).openTooltip();

        var p1 = L.polygon([
            [14.171859915492952, 121.124809289957],
            [14.168638163949483, 121.11922567581915],
            [14.167181740706333, 121.1202574306055],
            [14.167475968377442, 121.12092503664371],
            [14.16847633960622, 121.12057606076011],
            [14.168402783048402, 121.12226024872017],
            [14.165372232143628, 121.12256370601024],
            [14.164136461909427, 121.12224507585567],
            [14.16378338346413, 121.12327683064201],
            [14.167034626727752, 121.12810180155462]
        ], {
            color: 'pink',
            fillColor: '#e83e8c', // Improved visibility (pink)
            fillOpacity: 0.3
        }).addTo(map).bindTooltip("Purok-1", {
            permanent: true,
            direction: "center",
            className: "polygon-label"
        }).openTooltip();


        var initialCoordinates = [14.162525303855341, 121.11590938129102];
        var initialZoom = 15;

        // Function to reset the map view
        function resetMapView() {
            map.setView(initialCoordinates, initialZoom);
        }

        // Add event listener to the button
        document.getElementById('autoFocusBtn').addEventListener('click', resetMapView);
    </script>

</body>

</html>