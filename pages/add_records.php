<?php
if (isset($_GET['success']) && $_GET['success'] == 'true') {
    echo "<script>alert('Record added successfully');</script>";
}

if (isset($_GET['error']) && $_GET['error'] == 'true') {
    echo "<script>alert('An error occurred while adding the record');</script>";
}
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
            <form action="/capstone/src/components/submit.php" method="POST" enctype="multipart/form-data">
                <div class="row mb-4">
                    <div class="col-md-12 text-center">
                        <div class="profile-picture-container">
                            <img id="profile-picture" src="/capstone/src/assets/kayanlog-logo.png" class="img-fluid border" alt="Profile Picture">
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12 text-center">
                        <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" onchange="previewImage(event)" required>
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
                                <input type="text" class="form-control" id="firstName" name="firstName">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="middleName">Middle Name:</label>
                                <input type="text" class="form-control" id="middleName" name="middleName">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="lastName">Last Name:</label>
                                <input type="text" class="form-control" id="lastName" name="lastName">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="dob">Date of Birth:</label>
                                <div class="input-group">
                                    <select class="form-control" id="dobDay" name="dobDay">
                                        <option value="">Day</option>
                                        <?php for ($day = 1; $day <= 31; $day++): ?>
                                            <option value="<?php echo str_pad($day, 2, '0', STR_PAD_LEFT); ?>"><?php echo $day; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <select class="form-control" id="dobMonth" name="dobMonth">
                                        <option value="">Month</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                    <select class="form-control" id="dobYear" name="dobYear">
                                        <option value="">Year</option>
                                        <?php
                                        $currentYear = date('Y');
                                        for ($year = 1900; $year <= $currentYear; $year++):
                                        ?>
                                            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="age">Age:</label>
                                <input type="text" class="form-control" id="age" name="age">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="gender">Gender:</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option>Male</option>
                                    <option>Female</option>
                                    <!-- Add more options here if needed -->
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contactNumber">Contact Number:</label>
                                <input type="text" class="form-control" id="contactNumber" name="contactNumber"
                                    pattern="\d{10,11}" title="Please enter a valid contact number." required>
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
                                <input type="text" class="form-control" id="streetAddress" name="streetAddress">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="houseNumber">House Number:</label>
                                <input type="text" class="form-control" id="houseNumber" name="houseNumber">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="subdivision">Subdivision:</label>
                                <input type="text" class="form-control" id="subdivision" name="subdivision">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="barangay">Barangay:</label>
                                <select class="form-control" id="barangay" name="barangay">
                                    <!-- Barangay options will be loaded here -->
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="city">City:</label>
                                <select class="form-control" id="city" name="city">
                                    <!-- City options will be loaded here -->
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="province">Province:</label>
                                <select class="form-control" id="province" name="province">
                                    <!-- Province options will be loaded here -->
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="region">Region:</label>
                                <select class="form-control" id="region" name="region">
                                    <!-- Region options will be loaded here -->
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="zipCode">Zip Code:</label>
                                <input type="text" class="form-control" id="zipCode" name="zipCode">
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const regionsSelect = document.getElementById('region');
                        const provincesSelect = document.getElementById('province');
                        const citiesSelect = document.getElementById('city');
                        const barangaysSelect = document.getElementById('barangay');

                        function populateDropdown(selectElement, data, valueKey, textKey) {
                            selectElement.innerHTML = '<option value="">Select</option>';
                            data.forEach(item => {
                                const option = document.createElement('option');
                                option.value = item[valueKey];
                                option.textContent = item[textKey];
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
                        fetchData('data/region.json').then(data => {
                            populateDropdown(regionsSelect, data, 'region_code', 'region_name');
                        });
                        regionsSelect.addEventListener('change', function() {
                            console.log('Region changed:', this.value);
                            const selectedRegionCode = this.value;
                            if (selectedRegionCode) {
                                fetchData('data/province.json').then(data => {
                                    console.log('Provinces data:', data);
                                    const provinces = data.filter(province => province.region_code === selectedRegionCode);
                                    console.log('Filtered provinces:', provinces);
                                    populateDropdown(provincesSelect, provinces, 'province_code', 'province_name');
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
                            console.log('Province changed:', this.value);
                            const selectedProvinceCode = this.value;
                            if (selectedProvinceCode) {
                                fetchData('data/city.json').then(data => {
                                    console.log('Cities data:', data);
                                    const cities = data.filter(city => city.province_code === selectedProvinceCode);
                                    console.log('Filtered cities:', cities);
                                    populateDropdown(citiesSelect, cities, 'city_code', 'city_name');
                                    barangaysSelect.innerHTML = '<option value="">Select Barangay</option>';
                                });
                            } else {
                                citiesSelect.innerHTML = '<option value="">Select City/Municipality</option>';
                                barangaysSelect.innerHTML = '<option value="">Select Barangay</option>';
                            }
                        });
                        citiesSelect.addEventListener('change', function() {
                            console.log('City changed:', this.value);
                            const selectedCityCode = this.value;
                            if (selectedCityCode) {
                                fetchData('data/barangay.json').then(data => {
                                    console.log('Barangays data:', data);
                                    const barangays = data.filter(barangay => barangay.city_code === selectedCityCode);
                                    console.log('Filtered barangays:', barangays);
                                    populateDropdown(barangaysSelect, barangays, 'brgy_code', 'brgy_name');
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
                                <input type="text" class="form-control" id="motherFirstName" name="motherFirstName">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="motherMiddleName">Mother Middle Name:</label>
                                <input type="text" class="form-control" id="motherMiddleName" name="motherMiddleName">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="motherLastName">Mother Last Name:</label>
                                <input type="text" class="form-control" id="motherLastName" name="motherLastName">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="fatherFirstName">Father First Name:</label>
                                <input type="text" class="form-control" id="fatherFirstName" name="fatherFirstName">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="fatherMiddleName">Father Middle Name:</label>
                                <input type="text" class="form-control" id="fatherMiddleName" name="fatherMiddleName">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="fatherLastName">Father Last Name:</label>
                                <input type="text" class="form-control" id="fatherLastName" name="fatherLastName">
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
                                    <input type="checkbox" class="form-check-input" id="outOfSchoolYouth" name="outOfSchoolYouth">
                                    <label class="form-check-label" for="outOfSchoolYouth">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="alternativeLearningSystem">Enrolled in Alternative Learning System:</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="alternativeLearningSystem" name="alternativeLearningSystem">
                                    <label class="form-check-label" for="alternativeLearningSystem">Yes</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Educational Attainment and Name of School Currently Enrolled In -->
                            <div class="col-md-6 mb-3">
                                <label for="educationalAttainment">Educational Attainment:</label>
                                <input type="text" class="form-control" id="educationalAttainment" name="educationalAttainment">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="currentSchool">Name of School Currently Enrolled In:</label>
                                <input type="text" class="form-control" id="currentSchool" name="currentSchool">
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
                                <input type="text" class="form-control" id="illness" name="illness">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="medication">Medication:</label>
                                <input type="text" class="form-control" id="medication" name="medication">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="disability">Disability:</label>
                                <input type="text" class="form-control" id="disability" name="disability">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="teenAgePregnancy">Teen Age Pregnancy:</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="teenAgePregnancy" name="teenAgePregnancy">
                                    <label class="form-check-label" for="teenAgePregnancy">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" id="deliveryContainer" style="display: none;">
                                <label for="typeOfDelivery">Type of Delivery:</label>
                                <input type="text" class="form-control" id="typeOfDelivery" name="typeOfDelivery">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Toggle script for the teenage pregnancy option -->
                <script>
                    document.getElementById('teenAgePregnancy').addEventListener('change', function() {
                        var deliveryContainer = document.getElementById('deliveryContainer');
                        if (this.checked) {
                            deliveryContainer.style.display = 'block';
                        } else {
                            deliveryContainer.style.display = 'none';
                        }
                    });
                </script>

                <!-- Additional Information -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5>Additional Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="organization">Organization:</label>
                                <input type="text" class="form-control" id="organization" name="organization">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="casesViolated">Cases Violated:</label>
                                <input type="text" class="form-control" id="casesViolated" name="casesViolated">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="yearsOfStay">Years of Stay:</label>
                                <input type="text" class="form-control" id="yearsOfStay" name="yearsOfStay">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="businessOwner">Business Owner:</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="businessOwner" name="businessOwner">
                                    <label class="form-check-label" for="businessOwner">Yes</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-right button-container">
                        <button type="submit" class="btn btn-custom btn-primary-custom">Add</button>
                        <button type="button" class="btn btn-custom btn-secondary-custom">Cancel</button>
                    </div>
                    <script>
                        // Check if URL has a success parameter
                        if (window.location.search.includes('success=1')) {
                            alert('Record added successfully!');
                            // Optionally, you can scroll to the top or perform other actions
                            window.scrollTo(0, 0);
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