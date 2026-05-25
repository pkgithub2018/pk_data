<?php
/**
 * Setup Permissions for FRM - MODULE (Module data form)
 * 
 * This script adds permissions for the new Module data form module to all user groups
 * 
 * @author ePhyto Development Team
 * @date May 18, 2026
 */

require_once("php-bin/connection.php");
require_once("php-bin/supports.php");

echo "<!DOCTYPE html>\n";
echo "<html><head><meta charset='UTF-8'><title>FRM - MODULE Permission Setup</title></head><body>\n";
echo "<h1>FRM - MODULE Permission Setup</h1>\n";
echo "<pre>\n";

// Step 1: Check if the module exists
echo "=== Step 1: Verifying Module Exists ===\n\n";

$moduleCode = 'FRM - MODULE';
$moduleCodeAlt = 'FRM-MODULE'; // Try alternative format without spaces

$sql = "SELECT id, code, title, enabled FROM tbmodules WHERE code IN ('$moduleCode', '$moduleCodeAlt')";
$result = pg_query($con, $sql);

if (!$result || pg_num_rows($result) == 0) {
    echo "❌ ERROR: Module '$moduleCode' or '$moduleCodeAlt' not found in tbmodules table!\n\n";
    echo "Please make sure the module is added to tbmodules first.\n";
    echo "You can add it using this SQL:\n\n";
    echo "INSERT INTO tbmodules (code, title, filename, enabled) \n";
    echo "VALUES ('FRM - MODULE', 'Module data form', 'modules.php', 'yes');\n\n";
    echo "</pre></body></html>";
    exit();
}

$moduleRow = pg_fetch_assoc($result);
$moduleId = $moduleRow['id'];
$actualCode = $moduleRow['code'];
$moduleTitle = $moduleRow['title'];
$moduleEnabled = $moduleRow['enabled'];

echo "✓ Found Module:\n";
echo "  ID: $moduleId\n";
echo "  Code: $actualCode\n";
echo "  Title: $moduleTitle\n";
echo "  Enabled: $moduleEnabled\n\n";

if ($moduleEnabled != 'yes') {
    echo "⚠ WARNING: Module is disabled. You may want to enable it first.\n\n";
}

// Step 2: Define permission matrix
echo "=== Step 2: Permission Matrix for FRM - MODULE ===\n\n";

/**
 * Permission Matrix:
 * FRM - MODULE (Module data form) - Administrative module for managing module list
 * 
 * Access Levels by Group:
 * - System Admin: Full Access (can manage all modules)
 * - Group Admin: No Access (cannot manage system modules)
 * - Data Officer: No Access (cannot manage system modules)
 * - LAB Officer: No Access (cannot manage system modules)
 * - Viewer: No Access (read-only user)
 */

$userGroups = [
    1 => ['name' => 'System Admin (DOA)', 'perms' => ['yes', 'yes', 'yes', 'yes']],     // Full Access
    2 => ['name' => 'Group Admin', 'perms' => ['no', 'no', 'no', 'no']],                 // No Access
    3 => ['name' => 'Data Officer (DOA/PAEO)', 'perms' => ['no', 'no', 'no', 'no']],     // No Access
    4 => ['name' => 'LAB Officer (PPC and ATC)', 'perms' => ['no', 'no', 'no', 'no']],   // No Access
    5 => ['name' => 'Viewer/Guest', 'perms' => ['no', 'no', 'no', 'no']],               // No Access
];

foreach ($userGroups as $gid => $groupData) {
    $groupName = $groupData['name'];
    list($read, $add, $update, $delete) = $groupData['perms'];
    
    // Determine access level
    if ($read == 'yes' && $add == 'yes' && $update == 'yes' && $delete == 'yes') {
        $accessLevel = 'Full Access';
    } elseif ($read == 'yes' && $add == 'no' && $update == 'no' && $delete == 'no') {
        $accessLevel = 'Read-Only';
    } else {
        $accessLevel = 'No Access';
    }
    
    echo "Group $gid ($groupName): $accessLevel\n";
    echo "  Read=$read, Add=$add, Update=$update, Delete=$delete\n";
}

echo "\n=== Step 3: Applying Permissions ===\n\n";

$successCount = 0;
$updateCount = 0;
$errorCount = 0;

