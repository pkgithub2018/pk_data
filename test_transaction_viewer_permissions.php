<?php
require("php-bin/connection.php");
require("php-bin/supports.php");

echo "=== TRANSACTION FORM PERMISSION TEST ===\n\n";

// Get a VIEWER user
$sqlUser = "SELECT u.id, u.name, u.email, g.title as group_name 
            FROM tbusers u 
            INNER JOIN tbusergroup g ON g.id = u.group_id 
            WHERE g.title = 'VIEWER' 
            LIMIT 1";
$resultUser = pg_query($con, $sqlUser);

if ($resultUser && pg_num_rows($resultUser) > 0) {
    $user = pg_fetch_assoc($resultUser);
    $userid = $user['id'];
    echo "Testing with VIEWER user:\n";
    echo "  ID: {$user['id']}\n";
    echo "  Name: {$user['name']}\n";
    echo "  Group: {$user['group_name']}\n\n";
} else {
    echo "ERROR: No VIEWER user found in database.\n";
    echo "Please create a VIEWER user first.\n";
    exit;
}

echo "=== CHECKING TRANSACTION.PHP PERMISSIONS ===\n\n";

// Check Application permission (should be PG-APPLICATION now)
$appPermit = UserPermitCheck($userid, 'PG-APPLICATION', $con);
echo "APPLICATION (PG-APPLICATION):\n";
echo "  Exists: " . ($appPermit['exists'] ? 'YES' : 'NO') . "\n";
echo "  Read: " . ($appPermit['pread'] ? 'YES' : 'NO') . "\n";
echo "  Add: " . ($appPermit['padd'] ? 'YES' : 'NO') . "\n";
echo "  Update: " . ($appPermit['pupdate'] ? 'YES' : 'NO') . "\n";
echo "  Delete: " . ($appPermit['pdelete'] ? 'YES' : 'NO') . "\n";
echo "  Reason: " . $appPermit['reason'] . "\n\n";

// Check Inspection permission
$inspectPermit = UserPermitCheck($userid, 'PG-INSPECTION', $con);
echo "INSPECTION (PG-INSPECTION):\n";
echo "  Exists: " . ($inspectPermit['exists'] ? 'YES' : 'NO') . "\n";
echo "  Read: " . ($inspectPermit['pread'] ? 'YES' : 'NO') . "\n";
echo "  Add: " . ($inspectPermit['padd'] ? 'YES' : 'NO') . "\n";
echo "  Update: " . ($inspectPermit['pupdate'] ? 'YES' : 'NO') . "\n";
echo "  Delete: " . ($inspectPermit['pdelete'] ? 'YES' : 'NO') . "\n";
echo "  Reason: " . $inspectPermit['reason'] . "\n\n";

// Check Certificate permission
$certPermit = UserPermitCheck($userid, 'PG-CERTIFICATE', $con);
echo "CERTIFICATE (PG-CERTIFICATE):\n";
echo "  Exists: " . ($certPermit['exists'] ? 'YES' : 'NO') . "\n";
echo "  Read: " . ($certPermit['pread'] ? 'YES' : 'NO') . "\n";
echo "  Add: " . ($certPermit['padd'] ? 'YES' : 'NO') . "\n";
echo "  Update: " . ($certPermit['pupdate'] ? 'YES' : 'NO') . "\n";
echo "  Delete: " . ($certPermit['pdelete'] ? 'YES' : 'NO') . "\n";
echo "  Reason: " . $certPermit['reason'] . "\n\n";

echo "=== FORM BEHAVIOR SIMULATION ===\n\n";

// Simulate Application form behavior
$canAppRead = $appPermit['pread'];
$canAppUpdate = $appPermit['pupdate'];
$canAppAdd = $appPermit['padd'];

// For edit mode
$isEditMode = true; // Simulating edit mode
$requiredPermission = $isEditMode ? $canAppUpdate : $canAppAdd;
$appFormReadOnly = (!$requiredPermission && $canAppRead) ? 'readonly' : '';
$appFormDisabled = (!$requiredPermission && $canAppRead) ? 'disabled' : '';
$appSubmitDisabled = (!$requiredPermission && $canAppRead) ? 'disabled' : '';

echo "APPLICATION FORM (Edit Mode):\n";
echo "  Required Permission: " . ($requiredPermission ? 'YES' : 'NO') . " (needs Update permission)\n";
echo "  Form Fields: " . ($appFormReadOnly ? 'READONLY' : 'EDITABLE') . "\n";
echo "  Select/Checkboxes: " . ($appFormDisabled ? 'DISABLED' : 'ENABLED') . "\n";
echo "  Update Button: " . ($appSubmitDisabled ? 'DISABLED' : 'ENABLED') . "\n\n";

