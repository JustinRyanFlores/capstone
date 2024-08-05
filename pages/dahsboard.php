<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Web Application</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/capstone/src/css/navbar.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <?php include '/xampp/htdocs/capstone/src/components/date_and_time.php'; ?>
    <style>
        .date-time-container {
            position: absolute;
            top: 10px;
            right: 20px;
            text-align: right;
        }

        .date-time-component {
            display: inline-block;
            font-size: 16px;
        }

        .date-time-component p {
            margin: 0;
            padding: 0;
        }

        .date-time-component .date {
            margin-bottom: 2px;

        }

        @media (max-width: 768px) {
            .date-time-container {
                position: static;
                text-align: center;
                margin-top: 10px;
            }
        }

        @media (max-width: 576px) {
            .date-time-container {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <?php include '/xampp/htdocs/capstone/src/components/moderator_navbar.php'; ?>
            </div>
            <div class="col-md-9">
                <h1>Dashboard</h1>
                <!-- Your main content goes here -->
            </div>
        </div>
        <div class="date-time-container">
            <?php displayDateTime(); ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>