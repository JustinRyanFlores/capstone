<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /system/website/login/login.php");
    exit();
}

include('../src/configs/connection.php');

// Fetch records from the archive tables
function fetchResidentsRecords($conn)
{
    $sql = "SELECT * FROM residents_records";
    $result = $conn->query($sql);
    return $result;
}

function fetchBlotterRecords($conn)
{
    $sql = "SELECT * FROM archive_blotter";
    $result = $conn->query($sql);
    return $result;
}

function fetchUserRecords($conn)
{
    $sql = "SELECT * FROM archive_user";
    $result = $conn->query($sql);
    return $result;
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
    <link rel="stylesheet" href="/system/src/css/navbar.css" />
    <link rel="stylesheet" href="/system/src/css/header.css" />
    <link rel="stylesheet" href="/system/src/css/dashboard.css" />
    <link rel="stylesheet" href="/system/src/css/archive.css" />
    <?php include '../src/components/header.php'; ?>
    <style>
        .nav-tabs .nav-link {
            color: #1c2455;
        }

        .nav-tabs .nav-link.active {
            color: white;
            background-color: #1c2455;
        }

        .btn-delete {
            background-color: #610000;
            border-color: #610000;
            color: #ffffff;
            /* Optional: Change text color */
        }

        .btn-delete:hover {
            background-color: white;
            border-color: #610000;
            color: #610000;
            /* Optional: Change hover text color */
        }

        .btn-restore {
            background-color: #013220;
            border-color: #013220;
            color: white;
        }

        .btn-restore:hover {
            background-color: white;
            /* Change hover color here */
            border-color: #08522e;
            color: #08522e;
        }
    </style>
</head>

<body>
    <?php include '../src/components/moderator_navbar.php'; ?>

    <div class="container-fluid main-content">
        <div class="row">
            <div class="h3 col-sm-6 col-md-6 text-start h5-sm">
                Archive
                <div class="h6" style="font-style: italic; color: grey">
                    Home / Archive
                </div>
            </div>
            <div class="col-sm-6 col-md-6 d-flex justify-content-sm-between justify-content-md-end">
                <div>
                    <?php displayDateTime(); ?>
                </div>
            </div>
        </div>


        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="archiveTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="residents-tab" data-bs-toggle="tab" href="#residents" role="tab" aria-controls="residents" aria-selected="true">Residents Records</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="blotter-tab" data-bs-toggle="tab" href="#blotter" role="tab" aria-controls="blotter" aria-selected="false">Blotter Records</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="user-tab" data-bs-toggle="tab" href="#user" role="tab" aria-controls="user" aria-selected="false">User Records</a>
            </li>
        </ul>

        <!-- Tabs Content -->
        <div class="tab-content" id="archiveTabContent">
            <!-- Residents Tab -->
            <div class="tab-pane fade show active" id="residents" role="tabpanel" aria-labelledby="residents-tab">
                <!-- Search Bar -->
                <div class="mt-3">
                    <input type="text" id="searchResidents" class="form-control" placeholder="Search Residents..." onkeyup="filterTable('residentsTable', this.value)">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mt-3" id="residentsTable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAllResidents" class="select-all"></th> <!-- Select All checkbox -->
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
                            $residentsResult = fetchResidentsRecords($mysqlConn4); // Using the archive connection
                            if ($residentsResult && $residentsResult->num_rows > 0) {
                                while ($row = $residentsResult->fetch_assoc()) {
                                    $residentID = $row['id'];
                                    echo "<tr>
                                <td><input type='checkbox' class='selectRow' data-id='{$residentID}'></td> <!-- Row selection checkbox -->
                                <td onclick='loadResidentDetails($residentID)' style='cursor:pointer'>{$row['first_name']} {$row['middle_name']} {$row['last_name']} {$row['suffix']}</td>
                                <td>{$row['age']}</td>
                                <td>{$row['gender']}</td>
                                <td>{$row['dob']}</td>
                                <td>{$row['contact_number']}</td>
                                <td>{$row['subdivision']}</td>
                            </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <button id="deleteSelectedResidentsBtn" class="btn btn-delete">Delete Selected</button>
            </div>

            <!-- Blotter Tab -->
            <div class="tab-pane fade" id="blotter" role="tabpanel" aria-labelledby="blotter-tab">
                <!-- Search Bar -->
                <div class="mt-3">
                    <input type="text" id="searchBlotter" class="form-control" placeholder="Search Blotter..." onkeyup="filterTable('blotterTable', this.value)">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mt-3" id="blotterTable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAllBlotter" class="select-all"></th> <!-- Select All checkbox -->
                                <th>ID</th>
                                <th>Incident</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $blotterResult = fetchBlotterRecords($mysqlConn4); // Archive connection
                            if ($blotterResult && $blotterResult->num_rows > 0) {
                                while ($row = $blotterResult->fetch_assoc()) {
                                    echo "<tr>
                                <td><input type='checkbox' class='selectRow' data-id='{$row['blotter_id']}'></td> <!-- Row selection checkbox -->
                                <td onclick='loadBlotterDetails({$row['blotter_id']})' style='cursor:pointer'>{$row['blotter_id']}</td>
                                <td>{$row['type_incident']}</td>
                                <td>{$row['blotter_status']}</td>
                            </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <button id="deleteSelectedBlotterBtn" class="btn btn-delete">Delete Selected</button>
            </div>

            <!-- User Tab -->
            <div class="tab-pane fade" id="user" role="tabpanel" aria-labelledby="user-tab">
                <!-- Search Bar -->
                <div class="mt-3">
                    <input type="text" id="searchUser" class="form-control" placeholder="Search Users..." onkeyup="filterTable('userTable', this.value)">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mt-3" id="userTable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAllUser" class="select-all"></th> <!-- Select All checkbox -->
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $userResult = fetchUserRecords($mysqlConn4); // Archive connection
                            if ($userResult && $userResult->num_rows > 0) {
                                while ($row = $userResult->fetch_assoc()) {
                                    echo "<tr>
                                <td><input type='checkbox' class='selectRow' data-id='{$row['user_id']}'></td> <!-- Row selection checkbox -->
                                <td onclick='loadUserDetails({$row['user_id']})' style='cursor:pointer'>{$row['user_id']}</td>
                                <td>{$row['fname']}</td>
                                <td>{$row['role']}</td>
                            </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <button id="deleteSelectedUserBtn" class="btn btn-delete">Delete Selected</button>
            </div>
        </div>

        <script>
            // Search Functionality for Filtering Tables
            function filterTable(tableId, query) {
                const table = document.getElementById(tableId);
                const rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
                query = query.toLowerCase();

                for (let i = 0; i < rows.length; i++) {
                    const cells = rows[i].getElementsByTagName("td");
                    let match = false;

                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].textContent.toLowerCase().includes(query)) {
                            match = true;
                            break;
                        }
                    }
                    rows[i].style.display = match ? "" : "none";
                }
            }

            // jQuery for Select All and Delete Selected Functions
            $(document).ready(function() {
                // Select All functionality for each tab
                $('#selectAllResidents').click(function() {
                    $('.selectRow').prop('checked', $(this).prop('checked'));
                });

                $('#selectAllBlotter').click(function() {
                    $('.selectRow').prop('checked', $(this).prop('checked'));
                });

                $('#selectAllUser').click(function() {
                    $('.selectRow').prop('checked', $(this).prop('checked'));
                });

                // Delete Selected Residents
                $('#deleteSelectedResidentsBtn').click(function() {
                    var selectedIDs = [];
                    $('.selectRow:checked').each(function() {
                        selectedIDs.push($(this).data('id'));
                    });

                    if (selectedIDs.length > 0) {
                        if (confirm("Are you sure you want to delete these residents?")) {
                            deleteRecords(selectedIDs, 'residents_records');
                        }
                    } else {
                        alert("Please select at least one resident to delete.");
                    }
                });

                // Delete Selected Blotter Records
                $('#deleteSelectedBlotterBtn').click(function() {
                    var selectedIDs = [];
                    $('.selectRow:checked').each(function() {
                        selectedIDs.push($(this).data('id'));
                    });

                    if (selectedIDs.length > 0) {
                        if (confirm("Are you sure you want to delete these blotter records?")) {
                            deleteRecords(selectedIDs, 'archive_blotter');
                        }
                    } else {
                        alert("Please select at least one blotter record to delete.");
                    }
                });

                // Delete Selected Users
                $('#deleteSelectedUserBtn').click(function() {
                    var selectedIDs = [];
                    $('.selectRow:checked').each(function() {
                        selectedIDs.push($(this).data('id'));
                    });

                    if (selectedIDs.length > 0) {
                        if (confirm("Are you sure you want to delete these users?")) {
                            deleteRecords(selectedIDs, 'archive_user');
                        }
                    } else {
                        alert("Please select at least one user to delete.");
                    }
                });
            });

            // This function will delete records from the selected table
            function deleteRecords(selectedIDs, table) {
                $.ajax({
                    type: 'POST',
                    url: '../src/components/deleteArchiveSelected.php', // The PHP file to handle the request
                    data: {
                        action: 'delete',
                        ids: selectedIDs, // Send the selected IDs
                        table: table, // Send the table name dynamically
                    },
                    success: function(response) {
                        alert(response.trim()); // Show a message with the response from PHP
                        location.reload(); // Reload the page to reflect changes
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: " + error); // Log any errors to the console
                    }
                });
            }

            // Example usage: Call deleteRecords() based on the table you're working with
            function deleteFromResidentsRecords(selectedIDs) {
                deleteRecords(selectedIDs, 'residents_records'); // Call with residents_records table
            }

            function deleteFromArchiveBlotter(selectedIDs) {
                deleteRecords(selectedIDs, 'archive_blotter'); // Call with archive_blotter table
            }

            function deleteFromArchiveUser(selectedIDs) {
                deleteRecords(selectedIDs, 'archive_user'); // Call with archive_user table
            }
        </script>

        <!-- Resident Details Modal -->
        <div class="modal fade" id="residentModal" tabindex="-1" aria-labelledby="residentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="residentModalLabel">Resident Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="residentDetailsBody">
                        <!-- Resident details will be loaded here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-delete" id="deleteResidentBtn" data-id="">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blotter Details Modal -->
        <div class="modal fade" id="blotterModal" tabindex="-1" role="dialog" aria-labelledby="blotterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="blotterModalLabel">Blotter Record Details</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="blotterDetailsBody">
                        <!-- Blotter details will be loaded here dynamically -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-restore" id="restoreBlotterBtn" data-id="">Restore</button>
                        <button type="button" class="btn btn-delete" id="deleteBlotterBtn" data-id="">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Details Modal -->
        <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">User Record Details</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="userDetailsBody">
                        <!-- User details will be loaded here dynamically -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-restore" id="restoreUserBtn" data-id="">Restore</button>
                        <button type="button" class="btn btn-delete" id="deleteUserBtn" data-id="">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <script>
            function loadResidentDetails(residentID) {
                $.ajax({
                    url: '../src/components/getArchiveResidentDetails.php',
                    type: 'POST',
                    data: {
                        id: residentID // Send the resident ID to the PHP script
                    },
                    success: function(response) {
                        $('#residentDetailsBody').html(response); // Populate the modal body with the response
                        $('#residentModal').modal('show'); // Show the modal
                        $('#deleteResidentBtn').attr('data-id', residentID); // Set the resident ID on the delete button
                    },
                    error: function() {
                        $('#residentDetailsBody').html('<p>Error loading details.</p>'); // Error message
                    }
                });
            }

            $(document).on('click', '#deleteResidentBtn', function() {
                const residentID = $(this).data('id'); // Get the resident ID from the data-id attribute

                // Confirm the deletion
                if (confirm('Are you sure you want to permanently delete this resident?')) {
                    // Send the delete request via AJAX
                    $.ajax({
                        url: '../src/components/deleteArchiveResident.php', // The PHP file that will handle the deletion
                        type: 'POST',
                        data: {
                            id: residentID // Send the resident ID to the PHP script
                        },
                        success: function(response) {
                            alert(response.trim());; // Show success message
                            $('#residentModal').modal('hide'); // Close the modal after deletion
                            location.reload(); // Reload the page to reflect the changes
                        },
                        error: function() {
                            alert('Error deleting resident. Please try again.'); // Handle error
                        }
                    });
                }
            });


            function loadBlotterDetails(blotterID) {
                $.ajax({
                    url: '../src/components/getArchiveBlotterDetails.php',
                    type: 'POST',
                    data: {
                        id: blotterID
                    },
                    success: function(response) {
                        $('#blotterDetailsBody').html(response);
                        $('#blotterModal').modal('show');
                        $('#deleteBlotterBtn').attr('data-id', blotterID); // Set the blotter ID on the delete button
                        $('#restoreBlotterBtn').data('id', blotterID); // Set data-id for restore button
                    },
                    error: function() {
                        $('#blotterDetailsBody').html('<p>Error loading details.</p>');
                    }
                });
            }

            // Delete button click event
            $(document).on('click', '#deleteBlotterBtn', function() {
                const blotterID = $(this).data('id');
                if (confirm('Are you sure you want to permanently delete this blotter record?')) {
                    $.ajax({
                        url: '../src/components/deleteArchiveBlotter.php', // File for deleting the blotter record
                        type: 'POST',
                        data: {
                            id: blotterID
                        },
                        success: function(response) {
                            alert(response.trim());; // Show success message
                            $('#blotterModal').modal('hide'); // Hide modal
                            // After reload, activate the Blotter tab explicitly
                            location.reload(); // Reload page to see changes
                        },
                        error: function() {
                            alert('Error deleting record.'); // Handle error
                        }
                    });
                }
            });

            //RESTORE BLOTTER
            $(document).ready(function() {
                $('#restoreBlotterBtn').on('click', function() {
                    const blotterID = $(this).data('id'); // Get the ID from data-id attribute

                    if (!blotterID) {
                        alert("No record selected for restoration");
                        return; // Exit the function if no ID is available
                    }

                    // AJAX request to restore the record
                    $.ajax({
                        url: '../src/components/restoreBlotterRecord.php', // Your PHP script for restoring
                        type: 'POST',
                        data: {
                            id: blotterID
                        },
                        success: function(response) {
                            alert(response.trim());; // Show success or error message
                            // Optionally, refresh the table or close the modal
                            $('#blotterModal').modal('hide'); // Close the modal
                            location.reload(); // Reload the page or refresh the table
                        },
                        error: function() {
                            alert("An error occurred while trying to restore the record.");
                        }
                    });
                });
            });

            function loadUserDetails(userID) {
                $.ajax({
                    url: '../src/components/getArchiveUserDetails.php', // Path to the new PHP script
                    type: 'POST',
                    data: {
                        id: userID
                    },
                    success: function(response) {
                        $('#userDetailsBody').html(response);
                        $('#userModal').modal('show'); // Show user details modal
                        $('#deleteUserBtn').attr('data-id', userID); // Set the user ID on the delete button
                        $('#restoreUserBtn').data('id', userID); // Set the user ID on the restore button
                    },
                    error: function() {
                        $('#userDetailsBody').html('<p>Error loading details.</p>');
                    }
                });
            }

            // Delete button click event
            $(document).on('click', '#deleteUserBtn', function() {
                const userID = $(this).data('id');
                if (confirm('Are you sure you want to permanently delete this user record?')) {
                    $.ajax({
                        url: '../src/components/deleteArchiveUser.php', // Create this PHP file for deleting user
                        type: 'POST',
                        data: {
                            id: userID
                        },
                        success: function(response) {
                            alert(response.trim());; // Show success message
                            $('#userModal').modal('hide'); // Hide modal
                            // Navigate to the user tab after reload
                            window.location.hash = '#user-tab'; // This ensures the user tab is selected
                            location.reload(); // Reload page to see changes
                        },
                        error: function() {
                            alert('Error deleting record.'); // Handle error
                        }
                    });
                }
            });


            // Restore user record
            $('#restoreUserBtn').click(function() {
                var userID = $(this).data('id'); // Get the user ID from the button's data-id attribute

                $.ajax({
                    url: '../src/components/restoreUserRecord.php', // The correct PHP script location
                    type: 'POST',
                    data: {
                        id: userID
                    }, // Send the user ID in the 'id' key
                    success: function(response) {
                        alert(response.trim());; // Display the success or error message
                        $('#userModal').modal('hide'); // Hide the modal after restoration
                        location.reload(); // Reload the page to reflect changes
                    },
                    error: function() {
                        alert('Error restoring user.');
                    }
                });
            });

            // Attach click event to user rows
            $(document).on('click', '.user-row', function() {
                const userID = $(this).data('id'); // Assuming you set data-id on the row
                loadUserDetails(userID);

            });

            $(document).ready(function() {
                // Check if the URL hash is set to either the Blotter or User tab
                if (window.location.hash === '#blotter-tab') {
                    $('#blotter-tab').tab('show'); // Show Blotter tab
                } else if (window.location.hash === '#user-tab') {
                    $('#user-tab').tab('show'); // Show User tab
                }
            });
        </script>

</body>

</html>