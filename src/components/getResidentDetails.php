<?php
if (!isset($_POST['id'])) {
    echo "No resident ID was provided.";
    exit();
}

// Include the MySQL connection file
include('../configs/connection.php');

// Check if the connection exists and is successful
if (!$mysqlConn) {
    die("Connection failed: " . mysqli_connect_error());
}

$residentId = $_POST['id'];
$query = "SELECT * FROM residents_records WHERE id = ?";
$stmt = $mysqlConn->prepare($query);
$stmt->bind_param("i", $residentId);
$stmt->execute();
$result = $stmt->get_result();

// Correct file paths to your JSON files
$barangayFilePath = __DIR__ . '/../../pages/data/barangay.json';
$cityFilePath = __DIR__ . '/../../pages/data/city.json';
$provinceFilePath = __DIR__ . '/../../pages/data/province.json';

// Read and decode JSON files
$barangayData = file_get_contents($barangayFilePath);
$cityData = file_get_contents($cityFilePath);
$provinceData = file_get_contents($provinceFilePath);

$barangayArray = json_decode($barangayData, true);
$cityArray = json_decode($cityData, true);
$provinceArray = json_decode($provinceData, true);

// Function to map barangay code to barangay name
function getBarangayName($brgy_code, $barangayArray)
{
    foreach ($barangayArray as $barangay) {
        if ($barangay['brgy_code'] == $brgy_code) {
            return $barangay['brgy_name'];
        }
    }
    return '';
}

// Function to map city code to city name
function getCityName($city_code, $cityArray)
{
    foreach ($cityArray as $city) {
        if ($city['city_code'] == $city_code) {
            return $city['city_name'];
        }
    }
    return '';
}

// Function to map province code to province name
function getProvinceName($province_code, $provinceArray)
{
    foreach ($provinceArray as $province) {
        if ($province['province_code'] == $province_code) {
            return $province['province_name'];
        }
    }
    return '';
}

// Function to calculate age from date of birth
function calculateAge($dob)
{
    $dobDate = new DateTime($dob);
    $currentDate = new DateTime(); // Current date
    $age = $dobDate->diff($currentDate)->y; // Difference in years
    return $age;
}

