<?php
/*
 * Test Dashboard Access for VIEWER Users
 * This script verifies that VIEWER users can access Application, Inspection, and Certificate dashboards
 */

require_once 'connection.php';
require_once 'php-bin/supports.php';

// Test with VIEWER user (adjust userid as needed)
// From the screenshot, uid=33 is a VIEWER user
$test_userid = 33;

echo "=== TESTING DASHBOARD ACCESS FOR VIEWER USER (ID: $test_userid) ===\n\n";

// Get user details
$query = "SELECT u.*, g.title as group_title FROM tbusers u 
          JOIN tbusergroup g ON g.id = u.group_id 
          WHERE u.id = $test_userid";
$result = pg_query($con, $query);
$user = pg_fetch_assoc($result);

if (!$user) {
    echo "❌ ERROR: User ID $test_userid not found!\n";
    exit();
}

echo "User: {$user['name']}\n";
echo "Email: {$user['email']}\n";
echo "Group: {$user['group_title']}\n\n";

// Test 1: Application Dashboard Access (application.php)
echo "=== TEST 1: APPLICATION DASHBOARD ACCESS ===\n";
$appPermit = UserPermitCheck($test_userid, 'PG-APPLICATION', $con);
echo "Module Code: PG-APPLICATION\n";
echo "Read Permission: " . ($appPermit['pread'] ? '✅ YES' : '❌ NO') . "\n";
echo "Access Result: " . ($appPermit['pread'] ? '✅ ALLOWED - Can view dashboard' : '❌ DENIED - Cannot access') . "\n\n";

// Test 2: Inspection Dashboard Access (inspection.php)
echo "=== TEST 2: INSPECTION DASHBOARD ACCESS ===\n";
$inspectPermit = UserPermitCheck($test_userid, 'PG-INSPECTION', $con);
echo "Module Code: PG-INSPECTION\n";
echo "Read Permission: " . ($inspectPermit['pread'] ? '✅ YES' : '❌ NO') . "\n";
echo "Access Result: " . ($inspectPermit['pread'] ? '✅ ALLOWED - Can view dashboard' : '❌ DENIED - Cannot access') . "\n\n";

// Test 3: Certificate Dashboard Access (certificate.php)
echo "=== TEST 3: CERTIFICATE DASHBOARD ACCESS ===\n";
$certPermit = UserPermitCheck($test_userid, 'PG-CERTIFICATE', $con);
echo "Module Code: PG-CERTIFICATE\n";
echo "Read Permission: " . ($certPermit['pread'] ? '✅ YES' : '❌ NO') . "\n";
echo "Access Result: " . ($certPermit['pread'] ? '✅ ALLOWED - Can view dashboard' : '❌ DENIED - Cannot access') . "\n\n";

// Test 4: Check other permissions (should be read-only)
echo "=== TEST 4: PERMISSION DETAILS ===\n";
echo "Application Module:\n";
echo "  - Add: " . ($appPermit['padd'] ? 'YES' : 'NO') . "\n";
echo "  - Update: " . ($appPermit['pupdate'] ? 'YES' : 'NO') . "\n";
echo "  - Delete: " . ($appPermit['pdelete'] ? 'YES' : 'NO') . "\n\n";

echo "Inspection Module:\n";
echo "  - Add: " . ($inspectPermit['padd'] ? 'YES' : 'NO') . "\n";
echo "  - Update: " . ($inspectPermit['pupdate'] ? 'YES' : 'NO') . "\n";
echo "  - Delete: " . ($inspectPermit['pdelete'] ? 'YES' : 'NO') . "\n\n";

echo "Certificate Module:\n";
echo "  - Add: " . ($certPermit['padd'] ? 'YES' : 'NO') . "\n";
echo "  - Update: " . ($certPermit['pupdate'] ? 'YES' : 'NO') . "\n";
echo "  - Delete: " . ($certPermit['pdelete'] ? 'YES' : 'NO') . "\n\n";

// Final result
echo "=== FINAL RESULT ===\n";
$allPassed = $appPermit['pread'] && $inspectPermit['pread'] && $certPermit['pread'];
$allReadOnly = !$appPermit['pupdate'] && !$inspectPermit['pupdate'] && !$certPermit['pupdate'];

if ($allPassed && $allReadOnly) {
    echo "✅ ✅ ✅ ALL TESTS PASSED!\n";
    echo "VIEWER users can now ACCESS all dashboards in READ-ONLY mode.\n";
    echo "They can view data but cannot add, edit, or delete.\n";
} else if ($allPassed) {
    echo "✅ PARTIAL PASS: Can access dashboards\n";
    echo "⚠️ WARNING: May have edit permissions (should be read-only)\n";
} else {
    echo "❌ TESTS FAILED!\n";
    echo "VIEWER users still cannot access some dashboards.\n";
    echo "Check module codes in database and PHP files.\n";
}
?>
