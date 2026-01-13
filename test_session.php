<?php
// Simple session simulation for testing
session_start();
$_SESSION['uid'] = 27;
$_SESSION['gid'] = 3;

echo "Session set for testing. Now you can test:\n";
echo "1. http://localhost/certificate_view.php?appid=163\n";
echo "2. http://localhost/certificate_pdf.php?action=generate&appid=163\n";
?>