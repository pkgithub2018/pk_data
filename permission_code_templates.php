/*
 * Code Templates for Module Permission Implementation
 * Copy and adapt these code snippets to your PHP files
 */

// ============================================================
// TEMPLATE 1: Add to the TOP of each PHP file (before session_start)
// ============================================================

<?php
/**
 * Define module code for permission checking
 * This should be unique for each file/module
 */
define('MODULE_CODE', 'APP-MAIN'); // Change to appropriate code for each file
define('MODULE_TITLE', 'Main Dashboard'); // Change to appropriate title

session_start();
require("php-bin/connection.php");
require("php-bin/supports.php");

// Rest of your code...
?>


// ============================================================
// TEMPLATE 2: Permission Check (Replace existing check in your files)
// ============================================================

<?php
// Get user information
$userid = isset($_SESSION["uid"]) ? $_SESSION["uid"] : '';

// Authentication check
if(empty($userid)){
    echo "<script>alert('You are not logged in. Please log in to access this page.');</script>"; 
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}

// Get user data
$userinfo = Userdata($userid, $con);
$groupid = isset($userinfo['group_id']) ? $userinfo['group_id'] : '';

// Check group permission using MODULE_CODE
$groupPermit = GrouppermitCheck($groupid, MODULE_CODE, $con);
$canRead = $groupPermit['pread'];
$canAdd = $groupPermit['padd'];
$canUpdate = $groupPermit['pupdate'];
$canDelete = $groupPermit['pdelete'];

// Block access if user doesn't have read permission
if ($groupPermit['exists'] && !$canRead) {
    echo "<script>alert('You do not have permission to access this page.');</script>";
    echo "<script>window.location.href = 'main.php';</script>";
    exit();
}
?>


// ============================================================
// TEMPLATE 3: Conditional Display Based on Permissions (in HTML/PHP)
// ============================================================

<?php
// Example: Show "Add New" button only if user has add permission
if ($canAdd) {
    echo '<button class="btn btn-success" onclick="showAddForm()">
            <i class="bi bi-plus-circle"></i> Add New
          </button>';
}

// Example: Enable/Disable edit button based on update permission
?>
<button class="btn btn-primary" 
        onclick="editRecord(<?php echo $id; ?>)" 
        <?php echo $canUpdate ? '' : 'disabled'; ?>>
    <i class="bi bi-pencil"></i> Edit
</button>

<?php
// Example: Show delete button only if user has delete permission
if ($canDelete) {
    echo '<button class="btn btn-danger" onclick="deleteRecord('.$id.')">
            <i class="bi bi-trash"></i> Delete
          </button>';
}
?>


// ============================================================
// TEMPLATE 4: Form Submission with Permission Check
// ============================================================

<?php
// Example: Add new record (check permission first)
if (isset($_POST['btnAdd'])) {
    if (!$canAdd) {
        echo "<script>alert('You do not have permission to add records.');</script>";
        exit();
    }
    
    // Process the add operation
    $name = pg_escape_string($con, $_POST['name']);
    $desc = pg_escape_string($con, $_POST['desc']);
    
    // Your insert code here...
}

// Example: Update record (check permission first)
if (isset($_POST['btnUpdate'])) {
    if (!$canUpdate) {
        echo "<script>alert('You do not have permission to update records.');</script>";
        exit();
    }
    
    // Process the update operation
    $id = $_POST['id'];
    $name = pg_escape_string($con, $_POST['name']);
    
    // Your update code here...
}

// Example: Delete record (check permission first)
if (isset($_GET['delete'])) {
    if (!$canDelete) {
        echo "<script>alert('You do not have permission to delete records.');</script>";
        exit();
    }
    
    // Process the delete operation
    $id = $_GET['id'];
    
    // Your delete code here...
}
?>


// ============================================================
// TEMPLATE 5: Dynamic Navigation Menu (show only permitted modules)
// ============================================================

<?php
/**
 * Function to check if user can access a module
 */
function canAccessModule($groupid, $moduleCode, $con) {
    $permit = GrouppermitCheck($groupid, $moduleCode, $con);
    return $permit['pread']; // User needs at least read permission
}

