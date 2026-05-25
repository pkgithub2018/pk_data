<?php
session_start();
require("php-bin/connection.php");
require("php-bin/supports.php");

// This page can be accessed directly to test VIEWER permissions
// Usage: viewer_permission_test.php?uid=USER_ID

$userid = isset($_GET['uid']) ? $_GET['uid'] : '';

if (empty($userid)) {
    echo "<h1>Permission Test Page</h1>";
    echo "<p>Please provide a user ID: viewer_permission_test.php?uid=USER_ID</p>";
    exit;
}

// Get user data
$userdata = Userdata($userid, $con);
if (!$userdata) {
    echo "<h1>Error</h1>";
    echo "<p>User not found with ID: $userid</p>";
    exit;
}

$groupid = $userdata['group_id'];
$groupname = GroupName($groupid, $con);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VIEWER Permission Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        h2 {
            color: #34495e;
            margin-top: 30px;
        }
        .user-info {
            background: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #3498db;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .status-ok {
            color: #27ae60;
            font-weight: bold;
        }
        .status-denied {
            color: #e74c3c;
            font-weight: bold;
        }
        .status-partial {
            color: #f39c12;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        .summary {
            background: #e8f4f8;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔒 VIEWER Permission Test</h1>
        
        <div class="user-info">
            <strong>User ID:</strong> <?php echo htmlspecialchars($userid); ?><br>
            <strong>Name:</strong> <?php echo htmlspecialchars($userdata['name'] ?? 'N/A'); ?><br>
            <strong>Email:</strong> <?php echo htmlspecialchars($userdata['email'] ?? 'N/A'); ?><br>
            <strong>Group ID:</strong> <?php echo htmlspecialchars($groupid); ?><br>
            <strong>Group Name:</strong> <?php echo htmlspecialchars($groupname); ?>
        </div>

        <?php if (strtoupper($groupname) !== 'VIEWER'): ?>
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
            <strong>⚠️ Warning:</strong> This user is not in the VIEWER group. 
            Current group: <strong><?php echo htmlspecialchars($groupname); ?></strong>
        </div>
        <?php endif; ?>

        <h2>📋 Permission Check Results</h2>

        <table>
            <thead>
                <tr>
                    <th>Module Code</th>
                    <th>Module Name</th>
                    <th>Status</th>
                    <th>Permissions</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $modulesToTest = [
                    ['code' => 'PG-MAIN', 'name' => 'Main Dashboard'],
                    ['code' => 'PG-APPLICATION', 'name' => 'Application Dashboard'],
                    ['code' => 'PG-INSPECTION', 'name' => 'Inspection Dashboard'],
                    ['code' => 'PG-CERTIFICATE', 'name' => 'Certificate Dashboard'],
                    ['code' => 'FRM - ENTITY', 'name' => 'Entity Management'],
                    ['code' => 'FRM - MASTER DATA', 'name' => 'Master Data'],
                    ['code' => 'FRM - USERS_PERMIT', 'name' => 'User Management'],
                    ['code' => 'FRM - MODULE', 'name' => 'Module Management'],
                    ['code' => 'FRM-USERPROFILE', 'name' => 'User Profile'],
                    ['code' => 'PG-MR', 'name' => 'Monitoring & Reporting'],
                    ['code' => 'APP-LAB', 'name' => 'Laboratory'],
                ];

                $totalOk = 0;
                $totalDenied = 0;
                $totalPartial = 0;

                foreach ($modulesToTest as $module) {
                    $permit = UserPermitCheck($userid, $module['code'], $con);
                    
                    $permissions = [];
                    if ($permit['pread']) $permissions[] = '<span class="badge badge-success">Read</span>';
                    if ($permit['padd']) $permissions[] = '<span class="badge badge-success">Add</span>';
                    if ($permit['pupdate']) $permissions[] = '<span class="badge badge-success">Update</span>';
                    if ($permit['pdelete']) $permissions[] = '<span class="badge badge-success">Delete</span>';
                    
                    $permissionText = count($permissions) > 0 ? implode(' ', $permissions) : '<span class="badge badge-danger">None</span>';
                    
                    if ($permit['exists']) {
                        if ($permit['pread'] || $permit['padd'] || $permit['pupdate'] || $permit['pdelete']) {
                            $status = '<span class="status-ok">✓ Found</span>';
                            $totalOk++;
                        } else {
                            $status = '<span class="status-partial">! Found (No Permissions)</span>';
                            $totalPartial++;
                        }
                    } else {
                        $status = '<span class="status-denied">✗ Not Found</span>';
                        $totalDenied++;
                    }
                    
                    echo "<tr>";
                    echo "<td><code>" . htmlspecialchars($module['code']) . "</code></td>";
                    echo "<td>" . htmlspecialchars($module['name']) . "</td>";
                    echo "<td>$status</td>";
                    echo "<td>$permissionText</td>";
                    echo "<td><small>" . htmlspecialchars($permit['reason']) . "</small></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="summary">
            <h3>📊 Summary</h3>
            <p>
                <strong>Total Modules Tested:</strong> <?php echo count($modulesToTest); ?><br>
                <strong class="status-ok">✓ Accessible:</strong> <?php echo $totalOk; ?><br>
                <strong class="status-partial">! Restricted:</strong> <?php echo $totalPartial; ?><br>
                <strong class="status-denied">✗ Not Found:</strong> <?php echo $totalDenied; ?>
            </p>
        </div>

        <h2>📚 Expected VIEWER Permissions</h2>
        <div style="background: #e8f4f8; padding: 15px; border-radius: 5px;">
            <p><strong>VIEWER users should have:</strong></p>
            <ul>
                <li>✅ <strong>Read-only access</strong> to: Main Dashboard, Application, Inspection, Certificate</li>
                <li>✅ <strong>Read-only access</strong> to: Entity Management, Master Data, Monitoring & Reporting, Laboratory</li>
                <li>✅ <strong>Full access</strong> to: User Profile (own profile only)</li>
                <li>❌ <strong>No access</strong> to: User Management, Module Management</li>
            </ul>
        </div>

        <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <p><strong>Note:</strong> If permissions are not as expected, verify:</p>
            <ol>
                <li>Database has correct module codes with proper spacing (e.g., <code>FRM - MASTER DATA</code> not <code>FRM-MASTER DATA</code>)</li>
                <li>PHP files use correct module codes matching the database</li>
                <li>Group permissions are properly set in <code>tbgrouppermits</code> table</li>
                <li>Modules are enabled in <code>tbmodules</code> table</li>
            </ol>
        </div>
    </div>
</body>
</html>
