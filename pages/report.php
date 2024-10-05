<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /capstone/website/login/login.php");
    exit();
}

include('../src/configs/connection.php');

// Query to get gender distribution
$query = "SELECT gender, COUNT(*) as count FROM residents_records GROUP BY gender";
$result = $mysqlConn->query($query);

$genderData = [];
while ($row = $result->fetch_assoc()) {
    $genderData[] = [$row['gender'], (int)$row['count']];
}

// Query to get age data for grouping based on date of birth
$ageQuery = "
    SELECT 
        CASE 
            WHEN FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) BETWEEN 0 AND 12 THEN '0-12'
            WHEN FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) BETWEEN 13 AND 18 THEN '13-18'
            WHEN FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) BETWEEN 19 AND 34 THEN '19-34'
            WHEN FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) BETWEEN 35 AND 49 THEN '34-49'
            WHEN FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) BETWEEN 50 AND 64 THEN '50-64'
            WHEN FLOOR(DATEDIFF(CURDATE(), dob) / 365.25) BETWEEN 65 AND 100 THEN '65-100'
            ELSE '100+' 
        END AS age_group,
        COUNT(*) as count
    FROM residents_records
    GROUP BY age_group
";
$ageResult = $mysqlConn->query($ageQuery);

$ageGroups = [];
while ($row = $ageResult->fetch_assoc()) {
    $ageGroups[$row['age_group']] = (int)$row['count'];
}

// Ensure all age groups exist, even if they have no records
$ageGroups = array_merge([
    '0-12' => 0,
    '13-18' => 0,
    '19-34' => 0,
    '34-49' => 0,
    '50-64' => 0,
    '65-100' => 0,
    '100+' => 0,
], $ageGroups);

// Query for population growth (grouping by date)
$populationQuery = "
    SELECT DATE(created_at) as date, COUNT(*) as count
    FROM residents_records
    GROUP BY DATE(created_at)
    ORDER BY date ASC
";
$populationResult = $mysqlConn->query($populationQuery);

$populationData = [];
while ($row = $populationResult->fetch_assoc()) {
    $populationData[] = [$row['date'], (int)$row['count']];
}

// Query for illness data
$illnessQuery = "
    SELECT illness, COUNT(*) as count 
    FROM residents_records 
    GROUP BY illness
";
$illnessResult = $mysqlConn->query($illnessQuery);

$illnessData = [];
while ($row = $illnessResult->fetch_assoc()) {
    $illnessData[] = [$row['illness'], (int)$row['count']];
}

// Query for medication data
$medicationQuery = "
    SELECT medication, COUNT(*) as count 
    FROM residents_records 
    GROUP BY medication
";
$medicationResult = $mysqlConn->query($medicationQuery);

$medicationData = [];
while ($row = $medicationResult->fetch_assoc()) {
    $medicationData[] = [$row['medication'], (int)$row['count']];
}

// Query for delivery type distribution
$deliveryQuery = "
    SELECT type_of_delivery, COUNT(*) as count 
    FROM residents_records 
    GROUP BY type_of_delivery
";
$deliveryResult = $mysqlConn->query($deliveryQuery);

$deliveryData = [];
while ($row = $deliveryResult->fetch_assoc()) {
    $deliveryData[] = [$row['type_of_delivery'], (int)$row['count']];
}

// Query for disability distribution
$disabilityQuery = "
    SELECT disability, COUNT(*) as count 
    FROM residents_records 
    GROUP BY disability
";
$disabilityResult = $mysqlConn->query($disabilityQuery);

