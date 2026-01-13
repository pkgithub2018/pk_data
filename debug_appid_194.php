<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection and functions
require("php-bin/connection.php");
require("php-bin/supports.php");

$appid = 194; // The failing application ID

echo "Testing application ID: $appid\n";

// Test if application info exists
$app_info = ApplicationInfo($appid, $con);
if ($app_info) {
    echo "✅ Application info found\n";
    print_r($app_info);
} else {
    echo "❌ No application found for ID $appid\n";
    die();
}

// Test if certificate info exists
$cert_info = CertificateInfo($appid, $con);
if ($cert_info) {
    echo "✅ Certificate info found\n";
    print_r($cert_info);
} else {
    echo "❌ No certificate found for ID $appid\n";
    die();
}

// Test session values
session_start();
$_SESSION['uid'] = $app_info['uid'] ?? 27;
$_SESSION['gid'] = $app_info['gid'] ?? 3;

echo "Session UID: " . $_SESSION['uid'] . "\n";
echo "Session GID: " . $_SESSION['gid'] . "\n";

// Test PDF generation
echo "Testing PDF generation...\n";
$result = GenerateCertificatePDF($appid, $_SESSION['uid'], $_SESSION['gid'], $con);

if ($result['success']) {
    echo "✅ PDF generated successfully!\n";
    echo "Filename: " . $result['filename'] . "\n";
} else {
    echo "❌ PDF generation failed: " . $result['error'] . "\n";
}
?>