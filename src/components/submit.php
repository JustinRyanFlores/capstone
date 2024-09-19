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

// Collect and sanitize form data
$firstName = htmlspecialchars($_POST['firstName'] ?? '');
$middleName = htmlspecialchars($_POST['middleName'] ?? '');
$lastName = htmlspecialchars($_POST['lastName'] ?? '');
$dobDay = htmlspecialchars($_POST['dobDay'] ?? '');
$dobMonth = htmlspecialchars($_POST['dobMonth'] ?? '');
$dobYear = htmlspecialchars($_POST['dobYear'] ?? '');
$dob = "$dobYear-$dobMonth-$dobDay"; // Format the date as YYYY-MM-DD
$age = htmlspecialchars($_POST['age'] ?? '');
$gender = htmlspecialchars($_POST['gender'] ?? '');
$contactNumber = htmlspecialchars($_POST['contactNumber'] ?? '');
$streetAddress = htmlspecialchars($_POST['streetAddress'] ?? '');
$houseNumber = htmlspecialchars($_POST['houseNumber'] ?? '');
$subdivision = htmlspecialchars($_POST['subdivision'] ?? '');
$barangay = htmlspecialchars($_POST['barangay'] ?? '');
$city = htmlspecialchars($_POST['city'] ?? '');
$province = htmlspecialchars($_POST['province'] ?? '');
$region = htmlspecialchars($_POST['region'] ?? '');
$zipCode = htmlspecialchars($_POST['zipCode'] ?? '');
$motherFirstName = htmlspecialchars($_POST['motherFirstName'] ?? '');
$motherMiddleName = htmlspecialchars($_POST['motherMiddleName'] ?? '');
$motherLastName = htmlspecialchars($_POST['motherLastName'] ?? '');
$fatherFirstName = htmlspecialchars($_POST['fatherFirstName'] ?? '');
$fatherMiddleName = htmlspecialchars($_POST['fatherMiddleName'] ?? '');
$fatherLastName = htmlspecialchars($_POST['fatherLastName'] ?? '');
$educationalAttainment = htmlspecialchars($_POST['educationalAttainment'] ?? '');
$currentSchool = htmlspecialchars($_POST['currentSchool'] ?? '');
$illness = htmlspecialchars($_POST['illness'] ?? '');
$medication = htmlspecialchars($_POST['medication'] ?? '');
$disability = htmlspecialchars($_POST['disability'] ?? '');
$teenPregnancy = isset($_POST['teenAgePregnancy']) ? 1 : 0;
$typeOfDelivery = htmlspecialchars($_POST['typeOfDelivery'] ?? '');
$organization = htmlspecialchars($_POST['organization'] ?? '');
$casesViolated = htmlspecialchars($_POST['casesViolated'] ?? '');
$yearsOfStay = htmlspecialchars($_POST['yearsOfStay'] ?? '');
$businessOwner = isset($_POST['businessOwner']) ? 1 : 0;

// Handle file upload
$imagePath = '';
if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] == 0) {
    $update_image = $_FILES['update_image']['name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];

    // Validate file size (e.g., max 2MB)
    if ($update_image_size > 2 * 1024 * 1024) {
        die("Error: File size is too large.");
    }

    // Validate file type (e.g., only jpg, jpeg, png)
    $allowed_types = ['jpg', 'jpeg', 'png'];
    $file_ext = strtolower(pathinfo($update_image, PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        die("Error: Invalid file type.");
    }

    // Move the uploaded file
    $imagePath = '../../src/assets/' . basename($update_image);
    if (!move_uploaded_file($update_image_tmp_name, $imagePath)) {
        die("Error: Failed to upload image.");
    }
}



$sql = "INSERT INTO residents_records (first_name, middle_name, last_name, dob, age, gender, contact_number, 
        street_address, house_number, subdivision, barangay, city, province, region, zip_code, 
        mother_first_name, mother_middle_name, mother_last_name, father_first_name, father_middle_name, father_last_name, 
        educational_attainment, current_school, illness, medication, disability, teen_pregnancy, type_of_delivery, 
        organization, cases_violated, years_of_stay, business_owner, residents_img) 
        VALUES ('$firstName', '$middleName', '$lastName', '$dob', '$age', '$gender', '$contactNumber', 
                '$streetAddress', '$houseNumber', '$subdivision', '$barangay', '$city', '$province', '$region', '$zipCode', 
                '$motherFirstName', '$motherMiddleName', '$motherLastName', '$fatherFirstName', '$fatherMiddleName', '$fatherLastName', 
                '$educationalAttainment', '$currentSchool', '$illness', '$medication', '$disability', '$teenPregnancy', '$typeOfDelivery', 
                '$organization', '$casesViolated', '$yearsOfStay', '$businessOwner', '$update_image')";



// Execute the statement

if ($conn->query($sql) === TRUE) {
    // Redirect to add_records.php with a success message
    header("Location: ../../pages/add_records.php?success=true");
    exit();
} else {
    // Redirect to add_records.php with an error message
    header("Location: ../../pages/add_records.php?error=true");
    exit();
}


// Close the statement and connection
$conn->close();
