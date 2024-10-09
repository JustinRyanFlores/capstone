<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /capstone/website/login/login.php");
    exit();
}

include("../src/configs/connection.php");

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
    <link rel="stylesheet" href="/capstone/src/css/navbar.css" />
    <link rel="stylesheet" href="/capstone/src/css/header.css" />
    <link rel="stylesheet" href="/capstone/src/css/dashboard.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #autoFocusBtn {
            z-index: 1000;
            background-color: #1c2455;
            color: whitesmoke;
            border-color: whitesmoke;
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
            width: 70%;
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
                <a href="report.php" style="text-decoration: none;">
                    <div class="custom-container text-center">
                        <h5>Total Registered Voters</h5>
                        <h1><?php echo $total_voters; ?></h1>
                    </div>
                </a>
            </div>
            <div class="col-sm-4 mb-3 d-flex justify-content-center">
                <a href="report.php#characterSection" style="text-decoration: none;">
                    <div class="custom-container text-center">
                        <h5>Unsettled Cases</h5>
                        <h1><?php echo $row_blotter['total_unsettled_cases']; ?></h1>
                    </div>
                </a>
            </div>
        </div>
        <div class="container text-center mb-5">
            <div class="position-relative">
                <div id="map" class="w-100" style="height: 600px;"></div> <!-- Adjust height as necessary -->
                <button id="autoFocusBtn" class="btn btn-primary position-absolute" style="bottom: 20px; left: 50%; transform: translateX(-50%);">
                    <i class="fas fa-crosshairs"></i>
                </button>
            </div>
        </div>



        <div class="row m-3 bg-light text-white p-2 shadow rounded">
            <div class="col-4">
                <!-- Search form -->
                <form method="GET" action="dashboard.php">
                    <input type="text" name="search" class="form-control" placeholder="Type Here to Search..." style="max-width: 500px;" value="<?php echo htmlspecialchars($search_query); ?>" />
                </form>
            </div>

            <!-- Filter button -->
            <div class="col-auto">
                <button type="button" class="btn btn-outline-secondary me-2" onclick="toggleFilterPopup()">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>

            <!-- Clear Filters button (icon button) -->
            <div class="col-auto">
                <button type="button" class="btn btn-danger" onclick="clearFilters()" title="Clear Filters">
                    <i class="bi bi-x-circle"></i>
                </button>

            </div>
        </div>

        <!-- Filter Panel (Hidden by Default) -->
        <div id="filterPanel" class="filter-popup shadow rounded p-3 bg-white" style="display: none; position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); z-index: 1050; max-width: 300px;">
            <form id="filterForm" action="dashboard.php" method="GET">
                <!-- Demographic Filters -->
                <h6 class="mt-2">Demographic Details</h6>
                <div class="mb-2">
                    <label for="age_range">Age Range</label>
                    <input type="range" id="age_range" name="age_range" class="form-range" min="0" max="100" step="1" value="0" oninput="updateAgeRange(this.value)">
                    <span id="ageRangeLabel">0-0</span>
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
                    <label for="voter_status">Voter Status</label>
                    <select name="voter_status" class="form-select">
                        <option value="">Any</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <!-- Socio-Economic and Educational Filters -->
                <h6 class="mt-2">Socio-Economic & Educational</h6>
                <div class="row mb-2">
                    <div class="col-6">
                        <label for="osy_status">OSY Status</label>
                        <select name="osy_status" class="form-select">
                            <option value="">Any</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="als_status">ALS Status</label>
                        <select name="als_status" class="form-select">
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

                <!-- Button Group -->
                <div class="d-flex flex-column mt-3">
                    <button type="submit" class="btnfilter btn-primary w-100 mb-2">Apply Filters</button>
                    <button type="button" class="btnfilter2 btn-secondary w-100" onclick="toggleFilterPopup()">Cancel</button>
                </div>

            </form>
        </div>

        <script>
            function toggleFilterPopup() {
                const filterPanel = document.getElementById('filterPanel');
                if (filterPanel.style.display === 'none') {
                    filterPanel.style.display = 'block';
                } else {
                    filterPanel.style.display = 'none';
                }
            }

            function clearFilters() {
                document.getElementById('filterForm').reset();
                // Optionally, hide the filter panel after clearing
                document.getElementById('filterPanel').style.display = 'none';
            }
        </script>




        <!-- Resident Table -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="resident-table-container" tabindex="-1" id="residentTableContainer">
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
                            $limit = 10; // Number of records per page
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
                            $start_from = ($page - 1) * $limit;

                            // Check if the search query is numeric (for age search)
                            $is_numeric_search = is_numeric($search_query);

                            // Base query
                            $query = "
                            SELECT id, first_name, middle_name, last_name, dob, gender, contact_number, subdivision,
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

                            if (!empty($_GET['age_range'])) {
                                $age_max = (int)$_GET['age_range'];
                                $age_min = 0; // Since the range starts from 0
                                $query .= " AND age BETWEEN $age_min AND $age_max";
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

                            if (!empty($_GET['voter_status'])) {
                                $voter_status = $mysqlConn->real_escape_string($_GET['voter_status']);
                                $query .= " AND voter_status = '$voter_status'";
                            }

                            if (!empty($_GET['philhealth'])) {
                                $philhealth = $mysqlConn->real_escape_string($_GET['philhealth']);
                                $query .= " AND philhealth = '$philhealth'";
                            }

                            if (!empty($_GET['subdivision'])) {
                                $subdivision = $mysqlConn->real_escape_string($_GET['subdivision']);
                                $query .= " AND subdivision LIKE '%$subdivision%'";
                            }

                            if (!empty($_GET['osy_status'])) {
                                $osy_status = $mysqlConn->real_escape_string($_GET['osy_status']);
                                $query .= " AND osy_status = '$osy_status'";
                            }

                            if (!empty($_GET['als_status'])) {
                                $als_status = $mysqlConn->real_escape_string($_GET['als_status']);
                                $query .= " AND als_status = '$als_status'";
                            }

                            if (!empty($_GET['immunization_status'])) {
                                $immunization_status = $_GET['immunization_status'];

                                if ($immunization_status === 'Yes') {
                                    // Check if the immunization field is not empty
                                    $query .= " AND immunization != ''";
                                } elseif ($immunization_status === 'No') {
                                    // Check if the immunization field is empty
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
                                    // Filter for Vaginal Delivery (which corresponds to Normal)
                                    $query .= " AND typeOfDelivery = 'Vaginal Delivery'";
                                } elseif ($type_of_delivery === 'Cesarean') {
                                    // Filter for Cesarean Section
                                    $query .= " AND typeOfDelivery = 'Cesarean Section'";
                                }
                            }


                            if (!empty($_GET['assisted_by'])) {
                                $assisted_by = $_GET['assisted_by'];

                                // Add the condition to filter based on the selected value
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

                            if (!empty($_GET['subdivision'])) {
                                $subdivision = $_GET['subdivision'];
                                $query .= " AND subdivision = '$subdivision'";
                            }

                            if (isset($_GET['subdivision'])) {
                                $subdivisions = explode(',', $_GET['subdivision']);
                                $subdivisionList = "'" . implode("','", $subdivisions) . "'";

                                // Assuming you have a residents table with a 'subdivision' column
                                $query = "SELECT * FROM residents_records WHERE subdivision IN ($subdivisionList)";
                            }



                            // If a search query exists, include it in the filter
                            if (!empty($search_query)) {
                                $query .= " AND (first_name LIKE '%$search_query%' 
                OR middle_name LIKE '%$search_query%' 
                OR last_name LIKE '%$search_query%' 
                OR gender LIKE '%$search_query%' 
                OR dob LIKE '%$search_query%' 
                OR contact_number LIKE '%$search_query%' 
                OR subdivision LIKE '%$search_query%') ";
                            }

                            // If the search query is numeric, include it in the age filter
                            if ($is_numeric_search) {
                                $query .= " OR FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) = $search_query";
                            }
                            // Add the ORDER BY clause to sort by name
                            $query .= " ORDER BY last_name, first_name, middle_name"; // Alphabetical order

                            // Add the pagination limit
                            $query .= " LIMIT $start_from, $limit";

                            // Execute the query
                            $result = $mysqlConn->query($query);

                            // Loop through the records and display them
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr data-id='{$row['id']}' onclick='fetchResidentDetails({$row['id']})'>
                                <td>{$row['last_name']}, {$row['first_name']} {$row['middle_name']}</td>
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
                            OR dob LIKE '%$search_query%' 
                            OR contact_number LIKE '%$search_query%' 
                            OR subdivision LIKE '%$search_query%'";

                            // If the search query is numeric, include it in the total count query
                            if ($is_numeric_search) {
                                $query_total .= "OR FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) = $search_query";
                            }

                            $result_total = $mysqlConn->query($query_total);
                            $row_total = $result_total->fetch_row();
                            $total_records = $row_total[0];
                            $start_entry = ($page - 1) * $limit + 1;
                            $end_entry = min($start_entry + $limit - 1, $total_records);

                            echo "Showing $start_entry to $end_entry of $total_records entries";
                            ?>
                        </div>

                        <ul class="pagination">
                            <?php
                            $total_pages = ceil($total_records / $limit);

                            $url_filters = $_SERVER['QUERY_STRING'];
                            parse_str($url_filters, $query_params);
                            unset($query_params['page']); // Remove the current page from the query params

                            $base_url = '?' . http_build_query($query_params);

                            if ($page > 1) {
                                echo "<li class='page-item'><a class='page-link' href='$base_url&page=" . ($page - 1) . "'>Previous</a></li>";
                            }

                            for ($i = 1; $i <= $total_pages; $i++) {
                                if ($i == $page) {
                                    echo "<li class='page-item active'><a class='page-link' href='$base_url&page=$i'>$i</a></li>";
                                } else {
                                    echo "<li class='page-item'><a class='page-link' href='$base_url&page=$i'>$i</a></li>";
                                }
                            }

                            if ($page < $total_pages) {
                                echo "<li class='page-item'><a class='page-link' href='$base_url&page=" . ($page + 1) . "'>Next</a></li>";
                            }
                            ?>
                        </ul>

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
                            <button class="btn btn-primary" onclick="editResident(<?php echo $residentId; ?>)">Edit</button>
                            <button class="btn btn-danger" onclick="printResidentDetails()">Print</button>
                            <button class="btn btn-danger" onclick="deleteResident()">Delete</button>
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>

                        <script>
                            function editResident(residentId) {
                                // Redirect to the add_records.php page with the resident ID in the query string
                                window.location.href = "/capstone/pages/add_records.php?id=" + residentId;
                            }

                            var selectedResidentId; // Declare a global variable

                            function fetchResidentDetails(residentId) {
                                selectedResidentId = residentId; // Store the resident ID globally for other actions (e.g., edit/delete)

                                // Show a loading spinner while fetching data
                                $("#resident-details").html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

                                $.ajax({
                                    url: "/capstone/src/components/getResidentDetails.php", // Backend script to fetch details
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
                                // Get the content of the modal that we want to print
                                var printContent = document.getElementById('printable-details').innerHTML;

                                // Open a new window for printing
                                var printWindow = window.open('', '', 'height=600,width=800');

                                // Write the modal content to the new window with print-specific styles
                                printWindow.document.write('<html><head><title>Resident Details</title>');
                                printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />');
                                printWindow.document.write('<style>');
                                printWindow.document.write('body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }');
                                printWindow.document.write('.modal-body { padding: 10px; }');
                                printWindow.document.write('h5 { font-size: 1.5em; margin-bottom: 10px; }'); // Adjust heading size
                                printWindow.document.write('p { font-size: 1em; margin: 0; }'); // Adjust paragraph size
                                printWindow.document.write('.button-container { display: none; }'); // Hide buttons in print view
                                printWindow.document.write('@media print {');
                                printWindow.document.write('body { -webkit-print-color-adjust: exact; }'); // Print colors exactly
                                printWindow.document.write('}');
                                printWindow.document.write('</style>');
                                printWindow.document.write('</head><body>');
                                printWindow.document.write(printContent);
                                printWindow.document.write('</body></html>');

                                // Close the document to finish writing
                                printWindow.document.close();

                                // Wait for the new window to fully load before triggering print
                                printWindow.onload = function() {
                                    printWindow.focus(); // Ensure the print window has focus
                                    printWindow.print(); // Trigger the print

                                    // Use a delay before closing the window to ensure the print dialog fully processes
                                    setTimeout(function() {
                                        printWindow.close(); // Close the print window after a small delay
                                    }, 500); // 500ms delay
                                };
                            }

                            function deleteResident() {
                                if (confirm("Are you sure you want to delete this resident?")) {
                                    $.ajax({
                                        url: '/capstone/src/components/delete_resident.php',
                                        type: 'POST',
                                        data: {
                                            residentId: selectedResidentId
                                        },
                                        success: function(response) {
                                            alert(response); // Show success or error message

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
                                let searchValue = $(this).val();
                                $.ajax({
                                    url: 'dashboard.php',
                                    method: 'GET',
                                    data: {
                                        search: searchValue
                                    },
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

                                // Reload the page without query parameters to remove all filters
                                window.location.href = window.location.pathname;
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

            // Add click event to trigger the filter by redirecting
            sv.on('click', function() {
                // Redirect to the URL with the selected subdivision as a query parameter
                window.location.href = window.location.pathname + "?subdivision=Southville 6";
            });

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

            p1.on('click', function() {
                // Redirect to the URL with the selected subdivision as a query parameter
                window.location.href = window.location.pathname + "?subdivision=Purok-1";
            });

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

            p4.on('click', function() {
                // Redirect to the URL with the selected subdivision as a query parameter
                window.location.href = window.location.pathname + "?subdivision=Purok-4";
            });

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
                // Redirect with a filter for all three subdivisions
                window.location.href = window.location.pathname + "?subdivision=Mother Ignacia,Villa Javier,Villa Andrea";
            });


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

            cv5.on('click', function() {
                // Redirect to the URL with the selected subdivision as a query parameter
                window.location.href = window.location.pathname + "?subdivision=Calambeño Ville 5";
            });

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
                // Redirect to the URL with the selected subdivision as a query parameter
                window.location.href = window.location.pathname + "?subdivision=Purok-2";
            });

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
                // Redirect to the URL with the selected subdivision as a query parameter
                window.location.href = window.location.pathname + "?subdivision=Purok-3";
            });


            var initialCoordinates = [14.162525303855341, 121.11590938129102];
            var initialZoom = 15;

            // Function to reset the map view
            function resetMapView() {
                map.setView(initialCoordinates, initialZoom);
            }

            // Add event listener to the button
            document.getElementById('autoFocusBtn').addEventListener('click', resetMapView);
        </script>
    </div>

</body>

</html>