<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /system/website/login/login.php");
    exit();
}

include("../src/configs/connection.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Kay-Anlog Sys Info | Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/system/src/css/navbar.css" />
    <link rel="stylesheet" href="/system/src/css/header.css" />
    <link rel="stylesheet" href="/system/src/css/admin_panel.css" />
    <?php include '../src/components/header.php'; ?>
</head>
<body>
    <?php include '../src/components/moderator_navbar.php'; ?>
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

        <?php
// Initialize search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}
?>

<!-- Tabs for Admin Panel and Activity Log -->
<ul class="nav nav-tabs" id="adminTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="adminPanelTab" data-bs-toggle="tab" href="#adminPanel" role="tab" aria-controls="adminPanel" aria-selected="true">Admin Panel</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="activityLogTab" data-bs-toggle="tab" href="#activityLog" role="tab" aria-controls="activityLog" aria-selected="false">Activity Log</a>
    </li>
</ul>


        <!-- Search and Buttons -->
        <div class="row mt-4 search-bar-container">
            <div class="col-md-12">
                <form method="GET" action="admin_panel.php">
                    <input type="text" name="search" class="form-control" placeholder="Type Here to Search..." style="max-width: 300px;" value="<?php echo htmlspecialchars($search_query); ?>" />
                    <div class="action-buttons d-flex mt-3">
                        <button type="button" class="btn btn-new-user" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
                    </div>
                </form>
            </div>
        </div>


    </div>

    <!-- Activity Log Tab -->
    <div class="tab-pane fade" id="activityLog" role="tabpanel" aria-labelledby="activityLogTab">
        <div class="row mt-4">
            <!-- Search Bar -->
            <div class="col-md-12">
            <form method="GET" id="activityLogSearchForm">
                <input type="text" id="searchActivityLog" class="form-control mb-3" placeholder="Search Activity Log..." onkeyup="filterTable('activityLogTable', this.value)" style="max-width: 300px;">
            </form>
            </div>
            <div class="col-md-12">
                <div class="user-table-container">
                    <table class="table table-custom" id="activityLogTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Time Logged In</th>
                                <th>Time Logged Out</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Pagination settings for Activity Log
                            $limit = 8; // Number of records per page
                            if (isset($_GET['activityPage'])) {
                                $activityPage = $_GET['activityPage'];
                            } else {
                                $activityPage = 1;
                            }
                            $start_from = ($activityPage - 1) * $limit;

                        // Get the search query specifically for the Activity Log search
                        $search_query = isset($_GET['search_activity_log']) ? $_GET['search_activity_log'] : '';


                            // Fetch data from the database for Activity Log
                            if (!empty($search_query)) {
                                $query = "SELECT id, name, login_time, logout_time, date
                                        FROM activity_log
                                        WHERE name LIKE '%$search_query%' 
                                        OR login_time LIKE '%$search_query%' 
                                        OR logout_time LIKE '%$search_query%'
                                        OR date LIKE '%$search_query%' 
                                        ORDER BY id DESC
                                        LIMIT $start_from, $limit";
                            } else {
                                $query = "SELECT id, name, login_time, logout_time, date
                                        FROM activity_log
                                        ORDER BY id DESC
                                        LIMIT $start_from, $limit";
                            }

                            $result = $mysqlConn3->query($query);

                            // Loop through the records for Activity Log
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['name']}</td>
                                        <td>{$row['login_time']}</td>
                                        <td>{$row['logout_time']}</td>
                                        <td>{$row['date']}</td>
                                    </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>

                    <!-- Pagination and Info for Activity Log -->
                    <div class="pagination-container">
                        <div class="pagination-info">
                            <?php
                            // Fetch total records for Activity Log
                            $query_total = "SELECT COUNT(*) FROM activity_log";
                            $result_total = $mysqlConn3->query($query_total);
                            $row_total = $result_total->fetch_row();
                            $total_records = $row_total[0];
                            $start_entry = ($activityPage - 1) * $limit + 1;
                            $end_entry = min($start_entry + $limit - 1, $total_records);

                            echo "Showing $start_entry to $end_entry of $total_records entries";
                            ?>
                        </div>
                        <ul class="pagination">
                        <?php
                        $total_pages = ceil($total_records / $limit);
                        $search_param = !empty($search_query) ? "&search=" . urlencode($search_query) : "";

                        // Ensure #activityLog is added to the URL so the tab stays active
                        $tab_param = "#activityLog";

                        if ($activityPage > 1) {
                            echo "<li class='page-item'><a class='page-link' href='?activityPage=" . ($activityPage - 1) . $search_param . $tab_param . "'>Previous</a></li>";
                        }

                        for ($i = 1; $i <= $total_pages; $i++) {
                            if ($i == $activityPage) {
                                echo "<li class='page-item active'><a class='page-link' href='?activityPage=$i$search_param$tab_param'>$i</a></li>";
                            } else {
                                echo "<li class='page-item'><a class='page-link' href='?activityPage=$i$search_param$tab_param'>$i</a></li>";
                            }
                        }

                        if ($activityPage < $total_pages) {
                            echo "<li class='page-item'><a class='page-link' href='?activityPage=" . ($activityPage + 1) . $search_param . $tab_param . "'>Next</a></li>";
                        }
                        ?>
                    </ul>
                    </div>
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
                                <input type="text" class="form-control" id="newContactNumber" name="contactNumber" 
                                    required pattern="^\d{11}$" title="Contact number must be 11 digits">
                            </div>
                            <div class="mb-3">
                                <label for="newAddress" class="form-label">Address</label>
                                <input type="text" class="form-control" id="newAddress" name="address" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="newRole" class="form-label">Role</label>
                                <select class="form-control" id="newRole" name="role" required>
                                    <option value="" disabled selected>Select a role</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Employee">Employee</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="newUsername" class="form-label">Username</label>
                                <input type="text" class="form-control" id="newUsername" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="newPassword" name="password" 
                                    required minlength="8" pattern="(?=.*\d).{8,}" 
                                    title="Password must be at least 8 characters long and contain at least one number">
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
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <button type="button" class="btn btn-secondary" id="exitModalUser" data-bs-dismiss="modal">Exit</button>
                        <button type="button" class="btn btn-danger" id="deleteBtn">Archive</button>
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
    // Function to bind click event
    function bindRowClick() {
    $('tbody tr').click(function() {
        // Check if the Admin Panel Tab is active
        if ($('#adminPanelTab').hasClass('active')) {
            var user_id = $(this).data('id');
            var fname = $(this).find('td:eq(1)').text();
            var lname = $(this).find('td:eq(2)').text();
            var contact_no = $(this).data('name-contact');
            var address = $(this).data('address');
            var username = $(this).data('username');
            var password = $(this).data('password');
            var role = $(this).find('td:eq(3)').text();

            // Set the modal inputs
            $('#userId').val(user_id);
            $('#firstName').val(fname);
            $('#lastName').val(lname);
            $('#contactNumber').val(contact_no);
            $('#address').val(address);
            $('#username').val(username);
            $('#password').val(password);
            $('#role').val(role);

            // Show the modal
            $('#viewModal').modal('show');
        }
    });
}