foreach ($userGroups as $groupId => $groupData) {
    $groupName = $groupData['name'];
    list($read, $add, $update, $delete) = $groupData['perms'];
    
    // Verify group exists
    $groupCheckSql = "SELECT id, title FROM tbusergroup WHERE id = $groupId";
    $groupResult = pg_query($con, $groupCheckSql);
    
    if (!$groupResult || pg_num_rows($groupResult) == 0) {
        echo "⚠ Group ID $groupId not found, skipping...\n";
        continue;
    }
    
    $groupRow = pg_fetch_assoc($groupResult);
    $actualGroupName = $groupRow['title'];
    
    echo "Processing Group $groupId: $actualGroupName\n";
    
    // Check if permission already exists
    $checkSql = "SELECT id, pread, padd, pupdate, pdelete 
                 FROM tbgrouppermits 
                 WHERE gid = $groupId AND mid = $moduleId";
    $checkResult = pg_query($con, $checkSql);
    
    if ($checkResult && pg_num_rows($checkResult) > 0) {
        // Permission exists - update if different
        $permRow = pg_fetch_assoc($checkResult);
        $permId = $permRow['id'];
        
        if ($permRow['pread'] != $read || $permRow['padd'] != $add || 
            $permRow['pupdate'] != $update || $permRow['pdelete'] != $delete) {
            
            $updateSql = "UPDATE tbgrouppermits 
                         SET pread='$read', padd='$add', pupdate='$update', pdelete='$delete' 
                         WHERE id = $permId";
            
            if (pg_query($con, $updateSql)) {
                echo "  ✓ Updated existing permission (ID: $permId)\n";
                $updateCount++;
            } else {
                echo "  ✗ Error updating: " . pg_last_error($con) . "\n";
                $errorCount++;
            }
        } else {
            echo "  ✓ Permission already correct (no update needed)\n";
            $successCount++;
        }
    } else {
        // Permission doesn't exist - insert new
        $insertSql = "INSERT INTO tbgrouppermits (gid, mid, pread, padd, pupdate, pdelete) 
                     VALUES ($groupId, $moduleId, '$read', '$add', '$update', '$delete')";
        
        if (pg_query($con, $insertSql)) {
            echo "  ✓ Added new permission\n";
            $successCount++;
        } else {
            echo "  ✗ Error inserting: " . pg_last_error($con) . "\n";
            $errorCount++;
        }
    }
}

echo "\n=== Step 4: Verification ===\n\n";

// Display final permission state
$verifySql = "SELECT 
    ug.id as gid,
    ug.title as group_name,
    gp.pread,
    gp.padd,
    gp.pupdate,
    gp.pdelete,
    gp.id as perm_id
FROM tbusergroup ug
LEFT JOIN tbgrouppermits gp ON ug.id = gp.gid AND gp.mid = $moduleId
ORDER BY ug.id";

$verifyResult = pg_query($con, $verifySql);

echo "Current permissions for '$actualCode':\n\n";
echo "┌──────┬────────────────────────────┬──────┬─────┬────────┬────────┐\n";
echo "│ GID  │ Group Name                 │ Read │ Add │ Update │ Delete │\n";
echo "├──────┼────────────────────────────┼──────┼─────┼────────┼────────┤\n";

while ($row = pg_fetch_assoc($verifyResult)) {
    $gid = str_pad($row['gid'], 4);
    $gname = str_pad(substr($row['group_name'], 0, 26), 26);
    $read = str_pad($row['pread'] ?? 'NULL', 4);
    $add = str_pad($row['padd'] ?? 'NULL', 3);
    $update = str_pad($row['pupdate'] ?? 'NULL', 6);
    $delete = str_pad($row['pdelete'] ?? 'NULL', 6);
    
    echo "│ $gid │ $gname │ $read │ $add │ $update │ $delete │\n";
}

echo "└──────┴────────────────────────────┴──────┴─────┴────────┴────────┘\n\n";

// Summary
echo "=== Summary ===\n\n";
echo "Module: $actualCode (ID: $moduleId)\n";
echo "✓ Permissions already correct: $successCount\n";
echo "✓ Permissions updated: $updateCount\n";
if ($errorCount > 0) {
    echo "✗ Errors encountered: $errorCount\n";
}

echo "\n✅ Setup complete!\n\n";

echo "Next steps:\n";
echo "1. Clear any cached permissions\n";
echo "2. Test access with different user groups\n";
echo "3. Verify menu visibility in application.php, certificate.php, inspection.php\n";
echo "4. Make sure modules.php has proper permission checks\n\n";

echo "</pre></body></html>";
?>
