<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting PDF generation debug...\n";

// Include database connection and functions
require("php-bin/connection.php");
require("php-bin/supports.php");

echo "Database connection included\n";

// Test application ID
$appid = 163; // Use a valid application ID that has a certificate

echo "Testing with application ID: $appid\n";

// Test if TCPDF is available
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "TCPDF autoloader included successfully\n";
    
    // Test creating TCPDF instance
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    echo "TCPDF instance created successfully\n";
    
} catch (Exception $e) {
    echo "TCPDF Error: " . $e->getMessage() . "\n";
    die();
}

// Test if application info exists
$app_info = ApplicationInfo($appid, $con);
if ($app_info) {
    echo "Application info found for ID $appid\n";
    echo "Application data: " . print_r($app_info, true) . "\n";
} else {
    echo "No application found for ID $appid\n";
    die();
}

// Test if certificate info exists
$cert_info = CertificateInfo($appid, $con);
if ($cert_info) {
    echo "Certificate info found for ID $appid\n";
    echo "Certificate data: " . print_r($cert_info, true) . "\n";
} else {
    echo "No certificate found for ID $appid\n";
    die();
}

// Test if certificate_sources directory exists and is writable
$cert_dir = __DIR__ . '/certificate_sources/';
if (!is_dir($cert_dir)) {
    echo "Creating certificate_sources directory\n";
    mkdir($cert_dir, 0755, true);
}

if (is_writable($cert_dir)) {
    echo "Certificate sources directory is writable\n";
} else {
    echo "Certificate sources directory is NOT writable\n";
    die();
}

// Test if table exists
$sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'tbcertificate_sources'";
$result = pg_query($con, $sql);
if ($result) {
    $row = pg_fetch_row($result);
    if ($row[0] > 0) {
        echo "tbcertificate_sources table exists\n";
    } else {
        echo "tbcertificate_sources table does NOT exist - creating it...\n";
        
        // Create the table
        $create_sql = "
        CREATE TABLE tbcertificate_sources (
            id SERIAL PRIMARY KEY,
            application_id INTEGER NOT NULL,
            certificate_id INTEGER,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            uid VARCHAR(50),
            gid VARCHAR(50),
            filelink VARCHAR(255) NOT NULL,
            enabled VARCHAR(3) DEFAULT 'yes'
        );
        ";
        
        $create_result = pg_query($con, $create_sql);
        if ($create_result) {
            echo "Table created successfully\n";
        } else {
            echo "Error creating table: " . pg_last_error($con) . "\n";
            die();
        }
    }
} else {
    echo "Error checking table existence: " . pg_last_error($con) . "\n";
    die();
}

// Test the GenerateCertificatePDF function
echo "Testing PDF generation...\n";
$result = GenerateCertificatePDF($appid, 27, 3, $con);

if ($result['success']) {
    echo "PDF generated successfully!\n";
    echo "Filename: " . $result['filename'] . "\n";
    echo "File path: " . $result['filepath'] . "\n";
    echo "File link: " . $result['filelink'] . "\n";
    echo "Database ID: " . $result['db_id'] . "\n";
} else {
    echo "PDF generation failed: " . $result['error'] . "\n";
}

echo "Debug test completed.\n";
?>