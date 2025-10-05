<?php
require('php-bin/connection.php');

echo "Checking tbapprovers table...\n";

$sql = "SELECT id, name, surname, roles, position, workplace, enabled FROM tbapprovers ORDER BY name ASC";
$result = pg_query($con, $sql);

if ($result) {
    $count = pg_num_rows($result);
    echo "Found $count records in tbapprovers table:\n";
    
    if ($count > 0) {
        while ($row = pg_fetch_assoc($result)) {
            echo "ID: {$row['id']}, Name: {$row['name']} {$row['surname']}, Role: {$row['roles']}, Position: {$row['position']}, Enabled: {$row['enabled']}\n";
        }
    } else {
        echo "No records found in tbapprovers table.\n";
    }
} else {
    echo "Error querying tbapprovers table: " . pg_last_error($con) . "\n";
}

// Also check if table exists
$check_table = "SELECT to_regclass('public.tbapprovers') IS NOT NULL as table_exists";
$table_result = pg_query($con, $check_table);
if ($table_result) {
    $table_row = pg_fetch_assoc($table_result);
    echo "Table exists: " . ($table_row['table_exists'] ? 'Yes' : 'No') . "\n";
}
?>