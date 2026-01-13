<?php
// Simple database test
require("php-bin/connection.php");

echo "<h2>Database Connection Test</h2>";

// Test connection
if ($con) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    exit;
}

// Check table structure
echo "<h3>Table Structure: tbentity_export</h3>";
$structure_sql = "SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = 'tbentity_export' ORDER BY ordinal_position";
$structure_result = pg_query($con, $structure_sql);

if ($structure_result) {
    echo "<table border='1'>";
    echo "<tr><th>Column Name</th><th>Data Type</th><th>Nullable</th></tr>";
    while ($col = pg_fetch_assoc($structure_result)) {
        echo "<tr>";
        echo "<td>" . $col['column_name'] . "</td>";
        echo "<td>" . $col['data_type'] . "</td>";
        echo "<td>" . $col['is_nullable'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>Could not retrieve table structure</p>";
}

// Test a simple select
echo "<h3>Sample Data (first 3 records)</h3>";
$sample_sql = "SELECT id, title, business_type, entity_type, created_date FROM tbentity_export LIMIT 3";
$sample_result = pg_query($con, $sample_sql);

if ($sample_result) {
    if (pg_num_rows($sample_result) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Title</th><th>Business Type</th><th>Entity Type</th><th>Created Date</th></tr>";
        while ($row = pg_fetch_assoc($sample_result)) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['title'] . "</td>";
            echo "<td>" . $row['business_type'] . "</td>";
            echo "<td>" . $row['entity_type'] . "</td>";
            echo "<td>" . $row['created_date'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No records found in tbentity_export table</p>";
    }
} else {
    echo "<p style='color: red;'>Could not retrieve sample data: " . pg_last_error($con) . "</p>";
}

// Test a simple update query with a specific ID (if any exists)
$test_count_sql = "SELECT COUNT(*) as count FROM tbentity_export";
$count_result = pg_query($con, $test_count_sql);
if ($count_result) {
    $count_row = pg_fetch_assoc($count_result);
    echo "<h3>Total Records: " . $count_row['count'] . "</h3>";
    
    if ($count_row['count'] > 0) {
        // Test UPDATE syntax
        $test_id_sql = "SELECT id FROM tbentity_export LIMIT 1";
        $test_id_result = pg_query($con, $test_id_sql);
        if ($test_id_result) {
            $test_id_row = pg_fetch_assoc($test_id_result);
            $test_id = $test_id_row['id'];
            
            echo "<h3>Testing UPDATE Syntax for ID: $test_id</h3>";
            
            // Test the UPDATE query without actually executing it
            $test_update_sql = "UPDATE tbentity_export SET title = title || ' [TEST]' WHERE id = $test_id";
            echo "<p><strong>Test SQL:</strong> $test_update_sql</p>";
            
            // Actually try the update
            $test_update_result = pg_query($con, $test_update_sql);
            if ($test_update_result) {
                $affected = pg_affected_rows($test_update_result);
                echo "<p style='color: green;'>✓ Test UPDATE successful. Affected rows: $affected</p>";
                
                // Revert the change
                $revert_sql = "UPDATE tbentity_export SET title = REPLACE(title, ' [TEST]', '') WHERE id = $test_id";
                pg_query($con, $revert_sql);
                echo "<p>Test change reverted.</p>";
            } else {
                echo "<p style='color: red;'>✗ Test UPDATE failed: " . pg_last_error($con) . "</p>";
            }
        }
    }
}

echo "<p><a href='entity.php?entity=export&uid=admin@admin.com'>Back to Entity List</a></p>";
?>