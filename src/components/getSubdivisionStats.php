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
    // Expanded query with additional statistics
    $query = "
        SELECT 
            CASE 
                WHEN subdivision IN ('Mother Ignacia', 'Villa Javier', 'Villa Andrea') THEN 'Mother Ignacia, Villa Javier, Villa Andrea'
                ELSE subdivision 
            END AS combinedSubdivision,
            
            COUNT(*) AS totalResidents, 
            AVG(age) AS avgAge,
            
            SUM(CASE WHEN gender = 'M' THEN 1 ELSE 0 END) / COUNT(*) * 100 AS genderRatio,
            SUM(CASE WHEN philhealth IS NOT NULL AND philhealth <> '' THEN 1 ELSE 0 END) / COUNT(*) * 100 AS philhealthPercentage,
            SUM(CASE WHEN voterstatus = 1 THEN 1 ELSE 0 END) AS totalVoters,
            
            -- Count of residents with disabilities
            SUM(CASE WHEN disability IS NOT NULL AND disability <> '' THEN 1 ELSE 0 END) AS disabilityCount,
            
            -- Count of residents who are out-of-school youth (OSY)
            SUM(CASE WHEN osy = 1 THEN 1 ELSE 0 END) AS osyCount,
            
            -- Count of residents who are persons with disabilities (PWD)
            SUM(CASE WHEN pwd = 1 THEN 1 ELSE 0 END) AS pwdCount,
            
            -- Percentage of residents employed
            SUM(CASE WHEN employment IS NOT NULL AND employment <> '' THEN 1 ELSE 0 END) / COUNT(*) * 100 AS employmentPercentage,
            
            -- Count of OFW (Overseas Filipino Workers)
            SUM(CASE WHEN ofw = 1 THEN 1 ELSE 0 END) AS ofwCount,
            
            -- Average years of stay
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
            'genderRatio' => round($row['genderRatio'], 1) . '%',
            'philhealthPercentage' => round($row['philhealthPercentage'], 1) . '%',
            'totalVoters' => (int) $row['totalVoters'],
            'disabilityCount' => (int) $row['disabilityCount'],
            'osyCount' => (int) $row['osyCount'],
            'pwdCount' => (int) $row['pwdCount'],
            'employmentPercentage' => round($row['employmentPercentage'], 1) . '%',
            'ofwCount' => (int) $row['ofwCount'],
            'avgYearsOfStay' => round($row['avgYearsOfStay'], 1)
        ];
    }

    echo json_encode($subdivisionStats);

} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch data']);
}

$stmt->close();
$mysqlConn->close();
?>
