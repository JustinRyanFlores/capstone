<?php
include('../configs/connection.php'); // Adjust this path as necessary

// Check if 'id' is set in the POST request
if (isset($_POST['id'])) {
    $id = $_POST['id']; // Get the id of the resident to retrieve

    // Use the correct database connection
    $sql = "SELECT * FROM residents_records WHERE id = ?";
    $stmt = $mysqlConn4->prepare($sql); // Use $mysqlConn4 instead of $conn

    if ($stmt) { // Check if preparation was successful
        $stmt->bind_param("i", $id); // "i" for integer type (id is typically an integer)
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $fullName = $row['first_name'] . " " . $row['middle_name'] . " " . $row['last_name'] . " " . $row['suffix'];

            // Start landscape layout with Bootstrap grid
            echo "<h5 style='color: #1c2455;'>Resident Details</h5>";
            echo "<p><strong>Name:</strong> $fullName</p>";

            // Address Information in two columns
            echo "<h5 style='color: #1c2455;'>Address Information</h5>";
            echo "<div class='row'>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Street Address:</strong> {$row['street_address']}</p>";
            echo "<p><strong>House Number:</strong> {$row['house_number']}</p>";
            echo "<p><strong>Subdivision:</strong> {$row['subdivision']}</p>";
            echo "<p><strong>Barangay:</strong> {$row['barangay']}</p>";
            echo "</div>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>City:</strong> {$row['city']}</p>";
            echo "<p><strong>Province:</strong> {$row['province']}</p>";
            echo "<p><strong>ZIP Code:</strong> {$row['zip_code']}</p>";
            echo "</div>";
            echo "</div>";

            // Family Information in two columns
            echo "<h5 style='color: #1c2455;'>Family Information</h5>";
            echo "<div class='row'>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Mother's Name:</strong> {$row['mother_first_name']} {$row['mother_middle_name']} {$row['mother_last_name']}</p>";
            echo "<p><strong>Father's Name:</strong> {$row['father_first_name']} {$row['father_middle_name']} {$row['father_last_name']}</p>";
            echo "</div>";
            echo "</div>";

            // Educational Information in two columns
            echo "<h5 style='color: #1c2455;'>Educational Information</h5>";
            echo "<div class='row'>";
            echo "<div class='col-md-6'>";
            $osyStatus = $row['osy'] == 1 ? 'Yes' : 'No';
            echo "<p><strong>Out of School Youth:</strong> {$osyStatus}</p>";
            echo "<p><strong>Educational Attainment:</strong> {$row['educational_attainment']}</p>";
            $alsStatus = $row['als'] == 1 ? 'Yes' : 'No';
            echo "<p><strong>Enrolled in ALS:</strong> {$alsStatus}</p>";
            echo "</div>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Current School:</strong> {$row['current_school']}</p>";
            echo "</div>";
            echo "</div>";

            // Health Information in two columns
            echo "<h5 style='color: #1c2455;'>Health Information</h5>";
            echo "<div class='row'>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Illness:</strong> {$row['illness']}</p>";
            echo "<p><strong>Medication:</strong> {$row['medication']}</p>";
            echo "<p><strong>Disability:</strong> {$row['disability']}</p>";
            $pwdStatus = $row['pwd'] == 1 ? 'Yes' : 'No';
            echo "<p><strong>PWD:</strong> {$pwdStatus}</p>";
            echo "</div>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Immunization Status:</strong> {$row['immunization']}</p>";
            $teenPregnancyStatus = $row['teen_pregnancy'] == 1 ? 'Yes' : 'No';
            echo "<p><strong>Teen Pregnancy:</strong> {$teenPregnancyStatus}</p>";
            echo "<p><strong>Type of Delivery:</strong> {$row['type_of_delivery']}</p>";
            echo "<p><strong>Assisted By:</strong> {$row['assisted_by']}</p>";
            echo "</div>";
            echo "</div>";

            // Additional Information in two columns
            echo "<h5 style='color: #1c2455;'>Additional Information</h5>";
            echo "<div class='row'>";
            echo "<div class='col-md-6'>";
            echo "<p><strong>Organization:</strong> {$row['organization']}</p>";
            echo "<p><strong>Cases Violated:</strong> {$row['cases_violated']}</p>";
            echo "<p><strong>Years of Stay:</strong> {$row['years_of_stay']}</p>";
            echo "</div>";
            echo "<div class='col-md-6'>";
            $businessOwnerStatus = $row['business_owner'] == 1 ? 'Yes' : 'No';
            echo "<p><strong>Business Owner:</strong> {$businessOwnerStatus}</p>";
            $ofwStatus = isset($row['ofw']) && $row['ofw'] == 1 ? 'Yes' : 'No'; // Make sure 'ofw' exists in the DB if needed
            echo "<p><strong>OFW:</strong> {$ofwStatus}</p>";
            echo "<p><strong>Employment Status:</strong> {$row['employment']}</p>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<p>No details found for the selected resident.</p>";
        }
    } else {
        echo "<p>Error preparing statement: " . $mysqlConn4->error . "</p>";
    }
}
?>
