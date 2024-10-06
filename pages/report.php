<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /capstone/website/login/login.php");
    exit();
}

include('../src/configs/connection.php');

// Query to get gender distribution
$query = "SELECT gender, COUNT(*) as count FROM residents_records WHERE gender IS NOT NULL AND gender != 'N/A' AND gender != '' GROUP BY gender";
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
    WHERE illness IS NOT NULL AND illness != 'N/A' AND illness != '' 
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
    WHERE medication IS NOT NULL AND medication != 'N/A' AND medication != '' 
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
    WHERE type_of_delivery IS NOT NULL AND type_of_delivery != 'N/A' AND type_of_delivery != '' 
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
    WHERE disability IS NOT NULL AND disability != 'N/A' AND disability != '' 
    GROUP BY disability
";
$disabilityResult = $mysqlConn->query($disabilityQuery);

$disabilityData = [];
while ($row = $disabilityResult->fetch_assoc()) {
    $disabilityData[] = [$row['disability'], (int)$row['count']];
}

// Query for pwd over the years
$pwdQuery = "
    SELECT DATE(created_at) as date, COUNT(*) as count
    FROM residents_records
    WHERE pwd = '1'
    GROUP BY DATE(created_at)
    ORDER BY date ASC
";
$pwdResult = $mysqlConn->query($pwdQuery);

$pwdData = [];
while ($row = $pwdResult->fetch_assoc()) {
    $pwdData[] = [$row['date'], (int)$row['count']];
}

// Query for teen pregnancy over the years
$teenPregnancyQuery = "
    SELECT DATE(created_at) as date, COUNT(*) as count
    FROM residents_records
    WHERE teen_pregnancy = '1'
    GROUP BY DATE(created_at)
    ORDER BY date ASC
";
$teenPregnancyResult = $mysqlConn->query($teenPregnancyQuery);

$teenPregnancyData = [];
while ($row = $teenPregnancyResult->fetch_assoc()) {
    $teenPregnancyData[] = [$row['date'], (int)$row['count']];
}

// Query to get counts for ALS and OSY
$alsOsyQuery = "
    SELECT 
        SUM(CASE WHEN als = 1 THEN 1 ELSE 0 END) AS als_count,
        SUM(CASE WHEN osy = 1 THEN 1 ELSE 0 END) AS osy_count
    FROM residents_records
";
$alsOsyResult = $mysqlConn->query($alsOsyQuery);
$alsOsyData = $alsOsyResult->fetch_assoc();

// Ensure ALS and OSY counts are integers
$alsCount = (int)$alsOsyData['als_count'];
$osyCount = (int)$alsOsyData['osy_count'];

// Query to get counts for educational attainment
$educationalAttainmentQuery = "
    SELECT educational_attainment, COUNT(*) AS count
    FROM residents_records
    WHERE educational_attainment IS NOT NULL AND educational_attainment != 'N/A' AND educational_attainment != '' 
    GROUP BY educational_attainment
";
$educationalAttainmentResult = $mysqlConn->query($educationalAttainmentQuery);

$educationalData = [];
while ($row = $educationalAttainmentResult->fetch_assoc()) {
    $educationalData[] = [$row['educational_attainment'], (int)$row['count']];
}

// Fetch ALS Data
$alsQuery = "
    SELECT DATE(created_at) as date, COUNT(*) as count
    FROM residents_records
    WHERE als = '1'
    GROUP BY DATE(created_at)
    ORDER BY date ASC
";
$alsResult = $mysqlConn->query($alsQuery);

$alsData = [];
while ($row = $alsResult->fetch_assoc()) {
    $alsData[] = [$row['date'], (int)$row['count']]; // Push date and count for ALS
}

// Fetch OSY Data
$osyQuery = "
    SELECT DATE(created_at) as date, COUNT(*) as count
    FROM residents_records
    WHERE osy = '1'
    GROUP BY DATE(created_at)
    ORDER BY date ASC
";
$osyResult = $mysqlConn->query($osyQuery);

$osyData = [];
while ($row = $osyResult->fetch_assoc()) {
    $osyData[] = [$row['date'], (int)$row['count']]; // Push date and count for OSY
}

// Query to count business owners and non-business owners
$query = "SELECT 
    SUM(CASE WHEN business_owner = 1 THEN 1 ELSE 0 END) AS business_owners,
    SUM(CASE WHEN business_owner = 0 THEN 1 ELSE 0 END) AS non_business_owners
    FROM residents_records";

$result = mysqli_query($mysqlConn, $query);
$row = mysqli_fetch_assoc($result);

