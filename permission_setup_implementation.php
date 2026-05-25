<?php
/**
 * Permission Setup Implementation Script
 * 
 * This script helps set up module codes and permissions for the ePhytosanitary system
 * Run this once to initialize or update the permission structure
 * 
 * @author ePhyto Development Team
 * @date May 5, 2026
 */

require_once("php-bin/connection.php");
require_once("php-bin/supports.php");

// Prevent direct browser access - uncomment in production
// if (php_sapi_name() !== 'cli') {
//     die('This script can only be run from command line');
// }

echo "<h1>Permission Setup Script</h1>\n";
echo "<pre>\n";

// Step 1: Update/Insert Standard Module Codes
echo "=== STEP 1: Setting up Module Codes ===\n\n";

$standardModules = [
    // Application Processing Modules
    [
        'code' => 'APP-MAIN',
        'title' => 'Main Dashboard',
        'desc' => 'Main dashboard - first page after login',
        'file' => 'main.php'
    ],
    [
        'code' => 'APP-ENTITY',
        'title' => 'Application Processing',
        'desc' => 'Application data processing',
        'file' => 'application.php'
    ],
    [
        'code' => 'APP-INSPECT',
        'title' => 'Inspection Processing',
        'desc' => 'Inspection results and processing',
        'file' => 'inspection.php'
    ],
    [
        'code' => 'APP-CERT',
        'title' => 'Certificate Issuance',
        'desc' => 'Certificate processing and issuance',
        'file' => 'certificate.php'
    ],
    [
        'code' => 'APP-MONITOR',
        'title' => 'Monitoring and Reporting',
        'desc' => 'Data monitoring and reporting',
        'file' => 'monitor_report.php'
    ],
    [
        'code' => 'APP-EXPORT',
        'title' => 'Export Information',
        'desc' => 'Export entity information management',
        'file' => 'exp'
    ],
    [
        'code' => 'APP-IMPORT',
        'title' => 'Import Information',
        'desc' => 'Import countries information management',
        'file' => 'imp'
    ],
    
    // Master Data Modules
    [
        'code' => 'MST-DATA',
        'title' => 'Master Data',
        'desc' => 'Master data - basic information of the system',
        'file' => 'masterdata.php'
    ],
    [
        'code' => 'MST-PRODUCT',
        'title' => 'Product Management',
        'desc' => 'Product catalog and classifications',
        'file' => 'masterdata.php?part=product'
    ],
    [
        'code' => 'MST-COUNTRY',
        'title' => 'Country Management',
        'desc' => 'Country master data management',
        'file' => 'masterdata.php?part=countries'
    ],
    [
        'code' => 'MST-LOCATION',
        'title' => 'Location Management',
        'desc' => 'Location and border point management',
        'file' => 'masterdata.php?part=locations'
    ],
    [
        'code' => 'MST-CONVEY',
        'title' => 'Conveyance Management',
        'desc' => 'Conveyance type management',
        'file' => 'masterdata.php?part=conveyance'
    ],
    [
        'code' => 'MST-MODULE',
        'title' => 'Module Configuration',
        'desc' => 'System module management',
        'file' => 'masterdata.php?part=modules'
    ],
    
    // User Management Modules
    [
        'code' => 'USR-PROFILE',
        'title' => 'User Profile',
        'desc' => 'User profile management',
        'file' => 'users-profile.php'
    ],
    [
        'code' => 'USR-GROUP',
        'title' => 'User Group',
        'desc' => 'User group management',
        'file' => 'users.php?part=ugroup'
    ],
    [
        'code' => 'USR-PERMIT',
        'title' => 'Group Permissions',
        'desc' => 'Group permission management',
        'file' => 'users.php?part=upermits'
    ],
    [
        'code' => 'USR-LIST',
        'title' => 'User Management',
        'desc' => 'User account management',
        'file' => 'users.php?part=userslist'
    ],
];

foreach ($standardModules as $module) {
    $code = pg_escape_string($con, $module['code']);
    $title = pg_escape_string($con, $module['title']);
    $desc = pg_escape_string($con, $module['desc']);
    
    // Check if module exists
    $checkSql = "SELECT id, code, title FROM tbmodules WHERE code = '$code'";
    $result = pg_query($con, $checkSql);
    
    if (pg_num_rows($result) > 0) {
        // Update existing module
        $row = pg_fetch_assoc($result);
        $id = $row['id'];
        $updateSql = "UPDATE tbmodules SET title = '$title', \"desc\" = '$desc' WHERE id = $id";
        if (pg_query($con, $updateSql)) {
            echo "✓ Updated module: $code - $title\n";
        } else {
            echo "✗ Error updating module $code: " . pg_last_error($con) . "\n";
        }
    } else {
        // Insert new module
        $insertSql = "INSERT INTO tbmodules (code, title, \"desc\", enabled) 
                      VALUES ('$code', '$title', '$desc', 'yes')";
        if (pg_query($con, $insertSql)) {
            echo "✓ Added new module: $code - $title\n";
        } else {
            echo "✗ Error adding module $code: " . pg_last_error($con) . "\n";
        }
    }
}

