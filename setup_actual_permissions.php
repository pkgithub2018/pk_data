<?php
/**
 * Custom Permission Setup for ePhytosanitary System
 * Based on actual module codes and permission matrix
 * 
 * This script sets up permissions according to your specific requirements
 * 
 * @author ePhyto Development Team
 * @date May 6, 2026
 */

require_once("php-bin/connection.php");
require_once("php-bin/supports.php");

echo "<h1>Custom Permission Setup</h1>\n";
echo "<pre>\n";

// Define your actual user groups
echo "=== Your User Groups ===\n\n";
$userGroups = [
    1 => 'System Admin (DOA)',
    2 => 'Group Admin',
    3 => 'Data Officer (DOA/PAEO)',
    4 => 'LAB Officer (PPC and ATC)',
    5 => 'Viewer/Guest'
];

foreach ($userGroups as $gid => $gname) {
    echo "$gid. $gname\n";
}

echo "\n=== Setting up Permissions Based on Your Matrix ===\n\n";

/**
 * Permission Matrix based on your image:
 * 
 * Module Codes:
 * - PG-MAIN: Main dashboard
 * - FRM - ENTITY: Import & Export entity
 * - FRM - DATA PROCESSING: Application/Inspection/Certificate
 * - FRM - MASTER DATA: Master Data
 * - PG-MR: Monitoring and data reporting
 * - FRM - USERS_PERMIT: User, User group and permission
 * - FRM-USERPROFILE: User profile
 * - APP-LAB: Laboratory
 * 
 * Access Levels:
 * - Full Access: Read=yes, Add=yes, Update=yes, Delete=yes
 * - Process: Read=yes, Add=yes, Update=yes, Delete=no (or yes depending on context)
 * - Read-Only: Read=yes, Add=no, Update=no, Delete=no
 * - No Access: Read=no, Add=no, Update=no, Delete=no
 */

