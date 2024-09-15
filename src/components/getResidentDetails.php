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

if ($row = $result->fetch_assoc()) {
    ob_start();
    ?>
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5><?php echo $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']; ?></h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <tbody>
                    <tr><th>Date of Birth</th><td><?php echo $row['dob']; ?></td></tr>
                    <tr><th>Age</th><td><?php echo $row['age']; ?></td></tr>
                    <tr><th>Gender</th><td><?php echo $row['gender']; ?></td></tr>
                    <tr><th>Contact Number</th><td><?php echo $row['contact_number']; ?></td></tr>
                    <tr><th>Address</th><td><?php echo $row['house_number'] . ' ' . $row['street_address'] . ', ' . $row['subdivision'] . ', ' . $row['barangay'] . ', ' . $row['city'] . ', ' . $row['province'] . ' ' . $row['zip_code']; ?></td></tr>
                    <tr><th>Mother's Name</th><td><?php echo $row['mother_first_name'] . ' ' . $row['mother_middle_name'] . ' ' . $row['mother_last_name']; ?></td></tr>
                    <tr><th>Father's Name</th><td><?php echo $row['father_first_name'] . ' ' . $row['father_middle_name'] . ' ' . $row['father_last_name']; ?></td></tr>
                    <tr><th>Educational Attainment</th><td><?php echo $row['educational_attainment']; ?></td></tr>
                    <tr><th>Current School</th><td><?php echo $row['current_school']; ?></td></tr>
                    <tr><th>Illness</th><td><?php echo $row['illness']; ?></td></tr>
                    <tr><th>Medication</th><td><?php echo $row['medication']; ?></td></tr>
                    <tr><th>Disability</th><td><?php echo $row['disability']; ?></td></tr>
                    <tr><th>Teen Pregnancy</th><td><?php echo $row['teen_pregnancy'] ? 'Yes' : 'No'; ?></td></tr>
                    <tr><th>Type of Delivery</th><td><?php echo $row['type_of_delivery']; ?></td></tr>
                    <tr><th>Organization</th><td><?php echo $row['organization']; ?></td></tr>
                    <tr><th>Cases Violated</th><td><?php echo $row['cases_violated']; ?></td></tr>
                    <tr><th>Years of Stay</th><td><?php echo $row['years_of_stay']; ?></td></tr>
                    <tr><th>Business Owner</th><td><?php echo $row['business_owner'] ? 'Yes' : 'No'; ?></td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    echo ob_get_clean();
} else {
    echo "<div class='alert alert-warning'>No details found for this resident.</div>";
}

$stmt->close();
$conn->close();