$businessOwnersCount = $row['business_owners'];
$nonBusinessOwnersCount = $row['non_business_owners'];

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
            drawPwdLineChart();
            drawTeenPregnancyLineChart();
            drawAlSOYChart();
            drawEducationalAttainmentChart();
            drawAlsLineChart();
            drawOsyLineChart();
            drawBusinessOwnerPieChart();
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

    function drawPwdLineChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date'); 
            data.addColumn('number', 'Number of PWD');
            
            data.addRows([
                <?php
                foreach ($pwdData as $data) {
                    $dateParts = explode('-', $data[0]);
                    echo "[new Date(" . $dateParts[0] . ", " . ($dateParts[1] - 1) . ", " . $dateParts[2] . "), " . $data[1] . "],";
                }
                ?>
            ]);

            var dashboard = new google.visualization.Dashboard(document.getElementById('pwdDashboard'));

            var control = new google.visualization.ControlWrapper({
                controlType: 'ChartRangeFilter',
                containerId: 'pwdFilter',
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
                containerId: 'pwdLineChart',
                options: {
                    title: 'PWD Over Time',
                    hAxis: { title: 'Date', format: 'yyyy-MMM-dd' },
                    vAxis: { title: 'Number of PWD', format: '0' },
                    chartArea: { width: '85%', height: '70%' },
                }
            });

            dashboard.bind(control, chart);
            dashboard.draw(data);
        }

        function drawTeenPregnancyLineChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date'); 
            data.addColumn('number', 'Number of Teen Pregnancies');
            
            data.addRows([
                <?php
                foreach ($teenPregnancyData as $data) {
                    $dateParts = explode('-', $data[0]);
                    echo "[new Date(" . $dateParts[0] . ", " . ($dateParts[1] - 1) . ", " . $dateParts[2] . "), " . $data[1] . "],";
                }
                ?>
            ]);

            var dashboard = new google.visualization.Dashboard(document.getElementById('teenPregnancyDashboard'));

            var control = new google.visualization.ControlWrapper({
                controlType: 'ChartRangeFilter',
                containerId: 'teenPregnancyFilter',
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
                containerId: 'teenPregnancyLineChart',
                options: {
                    title: 'Teen Pregnancy Over Time',
                    hAxis: { title: 'Date', format: 'yyyy-MMM-dd' },
                    vAxis: { title: 'Number of Teen Pregnancies', format: '0' },
                    chartArea: { width: '85%', height: '70%' },
                }
            });

            dashboard.bind(control, chart);
            dashboard.draw(data);
        }

        function drawAlSOYChart() {
            var data = google.visualization.arrayToDataTable([
                ['Type', 'Count'],
                ['ALS', <?php echo $alsCount; ?>],
                ['OSY', <?php echo $osyCount; ?>]
            ]);

            var options = {
                title: 'Comparison of ALS and OSY',
                is3D: true,
                legend: { position: 'bottom' },
                chartArea: { width: '85%', height: '75%' },
            };

            var chart = new google.visualization.PieChart(document.getElementById('alsOsyChart'));
            chart.draw(data, options);
        }

        function drawEducationalAttainmentChart() {
            var data = google.visualization.arrayToDataTable([
                ['Educational Attainment', 'Count'],
                <?php
                foreach ($educationalData as $item) {
                    echo "['" . $item[0] . "', " . $item[1] . "],";
                }
                ?>
            ]);

            var options = {
                title: 'Educational Attainment Distribution',
                hAxis: {
                    title: 'Count',
                    format: '0'
                },
                vAxis: {
                    title: 'Educational Attainment'
                },
                legend: { position: 'bottom' },
                bars: 'horizontal', // Use horizontal bars
            };

            var chart = new google.visualization.BarChart(document.getElementById('educationalAttainmentChart'));
            chart.draw(data, options);
        }

        function drawAlsLineChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'ALS Participation');

            data.addRows([
                <?php
                foreach ($alsData as $data) {
                    $dateParts = explode('-', $data[0]);
                    echo "[new Date(" . $dateParts[0] . ", " . ($dateParts[1] - 1) . ", " . $dateParts[2] . "), " . $data[1] . "],";
                }
                ?>
            ]);

            var dashboard = new google.visualization.Dashboard(document.getElementById('alsDashboard'));

            var control = new google.visualization.ControlWrapper({
                controlType: 'ChartRangeFilter',
                containerId: 'alsFilter',
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
                containerId: 'alsLineChart',
                options: {
                    title: 'ALS Participation Over Time',
                    hAxis: { title: 'Date', format: 'yyyy-MMM-dd' },
                    vAxis: { title: 'Number of ALS Participants', format: '0' },
                    chartArea: { width: '85%', height: '70%' },
                }
            });

            dashboard.bind(control, chart);
            dashboard.draw(data);
        }

        function drawOsyLineChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'OSY Participation');

            data.addRows([
                <?php
                foreach ($osyData as $data) {
                    $dateParts = explode('-', $data[0]);
                    echo "[new Date(" . $dateParts[0] . ", " . ($dateParts[1] - 1) . ", " . $dateParts[2] . "), " . $data[1] . "],";
                }
                ?>
            ]);

            var dashboard = new google.visualization.Dashboard(document.getElementById('osyDashboard'));

            var control = new google.visualization.ControlWrapper({
                controlType: 'ChartRangeFilter',
                containerId: 'osyFilter',
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
                containerId: 'osyLineChart',
                options: {
                    title: 'OSY Participation Over Time',
                    hAxis: { title: 'Date', format: 'yyyy-MMM-dd' },
                    vAxis: { title: 'Number of OSY Participants', format: '0' },
                    chartArea: { width: '85%', height: '70%' },
                }
            });

            dashboard.bind(control, chart);
            dashboard.draw(data);
        }

        function drawBusinessOwnerPieChart() {
        // Prepare the data for the pie chart
        var data = google.visualization.arrayToDataTable([
            ['Type', 'Count'],
            ['Business Owners', <?php echo $businessOwnersCount; ?>],
            ['Non-Business Owners', <?php echo $nonBusinessOwnersCount; ?>]
        ]);

        // Set the options for the pie chart
        var options = {
            title: 'Business Owners vs Non-Business Owners',
            is3D: true,
            legend: { position: 'bottom' },
            chartArea: { width: '85%', height: '75%' },
        };

        // Create and draw the pie chart
        var chart = new google.visualization.PieChart(document.getElementById('businessOwnerPieChart'));
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
        
        <!-- Health Section -->
        <div class="container mt-4">
            <div class="demographics-header">
                <div class="header-container">
                    <h4>Health</h4>
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

                    <!-- PWD Chart -->
                    <div id="pwdDashboard" class="chart-container">
                        <h5>PWD over time</h5>
                        <div id="pwdFilter" style="width: 100%; height: 100px;"></div>
                        <div id="pwdLineChart" style="width: 100%; height: 400px;"></div>
                    </div>

                    <!-- Disability Distribution Bar Chart -->
                    <div class="chart-container">
                        <h5>Disability Distribution</h5>
                        <div id="disabilityBarChart" style="width: 100%; height: 400px;"></div>
                    </div>

                    <!-- Teen Pregnancy Chart -->
                    <div id="teenPregnancyDashboard" class="chart-container">
                        <div id="teenPregnancyFilter" style="width: 100%; height: 100px;"></div>
                        <div id="teenPregnancyLineChart" style="width: 100%; height: 400px;"></div>
                    </div>

                    <!-- Type of Delivery Distribution Pie Chart -->
                    <div class="chart-container">
                        <h5>Type of Delivery Distribution</h5>
                        <div id="deliveryPieChart" style="width: 100%; height: 400px;"></div>
                    </div>

                </div>
            </div>
        </div>
        
        <!-- Character Records Section -->
        <div class="container mt-4">
            <div class="demographics-header">
                <div class="header-container">
                    <h4>Character Records</h4>
                    <button class="collapse-button" data-toggle="collapse" href="#characterSection" role="button" aria-expanded="false" aria-controls="characterSection">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>

            <div class="collapse show" id="characterSection">
                <div class="scrollable-graphs mt-3">



                </div>
            </div>
        </div>

        <!-- Employment Section -->
        <div class="container mt-4">
            <div class="demographics-header">
                <div class="header-container">
                    <h4>Employment</h4>
                    <button class="collapse-button" data-toggle="collapse" href="#employmentSection" role="button" aria-expanded="false" aria-controls="employmentSection">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>

            <div class="collapse show" id="employmentSection">
                <div class="scrollable-graphs mt-3">

                    <!-- Business Owner Pie Chart -->
                    <div class="chart-container">
                        <h4>Business Owners vs Non-Business Owners</h4>
                        <div id="businessOwnerPieChart" style="height: 400px;"></div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Education Section -->
        <div class="container mt-4">
            <div class="demographics-header">
                <div class="header-container">
                    <h4>Education</h4>
                    <button class="collapse-button" data-toggle="collapse" href="#educationSection" role="button" aria-expanded="false" aria-controls="educationSection">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>

            <div class="collapse show" id="educationSection">
                <div class="scrollable-graphs mt-3">

                    <!-- ALS vs OSY Pie Chart -->
                    <div class="chart-container">
                        <h5>ALS vs OSY</h5>
                        <div id="alsOsyChart" style="width: 100%; height: 500px;"></div>
                    </div>

                    <!-- ALS Participation Dashboard -->
                    <div id="alsDashboard" class="chart-container">
                        <h5>ALS over time</h5>
                        <div id="alsFilter" style="width: 100%; height: 100px;"></div>
                        <div id="alsLineChart" style="width: 100%; height: 400px;"></div>
                    </div>

                    <!-- OSY Participation Dashboard -->
                    <div id="osyDashboard" class="chart-container">
                        <h5>OSY over time</h5>
                        <div id="osyFilter" style="width: 100%; height: 100px;"></div>
                        <div id="osyLineChart" style="width: 100%; height: 400px;"></div>
                    </div>

                    <!-- Educational Attainment Bar Chart -->
                    <div class="chart-container">
                        <h5>Educational Attainment Distribution</h5>
                        <div id="educationalAttainmentChart" style="width: 100%; height: 500px;"></div>
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
