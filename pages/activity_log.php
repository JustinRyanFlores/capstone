<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /system/website/login/login.php");
    exit();
}
include_once "../src/components/session_handler.php";
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
                Activity Log
                <div class="h6" style="font-style: italic; color: grey">
                    Admin Panel / Activity Log
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
            <div class="col-md-12 d-flex align-items-center">
                <form method="GET" action="activity_log.php" class="d-flex w-100">
                    <input type="text"
                        class="form-control"
                        name="search"
                        placeholder="Type Here to Search..."
                        style="max-width: 300px;"
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />

                    <!-- Search Button -->
                    <button type="submit" class="btn btn-primary ml-2" style="display: flex; align-items: center;">
                        <i class="fas fa-search"></i>
                    </button>

                    <!-- Reset Button -->
                    <a href="activity_log.php" class="btn btn-secondary ml-2" style="display: flex; align-items: center;">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </form>
            </div>
        </div>

        <div class="mt-3">
            <a href="admin_panel.php">
                <button type="button" class="btn btn-primary">Back to Admin Panel</button>
            </a>
        </div>
        <!-- User Records Table -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="user-table-container">
                    <div class="table-responsive">
                    <table class="table table-custom">
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
                            // Pagination settings
                            $limit = 8; // Number of records per page
                            if (isset($_GET['page'])) {
                                $page = $_GET['page'];
                            } else {
                                $page = 1;
                            }
                            $start_from = ($page - 1) * $limit;

                            // Initialize search query
                            $search_query = "";
                            if (isset($_GET['search'])) {
                                $search_query = trim($_GET['search']);
                            }

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

                    <!-- Pagination and Info -->
                    <div class="pagination-container">
                        <div class="pagination-info">
                            <?php
                            // Fetch total records for "Showing X to Y of Z entries"
                            $query_total = "SELECT COUNT(*) FROM activity_log";
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
                            $total_pages = ceil($total_records / $limit);
                            $search_param = !empty($search_query) ? "&search=" . urlencode($search_query) : "";

                            if ($page > 1) {
                                echo "<li class='page-item'><a class='page-link' href='?page=" . ($page - 1) . $search_param . "'>Previous</a></li>";
                            }

                            for ($i = 1; $i <= $total_pages; $i++) {
                                if ($i == $page) {
                                    echo "<li class='page-item active'><a class='page-link' href='?page=$i$search_param'>$i</a></li>";
                                } else {
                                    echo "<li class='page-item'><a class='page-link' href='?page=$i$search_param'>$i</a></li>";
                                }
                            }

                            if ($page < $total_pages) {
                                echo "<li class='page-item'><a class='page-link' href='?page=" . ($page + 1) . $search_param . "'>Next</a></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
<script>

</script>


</html>