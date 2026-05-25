<?php
/**
 * Custom Permission Setup Script
 * 
 * User Groups (4 groups):
 * 1. admin - System Administrators with full access
 * 2. Data Officer - Main user group (role determined by group_admin flag):
 *    - If group_admin='yes' → Group Admin role (read-only for most, Process for monitoring)
 *    - If group_admin='no' → Data Officer role (full access to data processing)
 * 3. LAB - Laboratory officers for lab operations
 * 4. VIEWER - Guest viewers with read-only access
 * 
 * Module Codes:
 * - PG-MAIN: Main dashboard
 * - FRM - ENTITY: Import and Export entity
 * - FRM - DATA PROCESSING: Application/Inspection/Certificate processing
 * - PG-APPLICATION: Application module
 * - PG-INSPECTION: Inspection module
 * - PG-CERTIFICATE: Certificate module
 * - FRM - MASTER DATA: Master data management
 * - PG-MR: Monitoring and reporting
 * - FRM - USERS_PERMIT: User and permission management
 * - FRM-USERPROFILE: User profile
 * - APP-LAB: Laboratory
 */

require_once("php-bin/connection.php");

echo "<h1>Custom Permission Setup</h1>";
echo "<p>Setting up permissions based on user group and group_admin roles...</p>";

// Define permission levels
$fullAccess = ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'yes'];
$process = ['read' => 'yes', 'add' => 'yes', 'update' => 'yes', 'delete' => 'no'];
$readOnly = ['read' => 'yes', 'add' => 'no', 'update' => 'no', 'delete' => 'no'];
$noAccess = ['read' => 'no', 'add' => 'no', 'update' => 'no', 'delete' => 'no'];

// Get or create user groups (4 groups only)
$userGroups = [
    'admin' => ['title' => 'admin', 'desc' => 'System Administrators with full access'],
    'Data Officer' => ['title' => 'Data Officer', 'desc' => 'Main user group - role determined by group_admin flag (yes=Group Admin, no=Data Officer)'],
    'LAB' => ['title' => 'LAB', 'desc' => 'Laboratory officers for lab operations'],
    'VIEWER' => ['title' => 'VIEWER', 'desc' => 'Guest viewers with read-only access to most modules']
];

$groupIds = [];

echo "<h2>Step 1: Setting up User Groups</h2>";

foreach ($userGroups as $key => $group) {
    $checkSql = "SELECT id FROM tbusergroup WHERE title = '" . pg_escape_string($con, $group['title']) . "'";
    $result = pg_query($con, $checkSql);
    
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $groupIds[$key] = $row['id'];
        echo "✓ Group exists: {$group['title']} (ID: {$groupIds[$key]})<br>";
    } else {
        $insertSql = "INSERT INTO tbusergroup (title, \"desc\", enabled) 
                      VALUES ('" . pg_escape_string($con, $group['title']) . "', 
                              '" . pg_escape_string($con, $group['desc']) . "', 
                              'yes') RETURNING id";
        $result = pg_query($con, $insertSql);
        if ($result) {
            $groupIds[$key] = pg_fetch_result($result, 0, 'id');
            echo "✓ Created group: {$group['title']} (ID: {$groupIds[$key]})<br>";
        }
    }
}

// Get module IDs
$modules = [
    'PG-MAIN' => 'Main dashboard',
    'FRM - ENTITY' => 'Import and Export entity\'s information',
    'FRM - DATA PROCESSING' => 'Applicaton, inpsection and certificate data processing',
    'PG-APPLICATION' => 'Application module',
    'PG-INSPECTION' => 'Inspection module',
    'PG-CERTIFICATE' => 'Certificate module',
    'FRM - MASTER DATA' => 'Master data',
    'PG-MR' => 'Monitoring and data reporting',
    'FRM - USERS_PERMIT' => 'User and user group management, and group permits',
    'FRM-USERPROFILE' => 'User profile',
    'APP-LAB' => 'Laboratory'
];

$moduleIds = [];

echo "<br><h2>Step 2: Verifying Modules</h2>";

