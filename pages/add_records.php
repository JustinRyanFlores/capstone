<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /capstone/website/login/login.php");
    exit();
}

if (isset($_GET['success']) && $_GET['success'] == 'true') {
    echo "<script>alert('Record added successfully');</script>";
}

if (isset($_GET['error']) && $_GET['error'] == 'true') {
    echo "<script>alert('An error occurred while adding the record');</script>";
}
?>

<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "residents_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables to hold form values
$residentImage = $firstName = $middleName = $lastName = $dob = $year = $month = $day = $age = $gender = $contactNumber =
    $streetAddress = $houseNumber = $subdivision = $barangay = $city = $province = $region = $zipCode =
    $motherFirstName = $motherMiddleName =  $motherLastName = $fatherFirstName = $fatherMiddleName = $fatherLastName =
    $outOfSchoolYouth = $alternativeLearningSystem = $educationalAttainment = $currentSchool = $illness =
    $illness = $medication = $disability = $teenAgePregnancy = $typeOfDelivery =
    $organization = $casesViolated = $yearsOfStay = $businessOwner = "";

// Check if an ID is passed in the URL (Edit mode)
if (isset($_GET['id'])) {
    $residentId = $_GET['id'];
    // Fetch resident data from the database
    $query = "SELECT * FROM residents_records WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $residentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $residentImage = htmlspecialchars('/capstone/src/assets/' . $row['residents_img']);
        $firstName = htmlspecialchars($row['first_name']);
        $middleName = htmlspecialchars($row['middle_name']);
        $lastName = htmlspecialchars($row['last_name']);
        $dob = htmlspecialchars($row['dob']);
        list($year, $month, $day) = explode('-', $dob);
        $age = htmlspecialchars($row['age']);
        $gender = htmlspecialchars($row['gender']);
        $contactNumber = htmlspecialchars($row['contact_number']);
        $streetAddress = htmlspecialchars($row['street_address']);
        $houseNumber = htmlspecialchars($row['house_number']);
        $subdivision = htmlspecialchars($row['subdivision']);
        $barangay = htmlspecialchars($row['barangay']);
        $city = htmlspecialchars($row['city']);
        $province = htmlspecialchars($row['province']);
        $region = htmlspecialchars($row['region']);
        $zipCode = htmlspecialchars($row['zip_code']);
        $motherFirstName = htmlspecialchars($row['mother_first_name']);
        $motherMiddleName = htmlspecialchars($row['mother_middle_name']);
        $motherLastName = htmlspecialchars($row['mother_last_name']);
        $fatherFirstName = htmlspecialchars($row['father_first_name']);
        $fatherMiddleName = htmlspecialchars($row['father_middle_name']);
        $fatherLastName = htmlspecialchars($row['father_last_name']);
        $outOfSchoolYouth = htmlspecialchars($row['osy']);
        $alternativeLearningSystem = htmlspecialchars($row['als']);
        $educationalAttainment = htmlspecialchars($row['educational_attainment']);
        $currentSchool = htmlspecialchars($row['current_school']);
        $illness = htmlspecialchars($row['illness']);
        $medication = htmlspecialchars($row['medication']);
        $disability = htmlspecialchars($row['disability']);
        $teenAgePregnancy = htmlspecialchars($row['teen_pregnancy']);
        $typeOfDelivery = htmlspecialchars($row['type_of_delivery']);
        $organization = htmlspecialchars($row['organization']);
        $casesViolated = htmlspecialchars($row['cases_violated']);
        $yearsOfStay = htmlspecialchars($row['years_of_stay']);
        $businessOwner = htmlspecialchars($row['business_owner']);
    }

    $stmt->close();
}

$conn->close();
?>

<?php
$isEdit = isset($_GET['id']) ? true : false;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Residents Records</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/capstone/src/css/navbar.css" />
    <link rel="stylesheet" href="/capstone/src/css/header.css" />
    <link rel="stylesheet" href="/capstone/src/css/add_records.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <?php include '/xampp/htdocs/capstone/src/components/header.php'; ?>
</head>

