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
    $update_image = htmlspecialchars($row['residents_img']);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Resident Details</title>
        <style>
            /* Card Styles */
            .card {
                width: 80vh;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                border: 1px solid #ddd;
            }

            .card-header {
                padding: 15px;
                background-color: #1c2455;
                color: #fff;
                text-align: center;
            }

            .card-header h5 {
                margin: 0;
                font-size: 24px;
                font-weight: 600;
            }

            /* Table Styles */
            .card-body {
                padding: 15px;
            }

            .table {
                width: 100%;
                margin-bottom: 1rem;
                color: #333;
            }

            .table-striped tbody tr:nth-of-type(odd) {
                background-color: #f2f2f2;
            }

            .table th,
            .table td {
                padding: 12px;
                vertical-align: top;
                border-top: 1px solid #ddd;
            }

            .table th {
                font-weight: bold;
                width: 30%;
                background-color: #f1f1f1;
                color: #333;
            }

            .table td {
                width: 70%;
            }

            .profile-picture {
                width: 150px;
                height: 150px;
                object-fit: cover;
                border-radius: 50%;
                border: 2px solid #ddd;
                margin: 10px 0;
            }

            .profile-picture-container {
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 8px;
                background-color: #f9f9f9;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
        </style>
    </head>

    <body>
        <div class="card">
            <div class="card-header">
                <h5><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']); ?></h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <th>Profile Picture</th>
                            <td>
                                <div class="profile-picture-container">
                                    <?php if ($update_image): ?>
                                        <img src="/capstone/src/assets/<?php echo $update_image; ?>" alt="Resident Image" class="profile-picture">
                                    <?php else: ?>
                                        <p>No image available.</p>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Date of Birth</th>
                            <td><?php echo htmlspecialchars($row['dob']); ?></td>
                        </tr>
                        <tr>
                            <th>Age</th>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                        </tr>
                        <tr>
                            <th>Gender</th>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        </tr>
                        <tr>
                            <th>Contact Number</th>
                            <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td><?php echo htmlspecialchars($row['house_number'] . ' ' . $row['street_address'] . ', ' . $row['subdivision'] . ', ' . $row['barangay'] . ', ' . $row['city'] . ', ' . $row['province'] . ' ' . $row['zip_code']); ?></td>
                        </tr>
                        <tr>
                            <th>Mother's Name</th>
                            <td><?php echo htmlspecialchars($row['mother_first_name'] . ' ' . $row['mother_middle_name'] . ' ' . $row['mother_last_name']); ?></td>
                        </tr>
                        <tr>
                            <th>Father's Name</th>
                            <td><?php echo htmlspecialchars($row['father_first_name'] . ' ' . $row['father_middle_name'] . ' ' . $row['father_last_name']); ?></td>
                        </tr>
                        <tr>
                            <th>Educational Attainment</th>
                            <td><?php echo htmlspecialchars($row['educational_attainment']); ?></td>
                        </tr>
                        <tr>
                            <th>Current School</th>
                            <td><?php echo htmlspecialchars($row['current_school']); ?></td>
                        </tr>
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
                    </tbody>
                </table>
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