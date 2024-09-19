<?php
include("../src/configs/connection.php"); // Include your database connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>User Records</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/capstone/src/css/navbar.css" />
    <link rel="stylesheet" href="/capstone/src/css/header.css" />
    <link rel="stylesheet" href="/capstone/src/css/admin_panel.css" />
    <?php include '/xampp/htdocs/capstone/src/components/header.php'; ?>
</head>
<style>
    .text-end #exitModalUser {
        background-color: #1c2455 ; 
        border-color: #1c2455;     
        color: #ffffff;             
    }

    .text-end #exitModalUser:hover {
        background-color: #ffffff; 
        border-color: #1c2455;    
        color: #1c2455;          
    }

    .text-end #addModalUser {
        background-color: #6c757d;          
        border-color: #5a6268;
        color: #ffffff;
    }

    .text-end #addModalUser:hover {
        background-color: #ffffff;
        border-color: #5a6268; 
        color: #5a6268; 
    }
</style>
<body>
    <?php include '/xampp/htdocs/capstone/src/components/moderator_navbar.php'; ?>
    <div class="container-fluid main-content">
        <div class="row">
            <div class="h3 col-sm-6 col-md-6 text-start h5-sm">
                Admin Panel
                <div class="h6" style="font-style: italic; color: grey">
                    Home / Admin Panel
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
                <input type="text" class="form-control" placeholder="Type Here to Search..." style="max-width: 300px;" />
                <div class="action-buttons d-flex mt-3">
                    <button class="btn btn-new-user" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
                </div>

            </div>
        </div>

        <!-- User Records Table -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="user-table-container">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>UserID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Pagination settings
                            $limit = 8; // Number of records per page
                            if (isset($_GET['page'])) {
                                $page = $_GET['page'];
                            } else {
                                $page = 1;
                            }
                            $start_from = ($page - 1) * $limit;

                            // Fetch data from the database
                            $query = "SELECT user_id, fname, lname, role, contact_no, address, username, password
                                    FROM user 
                                    LIMIT $start_from, $limit";
                            $result = $mysqlConn3->query($query);

                            // Loop through the records
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr data-id='{$row['user_id']}' 
                                                data-name-contact='{$row['contact_no']}' 
                                                data-address='{$row['address']}' 
                                                data-username='{$row['username']}' 
                                                data-password='{$row['password']}'>
                                        <td>{$row['user_id']}</td>
                                        <td>{$row['fname']}</td>
                                        <td>{$row['lname']}</td>
                                        <td>{$row['role']}</td>
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
                            $query_total = "SELECT COUNT(*) FROM user";
                            $result_total = $mysqlConn3->query($query_total);
                            $row_total = $result_total->fetch_row();
                            $total_records = $row_total[0];
                            $start_entry = ($page - 1) * $limit + 1;
                            $end_entry = min($start_entry + $limit - 1, $total_records);

                            echo "Showing $start_entry to $end_entry of $total_records entries";
                            ?>
                        </div>
                        <ul class="pagination">
                            <?php
                            // Generate pagination links
                            $total_pages = ceil($total_records / $limit);

                            if ($page > 1) {
                                echo "<li class='page-item'><a class='page-link' href='?page=" . ($page - 1) . "'>Previous</a></li>";
                            }

                            for ($i = 1; $i <= $total_pages; $i++) {
                                if ($i == $page) {
                                    echo "<li class='page-item active'><a class='page-link' href='?page=$i'>$i</a></li>";
                                } else {
                                    echo "<li class='page-item'><a class='page-link' href='?page=$i'>$i</a></li>";
                                }
                            }

                            if ($page < $total_pages) {
                                echo "<li class='page-item'><a class='page-link' href='?page=" . ($page + 1) . "'>Next</a></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="newFirstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="newFirstName" name="firstName" required>
                            </div>
                            <div class="mb-3">
                                <label for="newLastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="newLastName" name="lastName" required>
                            </div>
                            <div class="mb-3">
                                <label for="newContactNumber" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="newContactNumber" name="contactNumber" required>
                            </div>
                            <div class="mb-3">
                                <label for="newAddress" class="form-label">Address</label>
                                <input type="text" class="form-control" id="newAddress" name="address" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="newRole" class="form-label">Role</label>
                                <input type="text" class="form-control" id="newRole" name="role" required>
                            </div>
                            <div class="mb-3">
                                <label for="newUsername" class="form-label">Username</label>
                                <input type="text" class="form-control" id="newUsername" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="newPassword" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="modalForm">
                    <div class="row">
                        <!-- Left side: Basic Information -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userId" class="form-label">UserID</label>
                                <input type="text" class="form-control" id="userId" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="contactNumber" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contactNumber" readonly>
                            </div>
                        </div>

                        <!-- Right side: Additional Information -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" class="form-control" id="role" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="text" class="form-control" id="password" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <button type="button" class="btn btn-danger" id="exitModalUser" data-bs-dismiss="modal">Exit</button>
                        <button type="button" class="btn btn-primary" id="addModalUser" data-bs-dismiss="modal">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Event handler for table row click (already provided)
    $('tbody tr').click(function() {
        var user_id = $(this).data('id');
        var fname = $(this).find('td:eq(1)').text();
        var lname = $(this).find('td:eq(2)').text();
        var contact_no = $(this).data('name-contact');
        var address = $(this).data('address');
        var username = $(this).data('username');
        var password = $(this).data('password');
        var role = $(this).find('td:eq(3)').text();

        $('#userId').val(user_id);
        $('#firstName').val(fname);
        $('#lastName').val(lname);
        $('#contactNumber').val(contact_no);
        $('#address').val(address);
        $('#username').val(username);
        $('#password').val(password);
        $('#role').val(role);

        $('#viewModal').modal('show');
    });

    // Show the "Add New User" modal
    $('.btn-new-user').on('click', function() {
        $('#addUserModal').modal('show');
    });

    // Handle form submission for adding a user
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        // Get the values of password and confirm password
        var password = $('#newPassword').val();
        var confirmPassword = $('#confirmPassword').val();

        // Check if the passwords match
        if (password !== confirmPassword) {
            alert('Passwords do not match. Please try again.');
            return; // Stop the form submission
        }

        // If passwords match, proceed with AJAX request
        $.ajax({
            url: '../src/components/add_user_panel.php', // Adjust path as needed
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert(response); // Show success message
                $('#addUserModal').modal('hide'); // Hide the modal
                location.reload(); // Optionally, refresh the page or update the user table
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error);
            }
        });
    });
});
</script>


</html>