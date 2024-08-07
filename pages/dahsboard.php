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
</head>

<body>
    <div class="container-fluid main-content">
        <div class="row">
            <div class="col-md-3">
                <?php include '/xampp/htdocs/capstone/src/components/moderator_navbar.php'; ?>
            </div>
            <div class="col-md-9    ">
                <div class="date-time-container">
                    <h1>Dashboard</h1>
                    <?php displayDateTime(); ?>
                </div>
                <!-- Add your main content here -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>