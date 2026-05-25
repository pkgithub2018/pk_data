<?php
require("php-bin/connection.php");
require("php-bin/supports.php");

echo "=== VIEWER PERMISSION TEST ===\n\n";

// Get a VIEWER user (if exists)
$sqlUser = "SELECT u.id, u.username, u.email, g.title as group_name 
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
    echo "  Username: {$user['username']}\n";
    echo "  Group: {$user['group_name']}\n\n";
} else {
    echo "No VIEWER user found. Using test group ID 13 (VIEWER group)\n\n";
    $userid = null;
}

echo "=== PERMISSION CHECK RESULTS ===\n\n";

$modulesToTest = [
    ['code' => 'PG-MAIN', 'description' => 'Main Dashboard'],
    ['code' => 'PG-APPLICATION', 'description' => 'Application'],
    ['code' => 'PG-INSPECTION', 'description' => 'Inspection'],
    ['code' => 'PG-CERTIFICATE', 'description' => 'Certificate'],
    ['code' => 'FRM - ENTITY', 'description' => 'Entity Form'],
    ['code' => 'FRM - MASTER DATA', 'description' => 'Master Data'],
    ['code' => 'FRM - USERS_PERMIT', 'description' => 'User Management'],
    ['code' => 'FRM - MODULE', 'description' => 'Modules'],
    ['code' => 'FRM-USERPROFILE', 'description' => 'User Profile'],
    ['code' => 'PG-MR', 'description' => 'Monitoring & Reporting'],
    ['code' => 'APP-LAB', 'description' => 'Laboratory']
];

if ($userid) {
    foreach ($modulesToTest as $module) {
        $permit = UserPermitCheck($userid, $module['code'], $con);
        
        echo str_pad($module['code'], 25) . " - " . str_pad($module['description'], 30);
        
        if ($permit['exists']) {
            $perms = [];
            if ($permit['pread']) $perms[] = 'Read';
            if ($permit['padd']) $perms[] = 'Add';
            if ($permit['pupdate']) $perms[] = 'Update';
            if ($permit['pdelete']) $perms[] = 'Delete';
            
            echo " [✓] " . (count($perms) > 0 ? implode(', ', $perms) : 'NO PERMISSIONS') . "\n";
            echo str_repeat(' ', 58) . "Reason: {$permit['reason']}\n";
        } else {
            echo " [✗] NOT FOUND\n";
            echo str_repeat(' ', 58) . "Reason: {$permit['reason']}\n";
        }
    }
} else {
    echo "Cannot test UserPermitCheck without a valid user ID.\n";
    echo "Testing GrouppermitCheck with VIEWER group ID (13) instead:\n\n";
    
    foreach ($modulesToTest as $module) {
        $permit = GrouppermitCheck(13, $module['code'], $con);
        
        echo str_pad($module['code'], 25) . " - " . str_pad($module['description'], 30);
        
        if ($permit['exists']) {
            $perms = [];
            if ($permit['pread']) $perms[] = 'Read';
            if ($permit['padd']) $perms[] = 'Add';
            if ($permit['pupdate']) $perms[] = 'Update';
            if ($permit['pdelete']) $perms[] = 'Delete';
            
            echo " [✓] " . (count($perms) > 0 ? implode(', ', $perms) : 'NO PERMISSIONS') . "\n";
        } else {
            echo " [✗] NOT FOUND\n";
        }
    }
}

echo "\n=== SUMMARY ===\n";
echo "VIEWER group should have:\n";
echo "  ✓ Read-only access to: PG-MAIN, PG-APPLICATION, PG-INSPECTION, PG-CERTIFICATE\n";
echo "  ✓ Read-only access to: FRM - ENTITY, FRM - MASTER DATA, PG-MR, APP-LAB\n";
echo "  ✓ Full access to: FRM-USERPROFILE (own profile)\n";
echo "  ✗ No access to: FRM - USERS_PERMIT (user management)\n";
echo "  ✗ Should not see: FRM - MODULE (if no permission set)\n";

pg_close($con);
?>