foreach ($modules as $code => $title) {
    // Try to find by code first, then by title
    $checkSql = "SELECT id, code, title FROM tbmodules WHERE code = '" . pg_escape_string($con, $code) . "' 
                 OR title = '" . pg_escape_string($con, $title) . "' LIMIT 1";
    $result = pg_query($con, $checkSql);
    
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $moduleIds[$code] = $row['id'];
        echo "✓ Module found: {$code} - {$title} (ID: {$moduleIds[$code]})<br>";
    } else {
        echo "⚠ Module not found: {$code} - {$title}<br>";
    }
}

// Define permission matrix according to specifications
echo "<br><h2>Step 3: Setting up Permission Matrix</h2>";

$permissionMatrix = [];

// 1. System Administrator (admin group) - Full access to ALL modules
if (isset($groupIds['admin'])) {
    foreach ($moduleIds as $code => $mid) {
        $permissionMatrix['admin'][$code] = $fullAccess;
    }
    echo "<strong>1. System Administrator (admin):</strong> Full access to all modules<br>";
}

// 2. Data Officer - Base permissions (UserPermitCheck() function differentiates between group_admin='yes' and 'no')
// Set full access as base - group_admin='yes' users will be restricted by UserPermitCheck() function
if (isset($groupIds['Data Officer'])) {
    $dataOfficerFullAccess = ['PG-MAIN', 'FRM - ENTITY', 'FRM - DATA PROCESSING', 
                              'PG-MR', 'PG-APPLICATION', 'PG-INSPECTION', 'PG-CERTIFICATE', 'FRM-USERPROFILE'];
    
    foreach ($dataOfficerFullAccess as $code) {
        if (isset($moduleIds[$code])) {
            $permissionMatrix['Data Officer'][$code] = $fullAccess;
        }
    }
    
    // Read-only for master data
    if (isset($moduleIds['FRM - MASTER DATA'])) {
        $permissionMatrix['Data Officer']['FRM - MASTER DATA'] = $readOnly;
    }
    
    // No access to LAB
    if (isset($moduleIds['APP-LAB'])) {
        $permissionMatrix['Data Officer']['APP-LAB'] = $noAccess;
    }
    
    echo "<strong>2. Data Officer:</strong> Full access to data processing modules (group_admin flag differentiates Group Admin vs Data Officer role)<br>";
}

// 3. Lab Officer (LAB group) - Limited access
if (isset($groupIds['LAB'])) {
    // Read-only for entity
    if (isset($moduleIds['FRM - ENTITY'])) {
        $permissionMatrix['LAB']['FRM - ENTITY'] = $readOnly;
    }
    
    // Full access for profile and lab
    if (isset($moduleIds['FRM-USERPROFILE'])) {
        $permissionMatrix['LAB']['FRM-USERPROFILE'] = $fullAccess;
    }
    if (isset($moduleIds['APP-LAB'])) {
        $permissionMatrix['LAB']['APP-LAB'] = $fullAccess;
    }
    
    // No access to other modules
    $noAccessModules = ['PG-MAIN', 'FRM - DATA PROCESSING', 'PG-APPLICATION', 
                        'PG-INSPECTION', 'PG-CERTIFICATE', 'FRM - MASTER DATA', 'PG-MR'];
    foreach ($noAccessModules as $code) {
        if (isset($moduleIds[$code])) {
            $permissionMatrix['LAB'][$code] = $noAccess;
        }
    }
    
    echo "<strong>3. Lab Officer (LAB):</strong> Read-only entity, Full lab and profile, No access to others<br>";
}

// 4. Viewer (VIEWER group) - Guest with read-only access
if (isset($groupIds['VIEWER'])) {
    // Read-only for all modules except users management
    $viewerReadOnlyModules = ['PG-MAIN', 'FRM - ENTITY', 'FRM - DATA PROCESSING', 'PG-APPLICATION', 
                              'PG-INSPECTION', 'PG-CERTIFICATE', 'FRM - MASTER DATA', 'PG-MR', 'APP-LAB'];
    
    foreach ($viewerReadOnlyModules as $code) {
        if (isset($moduleIds[$code])) {
            $permissionMatrix['VIEWER'][$code] = $readOnly;
        }
    }
    
    // Full access to user profile (their own profile)
    if (isset($moduleIds['FRM-USERPROFILE'])) {
        $permissionMatrix['VIEWER']['FRM-USERPROFILE'] = $fullAccess;
    }
    
    // No access to user management
    if (isset($moduleIds['FRM - USERS_PERMIT'])) {
        $permissionMatrix['VIEWER']['FRM - USERS_PERMIT'] = $noAccess;
    }
    
    echo "<strong>4. Viewer (VIEWER):</strong> Read-only for all modules, Full access to own profile, No access to user management<br>";
}

