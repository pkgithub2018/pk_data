<?php
require('php-bin/connection.php');
require('php-bin/supports.php');

echo "<h3>Testing CertificateApprovedBy Function</h3>\n";
echo "<select name='test_approved_by'>\n";
echo "<option value=''>Select approver...</option>\n";

// Test the function
CertificateApprovedBy($con);

echo "</select>\n";

echo "<hr>\n";
echo "<h3>Testing with selected value (ID = 1)</h3>\n";
echo "<select name='test_approved_by_selected'>\n";
echo "<option value=''>Select approver...</option>\n";

// Test with a selected value
CertificateApprovedBy($con, 1);

echo "</select>\n";
?>