echo "\n=== STEP 2: Setting up Permission Templates ===\n\n";

// Define permission templates for each user group
$permissionTemplates = [
    1 => [ // System Administrator - Full access
        'name' => 'Administrator',
        'modules' => [
            'APP-MAIN' => ['yes', 'yes', 'yes', 'yes'],
            'APP-ENTITY' => ['yes', 'yes', 'yes', 'yes'],
            'APP-INSPECT' => ['yes', 'yes', 'yes', 'yes'],
            'APP-CERT' => ['yes', 'yes', 'yes', 'yes'],
            'APP-MONITOR' => ['yes', 'yes', 'yes', 'yes'],
            'APP-EXPORT' => ['yes', 'yes', 'yes', 'yes'],
            'APP-IMPORT' => ['yes', 'yes', 'yes', 'yes'],
            'MST-DATA' => ['yes', 'yes', 'yes', 'yes'],
            'MST-PRODUCT' => ['yes', 'yes', 'yes', 'yes'],
            'MST-COUNTRY' => ['yes', 'yes', 'yes', 'yes'],
            'MST-LOCATION' => ['yes', 'yes', 'yes', 'yes'],
            'MST-CONVEY' => ['yes', 'yes', 'yes', 'yes'],
            'MST-MODULE' => ['yes', 'yes', 'yes', 'yes'],
            'USR-PROFILE' => ['yes', 'yes', 'yes', 'yes'],
            'USR-GROUP' => ['yes', 'yes', 'yes', 'yes'],
            'USR-PERMIT' => ['yes', 'yes', 'yes', 'yes'],
            'USR-LIST' => ['yes', 'yes', 'yes', 'yes'],
        ]
    ],
    2 => [ // Provincial Office (PAFO)
        'name' => 'Provincial Office',
        'modules' => [
            'APP-MAIN' => ['yes', 'no', 'no', 'no'],
            'APP-ENTITY' => ['yes', 'yes', 'yes', 'no'],
            'APP-INSPECT' => ['yes', 'yes', 'yes', 'no'],
            'APP-CERT' => ['yes', 'yes', 'yes', 'no'],
            'APP-MONITOR' => ['yes', 'no', 'no', 'no'],
            'APP-EXPORT' => ['yes', 'yes', 'yes', 'no'],
            'APP-IMPORT' => ['yes', 'no', 'no', 'no'],
            'MST-DATA' => ['yes', 'no', 'no', 'no'],
            'MST-PRODUCT' => ['yes', 'no', 'no', 'no'],
            'MST-COUNTRY' => ['yes', 'no', 'no', 'no'],
            'MST-LOCATION' => ['yes', 'no', 'no', 'no'],
            'USR-PROFILE' => ['yes', 'no', 'yes', 'no'],
        ]
    ],
    3 => [ // Border Point Officer
        'name' => 'Border Point',
        'modules' => [
            'APP-MAIN' => ['yes', 'no', 'no', 'no'],
            'APP-ENTITY' => ['yes', 'yes', 'yes', 'no'],
            'APP-INSPECT' => ['yes', 'yes', 'yes', 'no'],
            'APP-CERT' => ['yes', 'yes', 'no', 'no'],
            'APP-EXPORT' => ['yes', 'yes', 'yes', 'no'],
            'APP-IMPORT' => ['yes', 'no', 'no', 'no'],
            'MST-DATA' => ['yes', 'no', 'no', 'no'],
            'MST-PRODUCT' => ['yes', 'no', 'no', 'no'],
            'MST-COUNTRY' => ['yes', 'no', 'no', 'no'],
            'USR-PROFILE' => ['yes', 'no', 'yes', 'no'],
        ]
    ],
    4 => [ // Laboratory Technician
        'name' => 'Laboratory',
        'modules' => [
            'APP-MAIN' => ['yes', 'no', 'no', 'no'],
            'APP-INSPECT' => ['yes', 'yes', 'yes', 'no'],
            'MST-DATA' => ['yes', 'no', 'no', 'no'],
            'MST-PRODUCT' => ['yes', 'no', 'no', 'no'],
            'USR-PROFILE' => ['yes', 'no', 'yes', 'no'],
        ]
    ],
    5 => [ // External Viewer/Other
        'name' => 'Viewer',
        'modules' => [
            'APP-MAIN' => ['yes', 'no', 'no', 'no'],
            'APP-ENTITY' => ['yes', 'no', 'no', 'no'],
            'APP-INSPECT' => ['yes', 'no', 'no', 'no'],
            'APP-CERT' => ['yes', 'no', 'no', 'no'],
            'APP-MONITOR' => ['yes', 'no', 'no', 'no'],
            'MST-DATA' => ['yes', 'no', 'no', 'no'],
            'USR-PROFILE' => ['yes', 'no', 'yes', 'no'],
        ]
    ]
];