// Build navigation menu based on permissions
$menuItems = [
    ['code' => 'APP-MAIN', 'title' => 'Dashboard', 'url' => 'main.php', 'icon' => 'bi-grid'],
    ['code' => 'APP-ENTITY', 'title' => 'Applications', 'url' => 'application.php', 'icon' => 'bi-journal-text'],
    ['code' => 'APP-INSPECT', 'title' => 'Inspection', 'url' => 'inspection.php', 'icon' => 'bi-search'],
    ['code' => 'APP-CERT', 'title' => 'Certificates', 'url' => 'certificate.php', 'icon' => 'bi-file-earmark-text'],
    ['code' => 'MST-DATA', 'title' => 'Master Data', 'url' => 'masterdata.php', 'icon' => 'bi-database'],
    ['code' => 'USR-LIST', 'title' => 'Users', 'url' => 'users.php', 'icon' => 'bi-people'],
];

echo '<ul class="sidebar-nav">';
foreach ($menuItems as $item) {
    if (canAccessModule($groupid, $item['code'], $con)) {
        echo '<li class="nav-item">
                <a class="nav-link" href="'.$item['url'].'">
                    <i class="'.$item['icon'].'"></i>
                    <span>'.$item['title'].'</span>
                </a>
              </li>';
    }
}
echo '</ul>';
?>


// ============================================================
// TEMPLATE 6: Ajax Permission Check (for dynamic content)
// ============================================================

<?php
// check_permission.php - AJAX endpoint for checking permissions
session_start();
require("php-bin/connection.php");
require("php-bin/supports.php");

header('Content-Type: application/json');

