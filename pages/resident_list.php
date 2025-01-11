<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /system/website/login/login.php");
    exit();
}
include_once "../src/components/session_handler.php";
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
    <link rel="stylesheet" href="/system/src/css/navbar.css" />
    <link rel="stylesheet" href="/system/src/css/header.css" />
    <link rel="stylesheet" href="/system/src/css/resident_list.css" />
    <?php include '../src/components/header.php'; ?>
    <style>
        .btn-delete {
            background-color: #610000;
            border-color: #610000;
            color: #ffffff;
        }

        .btn-delete:hover {
            background-color: white;
            border-color: #610000;
            color: #610000;
        }

        .btn-custom {
            background-color: #1c2455;
            border-color: #1c2455;
            color: #f1f1f1;
        }

        .btn-custom:hover {
            background-color: #f1f1f1;
            color: #141a3f;
        }
    </style>
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
                    <div class="input-group" style="max-width: 800px;">
                        <!-- Search Input -->
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Type Here to Search..."
                            value="<?php echo htmlspecialchars($search_query); ?>" />

                        <!-- Search Button -->
                        <div class="input-group-append">
                            <button class="btn btn-custom" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <!-- Reset Button -->
                        <a href="resident_list.php" class="btn btn-secondary ml-2" style="display: flex; align-items: center;">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
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

                            // Split the search query into individual terms
                            $search_terms = explode(' ', $search_query);

                            // Build the SQL query for fetching data
                            $query = "
                        SELECT id, first_name, middle_name, last_name, suffix, dob, gender, contact_number, subdivision,
                        FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) AS age
                        FROM residents_records 
                        WHERE (";

                            // Add each search term to the query
                            foreach ($search_terms as $index => $term) {
                                if ($index > 0) {
                                    $query .= " AND ";
                                }
                                // Check if the concatenated full name contains the term
                                $query .= "CONCAT_WS(' ', first_name, middle_name, last_name, suffix) LIKE '%$term%'";
                            }

                            $query .= " OR dob LIKE '%$search_query%' 
                                OR contact_number LIKE '%$search_query%' 
                                OR subdivision LIKE '%$search_query%' ";

                            // If the search query is numeric, include it in the age filter
                            if ($is_numeric_search) {
                                $query .= " OR FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) = $search_query";
                            }

                            $query .= ") LIMIT $start_from, $limit";
                            $result = $mysqlConn->query($query);

                            // Loop through the records
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr data-id='{$row['id']}' onclick='fetchResidentDetails({$row['id']})'>
                                <td>{$row['first_name']} {$row['middle_name']} {$row['last_name']} {$row['suffix']}</td>
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
                            // Build the total count query for pagination info
                            $query_total = "SELECT COUNT(*) FROM residents_records WHERE (";
                            foreach ($search_terms as $index => $term) {
                                if ($index > 0) {
                                    $query_total .= " AND ";
                                }
                                $query_total .= "CONCAT_WS(' ', first_name, middle_name, last_name, suffix) LIKE '%$term%'";
                            }
                            $query_total .= " OR dob LIKE '%$search_query%' 
                                      OR contact_number LIKE '%$search_query%' 
                                      OR subdivision LIKE '%$search_query%' ";

                            // If the search query is numeric, include it in the total count query
                            if ($is_numeric_search) {
                                $query_total .= " OR FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) = $search_query";
                            }

                            $query_total .= ")";
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

                            // Calculate the start and end page numbers for displaying 5 page links
                            $start = max(1, $page - 2);  // Start from 2 pages before the current page
                            $end = min($total_pages, $page + 2);  // End at 2 pages after the current page

                            // Display "Previous" button
                            if ($page > 1) {
                                echo "<li class='page-item'><a class='page-link' href='?page=" . ($page - 1) . "&search=" . urlencode($search_query) . "'>Previous</a></li>";
                            }

                            // Loop through and display the page links
                            for ($i = $start; $i <= $end; $i++) {
                                if ($i == $page) {
                                    echo "<li class='page-item active'><a class='page-link' href='?page=$i&search=" . urlencode($search_query) . "'>$i</a></li>";
                                } else {
                                    echo "<li class='page-item'><a class='page-link' href='?page=$i&search=" . urlencode($search_query) . "'>$i</a></li>";
                                }
                            }

                            // Display "Next" button
                            if ($page < $total_pages) {
                                echo "<li class='page-item'><a class='page-link' href='?page=" . ($page + 1) . "&search=" . urlencode($search_query) . "'>Next</a></li>";
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="redirectToResidentList()"></button>
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
                            <button id="editButton" class="btn btn-custom" onclick="editResident(<?php echo $residentId; ?>)">Edit</button>
                            <button class="btn btn-custom" onclick="printResidentDetails()">Print</button>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                                <button class="btn btn-delete" onclick="deleteResident()">Archive</button>
                            <?php endif; ?>
                            <button class="btn btn-secondary" data-bs-dismiss="modal" onclick="cancelAction()">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function editResident(residentId) {
                // Redirect to the add_records.php page with the resident ID in the query string
                window.location.href = "/system/pages/add_records.php?id=" + residentId;
            }

            var selectedResidentId; // Declare a global variable

            function fetchResidentDetails(residentId) {
                selectedResidentId = residentId; // Store the resident ID globally for other actions (e.g., edit/delete)

                // Show a loading spinner while fetching data
                $("#resident-details").html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

                $.ajax({
                    url: "/system/src/components/getResidentDetails.php", // Backend script to fetch details
                    type: "POST",
                    data: {
                        id: residentId
                    },
                    success: function(data) {
                        // Populate the modal body with the fetched resident details
                        $("#resident-details").html(data);
                        $('#residentDetailsModal').modal('show'); // Show the modal

                        // Ensure the Edit button has the correct ID for redirection
                        $('#editButton').attr('onclick', 'editResident(' + residentId + ')');
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
                var residentName = document.querySelector('h3.text-primary').innerText || 'Unknown Resident';

                // Consolidate all content into a document-style layout
                var content = `
                                <div style="font-family: Arial, sans-serif; margin: 10px; font-size: 0.9em; line-height: 1.2;">
                                <h1 style="text-align: center; font-size: 1.5em; margin-bottom: 5px;">Resident Details</h1>
                                <h2 style="text-align: center; font-size: 1.2em; margin-bottom: 10px;">${residentName}</h2>

                                <section style="margin-bottom: 10px;">
                                    <h3 style="font-size: 1em; text-decoration: underline;">Personal Information</h3>
                                    <p><strong>Birth Date:</strong> ${getData('#profile tr:nth-child(1) td:nth-child(2)')}</p>
                                    <p><strong>Age:</strong> ${getData('#profile tr:nth-child(2) td:nth-child(2)')}</p>
                                    <p><strong>Gender:</strong> ${getData('#profile tr:nth-child(3) td:nth-child(2)')}</p>
                                    <p><strong>Contact:</strong> ${getData('#profile tr:nth-child(4) td:nth-child(2)')}</p>
                                    <p><strong>Religion:</strong> ${getData('#profile tr:nth-child(5) td:nth-child(2)')}</p>
                                    <p><strong>Status:</strong> ${getData('#profile tr:nth-child(6) td:nth-child(2)')}</p>
                                    <p><strong>Voter:</strong> ${getData('#profile tr:nth-child(7) td:nth-child(2)')}</p>
                                </section>

                                <section style="margin-bottom: 10px;">
                                    <h3 style="font-size: 1em; text-decoration: underline;">Address</h3>
                                    <p><strong>Street:</strong> ${getData('#address tr:nth-child(1) td:nth-child(2)')}</p>
                                    <p><strong>House #:</strong> ${getData('#address tr:nth-child(2) td:nth-child(2)')}</p>
                                    <p><strong>Barangay:</strong> ${getData('#address tr:nth-child(4) td:nth-child(2)')}</p>
                                    <p><strong>City:</strong> ${getData('#address tr:nth-child(5) td:nth-child(2)')}</p>
                                    <p><strong>Province:</strong> ${getData('#address tr:nth-child(6) td:nth-child(2)')}</p>
                                </section>

                                <section style="margin-bottom: 10px;">
                                    <h3 style="font-size: 1em; text-decoration: underline;">Family Information</h3>
                                    <p><strong>Mother:</strong> ${getData('#family tr:nth-child(1) td:nth-child(2)')}</p>
                                    <p><strong>Father:</strong> ${getData('#family tr:nth-child(2) td:nth-child(2)')}</p>
                                </section>

                                <section style="margin-bottom: 10px;">
                                    <h3 style="font-size: 1em; text-decoration: underline;">Education</h3>
                                    <p><strong>Out of School:</strong> ${getData('#education tr:nth-child(1) td:nth-child(2)')}</p>
                                    <p><strong>Attainment:</strong> ${getData('#education tr:nth-child(2) td:nth-child(2)')}</p>
                                    <p><strong>Enrolled in ALS:</strong> ${getData('#education tr:nth-child(3) td:nth-child(2)')}</p>
                                    <p><strong>School:</strong> ${getData('#education tr:nth-child(4) td:nth-child(2)')}</p>
                                </section>

                                <section style="margin-bottom: 10px;">
                                    <h3 style="font-size: 1em; text-decoration: underline;">Health</h3>
                                    <p><strong>Illness:</strong> ${getData('#health tr:nth-child(1) td:nth-child(2)')}</p>
                                    <p><strong>Medication:</strong> ${getData('#health tr:nth-child(2) td:nth-child(2)')}</p>
                                    <p><strong>Disability:</strong> ${getData('#health tr:nth-child(3) td:nth-child(2)')}</p>
                                    <p><strong>Blood Type:</strong> ${getData('#health tr:nth-child(4) td:nth-child(2)')}</p>
                                    <p><strong>PWD:</strong> ${getData('#health tr:nth-child(5) td:nth-child(2)')}</p>
                                    <p><strong>Immunization:</strong> ${getData('#health tr:nth-child(6) td:nth-child(2)')}</p>
                                    <p><strong>Teen Pregnancy:</strong> ${getData('#health tr:nth-child(7) td:nth-child(2)')}</p>
                                    <p><strong>Delivery:</strong> ${getData('#health tr:nth-child(8) td:nth-child(2)')}</p>
                                    <p><strong>Assisted By:</strong> ${getData('#health tr:nth-child(9) td:nth-child(2)')}</p>
                                </section>

                                <section style="margin-bottom: 10px;">
                                    <h3 style="font-size: 1em; text-decoration: underline;">Identification</h3>
                                    <p><strong>Solo Parent ID:</strong> ${getData('#identification tr:nth-child(1) td:nth-child(2)')}</p>
                                    <p><strong>Senior ID:</strong> ${getData('#identification tr:nth-child(2) td:nth-child(2)')}</p>
                                    <p><strong>PWD ID:</strong> ${getData('#identification tr:nth-child(3) td:nth-child(2)')}</p>
                                    <p><strong>PhilHealth:</strong> ${getData('#identification tr:nth-child(4) td:nth-child(2)')}</p>
                                </section>
                            </div>`;

                // Helper function to get data safely
                function getData(selector) {
                    var element = document.querySelector(selector);
                    return element ? element.innerText : 'Not Available';
                }

                // Open a new window for printing
                var printWindow = window.open('', '', 'height=600,width=800');

                // Write the content to the print window with styles
                printWindow.document.write('<html><head><title>Resident Details</title>');
                printWindow.document.write('<style>');
                printWindow.document.write('body { margin: 0; padding: 0; font-size: 0.9em; line-height: 1.6; width: 100%; }');
                printWindow.document.write('h1, h2, h3 { margin: 0 0 10px; }');
                printWindow.document.write('section { page-break-inside: avoid; margin-bottom: 20px; }'); // Avoid breaks within sections
                printWindow.document.write('@media print {');
                printWindow.document.write('body { width: 100%; height: auto; overflow: visible; }');
                printWindow.document.write('}');
                printWindow.document.write('</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write(content);
                printWindow.document.write('</body></html>');

                // Close the document to finish writing
                printWindow.document.close();

                // Wait for the new window to fully load before triggering print
                printWindow.onload = function() {
                    printWindow.focus(); // Ensure the print window has focus
                    printWindow.print(); // Trigger the print

                    // Close the print window after a delay
                    setTimeout(function() {
                        printWindow.close(); // Close the print window
                    }, 500);
                };
            }


            function deleteResident() {
                if (confirm("Are you sure you want to delete this resident?")) {
                    $.ajax({
                        url: '/system/src/components/delete_resident.php',
                        type: 'POST',
                        data: {
                            residentId: selectedResidentId
                        },
                        success: function(response) {
                            alert(response.trim()); // Show success or error message

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

            // Cancel action logic
            function cancelAction() {
                // Logic to cancel the action or clear the form
                document.getElementById('resident-details').innerHTML = '<span class="text-muted">Select a resident to view details</span>';

                // Optionally hide buttons if needed
                $("#action-buttons").addClass('d-none');

                // Redirect to resident list page
                window.location.href = 'resident_list.php'; // Change to your desired URL
            }

            $('#residentDetailsModal').on('hide.bs.modal', function() {
                window.location.href = 'resident_list.php';
            });

            // Event listener for modal hidden
            $('#residentDetailsModal').on('hidden.bs.modal', function() {
                window.location.href = 'resident_list.php';
            });

            function redirectToResidentList() {
                window.location.href = 'resident_list.php'; // Change to your desired URL
            }
        </script>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>