// Bind the row click event immediately on page load
$(document).ready(function() {
    bindRowClick();
});

// Ensure the row click event is re-bound when the Admin Panel tab is shown
$('#adminPanelTab').on('shown.bs.tab', function () {
    bindRowClick();  // This ensures the click event is applied whenever the Admin Panel tab is active
});
    

    // Rebind click event after search
    $('input[name="search"]').on('keyup', function() {
        let searchValue = $(this).val();
        $.ajax({
            url: 'admin_panel.php',
            method: 'GET',
            data: { search: searchValue },
            success: function(response) {
                $('tbody').html($(response).find('tbody').html());
                bindRowClick(); // Rebind click event after updating tbody
            }
        });
    });

    // Show the "Add New User" modal
    $('.btn-new-user').on('click', function() {
        $('#addUserModal').modal('show');
    });

// Delete button click event
$('#deleteBtn').on('click', function() {
    let userId = $('#userId').val();

    $.ajax({
        url: '../src/components/delete_user_panel.php', // Path to your PHP script
        method: 'POST',
        data: { userId: userId },
        success: function(response) {
            console.log(response); // Log the response for debugging
            if (response.trim() === 'Record successfully moved and deleted.') {
                $('tr[data-id="' + userId + '"]').remove(); // Remove the row from the table
                alert('Record successfully moved to archive.');
            } else {
                alert('Failed to move record to archive.');
            }
            $('#viewModal').modal('hide');
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText); // Log the server response
            alert('An error occurred: ' + error);
        }
    });
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
                alert(response.trim()); // Show success message
                $('#addUserModal').modal('hide'); // Hide the modal
                location.reload(); // Optionally, refresh the page or update the user table
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error);
            }
        });
    });
});


    // JavaScript function for filtering table rows
    function filterTable(tableId, searchValue) {
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');
        const filter = searchValue.toLowerCase();

        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let rowMatches = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].innerText.toLowerCase().includes(filter)) {
                    rowMatches = true;
                    break;
                }
            }

            rows[i].style.display = rowMatches ? '' : 'none';
        }
    }


    document.addEventListener('DOMContentLoaded', function () {
    // Check if the hash is present in the URL
    var tabHash = window.location.hash;
    
    if (tabHash === '#activityLog') {
        // Activate the Activity Log tab using Bootstrap's Tab component
        var activityLogTab = new bootstrap.Tab(document.querySelector('#activityLogTab'));
        activityLogTab.show(); // This will make the Activity Log tab active
    } else {
        // If the hash is not #activityLog, ensure Admin Panel tab is active by default
        var adminPanelTab = new bootstrap.Tab(document.querySelector('#adminPanelTab'));
        adminPanelTab.show();
    }
});
</script>


</html>