if ($row = $result->fetch_assoc()) {
    ob_start();
    $update_image = htmlspecialchars($row['residents_img']);
    $imagePath = $update_image ? "/system/src/assets/$update_image" : "/system/src/assets/kayanlog-logo.png";

    // Get the barangay, city, and province names using the codes from the database
    $barangayName = getBarangayName($row['barangay'], $barangayArray);
    $cityName = getCityName($row['city'], $cityArray);
    $provinceName = getProvinceName($row['province'], $provinceArray);

    // Calculate age using the date of birth
    $age = calculateAge($row['dob']);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Resident details page for viewing and managing resident information">
        <title>Resident Details</title>

        <style>
            /* Tab styling */
            .nav-tabs .nav-link {
                color: #1c2455;
                /* Default tab text color */
            }

            .nav-tabs .nav-link.active {
                color: white;
                /* Active tab text color */
                background-color: #1c2455;
                /* Active tab background color */
            }

            .nav-tabs .nav-link:hover {
                color: #ffffff;
                /* Hover text color */
                background-color: #3b4a8b;
                /* Hover background color */
            }
        </style>
    </head>

    <body class="bg-light">
        <div class="container py-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="<?php echo $imagePath; ?>" alt="Resident Image" class="img-fluid rounded-circle" style="width: 180px; height: 180px; object-fit: cover; border: 2px solid black;">
                        <h3 class="text-primary mt-3">
                            <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']); ?>
                        </h3>
                    </div>

                    <ul class="nav nav-tabs" id="residentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Profile</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab" aria-controls="address" aria-selected="false">Address</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="family-tab" data-bs-toggle="tab" data-bs-target="#family" type="button" role="tab" aria-controls="family" aria-selected="false">Family</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="education-tab" data-bs-toggle="tab" data-bs-target="#education" type="button" role="tab" aria-controls="education" aria-selected="false">Education</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="health-tab" data-bs-toggle="tab" data-bs-target="#health" type="button" role="tab" aria-controls="health" aria-selected="false">Health</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="additional-tab" data-bs-toggle="tab" data-bs-target="#additional" type="button" role="tab" aria-controls="additional" aria-selected="false">Additional</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="identification-tab" data-bs-toggle="tab" data-bs-target="#identification" type="button" role="tab" aria-controls="identification" aria-selected="false">Identification</button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="residentTabsContent">
                        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Date of Birth</th>
                                    <td><?php echo htmlspecialchars($row['dob']); ?></td>
                                </tr>
                                <tr>
                                    <th>Age</th>
                                    <td><?php echo $age; ?></td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                </tr>
                                <tr>
                                    <th>Contact Number</th>
                                    <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                                </tr>
                                <tr>
                                    <th>Religion</th>
                                    <td><?php echo htmlspecialchars($row['religion']); ?></td>
                                </tr>
                                <tr>
                                    <th>Civil Status</th>
                                    <td><?php echo htmlspecialchars($row['civil_status']); ?></td>
                                </tr>
                                <tr>
                                    <th>Voter Status</th>
                                    <td><?php echo $row['voterstatus'] ? 'Yes' : 'No'; ?></td>
                                </tr>
                            </table>
                        </div>

                        <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Street Address</th>
                                    <td><?php echo htmlspecialchars($row['street_address']); ?></td>
                                </tr>
                                <tr>
                                    <th>House Number</th>
                                    <td><?php echo htmlspecialchars($row['house_number']); ?></td>
                                </tr>
                                <tr>
                                    <th>Subdivision</th>
                                    <td><?php echo htmlspecialchars($row['subdivision']); ?></td>
                                </tr>
                                <tr>
                                    <th>Barangay</th>
                                    <td><?php echo htmlspecialchars($barangayName); ?></td>
                                </tr>
                                <tr>
                                    <th>City</th>
                                    <td><?php echo htmlspecialchars($cityName); ?></td>
                                </tr>
                                <tr>
                                    <th>Province</th>
                                    <td><?php echo htmlspecialchars($provinceName); ?></td>
                                </tr>
                                <tr>
                                    <th>Zip Code</th>
                                    <td><?php echo htmlspecialchars($row['zip_code']); ?></td>
                                </tr>
                            </table>
                        </div>

                        <div class="tab-pane fade" id="family" role="tabpanel" aria-labelledby="family-tab">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Mother's Name</th>
                                    <td><?php echo htmlspecialchars($row['mother_first_name'] . ' ' . $row['mother_middle_name'] . ' ' . $row['mother_last_name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Father's Name</th>
                                    <td><?php echo htmlspecialchars($row['father_first_name'] . ' ' . $row['father_middle_name'] . ' ' . $row['father_last_name']); ?></td>
                                </tr>
                            </table>
                        </div>

                        <div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="education-tab">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Out of School Youth</th>
                                    <td><?php echo $row['osy'] ? 'Yes' : 'No'; ?></td>
                                </tr>
                                <tr>
                                    <th>Educational Attainment</th>
                                    <td><?php echo htmlspecialchars($row['educational_attainment']); ?></td>
                                </tr>
                                <tr>
                                    <th>Enrolled in ALS</th>
                                    <td><?php echo $row['als'] ? 'Yes' : 'No'; ?></td>
                                </tr>
                                <tr>
                                    <th>Current School</th>
                                    <td><?php echo htmlspecialchars($row['current_school']); ?></td>
                                </tr>
                            </table>
                        </div>

                        <div class="tab-pane fade" id="health" role="tabpanel" aria-labelledby="health-tab">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Illness</th>
                                    <td><?php echo htmlspecialchars($row['illness']); ?></td>
                                </tr>
                                <tr>
                                    <th>Medication</th>
                                    <td><?php echo htmlspecialchars($row['medication']); ?></td>
                                </tr>
                                <tr>
                                    <th>Disability</th>
                                    <td><?php echo htmlspecialchars($row['disability']); ?></td>
                                </tr>
                                <tr>
                                    <th>Blood Type</th>
                                    <td><?php echo htmlspecialchars($row['bloodtype']); ?></td>
                                </tr>
                                <tr>
                                    <th>PWD</th>
                                    <td><?php echo $row['pwd'] ? 'Yes' : 'No'; ?></td>
                                </tr>
                                <tr>
                                    <th>Immunization Status</th>
                                    <td><?php echo htmlspecialchars($row['immunization']); ?></td>
                                </tr>
                                <tr>
                                    <th>Teen Pregnancy</th>
                                    <td><?php echo $row['teen_pregnancy'] ? 'Yes' : 'No'; ?></td>
                                </tr>
                                <tr>
                                    <th>Type of Delivery</th>
                                    <td><?php echo htmlspecialchars($row['type_of_delivery']); ?></td>
                                </tr>
                                <tr>
                                    <th>Assisted By</th>
                                    <td><?php echo htmlspecialchars($row['assisted_by']); ?></td>
                                </tr>
                            </table>
                        </div>

                        <div class="tab-pane fade" id="additional" role="tabpanel" aria-labelledby="additional-tab">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Organization</th>
                                    <td><?php echo htmlspecialchars($row['organization']); ?></td>
                                </tr>
                                <tr>
                                    <th>Cases Violated</th>
                                    <td><?php echo htmlspecialchars($row['cases_violated']); ?></td>
                                </tr>
                                <tr>
                                    <th>Years of Stay</th>
                                    <td><?php echo htmlspecialchars($row['years_of_stay']); ?></td>
                                </tr>
                                <tr>
                                    <th>Business Owner</th>
                                    <td><?php echo $row['business_owner'] ? 'Yes' : 'No'; ?></td>
                                </tr>
                                <tr>
                                    <th>OFW</th>
                                    <td><?php echo $row['ofw'] ? 'Yes' : 'No'; ?></td>
                                </tr>
                                <tr>
                                    <th>Employment Status</th>
                                    <td><?php echo htmlspecialchars($row['employment']); ?></td>
                                </tr>
                                <tr>
                                    <th>Occupation</th>
                                    <td><?php echo htmlspecialchars($row['occupation']); ?></td>
                                </tr>

                            </table>
                        </div>
                        <div class="tab-pane fade" id="identification" role="tabpanel" aria-labelledby="identification-tab">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Solo Parent ID</th>
                                    <td><?php echo htmlspecialchars($row['soloparent_id']); ?></td>
                                </tr>
                                <tr>
                                    <th>Senior ID</th>
                                    <td><?php echo htmlspecialchars($row['senior_id']); ?></td>
                                </tr>
                                <tr>
                                    <th>PWD ID</th>
                                    <td><?php echo htmlspecialchars($row['pwd_id']); ?></td>
                                </tr>
                                <tr>
                                    <th>Philhealth Number</th>
                                    <td><?php echo htmlspecialchars($row['philhealth']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
<?php
    echo ob_get_clean();
} else {
    echo "<div class='alert alert-warning'>No details found for this resident.</div>";
}

$stmt->close();
$mysqlConn->close();
?>