<body>
    <?php include '/xampp/htdocs/capstone/src/components/moderator_navbar.php'; ?>
    <div class="container-fluid main-content mt-3">
        <div class="row mb-4">
            <div class="col-sm-6 col-md-6 text-start">
                <h3>Residents Records</h3>
                <div class="h6 text-muted" style="font-style: italic;">
                    Add Records
                </div>
            </div>
            <div class="col-sm-6 col-md-6 d-flex justify-content-sm-between justify-content-md-end">
                <div>
                    <?php displayDateTime(); ?>
                </div>
            </div>
        </div>

        <!-- Scrollable container -->
        <div class="container-fluid bg-light p-4 rounded scrollable-container">
            <form action="<?php echo $isEdit ? '/capstone/src/components/update.php?id=' . $_GET['id'] : '/capstone/src/components/submit.php'; ?>"
                method="POST"
                enctype="multipart/form-data"
                id="residentForm">
                <div class="row mb-4">
                    <div class="col-md-12 text-center">
                        <div class="profile-picture-container">
                            <img id="profile-picture" src="<?php echo !empty($residentImage) ? $residentImage : '/capstone/src/assets/kayanlog-logo.png'; ?>" class="img-fluid border" alt="Profile Picture">
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12 text-center">
                        <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" onchange="previewImage(event)" value="<?php echo $firstName; ?>">
                    </div>
                </div>
                <script>
                    function previewImage(event) {
                        var reader = new FileReader();
                        reader.onload = function() {
                            var output = document.getElementById('profile-picture');
                            output.src = reader.result;
                        };
                        reader.readAsDataURL(event.target.files[0]);
                    }
                </script>

                <!-- Personal Information -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5>Personal Information</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="firstName">First Name:</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $firstName; ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="middleName">Middle Name:</label>
                                <input type="text" class="form-control" id="middleName" name="middleName" value="<?php echo $middleName; ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="lastName">Last Name:</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $lastName; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="dob">Date of Birth:</label>
                                <div class="input-group">
                                    <select class="form-control" id="dobDay" name="dobDay">
                                        <option value="">Day</option>
                                        <?php for ($d = 1; $d <= 31; $d++): ?>
                                            <option value="<?php echo str_pad($d, 2, '0', STR_PAD_LEFT); ?>" <?php echo ($d == intval($day)) ? 'selected' : ''; ?>>
                                                <?php echo $d; ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <select class="form-control" id="dobMonth" name="dobMonth">
                                        <option value="">Month</option>
                                        <?php
                                        $months = [
                                            '01' => 'January',
                                            '02' => 'February',
                                            '03' => 'March',
                                            '04' => 'April',
                                            '05' => 'May',
                                            '06' => 'June',
                                            '07' => 'July',
                                            '08' => 'August',
                                            '09' => 'September',
                                            '10' => 'October',
                                            '11' => 'November',
                                            '12' => 'December'
                                        ];
                                        foreach ($months as $m => $monthName): ?>
                                            <option value="<?php echo $m; ?>" <?php echo ($m == str_pad($month, 2, '0', STR_PAD_LEFT)) ? 'selected' : ''; ?>>
                                                <?php echo $monthName; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select class="form-control" id="dobYear" name="dobYear">
                                        <option value="">Year</option>
                                        <?php
                                        $currentYear = date('Y');
                                        for ($y = 1900; $y <= $currentYear; $y++): ?>
                                            <option value="<?php echo $y; ?>" <?php echo ($y == intval($year)) ? 'selected' : ''; ?>>
                                                <?php echo $y; ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="age">Age:</label>
                                <input type="text" class="form-control" id="age" name="age" value="<?php echo $age; ?>">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="gender">Gender:</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="Male" <?php echo ($gender === 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($gender === 'Female') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contactNumber">Contact Number:</label>
                                <input type="text" class="form-control" id="contactNumber" name="contactNumber"
                                    pattern="\d{10,11}" title="Please enter a valid contact number." value="<?php echo $contactNumber; ?>" required>
                                <small class="form-text text-muted">Please enter a valid contact number with 10 or 11 digits.</small>
                            </div>


                        </div>
                    </div>
                </div>
                <script>
                    document.getElementById('contactNumber').addEventListener('input', function() {
                        const value = this.value;
                        const pattern = /^\d{10,11}$/;
                        if (!pattern.test(value)) {
                            this.setCustomValidity('Please enter a valid contact number');
                        } else {
                            this.setCustomValidity('');
                        }
                    });
                </script>
                <!-- Address Information -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5>Address Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="streetAddress">Street Address:</label>
                                <input type="text" class="form-control" id="streetAddress" name="streetAddress" value="<?php echo $streetAddress; ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="houseNumber">House Number:</label>
                                <input type="text" class="form-control" id="houseNumber" name="houseNumber" value="<?php echo $houseNumber; ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="subdivision">Subdivision:</label>
                                <input type="text" class="form-control" id="subdivision" name="subdivision" value="<?php echo $subdivision; ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="barangay">Barangay:</label>
                                <select class="form-control" id="barangay" name="barangay">
                                    <option value="" <?php echo ($barangay === '') ? 'selected' : ''; ?>>Select Barangay</option>
                                    <!-- Barangay options will be loaded here -->
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="city">City:</label>
                                <select class="form-control" id="city" name="city">
                                    <option value="" <?php echo ($city === '') ? 'selected' : ''; ?>>Select City/Municipality</option>
                                    <!-- City options will be loaded here -->
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="province">Province:</label>
                                <select class="form-control" id="province" name="province">
                                    <option value="" <?php echo ($province === '') ? 'selected' : ''; ?>>Select Province</option>
                                    <!-- Province options will be loaded here -->
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="region">Region:</label>
                                <select class="form-control" id="region" name="region">
                                    <option value="" <?php echo ($region === '') ? 'selected' : ''; ?>>Select Region</option>
                                    <!-- Region options will be loaded here -->
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="zipCode">Zip Code:</label>
                                <input type="text" class="form-control" id="zipCode" name="zipCode" value="<?php echo $zipCode; ?>">
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const regionsSelect = document.getElementById('region');
                                const provincesSelect = document.getElementById('province');
                                const citiesSelect = document.getElementById('city');
                                const barangaysSelect = document.getElementById('barangay');

                                function populateDropdown(selectElement, data, valueKey, textKey, selectedValue) {
                                    selectElement.innerHTML = '<option value="">Select</option>';
                                    data.forEach(item => {
                                        const option = document.createElement('option');
                                        option.value = item[valueKey];
                                        option.textContent = item[textKey];
                                        if (item[valueKey] === selectedValue) {
                                            option.selected = true;
                                        }
                                        selectElement.appendChild(option);
                                    });
                                }

                                function fetchData(url) {
                                    return fetch(url)
                                        .then(response => response.json())
                                        .then(data => {
                                            console.log('Data fetched from ' + url, data);
                                            return data;
                                        })
                                        .catch(error => {
                                            console.error('Error fetching data:', error);
                                            return [];
                                        });
                                }

                                // Fetch regions and populate the dropdown
                                fetchData('data/region.json').then(data => {
                                    populateDropdown(regionsSelect, data, 'region_code', 'region_name', '<?php echo $region; ?>');

                                    // Check if a region is selected and fetch provinces
                                    if ('<?php echo $region; ?>') {
                                        const selectedRegionCode = '<?php echo $region; ?>';
                                        fetchData('data/province.json').then(data => {
                                            const provinces = data.filter(province => province.region_code === selectedRegionCode);
                                            populateDropdown(provincesSelect, provinces, 'province_code', 'province_name', '<?php echo $province; ?>');

                                            // If a province is selected, fetch cities
                                            if ('<?php echo $province; ?>') {
                                                const selectedProvinceCode = '<?php echo $province; ?>';
                                                fetchData('data/city.json').then(data => {
                                                    const cities = data.filter(city => city.province_code === selectedProvinceCode);
                                                    populateDropdown(citiesSelect, cities, 'city_code', 'city_name', '<?php echo $city; ?>');

                                                    // If a city is selected, fetch barangays
                                                    if ('<?php echo $city; ?>') {
                                                        const selectedCityCode = '<?php echo $city; ?>';
                                                        fetchData('data/barangay.json').then(data => {
                                                            const barangays = data.filter(barangay => barangay.city_code === selectedCityCode);
                                                            populateDropdown(barangaysSelect, barangays, 'brgy_code', 'brgy_name', '<?php echo $barangay; ?>');
                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });

                                // Event listeners for dropdowns (as before)
                                regionsSelect.addEventListener('change', function() {
                                    const selectedRegionCode = this.value;
                                    if (selectedRegionCode) {
                                        fetchData('data/province.json').then(data => {
                                            const provinces = data.filter(province => province.region_code === selectedRegionCode);
                                            populateDropdown(provincesSelect, provinces, 'province_code', 'province_name', '<?php echo $province; ?>');
                                            citiesSelect.innerHTML = '<option value="">Select City/Municipality</option>';
                                            barangaysSelect.innerHTML = '<option value="">Select Barangay</option>';
                                        });
                                    } else {
                                        provincesSelect.innerHTML = '<option value="">Select Province</option>';
                                        citiesSelect.innerHTML = '<option value="">Select City/Municipality</option>';
                                        barangaysSelect.innerHTML = '<option value="">Select Barangay</option>';
                                    }
                                });

                                provincesSelect.addEventListener('change', function() {
                                    const selectedProvinceCode = this.value;
                                    if (selectedProvinceCode) {
                                        fetchData('data/city.json').then(data => {
                                            const cities = data.filter(city => city.province_code === selectedProvinceCode);
                                            populateDropdown(citiesSelect, cities, 'city_code', 'city_name', '<?php echo $city; ?>');
                                            barangaysSelect.innerHTML = '<option value="">Select Barangay</option>';
                                        });
                                    } else {
                                        citiesSelect.innerHTML = '<option value="">Select City/Municipality</option>';
                                        barangaysSelect.innerHTML = '<option value="">Select Barangay</option>';
                                    }
                                });

                                citiesSelect.addEventListener('change', function() {
                                    const selectedCityCode = this.value;
                                    if (selectedCityCode) {
                                        fetchData('data/barangay.json').then(data => {
                                            const barangays = data.filter(barangay => barangay.city_code === selectedCityCode);
                                            populateDropdown(barangaysSelect, barangays, 'brgy_code', 'brgy_name', '<?php echo $barangay; ?>');
                                        });
                                    } else {
                                        barangaysSelect.innerHTML = '<option value="">Select Barangay</option>';
                                    }
                                });
                            });
                        </script>


                        <!-- Family Information -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Family Information</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="motherFirstName">Mother First Name:</label>
                                        <input type="text" class="form-control" id="motherFirstName" name="motherFirstName" value="<?php echo $motherFirstName; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="motherMiddleName">Mother Middle Name:</label>
                                        <input type="text" class="form-control" id="motherMiddleName" name="motherMiddleName" value="<?php echo $motherMiddleName; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="motherLastName">Mother Last Name:</label>
                                        <input type="text" class="form-control" id="motherLastName" name="motherLastName" value="<?php echo $motherLastName; ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="fatherFirstName">Father First Name:</label>
                                        <input type="text" class="form-control" id="fatherFirstName" name="fatherFirstName" value="<?php echo $fatherFirstName; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="fatherMiddleName">Father Middle Name:</label>
                                        <input type="text" class="form-control" id="fatherMiddleName" name="fatherMiddleName" value="<?php echo $fatherMiddleName; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="fatherLastName">Father Last Name:</label>
                                        <input type="text" class="form-control" id="fatherLastName" name="fatherLastName" value="<?php echo $fatherLastName; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Educational Information -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Educational Information</h5>
                                <div class="row">
                                    <!-- Out of School Youth and Enrolled in Alternative Learning System -->
                                    <div class="col-md-6 mb-3">
                                        <label for="outOfSchoolYouth">Out of School Youth:</label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="outOfSchoolYouth" name="outOfSchoolYouth" <?php echo ($outOfSchoolYouth) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="outOfSchoolYouth">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="alternativeLearningSystem">Enrolled in Alternative Learning System:</label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="alternativeLearningSystem" name="alternativeLearningSystem" <?php echo ($alternativeLearningSystem) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="alternativeLearningSystem">Yes</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- Educational Attainment and Name of School Currently Enrolled In -->
                                    <div class="col-md-6 mb-3">
                                        <label for="educationalAttainment">Educational Attainment:</label>
                                        <input type="text" class="form-control" id="educationalAttainment" name="educationalAttainment" value="<?php echo $educationalAttainment; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="currentSchool">Name of School Currently Enrolled In:</label>
                                        <input type="text" class="form-control" id="currentSchool" name="currentSchool" value="<?php echo $currentSchool; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Health Information -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Health Information</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="illness">Illness:</label>
                                        <input type="text" class="form-control" id="illness" name="illness" value="<?php echo $illness; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="medication">Medication:</label>
                                        <input type="text" class="form-control" id="medication" name="medication" value="<?php echo $medication; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="disability">Disability:</label>
                                        <input type="text" class="form-control" id="disability" name="disability" value="<?php echo $disability; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="teenAgePregnancy">Teen Age Pregnancy:</label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="teenAgePregnancy" name="teenAgePregnancy" <?php echo ($teenAgePregnancy) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="teenAgePregnancy">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" id="deliveryContainer" style="display: none;">
                                        <label for="typeOfDelivery">Type of Delivery:</label>
                                        <input type="text" class="form-control" id="typeOfDelivery" name="typeOfDelivery" value="<?php echo $typeOfDelivery; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Toggle script for the teenage pregnancy option -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const teenAgePregnancyCheckbox = document.getElementById('teenAgePregnancy');
                                const deliveryContainer = document.getElementById('deliveryContainer');

                                // Function to toggle delivery container based on checkbox state
                                function toggleDeliveryContainer() {
                                    if (teenAgePregnancyCheckbox.checked) {
                                        deliveryContainer.style.display = 'block';
                                    } else {
                                        deliveryContainer.style.display = 'none';
                                    }
                                }

                                // Initial check to set display based on autofill or default value
                                toggleDeliveryContainer();

                                // Add event listener for checkbox change
                                teenAgePregnancyCheckbox.addEventListener('change', toggleDeliveryContainer);
                            });
                        </script>


                        <!-- Additional Information -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Additional Information</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="organization">Organization:</label>
                                        <input type="text" class="form-control" id="organization" name="organization" value="<?php echo $organization; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="casesViolated">Cases Violated:</label>
                                        <input type="text" class="form-control" id="casesViolated" name="casesViolated" value="<?php echo $casesViolated; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="yearsOfStay">Years of Stay:</label>
                                        <input type="text" class="form-control" id="yearsOfStay" name="yearsOfStay" value="<?php echo $yearsOfStay; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="businessOwner">Business Owner:</label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="businessOwner" name="businessOwner" value="<?php echo $businessOwner; ?>">
                                            <label class="form-check-label" for="businessOwner">Yes</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-right button-container">
                                <button id="submitButton" type="submit" class="btn btn-custom btn-primary-custom">
                                    <?php echo $isEdit ? 'Update' : 'Add'; ?>
                                </button>
                                <button type="button" class="btn btn-custom btn-secondary-custom">Cancel</button>
                            </div>
                            <script>
                                if (window.location.search.includes('success=1')) {
                                    alert('Record added successfully!');

                                    window.scrollTo(0, 0);
                                }

                                if (window.location.search.includes('success=1')) {
                                    alert('Record added successfully!');
                                    window.scrollTo(0, 0);
                                }
                            </script>

                            <script>
                                const urlParams = new URLSearchParams(window.location.search);
                                if (urlParams.has('id')) {
                                    document.getElementById('submitButton').textContent = 'Update';
                                } else {
                                    document.getElementById('submitButton').textContent = 'Add';
                                }
                            </script>
                        </div>
            </form>
        </div>
    </div>




    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>