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

// Collect form data with default values if not set
$firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
$middleName = isset($_POST['middleName']) ? $_POST['middleName'] : '';
$lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
$dobDay = isset($_POST['dobDay']) ? $_POST['dobDay'] : '';
$dobMonth = isset($_POST['dobMonth']) ? $_POST['dobMonth'] : '';
$dobYear = isset($_POST['dobYear']) ? $_POST['dobYear'] : '';
$dob = "$dobYear-$dobMonth-$dobDay"; // Format the date as YYYY-MM-DD
$age = isset($_POST['age']) ? $_POST['age'] : '';
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
$contactNumber = isset($_POST['contactNumber']) ? $_POST['contactNumber'] : '';
$streetAddress = isset($_POST['streetAddress']) ? $_POST['streetAddress'] : '';
$houseNumber = isset($_POST['houseNumber']) ? $_POST['houseNumber'] : '';
$subdivision = isset($_POST['subdivision']) ? $_POST['subdivision'] : '';
$barangay = isset($_POST['barangay']) ? $_POST['barangay'] : '';
$city = isset($_POST['city']) ? $_POST['city'] : '';
$province = isset($_POST['province']) ? $_POST['province'] : '';
$region = isset($_POST['region']) ? $_POST['region'] : '';
$zipCode = isset($_POST['zipCode']) ? $_POST['zipCode'] : '';
$motherFirstName = isset($_POST['motherFirstName']) ? $_POST['motherFirstName'] : '';
$motherMiddleName = isset($_POST['motherMiddleName']) ? $_POST['motherMiddleName'] : '';
$motherLastName = isset($_POST['motherLastName']) ? $_POST['motherLastName'] : '';
$fatherFirstName = isset($_POST['fatherFirstName']) ? $_POST['fatherFirstName'] : '';
$fatherMiddleName = isset($_POST['fatherMiddleName']) ? $_POST['fatherMiddleName'] : '';
$fatherLastName = isset($_POST['fatherLastName']) ? $_POST['fatherLastName'] : '';
$educationalAttainment = isset($_POST['educationalAttainment']) ? $_POST['educationalAttainment'] : '';
$currentSchool = isset($_POST['currentSchool']) ? $_POST['currentSchool'] : '';
$illness = isset($_POST['illness']) ? $_POST['illness'] : '';
$medication = isset($_POST['medication']) ? $_POST['medication'] : '';
$disability = isset($_POST['disability']) ? $_POST['disability'] : '';
$teenPregnancy = isset($_POST['teenAgePregnancy']) ? 1 : 0;
$typeOfDelivery = isset($_POST['typeOfDelivery']) ? $_POST['typeOfDelivery'] : '';
$organization = isset($_POST['organization']) ? $_POST['organization'] : '';
$casesViolated = isset($_POST['casesViolated']) ? $_POST['casesViolated'] : '';
$yearsOfStay = isset($_POST['yearsOfStay']) ? $_POST['yearsOfStay'] : '';
$businessOwner = isset($_POST['businessOwner']) ? 1 : 0;

// SQL query to insert data
$sql = "INSERT INTO residents_records (first_name, middle_name, last_name, dob, age, gender, contact_number, 
        street_address, house_number, subdivision, barangay, city, province, region, zip_code, 
        mother_first_name, mother_middle_name, mother_last_name, father_first_name, father_middle_name, father_last_name, 
        educational_attainment, current_school, illness, medication, disability, teen_pregnancy, type_of_delivery, 
        organization, cases_violated, years_of_stay, business_owner) 
        VALUES ('$firstName', '$middleName', '$lastName', '$dob', '$age', '$gender', '$contactNumber', 
                '$streetAddress', '$houseNumber', '$subdivision', '$barangay', '$city', '$province', '$region', '$zipCode', 
                '$motherFirstName', '$motherMiddleName', '$motherLastName', '$fatherFirstName', '$fatherMiddleName', '$fatherLastName', 
                '$educationalAttainment', '$currentSchool', '$illness', '$medication', '$disability', '$teenPregnancy', '$typeOfDelivery', 
                '$organization', '$casesViolated', '$yearsOfStay', '$businessOwner')";

if ($conn->query($sql) === TRUE) {
    // Redirect to add_records.php with a success message
    header("Location: ../../pages/add_records.php?success=true");
    exit();
} else {
    // Redirect to add_records.php with an error message
    header("Location: ../../pages/add_records.php?error=true");
    exit();
}

// Close the connection (this line is unreachable and should be removed or placed before the exit statements)
$conn->close();
?>
