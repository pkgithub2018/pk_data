<?php
require('php-bin/connection.php');
require('php-bin/supports.php');

echo "<h3>Testing CertificateApprovedBy Function</h3>\n";
echo "<select name='test_approved_by'>\n";
echo "<option value=''>Select approver...</option>\n";

// Test the function (using group ID 1 as example)
CertificateApprovedBy($con, 1);

echo "</select>\n";

echo "<hr>\n";
echo "<h3>Testing with selected value (ID = 1)</h3>\n";
echo "<select name='test_approved_by_selected'>\n";
echo "<option value=''>Select approver...</option>\n";

// Test with a selected value (groupId = 1, selectedId = 1)
CertificateApprovedBy($con, 1, 1);

echo "</select>\n";
?>