$permissionMatrix = [
    // Group 1: System Admin (DOA) - Full Access to everything
    1 => [
        'PG-MAIN' => ['yes', 'yes', 'yes', 'yes'],
        'FRM - ENTITY' => ['yes', 'yes', 'yes', 'yes'],
        'FRM - DATA PROCESSING' => ['yes', 'yes', 'yes', 'yes'],
        'FRM - MASTER DATA' => ['yes', 'yes', 'yes', 'yes'],
        'PG-MR' => ['yes', 'yes', 'yes', 'yes'],
        'FRM - USERS_PERMIT' => ['yes', 'yes', 'yes', 'yes'],
        'FRM-USERPROFILE' => ['yes', 'yes', 'yes', 'yes'],
        'APP-LAB' => ['yes', 'yes', 'yes', 'yes'],
    ],
    
    // Group 2: Group Admin
    2 => [
        'PG-MAIN' => ['yes', 'no', 'no', 'no'],              // Read-Only
        'FRM - ENTITY' => ['yes', 'no', 'no', 'no'],          // Read-Only
        'FRM - DATA PROCESSING' => ['yes', 'no', 'no', 'no'], // Read-Only
        'FRM - MASTER DATA' => ['yes', 'no', 'no', 'no'],     // Read-Only
        'PG-MR' => ['yes', 'yes', 'yes', 'no'],              // Process
        'FRM - USERS_PERMIT' => ['no', 'no', 'no', 'no'],     // No Access
        'FRM-USERPROFILE' => ['yes', 'yes', 'yes', 'yes'],    // Full Access (its own)
        'APP-LAB' => ['yes', 'no', 'no', 'no'],              // Read-Only
    ],
    
    // Group 3: Data Officer (DOA/PAEO) - Process its own data
    3 => [
        'PG-MAIN' => ['yes', 'yes', 'yes', 'no'],             // Process (its own data and dashboard)
        'FRM - ENTITY' => ['yes', 'yes', 'yes', 'no'],        // Process its own data
        'FRM - DATA PROCESSING' => ['yes', 'yes', 'yes', 'no'], // Process its own data
        'FRM - MASTER DATA' => ['yes', 'no', 'no', 'no'],     // Read-Only
        'PG-MR' => ['yes', 'yes', 'yes', 'no'],              // Process
        'FRM - USERS_PERMIT' => ['no', 'no', 'no', 'no'],     // No Access
        'FRM-USERPROFILE' => ['yes', 'yes', 'yes', 'yes'],    // Full Access (its own)
        'APP-LAB' => ['no', 'no', 'no', 'no'],               // No Access
    ],
    
    // Group 4: LAB Officer (PPC and ATC)
    4 => [
        'PG-MAIN' => ['no', 'no', 'no', 'no'],               // No Access
        'FRM - ENTITY' => ['no', 'no', 'no', 'no'],          // No Access
        'FRM - DATA PROCESSING' => ['no', 'no', 'no', 'no'], // No Access
        'FRM - MASTER DATA' => ['yes', 'no', 'no', 'no'],    // Read-Only
        'PG-MR' => ['no', 'no', 'no', 'no'],                 // No Access
        'FRM - USERS_PERMIT' => ['no', 'no', 'no', 'no'],    // No Access
        'FRM-USERPROFILE' => ['yes', 'yes', 'yes', 'yes'],   // Full Access (its own)
        'APP-LAB' => ['yes', 'yes', 'yes', 'no'],           // Process
    ],
    
    // Group 5: Viewer/Guest
    5 => [
        'PG-MAIN' => ['yes', 'no', 'no', 'no'],              // Read-Only
        'FRM - ENTITY' => ['yes', 'no', 'no', 'no'],         // Read-Only
        'FRM - DATA PROCESSING' => ['yes', 'no', 'no', 'no'], // Read-Only
        'FRM - MASTER DATA' => ['yes', 'no', 'no', 'no'],    // Read-Only
        'PG-MR' => ['yes', 'no', 'no', 'no'],               // Read-Only
        'FRM - USERS_PERMIT' => ['no', 'no', 'no', 'no'],    // No Access
        'FRM-USERPROFILE' => ['no', 'no', 'no', 'no'],       // No Access
        'APP-LAB' => ['yes', 'no', 'no', 'no'],             // Read-Only
    ],
];

// Apply permissions for each group
foreach ($permissionMatrix as $groupId => $modules) {
    $groupName = $userGroups[$groupId];
    echo "--- Setting up permissions for: $groupName (Group ID: $groupId) ---\n";
    
    foreach ($modules as $moduleCode => $perms) {
        // Get module ID from code
        $moduleCodeEscaped = pg_escape_string($con, $moduleCode);
        $sql = "SELECT id, title FROM tbmodules WHERE code='$moduleCodeEscaped' AND enabled='yes'";
        $result = pg_query($con, $sql);
        
        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            $moduleId = $row['id'];
            $moduleTitle = $row['title'];
            list($read, $add, $update, $delete) = $perms;
            
            // Determine access level description
            $accessLevel = '';
            if ($read == 'yes' && $add == 'yes' && $update == 'yes' && $delete == 'yes') {
                $accessLevel = 'Full Access';
            } elseif ($read == 'yes' && $add == 'yes' && $update == 'yes' && $delete == 'no') {
                $accessLevel = 'Process';
            } elseif ($read == 'yes' && $add == 'no' && $update == 'no' && $delete == 'no') {
                $accessLevel = 'Read-Only';
            } else {
                $accessLevel = 'No Access';
            }
            
            // Check if permission already exists
            $checkPermSql = "SELECT id, pread, padd, pupdate, pdelete FROM tbgrouppermits 
                            WHERE gid = $groupId AND mid = $moduleId";
            $permResult = pg_query($con, $checkPermSql);
            
            if (pg_num_rows($permResult) > 0) {
                // Update existing permission
                $permRow = pg_fetch_assoc($permResult);
                $permId = $permRow['id'];
                
                // Check if anything changed
                if ($permRow['pread'] != $read || $permRow['padd'] != $add || 
                    $permRow['pupdate'] != $update || $permRow['pdelete'] != $delete) {
                    
                    $updatePermSql = "UPDATE tbgrouppermits 
                                      SET pread='$read', padd='$add', pupdate='$update', pdelete='$delete' 
                                      WHERE id = $permId";
                    if (pg_query($con, $updatePermSql)) {
                        echo "  ✓ Updated: $moduleCode → $accessLevel\n";
                    } else {
                        echo "  ✗ Error updating $moduleCode: " . pg_last_error($con) . "\n";
                    }
                } else {
                    echo "  ✓ Already set: $moduleCode → $accessLevel\n";
                }
            } else {
                // Insert new permission
                $insertPermSql = "INSERT INTO tbgrouppermits (gid, mid, pread, padd, pupdate, pdelete) 
                                  VALUES ($groupId, $moduleId, '$read', '$add', '$update', '$delete')";
                if (pg_query($con, $insertPermSql)) {
                    echo "  ✓ Added: $moduleCode → $accessLevel\n";
                } else {
                    echo "  ✗ Error adding $moduleCode: " . pg_last_error($con) . "\n";
                }
            }
        } else {
            echo "  ⚠ Warning: Module '$moduleCode' not found or disabled\n";
        }
    }
    echo "\n";
}

