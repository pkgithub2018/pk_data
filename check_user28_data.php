<?php
// Diagnostic script to check user 28's data
require("php-bin/connection.php");
require("php-bin/supports.php");

$userid = 28;

echo "<h2>Diagnostic Report for User ID: $userid</h2>";

// Get user's location_group
$location_group = UserLocationGroup($userid, $con);
echo "<p><strong>User's Location Group:</strong> " . ($location_group ? $location_group : "NOT FOUND") . "</p>";

// Check applications
$sql_app = "SELECT COUNT(*) as total, MIN(application_date) as earliest, MAX(application_date) as latest
            FROM tbapplication 
            WHERE uid = '$userid'";
$result = pg_query($con, $sql_app);
$row = pg_fetch_assoc($result);
echo "<h3>Applications</h3>";
echo "<p>Total: {$row['total']}</p>";
echo "<p>Date range: {$row['earliest']} to {$row['latest']}</p>";

// Check inspections
$sql_insp = "SELECT COUNT(*) as total, MIN(inspection_date) as earliest, MAX(inspection_date) as latest
             FROM tbinspection i
             INNER JOIN tbapplication a ON i.application_id = a.id
             WHERE a.uid = '$userid'";
$result = pg_query($con, $sql_insp);
$row = pg_fetch_assoc($result);
echo "<h3>Inspections</h3>";
echo "<p>Total: {$row['total']}</p>";
echo "<p>Date range: {$row['earliest']} to {$row['latest']}</p>";

// Check pest detected records
$sql_pest = "SELECT COUNT(*) as total
             FROM tbpest_detected tpd
             INNER JOIN tbapplication a ON tpd.application_id = a.id
             WHERE a.uid = '$userid'";
$result = pg_query($con, $sql_pest);
$row = pg_fetch_assoc($result);
echo "<h3>Pest Detected Records</h3>";
echo "<p>Total: {$row['total']}</p>";

// Check pest detected in last 3 months
$sql_pest_recent = "SELECT COUNT(*) as total
                    FROM tbpest_detected tpd
                    INNER JOIN tbapplication a ON tpd.application_id = a.id
                    INNER JOIN tbinspection i ON i.application_id = a.id
                    WHERE a.uid = '$userid'
                    AND i.inspection_date >= DATE_TRUNC('month', CURRENT_DATE) - INTERVAL '2 months'
                    AND i.inspection_date < DATE_TRUNC('month', CURRENT_DATE) + INTERVAL '1 month'";
$result = pg_query($con, $sql_pest_recent);
$row = pg_fetch_assoc($result);
echo "<h3>Pest Detected in Last 3 Months (Apr-Jun 2026)</h3>";
echo "<p>Total: {$row['total']}</p>";

// Check data by location_group
if ($location_group) {
    echo "<hr>";
    echo "<h3>Data for Location Group: $location_group</h3>";
    
    // Applications by location_group
    $sql = "SELECT COUNT(*) as total
            FROM tbapplication a
            INNER JOIN tbusers u ON a.uid = u.id
            INNER JOIN tblocations l ON u.location_id::text = l.id::text
            WHERE l.location_group = '$location_group'";
    $result = pg_query($con, $sql);
    $row = pg_fetch_assoc($result);
    echo "<p>Applications: {$row['total']}</p>";
    
    // Inspections by location_group
    $sql = "SELECT COUNT(*) as total
            FROM tbinspection i
            INNER JOIN tbapplication a ON i.application_id = a.id
            INNER JOIN tbusers u ON a.uid = u.id
            INNER JOIN tblocations l ON u.location_id::text = l.id::text
            WHERE l.location_group = '$location_group'";
    $result = pg_query($con, $sql);
    $row = pg_fetch_assoc($result);
    echo "<p>Inspections: {$row['total']}</p>";
    
    // Pest detected by location_group
    $sql = "SELECT COUNT(*) as total
            FROM tbpest_detected tpd
            INNER JOIN tbapplication a ON tpd.application_id = a.id
            INNER JOIN tbusers u ON a.uid = u.id
            INNER JOIN tblocations l ON u.location_id::text = l.id::text
            WHERE l.location_group = '$location_group'";
    $result = pg_query($con, $sql);
    $row = pg_fetch_assoc($result);
    echo "<p>Pest Detected Records: {$row['total']}</p>";
    
    // Pest detected in last 3 months by location_group
    $sql = "SELECT COUNT(*) as total
            FROM tbpest_detected tpd
            INNER JOIN tbapplication a ON tpd.application_id = a.id
            INNER JOIN tbusers u ON a.uid = u.id
            INNER JOIN tblocations l ON u.location_id::text = l.id::text
            INNER JOIN tbinspection i ON i.application_id = a.id
            WHERE l.location_group = '$location_group'
            AND i.inspection_date >= DATE_TRUNC('month', CURRENT_DATE) - INTERVAL '2 months'
            AND i.inspection_date < DATE_TRUNC('month', CURRENT_DATE) + INTERVAL '1 month'";
    $result = pg_query($con, $sql);
    $row = pg_fetch_assoc($result);
    echo "<p>Pest Detected in Last 3 Months: {$row['total']}</p>";
}

echo "<hr>";
echo "<p><strong>Conclusion:</strong> If 'Pest Detected in Last 3 Months' is 0, the charts will be empty because they only show pest detection data.</p>";
?>
