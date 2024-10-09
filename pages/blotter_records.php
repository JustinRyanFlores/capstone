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
    <title>Kay-Anlog Sys Info | Blotter Records</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/capstone/src/css/navbar.css" />
    <link rel="stylesheet" href="/capstone/src/css/header.css" />
    <link rel="stylesheet" href="/capstone/src/css/blotter_records.css" />
    <?php include '../src/components/header.php'; ?>
</head>
<style>
    .text-end #exitModalBlotter {
        background-color: #1c2455;
        border-color: #1c2455;
        color: #ffffff;
    }

    .text-end #exitModalBlotter:hover {
        background-color: #ffffff;
        border-color: #1c2455;
        color: #1c2455;
    }

    .text-end #addModalBlotter {
        background-color: #6c757d;
        border-color: #5a6268;
        color: #ffffff;
    }

    .text-end #addModalBlotter:hover {
        background-color: #ffffff;
        border-color: #5a6268;
        color: #5a6268;
    }
</style>

<body>
    <?php include '../src/components/moderator_navbar.php'; ?>
    <div class="container-fluid main-content">
        <div class="row">
            <div class="h3 col-sm-6 col-md-6 text-start h5-sm">
                Blotter Records
                <div class="h6" style="font-style: italic; color: grey">
                    Home / Blotter Records
                </div>
            </div>
            <div class="col-sm-6 col-md-6 d-flex justify-content-sm-between justify-content-md-end">
                <div>
                    <?php displayDateTime(); ?>
                </div>
            </div>
        </div>

        <!-- Search and Buttons -->
        <div class="row mt-4 search-bar-container">
            <div class="col-md-12">
                <form method="GET" action="blotter_records.php">
                    <input type="text" name="search" class="form-control" placeholder="Type Here to Search..." style="max-width: 300px;" value="<?php echo htmlspecialchars($search_query); ?>" />
                </form>
                <div class="action-buttons d-flex mt-3">
                    <button class="btn btn-new-blotter" data-bs-toggle="modal" data-bs-target="#addBlotterModal">New Blotter</button>
                </div>
            </div>
        </div>

        <!-- Blotter Records Table -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="blotter-table-container">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>BlotterID</th>
                                <th>Name of Complainant</th>
                                <th>Name(s) of Accused</th>
                                <th>Type of Incident</th>
                                <th>Blotter Status</th>
                                <th>Place of Incident</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            // Pagination settings
                            $limit = 8; // Number of records per page
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $start_from = ($page - 1) * $limit;

                            // Fetch data from the database
                            $query = "SELECT blotter_id, type_incident, blotter_status, dt_reported, dt_incident, place_incident, name_complainant, name_accused, user_in_charge, narrative 
                            FROM blotter 
                            WHERE name_complainant LIKE '%$search_query%' 
                               OR name_accused LIKE '%$search_query%' 
                               OR type_incident LIKE '%$search_query%' 
                               OR blotter_status LIKE '%$search_query%' 
                               OR place_incident LIKE '%$search_query%'
                            LIMIT $start_from, $limit";

                            $result = $mysqlConn2->query($query);

                            // Loop through the records
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr data-id='{$row['blotter_id']}' 
                                                data-dt-incident='{$row['dt_incident']}' 
                                                data-dt-reported='{$row['dt_reported']}' 
                                                data-user-in-charge='{$row['user_in_charge']}' 
                                                data-narrative='{$row['narrative']}'>
                                        <td>{$row['blotter_id']}</td>
                                        <td>{$row['name_complainant']}</td>
                                        <td>{$row['name_accused']}</td>
                                        <td>{$row['type_incident']}</td>
                                        <td>{$row['blotter_status']}</td>
                                        <td>{$row['place_incident']}</td>
                                    </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>

                    <!-- Pagination and Info -->
                    <div class="pagination-container">
                        <div class="pagination-info">
                            <?php
                            // Fetch total records for "Showing X to Y of Z entries"
                            $query_total = "SELECT COUNT(*) FROM blotter";
                            $result_total = $mysqlConn2->query($query_total);
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
    </div>

    <!-- Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">Blotter Record Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="modalForm">
                        <div class="row">
                            <!-- Left side: Basic Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="blotterId" class="form-label">BlotterID</label>
                                    <input type="text" class="form-control" id="blotterId" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="nameComplainant" class="form-label">Name of Complainant</label>
                                    <input type="text" class="form-control" id="nameComplainant" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="nameAccused" class="form-label">Name of Accused</label>
                                    <input type="text" class="form-control" id="nameAccused" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="typeIncident" class="form-label">Type of Incident</label>
                                    <input type="text" class="form-control" id="typeIncident" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="blotterStatus" class="form-label">Blotter Status</label>
                                    <input type="text" class="form-control" id="blotterStatus" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="placeIncident" class="form-label">Place of Incident</label>
                                    <input type="text" class="form-control" id="placeIncident" readonly>
                                </div>
                            </div>

                            <!-- Right side: Additional Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dtReported" class="form-label">Date & Time Reported</label>
                                    <input type="text" class="form-control" id="dtReported" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="dtIncident" class="form-label">Date & Time of Incident</label>
                                    <input type="text" class="form-control" id="dtIncident" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="userInCharge" class="form-label">Name of the Statement Writer</label>
                                    <input type="text" class="form-control" id="userInCharge" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="narrative" class="form-label">Narrative</label>
                                    <textarea class="form-control" id="narrative" rows="4" readonly></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Exit</button>
                            <button type="button" class="btn btn-success" id="markDoneBtn" style="display: none;">Mark as Done</button>
                            <button type="button" class="btn btn-danger" id="deleteBtn">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Blotter Modal -->
    <div class="modal fade" id="addBlotterModal" tabindex="-1" aria-labelledby="addBlotterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBlotterModalLabel">Add New Blotter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addBlotterForm">
                        <div class="mb-3">
                            <label for="typeIncident" class="form-label">Type of Incident</label>
                            <input type="text" class="form-control" id="typeIncident" name="typeIncident" required>
                        </div>
                        <div class="mb-3">
                            <label for="blotterStatus" class="form-label">Blotter Status</label>
                            <select class="form-control" id="blotterStatus" name="blotterStatus" required>
                                <option value="Pending">Pending</option>
                                <option value="Done">Done</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dtReported" class="form-label">Date & Time Reported</label>
                            <input type="datetime-local" class="form-control" id="dtReported" name="dtReported" required>
                        </div>
                        <div class="mb-3">
                            <label for="dtIncident" class="form-label">Date & Time of Incident</label>
                            <input type="datetime-local" class="form-control" id="dtIncident" name="dtIncident" required>
                        </div>
                        <div class="mb-3">
                            <label for="placeIncident" class="form-label">Place of Incident</label>
                            <select class="form-control" id="placeIncident" name="placeIncident" required>
                                <option value="" disabled selected>Select a place</option>
                                <option value="Purok-1">Purok-1</option>
                                <option value="Purok-2">Purok-2</option>
                                <option value="Purok-3">Purok-3</option>
                                <option value="Purok-4">Purok-4</option>
                                <option value="Calambeño ville 5">Calambeño Ville 5</option>
                                <option value="Mother ignacia">Mother Ignacia</option>
                                <option value="Villa Javier">Villa Javier</option>
                                <option value="Villa Andrea">Villa Andrea</option>
                                <option value="Valley Breeze">Valley Breeze</option>
                                <option value="Southville 6">Southville 6</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nameComplainant" class="form-label">Name of Complainant</label>
                            <input type="text" class="form-control" id="nameComplainant" name="nameComplainant" required>
                        </div>
                        <div class="mb-3">
                            <label for="nameAccused" class="form-label">Name of Accused</label>
                            <input type="text" class="form-control" id="nameAccused" name="nameAccused" required>
                        </div>
                        <div class="mb-3">
                            <label for="userInCharge" class="form-label">Name of the Statement Writer</label>
                            <input type="text" class="form-control" id="userInCharge" name="userInCharge" required>
                        </div>
                        <div class="mb-3">
                            <label for="narrative" class="form-label">Narrative</label>
                            <textarea class="form-control" id="narrative" name="narrative" rows="4" required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-custom-exit" id="addModalBlotter" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-custom-add" id="exitModalBlotter">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            let selectedRow;

            function openModal(row) {
                selectedRow = row;
                $('#blotterId').val(row.find('td').eq(0).text());
                $('#nameComplainant').val(row.find('td').eq(1).text());
                $('#nameAccused').val(row.find('td').eq(2).text());
                $('#typeIncident').val(row.find('td').eq(3).text());
                $('#blotterStatus').val(row.find('td').eq(4).text());
                $('#placeIncident').val(row.find('td').eq(5).text());
                $('#dtReported').val(row.data('dt-reported'));
                $('#dtIncident').val(row.data('dt-incident'));
                $('#userInCharge').val(row.data('user-in-charge'));
                $('#narrative').val(row.data('narrative'));

                if ($('#blotterStatus').val() === 'Pending') {
                    $('#markDoneBtn').show();
                } else {
                    $('#markDoneBtn').hide();
                }

                $('#viewModal').modal('show');
            }

            $('table tbody tr').on('click', function() {
                openModal($(this));
            });

            // Rebind click event after search
            $('input[name="search"]').on('keyup', function() {
                let searchValue = $(this).val();
                $.ajax({
                    url: 'blotter_records.php',
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

            // Attach click event to rows
            $('table tbody tr').on('click', function() {
                openModal($(this));
            });

            // Mark as Done button click event
            $('#markDoneBtn').on('click', function() {
                let blotterId = $('#blotterId').val();

                $.ajax({
                    url: '../src/components/update_blotter_status.php', // Path to your PHP script
                    method: 'POST',
                    data: {
                        blotterId: blotterId,
                        status: 'Done'
                    },
                    success: function(response) {
                        if (response.trim() === 'Status updated successfully.') {
                            selectedRow.find('td').eq(2).text('Done'); // Update status in the table
                            $('#blotterStatus').val('Done'); // Update status in the modal
                            $('#markDoneBtn').hide(); // Hide the button after marking as done
                            alert('Blotter record marked as done.');
                            window.location.reload();
                        } else {
                            alert('Failed to update status.');
                        }
                        $('#viewModal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred: ' + error);
                    }
                });
            });

            // Delete button click event
            $('#deleteBtn').on('click', function() {
                let blotterId = $('#blotterId').val();

                $.ajax({
                    url: '../src/components/delete_blotter.php', // Path to your PHP script
                    method: 'POST',
                    data: {
                        blotterId: blotterId
                    },
                    success: function(response) {
                        if (response.trim() === 'Record successfully moved and deleted.') {
                            selectedRow.remove(); // Remove the row from the table
                            alert('Record successfully moved to archive.');
                        } else {
                            alert('Failed to move record to archive.');
                        }
                        $('#viewModal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred: ' + error);
                    }
                });
            });

            // Show the "Add New Blotter" modal
            $('.btn-new-blotter').on('click', function() {
                $('#addBlotterModal').modal('show');
            });

            // Handle form submission
            $('#addBlotterForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                $.ajax({
                    url: '../src/components/add_blotter.php', // Adjust path as needed
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        alert(response); // Show success message
                        $('#addBlotterModal').modal('hide'); // Hide the modal
                        // Optionally, you can refresh the table or page here
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred: ' + error);
                    }
                });
            });

        });
    </script>


</body>

</html>

<?php
// Closing connection at the end of the script
$mysqlConn2->close();
?>