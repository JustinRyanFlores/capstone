<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /capstone/website/login/login.php");
    exit();
}

include("../src/configs/connection.php"); // Include your database connection

// Initialize search_query to prevent undefined variable warning
$search_query = "";

// Check if search query is set
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Kay-Anlog Sys Info | Resident List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/capstone/src/css/navbar.css" />
    <link rel="stylesheet" href="/capstone/src/css/header.css" />
    <link rel="stylesheet" href="/capstone/src/css/resident_list.css" />
    <?php include '../src/components/header.php'; ?>
</head>

<body>
    <?php include '../src/components/moderator_navbar.php'; ?>
    <div class="container-fluid main-content">
        <div class="row">
            <div class="h3 col-sm-6 col-md-6 text-start h5-sm">
                Resident List
                <div class="h6" style="font-style: italic; color: grey">
                    Home / Resident List
                </div>
            </div>
            <div class="col-sm-6 col-md-6 d-flex justify-content-sm-between justify-content-md-end">
                <div>
                    <?php displayDateTime(); ?>
                </div>
            </div>
        </div>

        <div class="row m-3 bg-light text-white p-2 shadow rounded">
            <div class="col-6">
                <form method="GET" action="resident_list.php">
                    <input type="text" name="search" class="form-control" placeholder="Type Here to Search..." style="max-width: 800px;" value="<?php echo htmlspecialchars($search_query); ?>" />
                </form>
            </div>
        </div>


        <!-- Resident Table -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="resident-table-container">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Name of Resident</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Birthdate</th>
                                <th>Contact Number</th>
                                <th>Subdivision</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Pagination settings
                            $limit = 10; // Number of records per page
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
                            $start_from = ($page - 1) * $limit;

                            // Check if the search query is numeric (for age search)
                            $is_numeric_search = is_numeric($search_query);

                            // Fetch data from the database with age calculation
                            $query = "
                    SELECT id, first_name, middle_name, last_name, dob, gender, contact_number, subdivision,
                    FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) AS age
                    FROM residents_records 
                    WHERE first_name LIKE '%$search_query%' 
                    OR middle_name LIKE '%$search_query%' 
                    OR last_name LIKE '%$search_query%' 
                    OR gender LIKE '%$search_query%'
                    OR dob LIKE '%$search_query%' 
                    OR contact_number LIKE '%$search_query%' 
                    OR subdivision LIKE '%$search_query%' ";

                            // If the search query is numeric, include it in the age filter
                            if ($is_numeric_search) {
                                $query .= "OR FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) = $search_query ";
                            }

                            $query .= "LIMIT $start_from, $limit";
                            $result = $mysqlConn->query($query);
                            // Loop through the records
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr data-id='{$row['id']}' onclick='fetchResidentDetails({$row['id']})'>
                <td>{$row['first_name']} {$row['middle_name']} {$row['last_name']}</td>
                <td>{$row['age']}</td>
                <td>{$row['gender']}</td>
                <td>{$row['dob']}</td>
                <td>{$row['contact_number']}</td>
                <td>{$row['subdivision']}</td>
              </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No records found</td></tr>";
                            }

                            ?>
                        </tbody>
                    </table>

                    <!-- Pagination and Info -->
                    <div class="pagination-container">
                        <div class="pagination-info">
                            <?php
                            // Fetch total records for "Showing X to Y of Z entries"
                            $query_total = "SELECT COUNT(*) FROM residents_records 
                            WHERE first_name LIKE '%$search_query%' 
                            OR middle_name LIKE '%$search_query%' 
                            OR last_name LIKE '%$search_query%' 
                            OR dob LIKE '%$search_query%' 
                            OR contact_number LIKE '%$search_query%' 
                            OR subdivision LIKE '%$search_query%'";

                            // If the search query is numeric, include it in the total count query
                            if ($is_numeric_search) {
                                $query_total .= "OR FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) = $search_query";
                            }

                            $result_total = $mysqlConn->query($query_total);
                            $row_total = $result_total->fetch_row();
                            $total_records = $row_total[0];
                            $start_entry = ($page - 1) * $limit + 1;
                            $end_entry = min($start_entry + $limit - 1, $total_records);

                            echo "Showing $start_entry to $end_entry of $total_records entries";
                            ?>
                        </div>
                        <ul class="pagination">
                            <?php
                            // Generate pagination links with search query
                            $total_pages = ceil($total_records / $limit);

                            if ($page > 1) {
                                echo "<li class='page-item'><a class='page-link' href='?page=" . ($page - 1) . "&search=$search_query'>Previous</a></li>";
                            }

                            for ($i = 1; $i <= $total_pages; $i++) {
                                if ($i == $page) {
                                    echo "<li class='page-item active'><a class='page-link' href='?page=$i&search=$search_query'>$i</a></li>";
                                } else {
                                    echo "<li class='page-item'><a class='page-link' href='?page=$i&search=$search_query'>$i</a></li>";
                                }
                            }

                            if ($page < $total_pages) {
                                echo "<li class='page-item'><a class='page-link' href='?page=" . ($page + 1) . "&search=$search_query'>Next</a></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


        <!-- Resident Details Modal -->
        <div class="modal fade" id="residentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="residentDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
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
                            <button class="btn btn-custom" onclick="editResident(<?php echo $residentId; ?>)">Edit</button>
                            <button class="btn btn-custom" onclick="printResidentDetails()">Print</button>
                            <button class="btn btn-danger" onclick="deleteResident()">Delete</button>
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>

                        <script>
                            function editResident(residentId) {
                                // Redirect to the add_records.php page with the resident ID in the query string
                                window.location.href = "/capstone/pages/add_records.php?id=" + residentId;
                            }

                            var selectedResidentId; // Declare a global variable

                            function fetchResidentDetails(residentId) {
                                selectedResidentId = residentId; // Store the resident ID globally for other actions (e.g., edit/delete)

                                // Show a loading spinner while fetching data
                                $("#resident-details").html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

                                $.ajax({
                                    url: "/capstone/src/components/getResidentDetails.php", // Backend script to fetch details
                                    type: "POST",
                                    data: {
                                        id: residentId
                                    },
                                    success: function(data) {
                                        // Populate the modal body with the fetched resident details
                                        $("#resident-details").html(data);
                                        $('#residentDetailsModal').modal('show'); // Show the modal

                                        // Ensure the Edit button has the correct ID for redirection
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

                                // Close the document to finish writing
                                printWindow.document.close();

                                // Wait for the new window to fully load before triggering print
                                printWindow.onload = function() {
                                    printWindow.focus(); // Ensure the print window has focus
                                    printWindow.print(); // Trigger the print

                                    // Use a delay before closing the window to ensure the print dialog fully processes
                                    setTimeout(function() {
                                        printWindow.close(); // Close the print window after a small delay
                                    }, 500); // 500ms delay
                                };
                            }

                            function deleteResident() {
                                if (confirm("Are you sure you want to delete this resident?")) {
                                    $.ajax({
                                        url: '/capstone/src/components/delete_resident.php',
                                        type: 'POST',
                                        data: {
                                            residentId: selectedResidentId
                                        },
                                        success: function(response) {
                                            alert(response); // Show success or error message

                                            console.log("Page will reload now");
                                            $('#residentDetailsModal').modal('hide'); // Close the modal

                                            // Refresh the page to reflect the changes
                                            window.location.reload();
                                        },
                                        error: function() {
                                            alert("An error occurred while deleting the resident.");
                                        }
                                    });
                                }
                            }

                            $('input[name="search"]').on('keyup', function() {
                                let searchValue = $(this).val();
                                $.ajax({
                                    url: 'resident_list.php',
                                    method: 'GET',
                                    data: {
                                        search: searchValue
                                    },
                                    success: function(response) {
                                        $('tbody').html($(response).find('tbody').html());

                                        // Rebind click event to rows
                                        $('table tbody tr').on('click', function() {
                                            openModal($(this));
                                        });
                                    }
                                });
                            });

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