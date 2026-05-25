<?php
/**
 * Permission Setup Script - Based on Permission Matrix
 * 
 * This script sets up all permissions for 5 user groups across 8 modules
 * according to the defined permission matrix.
 * 
 * Permission Levels:
 * - Full Access: Read=yes, Add=yes, Update=yes, Delete=yes
 * - Read-Only: Read=yes, Add=no, Update=no, Delete=no
 * - Process: Read=yes, Add=yes, Update=yes, Delete=no
 * - Process (own data): Read=yes, Add=yes, Update=yes, Delete=no (with row-level security)
 * - No Access: Read=no, Add=no, Update=no, Delete=no
 */

require_once("php-bin/connection.php");

// Step 1: Get or create User Groups
$userGroups = [
    'System Admin (DOA)' => ['title' => 'System Admin (DOA)', 'desc' => 'Full system access for Department of Agriculture administrators'],
    'Group Admin' => ['title' => 'Group Admin', 'desc' => 'Administrative access with monitoring capabilities'],
    'Data Officer (DOA/PAEO)' => ['title' => 'Data Officer (DOA/PAEO)', 'desc' => 'Process own data for applications and certificates'],
    'LAB Officer (PPC and ATC)' => ['title' => 'LAB Officer (PPC and ATC)', 'desc' => 'Laboratory operations and master data access'],
    'Viewer/Guest' => ['title' => 'Viewer/Guest', 'desc' => 'Read-only access to system data']
];

$groupIds = [];

echo "<h2>Setting up User Groups...</h2>";

foreach ($userGroups as $key => $group) {
    // Check if group exists
    $checkSql = "SELECT id FROM tbusergroup WHERE title = '" . pg_escape_string($con, $group['title']) . "'";
    $result = pg_query($con, $checkSql);
    
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $groupIds[$key] = $row['id'];
        echo "✓ Group exists: {$group['title']} (ID: {$groupIds[$key]})<br>";
    } else {
        // Create group
        $insertSql = "INSERT INTO tbusergroup (title, \"desc\", enabled) 
                      VALUES ('" . pg_escape_string($con, $group['title']) . "', 
                              '" . pg_escape_string($con, $group['desc']) . "', 
                              'yes') RETURNING id";
        $result = pg_query($con, $insertSql);
        if ($result) {
            $groupIds[$key] = pg_fetch_result($result, 0, 'id');
            echo "✓ Created group: {$group['title']} (ID: {$groupIds[$key]})<br>";
        } else {
            echo "✗ Error creating group: {$group['title']}<br>";
        }
    }
}

// Step 2: Get Module IDs
$modules = [
    'Main dashboard',
    'Import and Export entity\'s information',
    'Applicaton, inpsection and certificate data processing',
    'Master data',
    'Monitoring and data reporting',
    'User and user group management, and group permits',
    'User profile',
    'Laboratory'
];

$moduleIds = [];

echo "<br><h2>Verifying Modules...</h2>";

foreach ($modules as $moduleName) {
    $checkSql = "SELECT id FROM tbmodules WHERE title = '" . pg_escape_string($con, $moduleName) . "'";
    $result = pg_query($con, $checkSql);
    
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $moduleIds[$moduleName] = $row['id'];
        echo "✓ Module found: {$moduleName} (ID: {$moduleIds[$moduleName]})<br>";
    } else {
        echo "✗ Module not found: {$moduleName}<br>";
    }
}

