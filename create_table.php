<?php
require('php-bin/connection.php');

// Drop existing table
$sql = 'DROP TABLE IF EXISTS tbcertificate_sources';
pg_query($con, $sql);

// Create table with correct types
$create_sql = "
CREATE TABLE tbcertificate_sources (
    id SERIAL PRIMARY KEY,
    application_id INTEGER NOT NULL,
    certificate_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    uid INTEGER,
    gid INTEGER,
    filelink VARCHAR(255) NOT NULL,
    enabled VARCHAR(3) DEFAULT 'yes'
);";

$result = pg_query($con, $create_sql);
if ($result) {
    echo "Table created successfully\n";
} else {
    echo "Error: " . pg_last_error($con) . "\n";
}
?>