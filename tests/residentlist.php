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
    <?php include '/xampp/htdocs/capstone/src/components/header.php'; ?>
    <style>
        body,
        html {
            height: 100%;
        }

        .main-content {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .content-row {
            flex-grow: 1;
        }
    </style>
</head>


<body>
    <?php include '/xampp/htdocs/capstone/src/components/moderator_navbar.php'; ?>
    <div class="container-fluid main-content">
        <div class="row">
            <div class="h3 col-sm-6 col-md-6 text-start h5-sm">
                Resident List
                <div class="h6" style="font-style: italic; color: grey">
                    Resident List
                </div>
            </div>
            <div class="col-sm-6 col-md-6 d-flex justify-content-sm-between justify-content-md-end">
                <div>
                    <?php displayDateTime(); ?>
                </div>
            </div>
        </div>
        <!-- Two Column Layout for selecting and displaying data -->
        <div class="container-fluid content-row d-flex">
            <div class="row flex-fill">
                <div class="col-sm-6 border p-3 d-flex flex-column" style="background-color: #f7f7f7; border-right: 1px solid #ddd; height: 100%;">
                    <!-- Left column with border -->
                    <h5>Resident's Records Search</h5>
                    <input type="text" class="form-control mb-3" placeholder="Type Here to Search...">
                    <ul class="list-group flex-grow-1">
                        <div class="list-group" id="resident-list">
                            <?php
                            $conn = new mysqli("localhost", "root", "", "residents_db");
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }
                            $query = "SELECT id, first_name, last_name FROM residents_records";
                            $result = $conn->query($query);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<li class="list-group-item list-group-item-action" onclick="fetchResidentDetails(' . $row['id'] . ')">' . $row['first_name'] . ' ' . $row['last_name'] . '</li>';
                                }
                            } else {
                                echo "<li class='list-group-item'>No residents found</li>";
                            }
                            $conn->close();
                            ?>
                        </div>
                    </ul>
                </div>
                <div class="col-sm-6 border p-3 d-flex flex-column" style="height: 100%;">
                    <!-- Right column with border -->
                    <h5>Resident Details</h5>
                    <div class="card flex-grow-1">
                        <dclass="card-body">
                            <div id="resident-details">
                                ????
                                <!-- Details will be loaded here using AJAX -->
                            </div>
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
                    $("#resident-details").html("Unable to retrieve data.");
                }
            });
        }
    </script>
</body>

</html>