// Insert or update permissions
echo "<br><h2>Step 4: Applying Permissions</h2>";

$successCount = 0;
$updateCount = 0;
$skipCount = 0;

foreach ($permissionMatrix as $groupKey => $modules) {
    if (!isset($groupIds[$groupKey])) continue;
    
    $gid = $groupIds[$groupKey];
    
    foreach ($modules as $code => $permissions) {
        if (!isset($moduleIds[$code])) {
            $skipCount++;
            continue;
        }
        
        $mid = $moduleIds[$code];
        
        // Check if permission exists
        $checkSql = "SELECT id FROM tbgrouppermits WHERE gid='$gid' AND mid='$mid'";
        $result = pg_query($con, $checkSql);
        
        if ($result && pg_num_rows($result) > 0) {
            // Update existing
            $updateSql = "UPDATE tbgrouppermits 
                          SET pread='{$permissions['read']}', 
                              padd='{$permissions['add']}', 
                              pupdate='{$permissions['update']}', 
                              pdelete='{$permissions['delete']}'
                          WHERE gid='$gid' AND mid='$mid'";
            if (pg_query($con, $updateSql)) {
                echo "↻ Updated: {$groupKey} → {$code} (R:{$permissions['read']}, A:{$permissions['add']}, U:{$permissions['update']}, D:{$permissions['delete']})<br>";
                $updateCount++;
            }
        } else {
            // Insert new
            $insertSql = "INSERT INTO tbgrouppermits (gid, mid, pread, padd, pupdate, pdelete) 
                          VALUES ('$gid', '$mid', '{$permissions['read']}', '{$permissions['add']}', '{$permissions['update']}', '{$permissions['delete']}')";
            if (pg_query($con, $insertSql)) {
                echo "✓ Created: {$groupKey} → {$code} (R:{$permissions['read']}, A:{$permissions['add']}, U:{$permissions['update']}, D:{$permissions['delete']})<br>";
                $successCount++;
            }
        }
    }
}

echo "<br><h2>Summary</h2>";
echo "<p>✓ New permissions created: <strong>{$successCount}</strong></p>";
echo "<p>↻ Permissions updated: <strong>{$updateCount}</strong></p>";
echo "<p>⊘ Skipped (module not found): <strong>{$skipCount}</strong></p>";

echo "<br><div class='alert alert-info'>";
echo "<h3>Important Notes:</h3>";
echo "<ul>";
echo "<li><strong>User Groups:</strong> There are 4 user groups: admin, Data Officer, LAB, VIEWER</li>";
echo "<li><strong>Group Admin is NOT a user group:</strong> It's a role within 'Data Officer' group determined by group_admin='yes' flag</li>";
echo "<li><strong>Data Officer group roles:</strong>";
echo "<ul>";
echo "<li>If group_admin='yes' → <strong>Group Admin role</strong> (read-only for most modules, Process for monitoring)</li>";
echo "<li>If group_admin='no' → <strong>Data Officer role</strong> (full access to data processing modules)</li>";
echo "</ul></li>";
echo "<li><strong>LAB group:</strong> Laboratory officers (group_admin flag not applicable)</li>";
echo "<li><strong>VIEWER group:</strong> Guest viewers with read-only access (group_admin flag not applicable)</li>";
echo "<li><strong>Permission Enforcement:</strong> The <code>UserPermitCheck()</code> function checks group_admin flag and applies appropriate permissions</li>";
echo "</ul>";
echo "</div>";

echo "<br><h2>✅ Permission Setup Complete!</h2>";
echo "<p><a href='users.php?part=upermits' class='btn btn-primary'>View Permissions</a> ";
echo "<a href='users.php?part=userslist' class='btn btn-secondary'>Manage Users</a></p>";

?>
