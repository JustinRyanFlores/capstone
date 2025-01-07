<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /system/website/login/login.php");
    exit();
}
include_once "../src/components/session_handler.php";
include("../src/configs/connection.php"); // Include your database connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Kay-Anlog Sys Info | F.A.Qs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/system/src/css/navbar.css" />
    <link rel="stylesheet" href="/system/src/css/header.css" />
    <link rel="stylesheet" href="/system/src/css/dashboard.css" />
    <link rel="stylesheet" href="/system/src/css/report.css" />
    <?php include '../src/components/header.php'; ?>
</head>

<body>
    <?php include '../src/components/moderator_navbar.php'; ?>

    <div class="container-fluid main-content">
        <div class="row">
            <div class="h3 col-sm-6 col-md-6 text-start h5-sm">
                F.A.Qs
                <div class="h6" style="font-style: italic; color: grey">
                    Home / F.A.Qs
                </div>
            </div>
            <div class="col-sm-6 col-md-6 d-flex justify-content-sm-between justify-content-md-end">
                <div>
                    <?php displayDateTime(); ?>
                </div>
            </div>
        </div>

        <div class="accordion" id="faqAccordion">
            <!-- FAQ 1 -->
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link text-dark" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            How to add Records (Residents & Blotter)?
                        </button>
                    </h5>
                </div>
                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#faqAccordion">
                    <div class="card-body">
                        <iframe id="video1" width="560" height="315" src="https://www.youtube.com/embed/oLBl_Ljnpok" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h5 class="mb-0">
                        <button class="btn btn-link text-dark" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            How to access Resident's List?
                        </button>
                    </h5>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#faqAccordion">
                    <div class="card-body">
                        <iframe id="video2" width="560" height="315" src="https://www.youtube.com/embed/nTRF1_lzOyg" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="card">
                <div class="card-header" id="headingThree">
                    <h5 class="mb-0">
                        <button class="btn btn-link text-dark" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            How to filter line graphs?
                        </button>
                    </h5>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#faqAccordion">
                    <div class="card-body">
                        <iframe id="video3" width="560" height="315" src="https://www.youtube.com/embed/aQG0gaglCv0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="card">
                <div class="card-header" id="headingFour">
                    <h5 class="mb-0">
                        <button class="btn btn-link text-dark" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            What is GIS and how to use it?
                        </button>
                    </h5>
                </div>
                <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#faqAccordion">
                    <div class="card-body">
                        <iframe id="video4" width="560" height="315" src="https://www.youtube.com/embed/_FDLTQHDuss" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <!-- FAQ 5 -->
            <div class="card">
                <div class="card-header" id="headingFive">
                    <h5 class="mb-0">
                        <button class="btn btn-link text-dark" type="button" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFour">
                            Intermission
                        </button>
                    </h5>
                </div>
                <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#faqAccordion">
                    <div class="card-body">
                        <iframe id="video5" width="560" height="315" src="https://www.youtube.com/embed/ur9pqPLoDKU" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Function to stop videos on collapse
        $('#faqAccordion').on('hidden.bs.collapse', function (e) {
            const videoId = $(e.target).find('iframe').attr('id'); // Get the iframe's id
            const video = document.getElementById(videoId);
            if (video) {
                video.src = video.src; // Reset the video src to stop it
            }
        });
    </script>
</body>

</html>
