<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /capstone/website/login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>My Web Application</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/capstone/src/css/navbar.css" />
    <link rel="stylesheet" href="/capstone/src/css/header.css" />
    <link rel="stylesheet" href="/capstone/src/css/resident_list.css" />
    <?php include '/xampp/htdocs/capstone/src/components/header.php'; ?>

</head>

<body>
    <?php include '/xampp/htdocs/capstone/src/components/moderator_navbar.php'; ?>

    <div class="container-fluid main-content">
        <div class="row mb-4">
            <div class="col-sm-6">
                <h3 class="mb-0">Resident List</h3>
                <div class="h6 text-muted" style="font-style: italic;">All Registered Residents</div>
            </div>
            <div class="col-sm-6 text-right">
                <?php displayDateTime(); ?>
            </div>
        </div>

        <div class="row flex-grow-1">
            <!-- Resident's Records Search -->
            <div class="col-md-12 p-4 scrollable-container1" style="background-color: #f7f7f7; border-right: 1px solid #ddd;">
                <div class="search-header">Resident's Records Search</div>
                <input type="text" id="searchInput" class="form-control search-bar mb-3" placeholder="Search by name..." aria-label="Search residents" onkeyup="searchResidents()">

                <ul class="list-group" id="resident-list">
                    <?php
                    $conn = new mysqli("localhost", "root", "", "residents_db");
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Fetch all residents to display in the list
                    $query = "SELECT id, first_name, last_name FROM residents_records";
                    $result = $conn->query($query);

                    while ($row = $result->fetch_assoc()) {
                        echo '<li class="list-group-item list-group-item-action" onclick="fetchResidentDetails(' . $row['id'] . ')">' . $row['first_name'] . ' ' . $row['last_name'] . '</li>';
                    }

                    $conn->close();
                    ?>
                </ul>

                <script>
                    function searchResidents() {
                        var query = document.getElementById("searchInput").value.toLowerCase();
                        var residentList = document.getElementById("resident-list");
                        var residents = residentList.getElementsByTagName("li");

                        for (var i = 0; i < residents.length; i++) {
                            var residentName = residents[i].innerText.toLowerCase();
                            if (residentName.includes(query)) {
                                residents[i].style.display = "";
                            } else {
                                residents[i].style.display = "none";
                            }
                        }
                    }
                </script>
            </div>
        </div>
    </div>
    <!-- Resident Details Modal -->
    <div class="modal fade" id="residentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="residentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="residentDetailsModalLabel">Resident's Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="printable-details">
                    <div class="modal-body" id="resident-details">
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="text-muted">Select a resident to view details</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="button-container">
                        <button class="btn btn-primary" onclick="editResident(<?php echo $residentId; ?>)">Edit</button>
                        <button class="btn btn-danger" onclick="printResidentDetails()">Print</button>
                        <button class="btn btn-danger" onclick="deleteResident()">Delete</button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>

                    <script>
                        function editResident(residentId) {
                            // Redirect to the add_records.php page with the resident ID in the query string
                            window.location.href = "/capstone/pages/add_records.php?id=" + residentId;
                        }

                        function fetchResidentDetails(residentId) {
                            // Show a loading spinner
                            $("#resident-details").html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

                            $.ajax({
                                url: "/capstone/src/components/getResidentDetails.php", // Make sure the path is correct
                                type: "POST",
                                data: {
                                    id: residentId
                                },
                                success: function(data) {
                                    // Populate the modal with the fetched data
                                    $("#resident-details").html(data);
                                    $('#residentDetailsModal').modal('show'); // Show the modal

                                    // Set the correct residentId for the Edit button
                                    $('.btn-primary').attr('onclick', 'editResident(' + residentId + ')');
                                },
                                error: function() {
                                    $("#resident-details").html('<div class="text-danger">Unable to retrieve data.</div>');
                                }
                            });
                        }

                        $(document).ready(function() {
                            const urlParams = new URLSearchParams(window.location.search);
                            const residentId = urlParams.get('residentId');

                            if (residentId) {
                                fetchResidentDetails(residentId); // Automatically load details if residentId is in the URL
                            }
                        });

                        if (window.location.search.includes('success=updated')) {
                            alert('Record updated successfully!');
                            window.scrollTo(0, 0); // Scroll to top
                        }



                        function printResidentDetails() {
                            // Get the content of the modal that we want to print
                            var printContent = document.getElementById('printable-details').innerHTML;

                            // Open a new window for printing
                            var printWindow = window.open('', '', 'height=600,width=800');

                            // Write the modal content to the new window with print-specific styles
                            printWindow.document.write('<html><head><title>Resident Details</title>');
                            printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />');
                            printWindow.document.write('<style>');
                            printWindow.document.write('body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }');
                            printWindow.document.write('.modal-body { padding: 10px; }');
                            printWindow.document.write('h5 { font-size: 1.5em; margin-bottom: 10px; }'); // Adjust heading size
                            printWindow.document.write('p { font-size: 1em; margin: 0; }'); // Adjust paragraph size
                            printWindow.document.write('.button-container { display: none; }'); // Hide buttons in print view
                            printWindow.document.write('@media print {');
                            printWindow.document.write('body { -webkit-print-color-adjust: exact; }'); // Print colors exactly
                            printWindow.document.write('}');
                            printWindow.document.write('</style>');
                            printWindow.document.write('</head><body>');
                            printWindow.document.write(printContent);
                            printWindow.document.write('</body></html>');

                            // Close the document to finish writing and call the print function
                            printWindow.document.close();
                            printWindow.focus(); // Make sure the print window has focus

                            printWindow.print(); // Print the contents
                            printWindow.close(); // Close the print window after printing
                        }


                        function cancelAction() {
                            // Logic to cancel the action or clear the form
                            document.getElementById('resident-details').innerHTML = '<span class="text-muted">Select a resident to view details</span>';
                            // Optionally hide buttons if needed
                            $("#action-buttons").addClass('d-none');
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>