$disabilityData = [];
while ($row = $disabilityResult->fetch_assoc()) {
    $disabilityData[] = [$row['disability'], (int)$row['count']];
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
    <link rel="stylesheet" href="/capstone/src/css/navbar.css" />
    <link rel="stylesheet" href="/capstone/src/css/header.css" />
    <link rel="stylesheet" href="/capstone/src/css/dashboard.css" />
    <link rel="stylesheet" href="/capstone/src/css/report.css" />
    <?php include '../src/components/header.php'; ?>

    <!-- Google Charts API -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['corechart', 'controls'] });
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            drawPopulationLineChart();
            drawGenderPieChart();
            drawAgeHistogram();
            drawIllnessBarChart();
            drawMedicationBarChart();
            drawDeliveryPieChart();
            drawDisabilityBarChart();
        }

        function drawGenderPieChart() {
            var data = google.visualization.arrayToDataTable([
                ['Gender', 'Count'],
                <?php
                foreach ($genderData as $data) {
                    echo "['" . $data[0] . "', " . $data[1] . "],";
                }
                ?>
            ]);

            var options = { 
                responsive: true,
                title: 'Gender Distribution of Residents',
                is3D: true,
                legend: { position: 'bottom' },
                chartArea: { width: '85%', height: '75%' },
            };

            var chart = new google.visualization.PieChart(document.getElementById('genderPieChart'));
            chart.draw(data, options);
        }

        function drawAgeHistogram() {
            var data = google.visualization.arrayToDataTable([
                ['Age Group', 'Count'],
                ['0-12', <?php echo (int)$ageGroups['0-12']; ?>],
                ['13-18', <?php echo (int)$ageGroups['13-18']; ?>],
                ['19-34', <?php echo (int)$ageGroups['19-34']; ?>],
                ['34-49', <?php echo (int)$ageGroups['34-49']; ?>],
                ['50-64', <?php echo (int)$ageGroups['50-64']; ?>],
                ['65-100', <?php echo (int)$ageGroups['65-100']; ?>],
                ['100+', <?php echo (int)$ageGroups['100+']; ?>]
            ]);

            var options = {
                responsive: true,
                title: 'Age Distribution of Residents',
                legend: { position: 'none' },
                chartArea: { width: '85%', height: '75%' },
                hAxis: {
                    title: 'Age Groups',
                    minValue: 0
                },
                vAxis: {
                    title: 'Number of Residents',
                    format: '0', // This ensures that only whole numbers are displayed on the Y-axis
                    viewWindow: {
                        min: 0 // This can ensure the Y-axis starts at 0
                    }
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('ageHistogram'));
            chart.draw(data, options);
        }

        function drawPopulationLineChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date'); // Use 'date' as the column type
            data.addColumn('number', 'Number of Residents');
            
            data.addRows([
                <?php
                foreach ($populationData as $data) {
                    // Convert the PHP date string to a format that can be interpreted as a Date object
                    $dateParts = explode('-', $data[0]); // Split the date into year, month, day
                    echo "[new Date(" . $dateParts[0] . ", " . ($dateParts[1] - 1) . ", " . $dateParts[2] . "), " . $data[1] . "],";
                }
                ?>
            ]);

            
            var dashboard = new google.visualization.Dashboard(
                document.getElementById('populationDashboard')
            );

            var control = new google.visualization.ControlWrapper({
                controlType: 'ChartRangeFilter',
                containerId: 'filter_div',
                options: {
                    filterColumnLabel: 'Date',
                    ui: { 
                        chartType: 'LineChart', 
                        chartOptions: { 
                            chartArea: { width: '60%' }, 
                            hAxis: { format: 'yyyy-MM-dd' } 
                        } 
                    }
                }
            });

            var chart = new google.visualization.ChartWrapper({
                chartType: 'LineChart',
                containerId: 'populationLineChart',
                options: {
                    title: 'Population Growth Over Time',
                    hAxis: { title: 'Date', format: 'yyyy-MMM-dd' }, // Format the x-axis as dates
                    vAxis: { title: 'Number of Residents', format: '0' },
                    chartArea: { width: '85%', height: '70%' },
                }
            });

            dashboard.bind(control, chart);
            dashboard.draw(data);
        }

        function drawIllnessBarChart() {
        var data = google.visualization.arrayToDataTable([
            ['Illness', 'Count'],
            <?php
            foreach ($illnessData as $data) {
                echo "['" . $data[0] . "', " . $data[1] . "],";
            }
            ?>
        ]);

        var options = {
            responsive: true,
            title: 'Illness Distribution',
            legend: { position: 'none' },
            chartArea: { width: '85%', height: '75%' },
            hAxis: {
                title: 'Illnesses',  // X-axis shows the categories (illnesses)
                slantedText: true,
                slantedTextAngle: 20 // Rotate labels if needed for long illness names
            },
            vAxis: {
                title: 'Number of Cases',  // Y-axis shows the count
                minValue: 0,
                format: '0'
            }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('illnessBarChart'));
        chart.draw(data, options);
    }

    function drawMedicationBarChart() {
        var data = google.visualization.arrayToDataTable([
            ['Medication', 'Count'],
            <?php
            foreach ($medicationData as $data) {
                echo "['" . $data[0] . "', " . $data[1] . "],";
            }
            ?>
        ]);

        var options = {
            responsive: true,
            title: 'Medication Distribution',
            legend: { position: 'none' },
            chartArea: { width: '85%', height: '75%' },
            hAxis: {
                title: 'Medications',  // X-axis shows the categories (medications)
                slantedText: true,
                slantedTextAngle: 20 // Rotate labels if needed for long medication names
            },
            vAxis: {
                title: 'Number of Cases',  // Y-axis shows the count
                minValue: 0,
                format: '0'
            }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('medicationBarChart'));
        chart.draw(data, options);
    }

    function drawDeliveryPieChart() {
        var data = google.visualization.arrayToDataTable([
            ['Delivery Type', 'Count'],
            <?php
            foreach ($deliveryData as $data) {
                echo "['" . $data[0] . "', " . $data[1] . "],";
            }
            ?>
        ]);

        var options = {
            responsive: true,
            title: 'Type of Delivery Distribution',
            is3D: true,
            legend: { position: 'bottom' },
            chartArea: { width: '85%', height: '75%' },
        };

        var chart = new google.visualization.PieChart(document.getElementById('deliveryPieChart'));
        chart.draw(data, options);
    }

    function drawDisabilityBarChart() {
        var data = google.visualization.arrayToDataTable([
            ['Disability', 'Count'],
            <?php
            foreach ($disabilityData as $data) {
                echo "['" . $data[0] . "', " . $data[1] . "],";
            }
            ?>
        ]);

        var options = {
            responsive: true,
            title: 'Disability Distribution',
            legend: { position: 'none' },
            chartArea: { width: '85%', height: '75%' },
            hAxis: {
                title: 'Disabilities',
                slantedText: true,
                slantedTextAngle: 20
            },
            vAxis: {
                title: 'Number of Cases',
                minValue: 0,
                format: '0'
            }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('disabilityBarChart'));
        chart.draw(data, options);
    }


        window.addEventListener('resize', function () {
            drawCharts();
        });

    </script>
