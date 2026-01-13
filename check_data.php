<?php
// Simple test to find available application IDs
require("php-bin/connection.php");
require("php-bin/supports.php");

echo "Checking available applications...\n";

$sql = "SELECT id, application_no, date_certificate FROM tbapplication LIMIT 5";
$result = pg_query($con, $sql);

if ($result && pg_num_rows($result) > 0) {
    echo "Available applications:\n";
    while ($row = pg_fetch_assoc($result)) {
        echo "ID: " . $row['id'] . ", App No: " . $row['application_no'] . ", Date: " . $row['date_certificate'] . "\n";
    }
} else {
    echo "No applications found in database.\n";
    echo "PostgreSQL error: " . pg_last_error($con) . "\n";
}

// Check if tbcertificate table exists and has data
$sql2 = "SELECT id, application_id, certificate_no FROM tbcertificate LIMIT 5";
$result2 = pg_query($con, $sql2);

if ($result2 && pg_num_rows($result2) > 0) {
    echo "\nAvailable certificates:\n";
    while ($row = pg_fetch_assoc($result2)) {
        echo "Cert ID: " . $row['id'] . ", App ID: " . $row['application_id'] . ", Cert No: " . $row['certificate_no'] . "\n";
    }
} else {
    echo "\nNo certificates found in database.\n";
    echo "PostgreSQL error: " . pg_last_error($con) . "\n";
}
?>