echo "=== Verification: Permission Summary ===\n\n";

// Generate permission summary table
$verifySql = "SELECT 
    ug.id as gid,
    ug.title as group_name,
    m.code as module_code,
    m.title as module_title,
    gp.pread,
    gp.padd,
    gp.pupdate,
    gp.pdelete
FROM tbusergroup ug
LEFT JOIN tbgrouppermits gp ON ug.id = gp.gid
LEFT JOIN tbmodules m ON gp.mid = m.id
WHERE ug.enabled = 'yes' AND (m.enabled = 'yes' OR m.enabled IS NULL)
ORDER BY ug.id, m.code";

$verifyResult = pg_query($con, $verifySql);

$currentGroup = null;
while ($row = pg_fetch_assoc($verifyResult)) {
    if ($currentGroup !== $row['gid']) {
        $currentGroup = $row['gid'];
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "Group {$row['gid']}: {$row['group_name']}\n";
        echo str_repeat("=", 80) . "\n";
        printf("%-30s %-8s %-8s %-8s %-8s\n", "Module", "Read", "Add", "Update", "Delete");
        echo str_repeat("-", 80) . "\n";
    }
    
    if ($row['module_code']) {
        printf("%-30s %-8s %-8s %-8s %-8s\n", 
            substr($row['module_code'], 0, 30),
            $row['pread'],
            $row['padd'],
            $row['pupdate'],
            $row['pdelete']
        );
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "\n=== Setup Complete! ===\n\n";

echo "Summary:\n";
echo "- All permissions have been configured according to your matrix\n";
echo "- System Admin (DOA) has full access to all modules\n";
echo "- Group Admin can process monitoring/reporting and manage own profile\n";
echo "- Data Officers can process their own data\n";
echo "- LAB Officers can process laboratory and view master data\n";
echo "- Viewers have read-only access\n\n";

echo "Next Steps:\n";
echo "1. Test each user group's access in the system\n";
echo "2. Verify users can only see/edit what they're allowed to\n";
echo "3. Add MODULE_CODE constants to your PHP files\n";
echo "4. Implement row-level security for 'Process its own data' feature\n\n";

echo "IMPORTANT NOTE:\n";
echo "For 'Process its own data' feature (Data Officers), you'll need to add\n";
echo "additional checks in your application code to ensure users can only\n";
echo "edit records they created. Example:\n\n";
echo "  if (\$canUpdate && \$record['created_by_uid'] == \$userid) {\n";
echo "      // Allow edit\n";
echo "  }\n\n";

echo "</pre>\n";

pg_close($con);
?>
