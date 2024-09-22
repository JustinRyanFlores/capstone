<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "residents_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and validate input
$firstName = htmlspecialchars($_POST['firstName'] ?? '', ENT_QUOTES);
$middleName = htmlspecialchars($_POST['middleName'] ?? '', ENT_QUOTES);
$lastName = htmlspecialchars($_POST['lastName'] ?? '', ENT_QUOTES);
$dobDay = htmlspecialchars($_POST['dobDay'] ?? '', ENT_QUOTES);
$dobMonth = htmlspecialchars($_POST['dobMonth'] ?? '', ENT_QUOTES);
$dobYear = htmlspecialchars($_POST['dobYear'] ?? '', ENT_QUOTES);
$dob = "$dobYear-$dobMonth-$dobDay"; // Format the date as YYYY-MM-DD
$age = htmlspecialchars($_POST['age'] ?? '', ENT_QUOTES);
$gender = htmlspecialchars($_POST['gender'] ?? '', ENT_QUOTES);
$contactNumber = htmlspecialchars($_POST['contactNumber'] ?? '', ENT_QUOTES);
$streetAddress = htmlspecialchars($_POST['streetAddress'] ?? '', ENT_QUOTES);
$houseNumber = htmlspecialchars($_POST['houseNumber'] ?? '', ENT_QUOTES);
$subdivision = htmlspecialchars($_POST['subdivision'] ?? '', ENT_QUOTES);
$barangay = htmlspecialchars($_POST['barangay'] ?? '', ENT_QUOTES);
$city = htmlspecialchars($_POST['city'] ?? '', ENT_QUOTES);
$province = htmlspecialchars($_POST['province'] ?? '', ENT_QUOTES);
$region = htmlspecialchars($_POST['region'] ?? '', ENT_QUOTES);
$zipCode = htmlspecialchars($_POST['zipCode'] ?? '', ENT_QUOTES);
$motherFirstName = htmlspecialchars($_POST['motherFirstName'] ?? '', ENT_QUOTES);
$motherMiddleName = htmlspecialchars($_POST['motherMiddleName'] ?? '', ENT_QUOTES);
$motherLastName = htmlspecialchars($_POST['motherLastName'] ?? '', ENT_QUOTES);
$fatherFirstName = htmlspecialchars($_POST['fatherFirstName'] ?? '', ENT_QUOTES);
$fatherMiddleName = htmlspecialchars($_POST['fatherMiddleName'] ?? '', ENT_QUOTES);
$fatherLastName = htmlspecialchars($_POST['fatherLastName'] ?? '', ENT_QUOTES);
$osy = isset($_POST['outOfSchoolYouth']) ? 1 : 0;
$als = isset($_POST['alternativeLearningSystem']) ? 1 : 0;
$educationalAttainment = htmlspecialchars($_POST['educationalAttainment'] ?? '', ENT_QUOTES);
$currentSchool = htmlspecialchars($_POST['currentSchool'] ?? '', ENT_QUOTES);
$illness = htmlspecialchars($_POST['illness'] ?? '', ENT_QUOTES);
$medication = htmlspecialchars($_POST['medication'] ?? '', ENT_QUOTES);
$disability = htmlspecialchars($_POST['disability'] ?? '', ENT_QUOTES);
$teenPregnancy = isset($_POST['teenAgePregnancy']) ? 1 : 0;
$typeOfDelivery = htmlspecialchars($_POST['typeOfDelivery'] ?? '', ENT_QUOTES);
$organization = htmlspecialchars($_POST['organization'] ?? '', ENT_QUOTES);
$casesViolated = htmlspecialchars($_POST['casesViolated'] ?? '', ENT_QUOTES);
$yearsOfStay = htmlspecialchars($_POST['yearsOfStay'] ?? '', ENT_QUOTES);
$businessOwner = isset($_POST['businessOwner']) ? 1 : 0;

// Handle file upload
$imagePath = '';
if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] == 0) {
    $update_image = $_FILES['update_image']['name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];

    if ($update_image_size > 10 * 1024 * 1024) {
        echo "<script>alert('Error: File size is too large.'); window.history.back();</script>";
        exit();
    }

    // Validate file type (only jpg, jpeg, png)
    $allowed_types = ['jpg', 'jpeg', 'png'];
    $file_ext = strtolower(pathinfo($update_image, PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types) || !getimagesize($update_image_tmp_name)) {
        echo "<script>alert('Error: Invalid file type or not an image.'); window.history.back();</script>";
        exit();
    }

    // Move the uploaded file
    $imagePath = '../../src/assets/' . basename($update_image);
    if (!move_uploaded_file($update_image_tmp_name, $imagePath)) {
        echo "<script>alert('Error: Failed to upload image.'); window.history.back();</script>";
        exit();
    }

    // If the upload is successful, you can redirect or show a success message
    echo "<script>alert('Image uploaded successfully!'); window.history.back();</script>";
}


// Prepare SQL statement
$sql = "INSERT INTO residents_records 
        (first_name, middle_name, last_name, dob, age, gender, contact_number, 
        street_address, house_number, subdivision, barangay, city, province, region, zip_code, 
        mother_first_name, mother_middle_name, mother_last_name, father_first_name, father_middle_name, father_last_name, 
        osy, als, educational_attainment, current_school, illness, medication, disability, 
        teen_pregnancy, type_of_delivery, organization, cases_violated, years_of_stay, business_owner, residents_img) 
        VALUES 
        ('$firstName', '$middleName', '$lastName', '$dob', '$age', '$gender', '$contactNumber', 
        '$streetAddress', '$houseNumber', '$subdivision', '$barangay', '$city', '$province', '$region', '$zipCode', 
        '$motherFirstName', '$motherMiddleName', '$motherLastName', '$fatherFirstName', '$fatherMiddleName', '$fatherLastName', 
        '$osy', '$als', '$educationalAttainment', '$currentSchool', '$illness', '$medication', '$disability', 
        '$teenPregnancy', '$typeOfDelivery', '$organization', '$casesViolated', '$yearsOfStay', '$businessOwner', '$update_image')";

// Execute the statement
if ($conn->query($sql) === TRUE) {
    // Redirect to add_records.php with a success message
    header("Location: ../../pages/add_records.php?success=true");
    exit();
} else {
    // Log the error (instead of redirecting)
    error_log("SQL error: " . $conn->error);
    header("Location: ../../pages/add_records.php?error=true");
    exit();
}

// Close the connection
$conn->close();