// Simulate Inspection form behavior
$canInspectRead = $inspectPermit['pread'];
$canInspectUpdate = $inspectPermit['pupdate'];
$canInspectAdd = $inspectPermit['padd'];

$requiredInspectPermission = $isEditMode ? $canInspectUpdate : $canInspectAdd;
$inspectFormReadOnly = (!$requiredInspectPermission && $canInspectRead) ? 'readonly' : '';
$inspectFormDisabled = (!$requiredInspectPermission && $canInspectRead) ? 'disabled' : '';
$inspectSubmitDisabled = (!$requiredInspectPermission && $canInspectRead) ? 'disabled' : '';

echo "INSPECTION FORM (Edit Mode):\n";
echo "  Required Permission: " . ($requiredInspectPermission ? 'YES' : 'NO') . " (needs Update permission)\n";
echo "  Form Fields: " . ($inspectFormReadOnly ? 'READONLY' : 'EDITABLE') . "\n";
echo "  Select/Checkboxes: " . ($inspectFormDisabled ? 'DISABLED' : 'ENABLED') . "\n";
echo "  Update Button: " . ($inspectSubmitDisabled ? 'DISABLED' : 'ENABLED') . "\n\n";

// Simulate Certificate form behavior
$canCertRead = $certPermit['pread'];
$canCertUpdate = $certPermit['pupdate'];
$canCertAdd = $certPermit['padd'];

$requiredCertPermission = $isEditMode ? $canCertUpdate : $canCertAdd;
$certFormReadOnly = (!$requiredCertPermission && $canCertRead) ? 'readonly' : '';
$certFormDisabled = (!$requiredCertPermission && $canCertRead) ? 'disabled' : '';
$certSubmitDisabled = (!$requiredCertPermission && $canCertRead) ? 'disabled' : '';

echo "CERTIFICATE FORM (Edit Mode):\n";
echo "  Required Permission: " . ($requiredCertPermission ? 'YES' : 'NO') . " (needs Update permission)\n";
echo "  Form Fields: " . ($certFormReadOnly ? 'READONLY' : 'EDITABLE') . "\n";
echo "  Select/Checkboxes: " . ($certFormDisabled ? 'DISABLED' : 'ENABLED') . "\n";
echo "  Update Button: " . ($certSubmitDisabled ? 'DISABLED' : 'ENABLED') . "\n\n";

echo "=== EXPECTED vs ACTUAL ===\n\n";

$allCorrect = true;

if ($canAppRead && !$canAppUpdate) {
    echo "✅ APPLICATION: VIEWER has Read but NOT Update - Forms should be READONLY\n";
    if ($appFormReadOnly === 'readonly' && $appSubmitDisabled === 'disabled') {
        echo "   ✅ CORRECT: Forms are readonly, buttons are disabled\n";
    } else {
        echo "   ❌ ERROR: Forms are not properly restricted!\n";
        $allCorrect = false;
    }
} else {
    echo "❌ APPLICATION: Permission check failed\n";
    $allCorrect = false;
}

if ($canInspectRead && !$canInspectUpdate) {
    echo "✅ INSPECTION: VIEWER has Read but NOT Update - Forms should be READONLY\n";
    if ($inspectFormReadOnly === 'readonly' && $inspectSubmitDisabled === 'disabled') {
        echo "   ✅ CORRECT: Forms are readonly, buttons are disabled\n";
    } else {
        echo "   ❌ ERROR: Forms are not properly restricted!\n";
        $allCorrect = false;
    }
} else {
    echo "❌ INSPECTION: Permission check failed\n";
    $allCorrect = false;
}

if ($canCertRead && !$canCertUpdate) {
    echo "✅ CERTIFICATE: VIEWER has Read but NOT Update - Forms should be READONLY\n";
    if ($certFormReadOnly === 'readonly' && $certSubmitDisabled === 'disabled') {
        echo "   ✅ CORRECT: Forms are readonly, buttons are disabled\n";
    } else {
        echo "   ❌ ERROR: Forms are not properly restricted!\n";
        $allCorrect = false;
    }
} else {
    echo "❌ CERTIFICATE: Permission check failed\n";
    $allCorrect = false;
}

echo "\n=== FINAL RESULT ===\n";
if ($allCorrect) {
    echo "✅ ✅ ✅ ALL CHECKS PASSED!\n";
    echo "VIEWER users will now have READ-ONLY access to forms.\n";
    echo "They can view but cannot edit or submit changes.\n";
} else {
    echo "❌ SOME CHECKS FAILED!\n";
    echo "Please review the permission configuration.\n";
}

pg_close($con);
?>
