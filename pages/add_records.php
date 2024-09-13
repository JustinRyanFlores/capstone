<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Residents Records</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/capstone/src/css/navbar.css" />
    <link rel="stylesheet" href="/capstone/src/css/header.css" />
    <link rel="stylesheet" href="/capstone/src/css/add_records.css" />
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
            <div class="row mb-4">
                <div class="col-md-12 text-center">
                    <div class="profile-picture-container">
                        <img src="/capstone/src/assets/kayanlog-logo.png" class="img-fluid rounded-circle border" alt="Profile Picture">
                    </div>
                </div>
            </div>

            <form>
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
                                        <option>Day</option>
                                        <!-- Populate options here -->
                                    </select>
                                    <select class="form-control" id="dobMonth" name="dobMonth">
                                        <option>Month</option>
                                        <!-- Populate options here -->
                                    </select>
                                    <select class="form-control" id="dobYear" name="dobYear">
                                        <option>Year</option>
                                        <!-- Populate options here -->
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
                                <input type="text" class="form-control" id="contactNumber" name="contactNumber">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5>Address Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="streetAddress">Street Address:</label>
                                <input type="text" class="form-control" id="streetAddress" name="streetAddress">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="houseNumber">House Number:</label>
                                <input type="text" class="form-control" id="houseNumber" name="houseNumber">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="subdivision">Subdivision:</label>
                                <input type="text" class="form-control" id="subdivision" name="subdivision">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="purok">Purok:</label>
                                <input type="text" class="form-control" id="purok" name="purok">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="barangay">Barangay:</label>
                                <input type="text" class="form-control" id="barangay" name="barangay">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="city">City:</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="province">Province:</label>
                                <input type="text" class="form-control" id="province" name="province">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="region">Region:</label>
                                <input type="text" class="form-control" id="region" name="region">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="zipCode">Zip Code:</label>
                                <input type="text" class="form-control" id="zipCode" name="zipCode">
                            </div>
                        </div>
                    </div>
                </div>

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
                        <button type="button" class="btn btn-custom btn-primary-custom">Add</button>
                        <button type="button" class="btn btn-custom btn-secondary-custom">Cancel</button>
                    </div>
                </div>
        </div>
    </div>




    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>