</head>

<body>
    <?php include '../src/components/moderator_navbar.php'; ?>

    <div class="container-fluid main-content">
        <div class="row">
            <div class="h3 col-sm-6 col-md-6 text-start h5-sm">
                Report
                <div class="h6" style="font-style: italic; color: grey">
                    Home/Report
                </div>
            </div>
            <div class="col-sm-6 col-md-6 d-flex justify-content-sm-between justify-content-md-end">
                <div>
                    <?php displayDateTime(); ?>
                </div>
            </div>
        </div>

        <!-- Demographics Section -->
        <div class="container mt-4">
            <div class="demographics-header">
                <div class="header-container">
                    <h4>Demographics</h4>
                    <button class="collapse-button" data-toggle="collapse" href="#demographicsSection" role="button" aria-expanded="false" aria-controls="demographicsSection">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>

            <div class="collapse show" id="demographicsSection">
                <div class="scrollable-graphs mt-3">

                    <!-- Population Growth Line Chart -->
                    <div class="chart-container">
                        <h5>Population Growth Over Time</h5>
                        <div id="populationDashboard">
                            <div id="filter_div" style="height: 100px;"></div>
                            <div id="populationLineChart" style="height: 400px;"></div>
                        </div>
                    </div>

                    <!-- Gender Pie Chart -->
                    <div class="chart-container">
                        <h5>Gender Distribution</h5>
                        <div id="genderPieChart" style="width: 100%; height: 500px;"></div>
                    </div>

                    <!-- Age Distribution Histogram -->
                    <div class="chart-container">
                        <h5>Age Distribution</h5>
                        <div id="ageHistogram" style="width: 100%; height: 500px;"></div>
                    </div>

                </div>
            </div>
        </div>
        
        <!-- Health and Social Issues Section -->
        <div class="container mt-4">
            <div class="demographics-header">
                <div class="header-container">
                    <h4>Health and Social Issues</h4>
                    <button class="collapse-button" data-toggle="collapse" href="#healthSection" role="button" aria-expanded="false" aria-controls="healthSection">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>

            <div class="collapse show" id="healthSection">
                <div class="scrollable-graphs mt-3">

                    <!-- Illness Distribution Bar Chart -->
                    <div class="chart-container">
                        <h5>Illness Distribution</h5>
                        <div id="illnessBarChart" style="width: 100%; height: 500px;"></div>
                    </div>

                    <!-- Medication Distribution Bar Chart -->
                    <div class="chart-container">
                        <h5>Medication Distribution</h5>
                        <div id="medicationBarChart" style="width: 100%; height: 500px;"></div>
                    </div>

                    <!-- Disability Distribution Bar Chart -->
                    <div class="chart-container">
                        <h5>Disability Distribution</h5>
                        <div id="disabilityBarChart" style="width: 100%; height: 400px;"></div>
                    </div>

                    <!-- Type of Delivery Distribution Pie Chart -->
                    <div class="chart-container">
                        <h5>Type of Delivery Distribution</h5>
                        <div id="deliveryPieChart" style="width: 100%; height: 400px;"></div>
                    </div>

                </div>
            </div>
        </div>
        
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
