<?php
include '../configs/connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $ids = $_POST['ids']; // Array of selected IDs
    $table = $_POST['table']; // The table name sent from JavaScript

    if (!empty($ids) && !empty($table)) {
        $idList = implode(',', array_map('intval', $ids)); // Sanitize IDs to prevent SQL injection

        // Allowed table names (for security)
        $allowedTables = ['archive_blotter', 'archive_user', 'residents_records'];

        // Define the corresponding column names for each table
        $tableColumns = [
            'archive_blotter' => 'blotter_id',
            'archive_user' => 'user_id',
            'residents_records' => 'id'
        ];

        // Check if the table is valid
        if (in_array($table, $allowedTables)) {
            // Get the correct column name for the selected table
            $columnName = $tableColumns[$table];

            // Perform the DELETE operation on the given table
            $sql = "DELETE FROM `$table` WHERE `$columnName` IN ($idList)"; // Use the correct column name
            if ($mysqlConn4->query($sql)) {
                echo "Selected records deleted successfully.";
            } else {
                echo "Error deleting records: " . $mysqlConn4->error;
            }
        } else {
            echo "Invalid table name.";
        }
    } else {
        echo "No IDs or table name provided.";
    }
    exit;  // End the script
}
?>
