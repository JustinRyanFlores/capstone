<?php
if (!isset($_POST['id'])) {
    echo "No resident ID was provided.";
    exit();
}

$conn = new mysqli("localhost", "root", "", "residents_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$residentId = $_POST['id'];
$query = "SELECT * FROM residents_records WHERE id = ?";
$stmt = $conn->prepare($query);
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
    return 'Unknown Barangay';
}

// Function to map city code to city name
function getCityName($city_code, $cityArray)
{
    foreach ($cityArray as $city) {
        if ($city['city_code'] == $city_code) {
            return $city['city_name'];
        }
    }
    return 'Unknown City';
}

// Function to map province code to province name
function getProvinceName($province_code, $provinceArray)
{
    foreach ($provinceArray as $province) {
        if ($province['province_code'] == $province_code) {
            return $province['province_name'];
        }
    }
    return 'Unknown Province';
}

// Function to calculate age from date of birth
function calculateAge($dob) {
    $dobDate = new DateTime($dob);
    $currentDate = new DateTime(); // Current date
    $age = $dobDate->diff($currentDate)->y; // Difference in years
    return $age;
}

if ($row = $result->fetch_assoc()) {
    ob_start();
    $update_image = htmlspecialchars($row['residents_img']);
    $imagePath = $update_image ? "/capstone/src/assets/$update_image" : "/path/to/default-image.jpg";

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
    </head>

    <body class="bg-light">
        <div class="container py-1">
            <div class="card shadow-sm">
                <div class="card-body shadow">
                    <!-- Profile Section -->
                    <div class="row align-items-center mb-4">
                        <div class="col-md-3 text-center">
                            <img src="<?php echo $imagePath; ?>" alt="Resident Image" class="img-fluid border" style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                        <div class="col-md-9">
                            <h3 class="text-primary"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']); ?></h3>
                            <p>Date of Birth: <?php echo htmlspecialchars($row['dob']); ?></p>
                            <p>Age: <?php echo $age; ?></p> <!-- Dynamically calculated age -->
                            <p>Gender: <?php echo htmlspecialchars($row['gender']); ?></p>
                            <p>Contact Number: <?php echo htmlspecialchars($row['contact_number']); ?></p>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <h4 class="text-primary">Address Information</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <?php
                                $address_fields = [
                                    'Street Address' => 'street_address',
                                    'House Number' => 'house_number',
                                    '   division' => 'subdivision',
                                    'Barangay' => $barangayName, // Barangay name from JSON
                                    'City' => $cityName,         // City name from JSON
                                    'Province' => $provinceName, // Province name from JSON
                                    'Zip Code' => 'zip_code'
                                ];
                                foreach ($address_fields as $label => $field) {
                                    if (in_array($label, ['Barangay', 'City', 'Province'])) {
                                        echo "<tr><th>$label</th><td>" . htmlspecialchars($field) . "</td></tr>";
                                    } else {
                                        echo "<tr><th>$label</th><td>" . htmlspecialchars($row[$field]) . "</td></tr>";
                                    }
                                }
                                ?>
                            </table>
                        </div>

                        <!-- Family Information -->
                        <div class="col-md-6">
                            <h4 class="text-primary">Family Information</h4>
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
                    </div>

                    <!-- Educational and Health Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="text-primary">Educational Information</h4>
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

                        <!-- Health Information -->
                        <div class="col-md-6">
                            <h4 class="text-primary">Health Information</h4>
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
                                    <th>Teen Pregnancy</th>
                                    <td><?php echo $row['teen_pregnancy'] ? 'Yes' : 'No'; ?></td>
                                </tr>
                                <tr>
                                    <th>Type of Delivery</th>
                                    <td><?php echo htmlspecialchars($row['type_of_delivery']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="text-primary">Additional Information</h4>
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
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>

    </html>
<?php
    echo ob_get_clean();
} else {
    echo "<div class='alert alert-warning'>No details found for this resident.</div>";
}

$stmt->close();
$conn->close();
?>