// Step 3: Define Permission Matrix
// Based on the image provided by the user
$permissionMatrix = [
    // System Admin (DOA) - Full Access to everything
    'System Admin (DOA)' => [
        'Main dashboard' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'yes'],
        'Import and Export entity\'s information' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'yes'],
        'Applicaton, inpsection and certificate data processing' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'yes'],
        'Master data' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'yes'],
        'Monitoring and data reporting' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'yes'],
        'User and user group management, and group permits' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'yes'],
        'User profile' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'yes'],
        'Laboratory' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'yes']
    ],
    
    // Group Admin - Read-Only for most, Process for Monitoring, Full Access for own profile
    'Group Admin' => [
        'Main dashboard' => ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no'],
        'Import and Export entity\'s information' => ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no'],
        'Applicaton, inpsection and certificate data processing' => ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no'],
        'Master data' => ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no'],
        'Monitoring and data reporting' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'no'], // Process
        'User and user group management, and group permits' => ['read' => 'no', 'add' => 'no', 'update' => 'no', 'delete' => 'no'], // No Access
        'User profile' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'yes'], // Full Access (its own)
        'Laboratory' => ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no']
    ],
    
    // Data Officer (DOA/PAEO) - Process own data
    'Data Officer (DOA/PAEO)' => [
        'Main dashboard' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'no'], // Process own data
        'Import and Export entity\'s information' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'no'], // Process own data
        'Applicaton, inpsection and certificate data processing' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'no'], // Process own data
        'Master data' => ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no'], // Read-Only
        'Monitoring and data reporting' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'no'], // Process
        'User and user group management, and group permits' => ['read' => 'no', 'add' => 'no', 'update' => 'no', 'delete' => 'no'], // No Access
        'User profile' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'yes'], // Full Access (its own)
        'Laboratory' => ['read' => 'no', 'add' => 'no', 'update' => 'no', 'delete' => 'no'] // No Access
    ],
    
    // LAB Officer (PPC and ATC) - Laboratory operations
    'LAB Officer (PPC and ATC)' => [
        'Main dashboard' => ['read' => 'no', 'add' => 'no', 'update' => 'no', 'delete' => 'no'], // No Access
        'Import and Export entity\'s information' => ['read' => 'no', 'add' => 'no', 'update' => 'no', 'delete' => 'no'], // No Access
        'Applicaton, inpsection and certificate data processing' => ['read' => 'no', 'add' => 'no', 'update' => 'no', 'delete' => 'no'], // No Access
        'Master data' => ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no'], // Read-Only
        'Monitoring and data reporting' => ['read' => 'no', 'add' => 'no', 'update' => 'no', 'delete' => 'no'], // No Access
        'User and user group management, and group permits' => ['read' => 'no', 'add' => 'no', 'update' => 'no', 'delete' => 'no'], // No Access
        'User profile' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'yes'], // Full Access (its own)
        'Laboratory' => ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'no'] // Process
    ],
    
    // Viewer/Guest - Read-Only for most
    'Viewer/Guest' => [
        'Main dashboard' => ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no'],
        'Import and Export entity\'s information' => ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no'],
        'Applicaton, inpsection and certificate data processing' => ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no'],
        'Master data' => ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no'],
        'Monitoring and data reporting' => ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no'],
        'User and user group management, and group permits' => ['read' => 'no', 'add' => 'no', 'update' => 'no', 'delete' => 'no'], // No Access
        'User profile' => ['read' => 'no', 'add' => 'no', 'update' => 'no', 'delete' => 'no'], // No Access
        'Laboratory' => ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no']
    ]
];

// Step 4: Clear existing permissions (optional - uncomment if you want to start fresh)
// echo "<br><h2>Clearing existing permissions...</h2>";
// $deleteSql = "DELETE FROM tbgrouppermits";
// pg_query($con, $deleteSql);
// echo "✓ Cleared all existing permissions<br>";

// Step 5: Insert all permissions
echo "<br><h2>Setting up Permissions...</h2>";

$successCount = 0;
$errorCount = 0;
$skipCount = 0;

foreach ($permissionMatrix as $groupName => $modules) {
    if (!isset($groupIds[$groupName])) {
        echo "✗ Group not found: {$groupName}<br>";
        continue;
    }
    
    $gid = $groupIds[$groupName];
    
    foreach ($modules as $moduleName => $permissions) {
        if (!isset($moduleIds[$moduleName])) {
            echo "✗ Module not found: {$moduleName}<br>";
            continue;
        }
        
        $mid = $moduleIds[$moduleName];
        
        // Check if permission already exists
        $checkSql = "SELECT id FROM tbgrouppermits WHERE gid='$gid' AND mid='$mid'";
        $result = pg_query($con, $checkSql);
        
        if ($result && pg_num_rows($result) > 0) {
            // Update existing permission
            $updateSql = "UPDATE tbgrouppermits 
                          SET pread='{$permissions['read']}', 
                              padd='{$permissions['add']}', 
                              pupdate='{$permissions['update']}', 
                              pdelete='{$permissions['delete']}'
                          WHERE gid='$gid' AND mid='$mid'";
            $updateResult = pg_query($con, $updateSql);
            
            if ($updateResult) {
                echo "↻ Updated: {$groupName} → {$moduleName} (R:{$permissions['read']}, A:{$permissions['add']}, U:{$permissions['update']}, D:{$permissions['delete']})<br>";
                $successCount++;
            } else {
                echo "✗ Error updating: {$groupName} → {$moduleName}<br>";
                $errorCount++;
            }
        } else {
            // Insert new permission
            $insertSql = "INSERT INTO tbgrouppermits (gid, mid, pread, padd, pupdate, pdelete) 
                          VALUES ('$gid', '$mid', '{$permissions['read']}', '{$permissions['add']}', '{$permissions['update']}', '{$permissions['delete']}')";
            $insertResult = pg_query($con, $insertSql);
            
            if ($insertResult) {
                echo "✓ Created: {$groupName} → {$moduleName} (R:{$permissions['read']}, A:{$permissions['add']}, U:{$permissions['update']}, D:{$permissions['delete']})<br>";
                $successCount++;
            } else {
                echo "✗ Error creating: {$groupName} → {$moduleName}<br>";
                $errorCount++;
            }
        }
    }
}

// Summary
echo "<br><h2>Summary</h2>";
echo "<p>✓ Successful operations: <strong>{$successCount}</strong></p>";
echo "<p>✗ Errors: <strong>{$errorCount}</strong></p>";

echo "<br><h2>Permission Matrix Setup Complete!</h2>";
echo "<p><a href='users.php?part=upermits'>View Permissions</a> | <a href='index.php'>Go to Login</a></p>";

?>