// Apply permissions for each group
foreach ($permissionTemplates as $groupId => $groupConfig) {
    $groupName = $groupConfig['name'];
    echo "--- Setting up permissions for: $groupName (Group ID: $groupId) ---\n";
    
    foreach ($groupConfig['modules'] as $moduleCode => $perms) {
        // Get module ID from code
        $moduleCode = pg_escape_string($con, $moduleCode);
        $sql = "SELECT id FROM tbmodules WHERE code='$moduleCode' AND enabled='yes'";
        $result = pg_query($con, $sql);
        
        if ($result && pg_num_rows($result) > 0) {
            $row = pg_fetch_assoc($result);
            $moduleId = $row['id'];
            list($read, $add, $update, $delete) = $perms;
            
            // Check if permission already exists
            $checkPermSql = "SELECT id FROM tbgrouppermits WHERE gid = $groupId AND mid = $moduleId";
            $permResult = pg_query($con, $checkPermSql);
            
            if (pg_num_rows($permResult) > 0) {
                // Update existing permission
                $permRow = pg_fetch_assoc($permResult);
                $permId = $permRow['id'];
                $updatePermSql = "UPDATE tbgrouppermits 
                                  SET pread='$read', padd='$add', pupdate='$update', pdelete='$delete' 
                                  WHERE id = $permId";
                if (pg_query($con, $updatePermSql)) {
                    echo "  ✓ Updated: $moduleCode [R:$read, A:$add, U:$update, D:$delete]\n";
                } else {
                    echo "  ✗ Error updating permission for $moduleCode: " . pg_last_error($con) . "\n";
                }
            } else {
                // Insert new permission
                $insertPermSql = "INSERT INTO tbgrouppermits (gid, mid, pread, padd, pupdate, pdelete) 
                                  VALUES ($groupId, $moduleId, '$read', '$add', '$update', '$delete')";
                if (pg_query($con, $insertPermSql)) {
                    echo "  ✓ Added: $moduleCode [R:$read, A:$add, U:$update, D:$delete]\n";
                } else {
                    echo "  ✗ Error adding permission for $moduleCode: " . pg_last_error($con) . "\n";
                }
            }
        } else {
            echo "  ⚠ Warning: Module '$moduleCode' not found or disabled\n";
        }
    }
    echo "\n";
}

echo "=== STEP 3: Verification ===\n\n";

// Verify setup
$verifySql = "SELECT 
    ug.id as group_id, 
    ug.title as group_name,
    COUNT(gp.id) as permission_count
FROM tbusergroup ug
LEFT JOIN tbgrouppermits gp ON ug.id = gp.gid
WHERE ug.enabled = 'yes'
GROUP BY ug.id, ug.title
ORDER BY ug.id";

$verifyResult = pg_query($con, $verifySql);

echo "Permission Summary by Group:\n";
echo str_repeat("-", 60) . "\n";
printf("%-5s %-30s %s\n", "ID", "Group Name", "Permissions");
echo str_repeat("-", 60) . "\n";

while ($row = pg_fetch_assoc($verifyResult)) {
    printf("%-5s %-30s %s\n", 
        $row['group_id'], 
        $row['group_name'], 
        $row['permission_count']
    );
}

echo str_repeat("-", 60) . "\n";
echo "\n=== Setup Complete! ===\n\n";

echo "Next Steps:\n";
echo "1. Review the permissions assigned to each group\n";
echo "2. Test user access with different group accounts\n";
echo "3. Add MODULE_CODE constant to each PHP file\n";
echo "4. Update permission checks to use module codes\n";
echo "5. Refer to MODULE_NAMING_PERMISSIONS_GUIDE.md for details\n\n";

echo "To view detailed permissions, run:\n";
echo "SELECT ug.title, m.code, m.title, gp.pread, gp.padd, gp.pupdate, gp.pdelete\n";
echo "FROM tbgrouppermits gp\n";
echo "JOIN tbusergroup ug ON gp.gid = ug.id\n";
echo "JOIN tbmodules m ON gp.mid = m.id\n";
echo "WHERE ug.enabled='yes' AND m.enabled='yes'\n";
echo "ORDER BY ug.id, m.code;\n";

echo "</pre>\n";

pg_close($con);
?>