$userid = isset($_SESSION["uid"]) ? $_SESSION["uid"] : '';
$moduleCode = isset($_GET['module']) ? $_GET['module'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : 'read'; // read, add, update, delete

if (empty($userid) || empty($moduleCode)) {
    echo json_encode(['allowed' => false, 'message' => 'Invalid request']);
    exit();
}

$userinfo = Userdata($userid, $con);
$groupid = isset($userinfo['group_id']) ? $userinfo['group_id'] : '';
$permit = GrouppermitCheck($groupid, $moduleCode, $con);

$allowed = false;
switch ($action) {
    case 'read':
        $allowed = $permit['pread'];
        break;
    case 'add':
        $allowed = $permit['padd'];
        break;
    case 'update':
        $allowed = $permit['pupdate'];
        break;
    case 'delete':
        $allowed = $permit['pdelete'];
        break;
}

echo json_encode([
    'allowed' => $allowed,
    'module' => $moduleCode,
    'action' => $action
]);
?>

<!-- JavaScript to use the permission check -->
<script>
function checkPermissionAndExecute(moduleCode, action, callback) {
    fetch(`check_permission.php?module=${moduleCode}&action=${action}`)
        .then(response => response.json())
        .then(data => {
            if (data.allowed) {
                callback();
            } else {
                alert('You do not have permission to perform this action.');
            }
        })
        .catch(error => {
            console.error('Permission check error:', error);
            alert('Error checking permissions.');
        });
}

// Usage example
document.getElementById('btnDelete').addEventListener('click', function() {
    checkPermissionAndExecute('APP-ENTITY', 'delete', function() {
        // Proceed with delete operation
        deleteRecord(recordId);
    });
});
</script>


// ============================================================
// TEMPLATE 7: Permission-aware Data Table
// ============================================================

<?php
/**
 * Display data table with action buttons based on permissions
 */
function displayDataTable($data, $canUpdate, $canDelete) {
    echo '<table class="table datatable">';
    echo '<thead>
            <tr>
                <th>No</th>
                <th>Code</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>';
    
    // Show action column only if user has update or delete permission
    if ($canUpdate || $canDelete) {
        echo '<th>Actions</th>';
    }
    
    echo '</tr></thead><tbody>';
    
    $i = 1;
    foreach ($data as $row) {
        echo '<tr>';
        echo '<td>'.$i++.'</td>';
        echo '<td>'.$row['code'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['desc'].'</td>';
        echo '<td>'.($row['enabled'] == 'yes' ? 'Active' : 'Inactive').'</td>';
        
        if ($canUpdate || $canDelete) {
            echo '<td>';
            
            if ($canUpdate) {
                echo '<a href="edit.php?id='.$row['id'].'" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil"></i>
                      </a> ';
            }
            
            if ($canDelete) {
                echo '<a href="delete.php?id='.$row['id'].'" class="btn btn-danger btn-sm" 
                         onclick="return confirm(\'Are you sure?\')">
                        <i class="bi bi-trash"></i>
                      </a>';
            }
            
            echo '</td>';
        }
        
        echo '</tr>';
    }
    
    echo '</tbody></table>';
}
?>


// ============================================================
// TEMPLATE 8: Permission Display Helper (for debugging)
// ============================================================

<?php
/**
 * Display current user's permissions for debugging
 * Remove or comment out in production
 */
function displayCurrentPermissions($userid, $con) {
    if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
        return; // Only show in debug mode
    }
    
    $userinfo = Userdata($userid, $con);
    $groupid = isset($userinfo['group_id']) ? $userinfo['group_id'] : '';
    $groupname = Groupname($groupid, $con);
    
    if (defined('MODULE_CODE')) {
        $permit = GrouppermitCheck($groupid, MODULE_CODE, $con);
        
        echo '<div class="alert alert-info" style="position: fixed; bottom: 10px; right: 10px; z-index: 9999; max-width: 300px;">';
        echo '<strong>Debug: Current Permissions</strong><br>';
        echo 'User ID: '.$userid.'<br>';
        echo 'Group: '.$groupname.' (ID: '.$groupid.')<br>';
        echo 'Module: '.MODULE_CODE.'<br>';
        echo 'Read: '.($permit['pread'] ? '✓' : '✗').' ';
        echo 'Add: '.($permit['padd'] ? '✓' : '✗').' ';
        echo 'Update: '.($permit['pupdate'] ? '✓' : '✗').' ';
        echo 'Delete: '.($permit['pdelete'] ? '✓' : '✗');
        echo '</div>';
    }
}

// Usage: Add at the bottom of your page (only in development)
define('DEBUG_MODE', true); // Set to false in production
displayCurrentPermissions($userid, $con);
?>


// ============================================================
// TEMPLATE 9: Module Code Constants for Common Files
// ============================================================

// main.php
define('MODULE_CODE', 'APP-MAIN');
define('MODULE_TITLE', 'Main Dashboard');

// application.php
define('MODULE_CODE', 'APP-ENTITY');
define('MODULE_TITLE', 'Application Processing');

// inspection.php
define('MODULE_CODE', 'APP-INSPECT');
define('MODULE_TITLE', 'Inspection Processing');

// certificate.php
define('MODULE_CODE', 'APP-CERT');
define('MODULE_TITLE', 'Certificate Issuance');

// masterdata.php
define('MODULE_CODE', 'MST-DATA');
define('MODULE_TITLE', 'Master Data Management');

// users.php
define('MODULE_CODE', 'USR-LIST');
define('MODULE_TITLE', 'User Management');

// users-profile.php
define('MODULE_CODE', 'USR-PROFILE');
define('MODULE_TITLE', 'User Profile');


// ============================================================
// TEMPLATE 10: Enhanced GrouppermitCheck with Logging (Optional)
// ============================================================

<?php
/**
 * Enhanced permission check with audit logging
 * Add this to supports.php if you want to track permission checks
 */
function GrouppermitCheckWithLog($groupid, $moduleRef, $con, $userid = null) {
    // Call the original function
    $result = GrouppermitCheck($groupid, $moduleRef, $con);
    
    // Log the permission check (optional - for audit purposes)
    if (defined('ENABLE_PERMISSION_LOGGING') && ENABLE_PERMISSION_LOGGING) {
        $moduleCode = is_numeric($moduleRef) ? $result['module_code'] : $moduleRef;
        $timestamp = date('Y-m-d H:i:s');
        $granted = $result['pread'] ? 'GRANTED' : 'DENIED';
        
        $logSql = "INSERT INTO tbpermission_log (userid, groupid, module_code, access_result, access_time) 
                   VALUES (" . ($userid ? "'$userid'" : "NULL") . ", '$groupid', '$moduleCode', '$granted', '$timestamp')";
        
        // Execute silently - don't block on logging failure
        @pg_query($con, $logSql);
    }
    
    return $result;
}

// Create log table (run once):
/*
CREATE TABLE IF NOT EXISTS tbpermission_log (
    id SERIAL PRIMARY KEY,
    userid INTEGER,
    groupid INTEGER,
    module_code TEXT,
    access_result TEXT,
    access_time TIMESTAMP,
    ip_address TEXT
);
*/
?>


// ============================================================
// USAGE INSTRUCTIONS
// ============================================================

/*
1. Choose the appropriate template for your needs
2. Copy the code to your PHP file
3. Modify MODULE_CODE and other constants as needed
4. Test thoroughly with different user groups
5. Refer to MODULE_NAMING_PERMISSIONS_GUIDE.md for complete documentation

IMPORTANT NOTES:
- Always define MODULE_CODE at the top of each file
- Use pg_escape_string() for all user inputs
- Test with multiple user groups
- Keep permission checks consistent across all files
- Document any custom permission logic

For questions or support, refer to:
- MODULE_NAMING_PERMISSIONS_GUIDE.md
- php-bin/supports.php (GrouppermitCheck function)
- permission_setup_implementation.php
*/
