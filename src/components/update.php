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

// Check if resident ID is passed
if (!isset($_GET['id'])) {
    die("Error: No resident ID provided.");
}

$residentId = intval($_GET['id']); // Sanitize the ID

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
$imagePath = ''; // Default image path
if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] == 0) {
    $update_image = $_FILES['update_image']['name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];

    // Validate file size (max 2MB)
    if ($update_image_size > 2 * 1024 * 1024) {
        die("Error: File size is too large.");
    }

    // Validate file type (only jpg, jpeg, png)
    $allowed_types = ['jpg', 'jpeg', 'png'];
    $file_ext = strtolower(pathinfo($update_image, PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types) || !getimagesize($update_image_tmp_name)) {
        die("Error: Invalid file type or not an image.");
    }

    // Move the uploaded file
    $imagePath = '../../src/assets/' . basename($update_image);
    if (!move_uploaded_file($update_image_tmp_name, $imagePath)) {
        die("Error: Failed to upload image.");
    }
}

// If a new image was uploaded, include it in the SQL query, otherwise skip the image update
$imageSql = $imagePath ? ", residents_img = '$imagePath'" : "";

// Prepare SQL statement to update resident data
$sql = "UPDATE residents_records 
        SET 
            first_name = '$firstName',
            middle_name = '$middleName',
            last_name = '$lastName',
            dob = '$dob',
            age = '$age',
            gender = '$gender',
            contact_number = '$contactNumber',
            street_address = '$streetAddress',
            house_number = '$houseNumber',
            subdivision = '$subdivision',
            barangay = '$barangay',
            city = '$city',
            province = '$province',
            region = '$region',
            zip_code = '$zipCode',
            mother_first_name = '$motherFirstName',
            mother_middle_name = '$motherMiddleName',
            mother_last_name = '$motherLastName',
            father_first_name = '$fatherFirstName',
            father_middle_name = '$fatherMiddleName',
            father_last_name = '$fatherLastName',
            osy = '$osy',
            als = '$als',
            educational_attainment = '$educationalAttainment',
            current_school = '$currentSchool',
            illness = '$illness',
            medication = '$medication',
            disability = '$disability',
            teen_pregnancy = '$teenPregnancy',
            type_of_delivery = '$typeOfDelivery',
            organization = '$organization',
            cases_violated = '$casesViolated',
            years_of_stay = '$yearsOfStay',
            business_owner = '$businessOwner'
            $imageSql
        WHERE id = $residentId";

// Execute the update statement
if ($conn->query($sql) === TRUE) {
    // Redirect to add_records.php with a success message
    header("Location: ../../pages/resident_list.php?residentId=$residentId&success=updated");
    exit();
} else {
    // Log the error
    error_log("SQL error: " . $conn->error);
    header("Location: ../../pages/resident_list.php?residentId=$residentId&success=updated");
    exit();
}

// Close the connection
$conn->close();
