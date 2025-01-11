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
    <title>Kay-Anlog Sys Info | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/system/src/css/navbar.css" />
    <link rel="stylesheet" href="/system/src/css/header.css" />
    <link rel="stylesheet" href="/system/src/css/dashboard.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
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
        <div class="container">
            <div class="row mb-4 justify-content-center">
                <!-- Total Population -->
                <div class="col-12 col-md-6 col-lg-4 mb-3 d-flex justify-content-center">
                    <a href="report.php#demographicsSection" style="text-decoration: none; width: 100%;">
                        <div class="custom-container text-center">
                            <h5>Total Population</h5>
                            <h1><?php echo $row['total_population']; ?></h1>
                        </div>
                    </a>
                </div>
                <!-- Total Registered Voters -->
                <div class="col-12 col-md-6 col-lg-4 mb-3 d-flex justify-content-center">
                    <a href="report.php" style="text-decoration: none; width: 100%;">
                        <div class="custom-container text-center">
                            <h5>Total Registered Voters</h5>
                            <h1><?php echo $total_voters; ?></h1>
                        </div>
                    </a>
                </div>
                <!-- Unsettled Cases -->
                <div class="col-12 col-md-6 col-lg-4 mb-3 d-flex justify-content-center">
                    <a href="report.php#characterSection" style="text-decoration: none; width: 100%;">
                        <div class="custom-container text-center">
                            <h5>Unsettled Cases</h5>
                            <h1><?php echo $row_blotter['total_unsettled_cases']; ?></h1>
                        </div>
                    </a>
                </div>
            </div>
        </div>


        <div class="row">
            <!-- GIS Map (Left Column) -->
            <div class="col-lg-5 col-md-7 col-sm-12">
                <div class="position-relative shadow rounded">
                    <div id="map" class="w-100" style="height: 600px; background-color: #eef;"></div>
                    <button id="autoFocusBtn" class="btn btn-dark position-absolute bottom-0 end-0 m-3">
                        <i class="fas fa-crosshairs"></i>
                    </button>
                </div>
            </div>


            <div class="col-lg-7 col-md-5 col-sm-12" style="margin-top: -25px;">
                <div class="row mt-4 d-flex justify-content-center">
                    <div class="col-12 col-md-11 m-3 bg-light text-white p-2 shadow rounded">
                        <!-- Search Form -->
                        <div class="col-12 mb-2">
                            <form method="GET" action="dashboard.php">
                                <div class="input-group">
                                    <input
                                        type="text"
                                        name="search"
                                        class="form-control"
                                        placeholder="Type Here to Search..."
                                        value="<?php echo htmlspecialchars($search_query); ?>" />
                                    <button type="submit" class="btn btn-custom">Search</button>
                                </div>
                            </form>
                        </div>
                        <!-- Filter and Clear Buttons -->
                        <div class="row">
                            <div class="col-6 mb-2">
                                <button
                                    type="button"
                                    class="btn btn-outline-secondary w-100"
                                    id="filterButton"
                                    onclick="toggleFilterPopup()">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                            <div class="col-6 mb-2">
                                <button
                                    type="button"
                                    class="btn btn-danger w-100"
                                    onclick="clearFilters()">
                                    <i class="bi bi-x-circle"></i> Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Filter Panel (Hidden by Default) -->
                <div id="filterPanel" class="filter-popup shadow rounded p-3 bg-white" style="display: none; position: absolute; max-width: 340px; max-height: 300px; overflow-y: auto; z-index: 1050;">
                    <form id="filterForm" action="dashboard.php" method="GET">
                        <input type="hidden" id="subdivision" name="subdivision" value="">
                        <div id="selectedSubdivision" class="mb-2" style="display:none;">
                            <strong>Area:</strong> <span id="subdivisionLabel"></span>
                        </div>

                        <!-- Demographic Filters -->
                        <h6 class="mt-2">Demographic Details</h6>
                        <div class="mb-2">
                            <label for="age_range">Age Range</label>
                            <div class="d-flex">
                                <input type="number" id="age_min" name="age_min" class="form-control me-2" min="1" max="100" value="1" oninput="updateAgeRange()" required>
                                <input type="number" id="age_max" name="age_max" class="form-control" min="1" max="100" value="100" oninput="updateAgeRange()" required>
                            </div>
                            <span id="ageRangeLabel">1-100</span> <!-- This span displays the selected age range -->
                        </div>

                        <div class="mb-2">
                            <label for="gender">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Any</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <label for="dob_start">DOB From</label>
                                <input type="date" class="form-control" name="dob_start" id="dob_start">
                            </div>
                            <div class="col-6">
                                <label for="dob_end">DOB To</label>
                                <input type="date" class="form-control" name="dob_end" id="dob_end">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="religion">Religion</label>
                            <input type="text" class="form-control" name="religion" id="religion">
                        </div>
                        <div class="mb-2">
                            <label for="civilstatus">Civil Status</label>
                            <select name="civilstatus" class="form-select">
                                <option value="">Any</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label for="voterstatus">Voter Status</label>
                            <select name="voterstatus" class="form-select">
                                <option value="">Any</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>


                        <!-- Socio-Economic and Educational Filters -->
                        <h6 class="mt-2">Socio-Economic & Educational</h6>
                        <div class="row mb-2">
                            <div class="col-6">
                                <label for="osy">OSY Status</label>
                                <select name="osy" class="form-select">
                                    <option value="">Any</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="als">ALS Status</label>
                                <select name="als" class="form-select">
                                    <option value="">Any</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <!-- Health and Medical Filters -->
                        <h6 class="mt-2">Health & Medical Data</h6>
                        <div class="row mb-2">
                            <div class="col-6">
                                <label for="immunization_status">Immunization</label>
                                <select name="immunization_status" class="form-select">
                                    <option value="">Any</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="pwd">PWD Status</label>
                                <select name="pwd" class="form-select">
                                    <option value="">Any</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <label for="teen_pregnancy">Teen Pregnancy</label>
                                <select name="teen_pregnancy" class="form-select">
                                    <option value="">Any</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="type_of_delivery">Type of Delivery</label>
                                <select name="type_of_delivery" class="form-select">
                                    <option value="">Any</option>
                                    <option value="Normal">Normal</option>
                                    <option value="Cesarean">Cesarean</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="assisted_by">Assisted By</label>
                            <select name="assisted_by" class="form-select">
                                <option value="">Any</option>
                                <option value="Doctor">Doctor</option>
                                <option value="Midwife">Midwife</option>
                                <option value="Nurse">Nurse</option>
                            </select>
                        </div>

                        <!-- Duration of Residency Filters -->
                        <h6 class="mt-2">Duration of Residency</h6>
                        <div class="mb-2">
                            <label for="years_of_stay">Years of Stay</label>
                            <input type="number" class="form-control" name="years_of_stay" id="years_of_stay" min="0">
                        </div>

                        <!-- Business and Organization Filters -->
                        <h6 class="mt-2">Business & Organization</h6>
                        <div class="mb-2">
                            <label for="business_owner">Business Owner</label>
                            <select name="business_owner" class="form-select">
                                <option value="">Any</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="ofw">OFW</label>
                            <select name="ofw" class="form-select">
                                <option value="">Any</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="employment">Employment</label>
                            <select name="employment" class="form-select">
                                <option value="">Any</option>
                                <option value="Employed">Yes</option>
                                <option value="Unemployed">No</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="occupation">Occupation</label>
                            <input type="text" class="form-control" name="occupation" id="occupation">
                        </div>


                        <!-- Button Group -->
                        <div class="d-flex flex-column mt-3">
                            <button type="submit" class="btnfilter btn-primary w-100 mb-2">Apply Filters</button>
                            <button type="button" class="btnfilter2 btn-secondary w-100" onclick="toggleFilterPopup()">Cancel</button>
                        </div>

                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_query) ?>">

                    </form>
                </div>
                <script>
                    function toggleFilterPopup() {
                        const filterPanel = document.getElementById('filterPanel');
                        const filterButton = document.getElementById('filterButton');

                        if (!filterPanel || !filterButton) {
                            console.error('Filter panel or button not found');
                            return;
                        }

                        // Get the button's position
                        const rect = filterButton.getBoundingClientRect();

                        // Toggle visibility
                        if (filterPanel.style.display === 'none' || filterPanel.style.display === '') {
                            filterPanel.style.display = 'block';
                        } else {
                            filterPanel.style.display = 'none';
                        }
                    }


                    // Ensure age range validation
                    function updateAgeRange() {
                        const minAgeInput = document.getElementById('age_min');
                        const maxAgeInput = document.getElementById('age_max');

                        let minAge = parseInt(minAgeInput.value, 10);
                        let maxAge = parseInt(maxAgeInput.value, 10);

                        if (minAge > maxAge) {
                            minAgeInput.value = maxAge;
                        }

                        if (maxAge < minAge) {
                            maxAgeInput.value = minAge;
                        }

                        document.getElementById('ageRangeLabel').textContent = `${minAgeInput.value}-${maxAgeInput.value}`;
                    }

                    // Clear filters and reset the form
                    function clearFilters() {
                        // Reset all input fields in the filter form
                        document.getElementById('filterForm').reset();

                        // Hide the filter panel
                        document.getElementById('filterPanel').style.display = 'none';

                        // Reset subdivision input, label, and hide selected subdivision display
                        const subdivisionInput = document.getElementById('subdivision');
                        const subdivisionLabel = document.getElementById('subdivisionLabel');
                        const selectedSubdivision = document.getElementById('selectedSubdivision');

                        subdivisionInput.value = ''; // Clear the subdivision input field
                        subdivisionLabel.textContent = ''; // Reset the label text
                        selectedSubdivision.style.display = 'none'; // Hide the selected subdivision display

                        // Reload the page without query parameters to remove all filters
                        window.location.href = window.location.pathname;
                    }



                    // Close filter panel on outside click
                    document.addEventListener('click', function(event) {
                        const filterPanel = document.getElementById('filterPanel');
                        const filterButton = document.getElementById('filterButton');

                        if (!filterPanel.contains(event.target) && event.target !== filterButton) {
                            filterPanel.style.display = 'none';
                        }
                    });
                </script>

                <!-- Resident Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="resident-table-container" tabindex="-1" id="residentTableContainer">
                            <!-- Add responsive container -->
                            <div class="table-responsive">
                                <div class="table-body-scroll">
                                    <div class="table-responsive">
                                        <table class="table table-custom">
                                            <thead>
                                                <tr>
                                                    <th>Name of Resident</th>
                                                    <th>Age</th>
                                                    <th>Gender</th>
                                                    <th>Birthdate</th>
                                                    <th>Contact Number</th>
                                                    <th>Subdivision</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Pagination settings
                                                $limit = 7; // Number of records per page
                                                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                                $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
                                                $start_from = ($page - 1) * $limit;

                                                // Check if the search query is numeric (for age search)
                                                $is_numeric_search = is_numeric($search_query);

                                                // Base query
                                                $query = "
                                                    SELECT id, first_name, middle_name, last_name, suffix, dob, gender, contact_number, subdivision,
                                                    FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) AS age
                                                    FROM residents_records 
                                                    WHERE 1 "; // Base condition to always be true for adding dynamic filters

                                                // Apply filters from GET parameters (add conditions to the query)
                                                if (!empty($_GET['first_name'])) {
                                                    $first_name = $mysqlConn->real_escape_string($_GET['first_name']);
                                                    $query .= " AND first_name LIKE '%$first_name%'";
                                                }

                                                if (!empty($_GET['middle_name'])) {
                                                    $middle_name = $mysqlConn->real_escape_string($_GET['middle_name']);
                                                    $query .= " AND middle_name LIKE '%$middle_name%'";
                                                }

                                                if (!empty($_GET['last_name'])) {
                                                    $last_name = $mysqlConn->real_escape_string($_GET['last_name']);
                                                    $query .= " AND last_name LIKE '%$last_name%'";
                                                }

                                                if (!empty($_GET['suffix'])) {
                                                    $suffix = $mysqlConn->real_escape_string($_GET['suffix']);
                                                    $query .= " AND suffix LIKE '%$suffix%'";
                                                }

                                                if (!empty($_GET['age_min']) && !empty($_GET['age_max'])) {
                                                    $age_min = (int)$_GET['age_min'];
                                                    $age_max = (int)$_GET['age_max'];

                                                    if ($age_min <= $age_max) {
                                                        $query .= " AND age BETWEEN $age_min AND $age_max";
                                                    }
                                                }

                                                if (!empty($_GET['gender'])) {
                                                    $gender = $mysqlConn->real_escape_string($_GET['gender']);
                                                    $query .= " AND gender = '$gender'";
                                                }

                                                if (!empty($_GET['dob_start']) && !empty($_GET['dob_end'])) {
                                                    $dob_start = $mysqlConn->real_escape_string($_GET['dob_start']);
                                                    $dob_end = $mysqlConn->real_escape_string($_GET['dob_end']);
                                                    $query .= " AND dob BETWEEN '$dob_start' AND '$dob_end'";
                                                }

                                                if (!empty($_GET['religion'])) {
                                                    $religion = $mysqlConn->real_escape_string($_GET['religion']);
                                                    $query .= " AND religion LIKE '%$religion%'";
                                                }

                                                if (!empty($_GET['civilstatus'])) {
                                                    $civilstatus = $mysqlConn->real_escape_string($_GET['civilstatus']);
                                                    $query .= " AND civil_status LIKE '%$civilstatus%'";
                                                }


                                                if (isset($_GET['voterstatus']) && $_GET['voterstatus'] !== '') {
                                                    $voterstatus = (int)$_GET['voterstatus'];
                                                    $query .= " AND voterstatus = $voterstatus";
                                                }

                                                if (!empty($_GET['philhealth'])) {
                                                    $philhealth = $mysqlConn->real_escape_string($_GET['philhealth']);
                                                    $query .= " AND philhealth = '$philhealth'";
                                                }

                                                if (!empty($_GET['osy'])) {
                                                    $osy = $mysqlConn->real_escape_string($_GET['osy']);
                                                    $query .= " AND osy = '$osy'";
                                                }

                                                if (!empty($_GET['als'])) {
                                                    $als = $mysqlConn->real_escape_string($_GET['als']);
                                                    $query .= " AND als = '$als'";
                                                }

                                                if (!empty($_GET['immunization_status'])) {
                                                    $immunization_status = $_GET['immunization_status'];
                                                    if ($immunization_status === 'Yes') {
                                                        $query .= " AND immunization != ''";
                                                    } elseif ($immunization_status === 'No') {
                                                        $query .= " AND immunization = ''";
                                                    }
                                                }

                                                if (!empty($_GET['pwd'])) {
                                                    $pwd = $mysqlConn->real_escape_string($_GET['pwd']);
                                                    $query .= " AND pwd = '$pwd'";
                                                }

                                                if (!empty($_GET['teen_pregnancy'])) {
                                                    $teen_pregnancy = $mysqlConn->real_escape_string($_GET['teen_pregnancy']);
                                                    $query .= " AND teen_pregnancy = '$teen_pregnancy'";
                                                }

                                                if (!empty($_GET['type_of_delivery'])) {
                                                    $type_of_delivery = $_GET['type_of_delivery'];
                                                    if ($type_of_delivery === 'Normal') {
                                                        $query .= " AND type_of_delivery = 'Vaginal Delivery'";
                                                    } elseif ($type_of_delivery === 'Cesarean') {
                                                        $query .= " AND type_of_delivery = 'Cesarean Section'";
                                                    }
                                                }

                                                if (!empty($_GET['assisted_by'])) {
                                                    $assisted_by = $_GET['assisted_by'];
                                                    if ($assisted_by === 'Doctor') {
                                                        $query .= " AND assisted_by = 'Doctor'";
                                                    } elseif ($assisted_by === 'Midwife') {
                                                        $query .= " AND assisted_by = 'Midwife'";
                                                    } elseif ($assisted_by === 'Nurse') {
                                                        $query .= " AND assisted_by = 'Nurse'";
                                                    }
                                                }

                                                if (!empty($_GET['years_of_stay'])) {
                                                    $years_of_stay = (int)$_GET['years_of_stay'];
                                                    $query .= " AND years_of_stay >= $years_of_stay";
                                                }

                                                if (!empty($_GET['business_owner'])) {
                                                    $business_owner = $mysqlConn->real_escape_string($_GET['business_owner']);
                                                    $query .= " AND business_owner = '$business_owner'";
                                                }

                                                if (!empty($_GET['ofw'])) {
                                                    $ofw = $mysqlConn->real_escape_string($_GET['ofw']);
                                                    $query .= " AND ofw = '$ofw'";
                                                }

                                                if (!empty($_GET['employment'])) {
                                                    $employment = $mysqlConn->real_escape_string($_GET['employment']);
                                                    $query .= " AND employment = '$employment'";
                                                }

                                                if (!empty($_GET['occupation'])) {
                                                    $occupation = $mysqlConn->real_escape_string($_GET['occupation']);
                                                    $query .= " AND occupation LIKE '%$occupation%'";
                                                }


                                                if (!empty($_GET['subdivision'])) {
                                                    $subdivisions = explode(',', $mysqlConn->real_escape_string($_GET['subdivision']));
                                                    $subdivisionList = "'" . implode("','", $subdivisions) . "'";
                                                    $query .= " AND subdivision IN ($subdivisionList)";
                                                }

                                                // If a search query exists, split it into individual words
                                                if (!empty($search_query)) {
                                                    $search_terms = explode(" ", $search_query);

                                                    // Build the search condition with each term matching any of the name fields
                                                    $query .= " AND (";
                                                    $first_term = true;
                                                    foreach ($search_terms as $term) {
                                                        $term = $mysqlConn->real_escape_string($term);
                                                        if (!$first_term) {
                                                            $query .= " AND ";
                                                        }
                                                        $query .= "(first_name LIKE '%$term%' OR middle_name LIKE '%$term%' OR last_name LIKE '%$term%' OR suffix LIKE '%$term%')";
                                                        $first_term = false;
                                                    }
                                                    $query .= " OR gender LIKE '%$search_query%' 
                                                    OR dob LIKE '%$search_query%' 
                                                    OR contact_number LIKE '%$search_query%' 
                                                    OR subdivision LIKE '%$search_query%') ";
                                                }

                                                // If the search query is numeric, include it in the age filter
                                                if ($is_numeric_search) {
                                                    $query .= " OR FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) = $search_query";
                                                }

                                                // Add the ORDER BY clause to sort by name
                                                $query .= " ORDER BY first_name, middle_name, last_name, suffix";

                                                // Add the pagination limit
                                                $query .= " LIMIT $start_from, $limit";

                                                // Execute the query
                                                $result = $mysqlConn->query($query);

                                                // Loop through the records and display them
                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<tr data-id='{$row['id']}' onclick='fetchResidentDetails({$row['id']})'>
                                                            <td>{$row['first_name']} {$row['middle_name']} {$row['last_name']} {$row['suffix']}</td>
                                                            <td>{$row['age']}</td>
                                                            <td>{$row['gender']}</td>
                                                            <td>{$row['dob']}</td>
                                                            <td>{$row['contact_number']}</td>
                                                            <td>{$row['subdivision']}</td>
                                                            </tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='6'>No records found</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>


                                        <!-- Pagination and Info -->
                                        <div class="pagination-container">
                                            <div class="pagination-info">
                                                <?php
                                                // Fetch total records for "Showing X to Y of Z entries"
                                                $query_total = "SELECT COUNT(*) FROM residents_records 
                                    WHERE first_name LIKE '%$search_query%' 
                                    OR middle_name LIKE '%$search_query%' 
                                    OR last_name LIKE '%$search_query%' 
                                    OR suffix LIKE '%$search_query%' 
                                    OR dob LIKE '%$search_query%' 
                                    OR contact_number LIKE '%$search_query%' 
                                    OR subdivision LIKE '%$search_query%'";

                                                // If the search query is numeric, include it in the total count query
                                                if ($is_numeric_search) {
                                                    $query_total .= " OR FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) = $search_query";
                                                }

                                                $total_result = $mysqlConn->query($query_total);
                                                $total_records = $total_result->fetch_row()[0];
                                                $total_pages = ceil($total_records / $limit);
                                                $end = min($page * $limit, $total_records);
                                                $start = ($page - 1) * $limit + 1;
                                                echo "Showing $start to $end of $total_records entries";
                                                ?>
                                            </div>
                                            <ul class="pagination">
                                                <?php
                                                // Build a query string with all current filters
                                                $query_string = $_GET; // Get all current GET parameters
                                                unset($query_string['page']); // Remove 'page' to handle it dynamically
                                                $query_string = http_build_query($query_string); // Convert to query string format

                                                // Define the maximum number of pages to show
                                                $max_visible_pages = 5;

                                                // Calculate start and end page for the pagination range
                                                $start_page = max(1, $page - floor($max_visible_pages / 2));
                                                $end_page = min($total_pages, $start_page + $max_visible_pages - 1);

                                                // Adjust start_page if near the end
                                                $start_page = max(1, $end_page - $max_visible_pages + 1);

                                                // Generate "Previous" button
                                                if ($page > 1) {
                                                    echo "<li class='page-item'><a class='page-link' href='?page=" . ($page - 1) . "&$query_string'>Previous</a></li>";
                                                }

                                                // Generate page links
                                                for ($i = $start_page; $i <= $end_page; $i++) {
                                                    if ($i == $page) {
                                                        echo "<li class='page-item active'><a class='page-link' href='?page=$i&$query_string'>$i</a></li>";
                                                    } else {
                                                        echo "<li class='page-item'><a class='page-link' href='?page=$i&$query_string'>$i</a></li>";
                                                    }
                                                }

                                                // Generate "Next" button
                                                if ($page < $total_pages) {
                                                    echo "<li class='page-item'><a class='page-link' href='?page=" . ($page + 1) . "&$query_string'>Next</a></li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resident Details Modal -->
                <div class="modal fade" id="residentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="residentDetailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="residentDetailsModalLabel">Resident's Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div id="printable-details">
                                <div class="modal-body" id="resident-details">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <span class="text-muted">Select a resident to view details</span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="button-container">
                                    <button class="btn btn-delete" onclick="printResidentDetails()">Print</button>
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>

                                <script>
                                    var selectedResidentId; // Declare a global variable

                                    function fetchResidentDetails(residentId) {
                                        selectedResidentId = residentId; // Store the resident ID globally for other actions (e.g., edit/delete)

                                        // Show a loading spinner while fetching data
                                        $("#resident-details").html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

                                        $.ajax({
                                            url: "/system/src/components/getResidentDetails.php", // Backend script to fetch details
                                            type: "POST",
                                            data: {
                                                id: residentId
                                            },
                                            success: function(data) {
                                                // Populate the modal body with the fetched resident details
                                                $("#resident-details").html(data);
                                                $('#residentDetailsModal').modal('show'); // Show the modal

                                                // Ensure the Edit button has the correct ID for redirection
                                                $('.btn-primary').attr('onclick', 'editResident(' + residentId + ')');
                                            },
                                            error: function() {
                                                $("#resident-details").html('<div class="text-danger">Unable to retrieve data.</div>');
                                            }
                                        });
                                    }


                                    $(document).ready(function() {
                                        const urlParams = new URLSearchParams(window.location.search);
                                        const residentId = urlParams.get('residentId');

                                        if (residentId) {
                                            fetchResidentDetails(residentId); // Automatically load details if residentId is in the URL
                                        }
                                    });

                                    if (window.location.search.includes('success=updated')) {
                                        alert('Record updated successfully!');
                                        window.scrollTo(0, 0); // Scroll to top
                                    }

                                    function printResidentDetails() {
                                        var residentName = document.querySelector('h3.text-primary').innerText || 'Unknown Resident';

                                        // Consolidate all content into a document-style layout
                                        var content = `
                                                        <div style="font-family: Arial, sans-serif; margin: 10px; font-size: 0.9em; line-height: 1.2;">
                                                    <h1 style="text-align: center; font-size: 1.5em; margin-bottom: 5px;">Resident Details</h1>
                                                    <h2 style="text-align: center; font-size: 1.2em; margin-bottom: 10px;">${residentName}</h2>

                                                    <section style="margin-bottom: 10px;">
                                                        <h3 style="font-size: 1em; text-decoration: underline;">Personal Information</h3>
                                                        <p><strong>Birth Date:</strong> ${getData('#profile tr:nth-child(1) td:nth-child(2)')}</p>
                                                        <p><strong>Age:</strong> ${getData('#profile tr:nth-child(2) td:nth-child(2)')}</p>
                                                        <p><strong>Gender:</strong> ${getData('#profile tr:nth-child(3) td:nth-child(2)')}</p>
                                                        <p><strong>Contact:</strong> ${getData('#profile tr:nth-child(4) td:nth-child(2)')}</p>
                                                        <p><strong>Religion:</strong> ${getData('#profile tr:nth-child(5) td:nth-child(2)')}</p>
                                                        <p><strong>Status:</strong> ${getData('#profile tr:nth-child(6) td:nth-child(2)')}</p>
                                                        <p><strong>Voter:</strong> ${getData('#profile tr:nth-child(7) td:nth-child(2)')}</p>
                                                    </section>

                                                    <section style="margin-bottom: 10px;">
                                                        <h3 style="font-size: 1em; text-decoration: underline;">Address</h3>
                                                        <p><strong>Street:</strong> ${getData('#address tr:nth-child(1) td:nth-child(2)')}</p>
                                                        <p><strong>House #:</strong> ${getData('#address tr:nth-child(2) td:nth-child(2)')}</p>
                                                        <p><strong>Barangay:</strong> ${getData('#address tr:nth-child(4) td:nth-child(2)')}</p>
                                                        <p><strong>City:</strong> ${getData('#address tr:nth-child(5) td:nth-child(2)')}</p>
                                                        <p><strong>Province:</strong> ${getData('#address tr:nth-child(6) td:nth-child(2)')}</p>
                                                    </section>

                                                    <section style="margin-bottom: 10px;">
                                                        <h3 style="font-size: 1em; text-decoration: underline;">Family Information</h3>
                                                        <p><strong>Mother:</strong> ${getData('#family tr:nth-child(1) td:nth-child(2)')}</p>
                                                        <p><strong>Father:</strong> ${getData('#family tr:nth-child(2) td:nth-child(2)')}</p>
                                                    </section>

                                                    <section style="margin-bottom: 10px;">
                                                        <h3 style="font-size: 1em; text-decoration: underline;">Education</h3>
                                                        <p><strong>Out of School:</strong> ${getData('#education tr:nth-child(1) td:nth-child(2)')}</p>
                                                        <p><strong>Attainment:</strong> ${getData('#education tr:nth-child(2) td:nth-child(2)')}</p>
                                                        <p><strong>Enrolled in ALS:</strong> ${getData('#education tr:nth-child(3) td:nth-child(2)')}</p>
                                                        <p><strong>School:</strong> ${getData('#education tr:nth-child(4) td:nth-child(2)')}</p>
                                                    </section>

                                                    <section style="margin-bottom: 10px;">
                                                        <h3 style="font-size: 1em; text-decoration: underline;">Health</h3>
                                                        <p><strong>Illness:</strong> ${getData('#health tr:nth-child(1) td:nth-child(2)')}</p>
                                                        <p><strong>Medication:</strong> ${getData('#health tr:nth-child(2) td:nth-child(2)')}</p>
                                                        <p><strong>Disability:</strong> ${getData('#health tr:nth-child(3) td:nth-child(2)')}</p>
                                                        <p><strong>Blood Type:</strong> ${getData('#health tr:nth-child(4) td:nth-child(2)')}</p>
                                                        <p><strong>PWD:</strong> ${getData('#health tr:nth-child(5) td:nth-child(2)')}</p>
                                                        <p><strong>Immunization:</strong> ${getData('#health tr:nth-child(6) td:nth-child(2)')}</p>
                                                        <p><strong>Teen Pregnancy:</strong> ${getData('#health tr:nth-child(7) td:nth-child(2)')}</p>
                                                        <p><strong>Delivery:</strong> ${getData('#health tr:nth-child(8) td:nth-child(2)')}</p>
                                                        <p><strong>Assisted By:</strong> ${getData('#health tr:nth-child(9) td:nth-child(2)')}</p>
                                                    </section>

                                                    <section style="margin-bottom: 10px;">
                                                        <h3 style="font-size: 1em; text-decoration: underline;">Identification</h3>
                                                        <p><strong>Solo Parent ID:</strong> ${getData('#identification tr:nth-child(1) td:nth-child(2)')}</p>
                                                        <p><strong>Senior ID:</strong> ${getData('#identification tr:nth-child(2) td:nth-child(2)')}</p>
                                                        <p><strong>PWD ID:</strong> ${getData('#identification tr:nth-child(3) td:nth-child(2)')}</p>
                                                        <p><strong>PhilHealth:</strong> ${getData('#identification tr:nth-child(4) td:nth-child(2)')}</p>
                                                    </section>
                                                </div> `;

                                        // Helper function to get data safely
                                        function getData(selector) {
                                            var element = document.querySelector(selector);
                                            return element ? element.innerText : 'Not Available';
                                        }

                                        // Open a new window for printing
                                        var printWindow = window.open('', '', 'height=600,width=800');

                                        // Write the content to the print window with styles
                                        printWindow.document.write('<html><head><title>Resident Details</title>');
                                        printWindow.document.write('<style>');
                                        printWindow.document.write('body { margin: 0; padding: 0; font-size: 0.9em; line-height: 1.6; width: 100%; }');
                                        printWindow.document.write('h1, h2, h3 { margin: 0 0 10px; }');
                                        printWindow.document.write('section { page-break-inside: avoid; margin-bottom: 20px; }'); // Avoid breaks within sections
                                        printWindow.document.write('@media print {');
                                        printWindow.document.write('body { width: 100%; height: auto; overflow: visible; }');
                                        printWindow.document.write('}');
                                        printWindow.document.write('</style>');
                                        printWindow.document.write('</head><body>');
                                        printWindow.document.write(content);
                                        printWindow.document.write('</body></html>');

                                        // Close the document to finish writing
                                        printWindow.document.close();

                                        // Wait for the new window to fully load before triggering print
                                        printWindow.onload = function() {
                                            printWindow.focus(); // Ensure the print window has focus
                                            printWindow.print(); // Trigger the print

                                            // Close the print window after a delay
                                            setTimeout(function() {
                                                printWindow.close(); // Close the print window
                                            }, 500);
                                        };
                                    }

                                    function deleteResident() {
                                        if (confirm("Are you sure you want to delete this resident?")) {
                                            $.ajax({
                                                url: '/system/src/components/delete_resident.php',
                                                type: 'POST',
                                                data: {
                                                    residentId: selectedResidentId
                                                },
                                                success: function(response) {
                                                    alert(response.trim()); // Show success or error message

                                                    console.log("Page will reload now");
                                                    $('#residentDetailsModal').modal('hide'); // Close the modal

                                                    // Refresh the page to reflect the changes
                                                    window.location.reload();
                                                },
                                                error: function() {
                                                    alert("An error occurred while deleting the resident.");
                                                }
                                            });
                                        }
                                    }

                                    $('input[name="search"]').on('keyup', function() {
                                        const urlParams = new URLSearchParams(window.location.search);
                                        let searchValue = $(this).val();
                                        urlParams.set('search', searchValue); // Update the search parameter

                                        $.ajax({
                                            url: 'dashboard.php?' + urlParams.toString(), // Include existing filters
                                            method: 'GET',
                                            success: function(response) {
                                                $('tbody').html($(response).find('tbody').html());

                                                // Rebind click event to rows
                                                $('table tbody tr').on('click', function() {
                                                    openModal($(this));
                                                });
                                            }
                                        });
                                    });

                                    function cancelAction() {
                                        // Logic to cancel the action or clear the form
                                        document.getElementById('resident-details').innerHTML = '<span class="text-muted">Select a resident to view details</span>';
                                        // Optionally hide buttons if needed
                                        $("#action-buttons").addClass('d-none');
                                    }

                                    function clearFilters() {
                                        // Get the filter form by its ID
                                        const form = document.getElementById('filterForm');

                                        // Clear all input fields
                                        form.reset();

                                        // Reset subdivision input and hide selected subdivision
                                        const subdivisionInput = document.getElementById('subdivision');
                                        const subdivisionLabel = document.getElementById('subdivisionLabel');
                                        const selectedSubdivision = document.getElementById('selectedSubdivision');

                                        subdivisionInput.value = ''; // Clear the subdivision input field
                                        subdivisionLabel.textContent = ''; // Reset the label text
                                        selectedSubdivision.style.display = 'none'; // Hide the selected subdivision display

                                        // Remove subdivision from localStorage
                                        localStorage.removeItem('selectedSubdivision');

                                        // Remove query parameters from the URL (no page reload yet)
                                        window.history.pushState({}, document.title, window.location.pathname);

                                        // Reload the page (without query parameters)
                                        location.reload();
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>


                <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

                <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
                <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

                <script>
                    let subdivisionStats = {}; // Global variable to store fetched data

                    window.onload = function() {
                        fetch('../src/components/getSubdivisionStats.php')
                            .then(response => response.json())
                            .then(data => {
                                subdivisionStats = data; // Store the fetched data globally
                                console.log(subdivisionStats); // For debugging, remove this in production
                            })
                            .catch(error => {
                                console.error('Error fetching subdivision stats:', error);
                            });
                    };

                    function setSubdivision(subdivision) {
                        // Get the input and label elements
                        const subdivisionInput = document.getElementById('subdivision');
                        const subdivisionLabel = document.getElementById('subdivisionLabel');
                        const selectedSubdivision = document.getElementById('selectedSubdivision');

                        // Update the input field and label
                        subdivisionInput.value = subdivision;
                        subdivisionLabel.textContent = subdivision;
                        selectedSubdivision.style.display = 'block';

                        // Debugging output
                        console.log("Subdivision value set to:", subdivisionInput.value);
                        console.log("Subdivision label updated to:", subdivisionLabel.textContent);
                    }


                    var map = L.map('map').setView([14.162525303855341, 121.11590938129102], 15);

                    L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Tiles &copy; Esri',
                        maxZoom: 18
                    }).addTo(map);

                    L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Labels &copy; Esri',
                        maxZoom: 18
                    }).addTo(map);

                    // Add polygons with hover tooltips
                    function addHoverTooltip(polygon, subdivisionName) {
                        polygon.on('mouseover', function() {
                            this.setStyle({
                                weight: 4,
                                fillOpacity: 0.6
                            });
                            // Use stats data if available, otherwise display 'N/A'
                            const stats = subdivisionStats[subdivisionName] || {
                                totalResidents: 'N/A',
                                avgAge: 'N/A',
                                genderDistribution: {
                                    male: 'N/A',
                                    female: 'N/A'
                                },
                                philhealthPercentage: 'N/A',
                                totalVoters: 'N/A',
                                disabilityCount: 'N/A',
                                osyCount: 'N/A',
                                pwdCount: 'N/A',
                                alsParticipationPercentage: 'N/A',
                                teenPregnancyCount: 'N/A',
                                employmentPercentage: 'N/A',
                                ofwCount: 'N/A',
                                avgYearsOfStay: 'N/A'
                            };

                            // Format and display the tooltip with updated stats
                            polygon.bindTooltip(
                                `<b>${subdivisionName}</b><br>
    Total Residents: ${stats.totalResidents}<br>
    Average Age: ${stats.avgAge}<br>
    Gender Distribution: Male: ${stats.genderDistribution.male}, Female: ${stats.genderDistribution.female}<br>
    Philhealth Coverage: ${stats.philhealthPercentage}<br>
    Registered Voters: ${stats.totalVoters}<br>
    Disabilities: ${stats.disabilityCount}<br>
    PWD Count: ${stats.pwdCount}<br>
    OSY Count: ${stats.osyCount}<br>
    ALS Participation: ${stats.alsParticipationPercentage}<br>
    Teen Pregnancies: ${stats.teenPregnancyCount}<br>
    Employment Rate: ${stats.employmentPercentage}<br>
    OFW Count: ${stats.ofwCount}<br>
    Average Years of Stay: ${stats.avgYearsOfStay}`, {
                                    direction: 'top',
                                    permanent: false
                                }
                            ).openTooltip();

                        });

                        polygon.on('mouseout', function() {
                            this.setStyle({
                                weight: 3,
                                fillOpacity: 0.3
                            });

                            polygon.bindTooltip(`${subdivisionName}`, {
                                permanent: true,
                                direction: "center",
                                className: "polygon-label"
                            }).openTooltip();
                        });
                    }


                    // Example polygons with hover effects
                    var sv1 = L.polygon([
                        [14.159358621907295, 121.10358862738792],
                        [14.161355966901166, 121.10615281896287],
                        [14.161813689321288, 121.10590605575693],
                        [14.162489868480904, 121.10706476994142],
                        [14.16091904918489, 121.10826639946609],
                        [14.160086822637803, 121.10711841411663],
                        [14.159348219022792, 121.1065712435295],
                        [14.158318331099064, 121.10450057836644]
                    ], {
                        color: '#0d47a1', // Dark Blue
                        fillColor: '#0d47a1',
                        fillOpacity: 0.3
                    }).addTo(map).bindTooltip("South Ville Phase-1", {
                        permanent: true,
                        direction: "center",
                        className: "polygon-label"
                    }).openTooltip();

                    sv1.on('click', function() {
                        localStorage.setItem('selectedSubdivision', 'Southville 6 Phase-1');
                        window.location.href = window.location.pathname + "?subdivision=Southville 6 Phase-1";
                    });

                    addHoverTooltip(sv1, "Southville 6 Phase-1");

                    var sv2 = L.polygon([
                        [14.158032250283204, 121.10899596029827],
                        [14.16018044826834, 121.10772995776337],
                        [14.160128434029327, 121.10693602397028],
                        [14.159291003141288, 121.10659806566647],
                        [14.157933422284106, 121.10683946445491],
                        [14.158422360386048, 121.10796062771675],
                        [14.157990638499344, 121.10818593325263],
                        [14.157652542472952, 121.10882966335514]
                    ], {
                        color: '#0d47a2', // Dark Blue
                        fillColor: '#0d47a2',
                        fillOpacity: 0.3
                    }).addTo(map).bindTooltip("South Ville Phase-2", {
                        permanent: true,
                        direction: "center",
                        className: "polygon-label"
                    }).openTooltip();

                    sv2.on('click', function() {
                        localStorage.setItem('selectedSubdivision', 'Southville 6 Phase-2');
                        window.location.href = window.location.pathname + "?subdivision=Southville 6 Phase-2";
                    });

                    addHoverTooltip(sv2, "Southville 6 Phase-2");

                    var sv3 = L.polygon([
                        [14.157938623711631, 121.10681264242476],
                        [14.157460087539823, 121.10510675765315],
                        [14.15602447297544, 121.10330431336614],
                        [14.155140213112574, 121.10346524589177],
                        [14.155951651705022, 121.10543935153943],
                        [14.155244243863327, 121.10586850494109],
                        [14.155764396902478, 121.10705940563071],
                        [14.156596639287823, 121.10654442154872]
                    ], {
                        color: '#42a5f5', // Light Blue
                        fillColor: '#42a5f5',
                        fillOpacity: 0.3
                    }).addTo(map).bindTooltip("South Ville Phase-3", {
                        permanent: true,
                        direction: "center",
                        className: "polygon-label"
                    }).openTooltip();

                    sv3.on('click', function() {
                        localStorage.setItem('selectedSubdivision', 'Southville 6 Phase-3');
                        window.location.href = window.location.pathname + "?subdivision=Southville 6 Phase-3";
                    });

                    addHoverTooltip(sv3, "Southville 6 Phase-3");

                    var sv4 = L.polygon([
                        [14.156008868413231, 121.105460809152],
                        [14.155093399252976, 121.10594360672889],
                        [14.153293659353544, 121.10577194536823],
                        [14.154344376286971, 121.10262839670101],
                        [14.15512460848963, 121.10352961884449]
                    ], {
                        color: '#90caf9', // Pale Blue
                        fillColor: '#90caf9',
                        fillOpacity: 0.4
                    }).addTo(map).bindTooltip("South Ville Phase-4", {
                        permanent: true,
                        direction: "center",
                        className: "polygon-label"
                    }).openTooltip();

                    sv4.on('click', function() {
                        localStorage.setItem('selectedSubdivision', 'Southville 6 Phase-4');
                        window.location.href = window.location.pathname + "?subdivision=Southville 6 Phase-4";
                    });

                    addHoverTooltip(sv4, "Southville 6 Phase-4");


                    var p1 = L.polygon([
                        [14.163232083344555, 121.1151981878794],
                        [14.157590125374346, 121.1089005000371],
                        [14.154970054428317, 121.10965537649142],
                        [14.153833822703815, 121.11239785663794],
                        [14.159241960976798, 121.12009221686279]
                    ], {
                        color: '#77DD77',
                        fillColor: '#B2E2D4',
                        fillOpacity: 0.3
                    }).addTo(map).bindTooltip("Purok-1", {
                        permanent: true,
                        direction: "center",
                        className: "polygon-label"
                    }).openTooltip();

                    p1.on('click', function() {
                        // Set the selected subdivision in localStorage
                        localStorage.setItem('selectedSubdivision', 'Purok-1');
                        // Redirect to the URL with the selected subdivision as a query parameter
                        window.location.href = window.location.pathname + "?subdivision=Purok-1";
                    });

                    addHoverTooltip(p1, "Purok-1");

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
                        fillColor: '#fd7e14',
                        fillOpacity: 0.3
                    }).addTo(map).bindTooltip("Purok-4", {
                        permanent: true,
                        direction: "center",
                        className: "polygon-label"
                    }).openTooltip();

                    p4.on('click', function() {
                        // Set the selected subdivision in localStorage
                        localStorage.setItem('selectedSubdivision', 'Purok-4');
                        // Redirect to the URL with the selected subdivision as a query parameter
                        window.location.href = window.location.pathname + "?subdivision=Purok-4";
                    });

                    addHoverTooltip(p4, "Purok-4");

                    // Polygon for Mother Ignacia, Villa Javier, Villa Andrea
                    var mvv = L.polygon([
                        [14.163272568088269, 121.12055037931074],
                        [14.163523080107609, 121.12120107848669],
                        [14.163383906797655, 121.12192195110315],
                        [14.162963293609048, 121.12189324378659],
                        [14.16222103314012, 121.1227002383528],
                        [14.161336502905604, 121.12122978579198]
                    ], {
                        color: 'red',
                        fillColor: '#dc3545',
                        fillOpacity: 0.3
                    }).addTo(map).bindTooltip("Mother Ignacia<br>Villa Javier<br>Villa Andrea", {
                        permanent: true,
                        direction: "center",
                        className: "polygon-label"
                    }).openTooltip();

                    mvv.on('click', function() {
                        // Set the selected subdivision in localStorage
                        localStorage.setItem('selectedSubdivision', 'Mother Ignacia,Villa Javier,Villa Andrea');
                        // Redirect to the URL with the selected subdivision as a query parameter
                        window.location.href = window.location.pathname + "?subdivision=Mother Ignacia,Villa Javier,Villa Andrea";
                    });

                    addHoverTooltip(mvv, "Mother Ignacia, Villa Javier, Villa Andrea");

                    var cv5 = L.polygon([
                        [14.164407737288329, 121.12010936398895],
                        [14.165275718428274, 121.12201355848433],
                        [14.164941031178447, 121.12232839542281],
                        [14.163502973754056, 121.12192631451343],
                        [14.163587565619409, 121.12120181023332],
                        [14.163377924851897, 121.1206062753015]
                    ], {
                        color: '#D6C9E5',
                        fillColor: '#6f42c1',


                        fillOpacity: 0.3
                    }).addTo(map).bindTooltip("Calamba Ville 5", {
                        permanent: true,
                        direction: "center",
                        className: "polygon-label"
                    }).openTooltip();

                    cv5.on('click', function() {
                        // Set the selected subdivision in localStorage
                        localStorage.setItem('selectedSubdivision', 'Calambeño Ville 5');
                        // Redirect to the URL with the selected subdivision as a query parameter
                        window.location.href = window.location.pathname + "?subdivision=Calambeño Ville 5";
                    });

                    addHoverTooltip(cv5, "Calambeño Ville 5");

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

                    p2.on('click', function() {
                        // Set the selected subdivision in localStorage
                        localStorage.setItem('selectedSubdivision', 'Purok-2');
                        // Redirect to the URL with the selected subdivision as a query parameter
                        window.location.href = window.location.pathname + "?subdivision=Purok-2";
                    });

                    addHoverTooltip(p2, "Purok-2");

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

                    vb.on('click', function() {
                        // Redirect to the URL with the selected subdivision as a query parameter
                        window.location.href = window.location.pathname + "?subdivision=Valley Breeze";
                    });

                    addHoverTooltip(vb, "Valley Breeze");

                    var p3 = L.polygon([
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
                    }).addTo(map).bindTooltip("Purok-3", {
                        permanent: true,
                        direction: "center",
                        className: "polygon-label"
                    }).openTooltip();

                    p3.on('click', function() {
                        // Set the selected subdivision in localStorage
                        localStorage.setItem('selectedSubdivision', 'Purok-3');
                        // Redirect to the URL with the selected subdivision as a query parameter
                        window.location.href = window.location.pathname + "?subdivision=Purok-3";
                    });

                    addHoverTooltip(p3, "Purok-3");



                    var initialCoordinates = [14.162525303855341, 121.11590938129102];
                    var initialZoom = 15;

                    // Function to reset the map view
                    function resetMapView() {
                        map.setView(initialCoordinates, initialZoom);
                    }

                    // Add event listener to the button
                    document.getElementById('autoFocusBtn').addEventListener('click', resetMapView);

                    // On page load, retrieve the subdivision from localStorage
                    document.addEventListener('DOMContentLoaded', function() {
                        const savedSubdivision = localStorage.getItem('selectedSubdivision');
                        if (savedSubdivision) {
                            // Set the subdivision in the UI or perform actions as needed
                            setSubdivision(savedSubdivision);

                            // Optional: Update the URL to reflect the stored subdivision
                            const url = new URL(window.location.href);
                            if (url.searchParams.get('subdivision') !== savedSubdivision) {
                                url.searchParams.set('subdivision', savedSubdivision);
                                window.history.replaceState(null, '', url.toString());
                            }
                        }
                    });
                </script>


            </div>
        </div>
    </div>
</body>

</html>