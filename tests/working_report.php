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
    <?php include '/xampp/htdocs/capstone/src/components/header.php'; ?>

    <!-- Google Charts API -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', { 'packages': ['corechart', 'controls'] });
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            drawGenderPieChart();
            drawAgeHistogram();
            drawPopulationLineChart();
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
                hAxis: { title: 'Age Groups', minValue: 0 },
                vAxis: { title: 'Number of Residents', format: '0', viewWindow: { min: 0 } }
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
                    hAxis: { title: 'Date', format: 'yyyy-MM-dd' }, // Format the x-axis as dates
                    vAxis: { title: 'Number of Residents' },
                    chartArea: { width: '85%', height: '70%' },
                }
            });

            dashboard.bind(control, chart);
            dashboard.draw(data);
        }


        window.addEventListener('resize', function () {
            drawCharts();
        });
    </script>
</head>

<body>
    <?php include '/xampp/htdocs/capstone/src/components/moderator_navbar.php'; ?>

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
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div id="genderPieChart"></div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div id="ageHistogram"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Population Growth Section -->
        <div class="container mt-4">
            <h4>Population Growth Over Time</h4>
            <div id="populationDashboard">
                <div id="filter_div" style="height: 100px;"></div>
                <div id="populationLineChart" style="height: 400px;"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
