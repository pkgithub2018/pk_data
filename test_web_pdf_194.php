<?php
session_start();
$_SESSION['uid'] = 29;
$_SESSION['gid'] = 8;
$_GET['appid'] = 194;
$_GET['action'] = 'generate';

// Capture output
ob_start();
include 'certificate_pdf.php';
$output = ob_get_clean();

echo "Response from certificate_pdf.php:\n";
echo $output . "\n";
?>