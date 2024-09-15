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

        <!-- Two Column Layout for selecting and displaying data -->
        <div class="row flex-grow-1">
            <!-- Left Column -->
            <div class="col-md-6 p-4 scrollable-container1" style="background-color: #f7f7f7; border-right: 1px solid #ddd;">
                <div class="search-header">Resident's Records Search</div>
                <input type="text" class="form-control search-bar mb-3" placeholder="Search by name..." aria-label="Search residents">

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
            </div>

            <!-- Right Column -->
            <!-- Right Column -->
            <div class="col-md-6 p-4 d-flex flex-column right-column">
            <div class="details-header">Resident's Records Search</div>
                <div class="card resident-details-card flex-grow-1 p-3 scrollable-container2">
                    <div id="resident-details" class="d-flex justify-content-center align-items-center">
                        <span class="text-muted">Select a resident to view details</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function fetchResidentDetails(residentId) {
            // Show a loading spinner
            $("#resident-details").html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

            $.ajax({
                url: "/capstone/src/components/getResidentDetails.php",
                type: "POST",
                data: {
                    id: residentId
                },
                success: function(data) {
                    $("#resident-details").html(data);
                },
                error: function() {
                    $("#resident-details").html('<div class="text-danger">Unable to retrieve data.</div>');
                }
            });
        }
    </script>
</body>

</html>