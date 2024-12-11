<?php
// getSubdivisionStats.php
header('Content-Type: application/json');

// Include the MySQL connection file
include('../configs/connection.php');

// Check if the connection exists and is successful
if (!$mysqlConn) {
    die(json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]));
}

try {
    // Expanded query with additional insights
    $query = "
        SELECT 
            CASE 
                WHEN subdivision IN ('Mother Ignacia', 'Villa Javier', 'Villa Andrea') THEN 'Mother Ignacia, Villa Javier, Villa Andrea'
                ELSE subdivision 
            END AS combinedSubdivision,
            
            COUNT(*) AS totalResidents, 
            AVG(age) AS avgAge,
            
            SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) / COUNT(*) * 100 AS malePercentage,
            SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) / COUNT(*) * 100 AS femalePercentage,

            
            SUM(CASE WHEN philhealth IS NOT NULL AND philhealth <> '' THEN 1 ELSE 0 END) / COUNT(*) * 100 AS philhealthPercentage,
            SUM(CASE WHEN voterstatus = 1 THEN 1 ELSE 0 END) AS totalVoters,
            
            SUM(CASE WHEN disability IS NOT NULL AND disability <> '' THEN 1 ELSE 0 END) AS disabilityCount,
            SUM(CASE WHEN pwd = 1 THEN 1 ELSE 0 END) AS pwdCount,
            
            SUM(CASE WHEN osy = 1 THEN 1 ELSE 0 END) AS osyCount,
            SUM(CASE WHEN als IS NOT NULL AND als <> '' THEN 1 ELSE 0 END) / COUNT(*) * 100 AS alsParticipationPercentage,
            
            SUM(CASE WHEN teen_pregnancy = 1 THEN 1 ELSE 0 END) AS teenPregnancyCount,
            
            SUM(CASE WHEN employment = 'Employed' THEN 1 ELSE 0 END) / COUNT(*) * 100 AS employmentPercentage,
            SUM(CASE WHEN ofw = 1 THEN 1 ELSE 0 END) AS ofwCount,
            
            AVG(years_of_stay) AS avgYearsOfStay

        FROM 
            residents_records
        GROUP BY 
            combinedSubdivision
    ";

    $stmt = $mysqlConn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    $subdivisionStats = [];
    while ($row = $result->fetch_assoc()) {
        $subdivisionStats[$row['combinedSubdivision']] = [
            'totalResidents' => (int) $row['totalResidents'],
            'avgAge' => round($row['avgAge'], 1),
            'genderDistribution' => [
                'male' => round($row['malePercentage'], 1) . '%',
                'female' => round($row['femalePercentage'], 1) . '%'
            ],
            'philhealthPercentage' => round($row['philhealthPercentage'], 1) . '%',
            'totalVoters' => (int) $row['totalVoters'],
            'disabilityCount' => (int) $row['disabilityCount'],
            'pwdCount' => (int) $row['pwdCount'],
            'osyCount' => (int) $row['osyCount'],
            'alsParticipationPercentage' => round($row['alsParticipationPercentage'], 1) . '%',
            'teenPregnancyCount' => (int) $row['teenPregnancyCount'],
            'employmentPercentage' => round($row['employmentPercentage'], 1) . '%',
            'ofwCount' => (int) $row['ofwCount'],
            'avgYearsOfStay' => round($row['avgYearsOfStay'], 1)
        ];
    }

    echo json_encode($subdivisionStats);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch data: ' . $e->getMessage()]);
}

$stmt->close();
$mysqlConn->close();
