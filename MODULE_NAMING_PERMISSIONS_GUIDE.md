# Module Naming & Permission Management Guide
## ePhytosanitary Certificate System

## Overview
This guide provides best practices for organizing module codes and names to enable effective user group-based permission management.

---

## Current System Structure

### Database Tables
1. **tbmodules** - Stores all modules in the system
   - `id` - Primary key
   - `code` - Module code (unique identifier)
   - `title` - Module name/title
   - `desc` - Description
   - `enabled` - Status (yes/no)

2. **tbusergroup** - User groups
   - `id` - Primary key
   - `title` - Group name (admin, provincial, borderpoint, etc.)
   - `desc` - Description
   - `enabled` - Status (yes/no)

3. **tbgrouppermits** - Permission mapping
   - `gid` - Group ID (foreign key to tbusergroup)
   - `mid` - Module ID (foreign key to tbmodules)
   - `pread` - Read permission (yes/no)
   - `padd` - Add/Create permission (yes/no)
   - `pupdate` - Update/Edit permission (yes/no)
   - `pdelete` - Delete permission (yes/no)

---

## Recommended Module Code Naming Convention

### Format Structure
```
[PREFIX]-[CATEGORY]-[FUNCTION]
```

### Examples:

#### Core Application Modules
```
APP-MAIN       - Main Dashboard
APP-ENTITY     - Entity/Company Management
APP-INSPECT    - Inspection Processing
APP-CERT       - Certificate Issuance
APP-MONITOR    - Monitoring & Reporting
```

#### Master Data Modules
```
MST-PRODUCT    - Product Management
MST-COUNTRY    - Country Management
MST-LOCATION   - Location Management
MST-CONVEY     - Conveyance Management
MST-ENTITY     - Entity Type Management
```

#### User Management Modules
```
USR-PROFILE    - User Profile
USR-GROUP      - User Group Management
USR-PERMIT     - Group Permissions
USR-LIST       - User List Management
```

#### Import/Export Modules
```
IMP-DATA       - Import Data
EXP-DATA       - Export Data
EXP-REPORT     - Export Reports
```

---

## Module Title Naming Convention

### Guidelines
1. **Clear and Descriptive** - Use full words, avoid abbreviations
2. **Action-Oriented** - Include what the module does
3. **Consistent Format** - Follow a standard pattern

### Examples:
```
Code: APP-MAIN
Title: Main Dashboard
Description: Main dashboard - first page after login

Code: APP-ENTITY
Title: Entity Management
Description: Manage exporter/importer companies and entities

Code: APP-INSPECT
Title: Inspection Processing
Description: Process inspection requests and results

Code: APP-CERT
Title: Certificate Issuance
Description: Generate and issue phytosanitary certificates

Code: MST-PRODUCT
Title: Product Master Data
Description: Manage product catalog and classifications

Code: USR-PERMIT
Title: Group Permissions
Description: Configure user group access permissions
```

---

## Mapping File Names to Module Codes

### Current System Files → Suggested Module Codes

| Current File | Module Code | Module Title | Description |
|-------------|-------------|--------------|-------------|
| main.php | APP-MAIN | Main Dashboard | Main dashboard after login |
| application.php | APP-ENTITY | Application Processing | Application data processing |
| entity.php | APP-ENTITY-MGT | Entity Management | Entity/Company management |
| inspection.php | APP-INSPECT | Inspection Processing | Inspection results |
| certificate.php | APP-CERT | Certificate Issuance | Certificate processing and issuance |
| masterdata.php | MST-DATA | Master Data | Master data management hub |
| modules.php | SYS-MODULE | System Modules | Module configuration |
| users.php | USR-MGMT | User Management | User management hub |

---

## User Group Categories & Typical Permissions

### 1. Administrator Group
**Group ID:** 1
**Group Name:** System Administrator

**Typical Permissions:**
- All modules: Read, Add, Update, Delete

```sql
-- Example permissions for Admin group
Module: ALL
Read: YES, Add: YES, Update: YES, Delete: YES
```

### 2. Provincial Office Group
**Group ID:** 2
**Group Name:** Provincial Agriculture Office (PAFO)

**Typical Permissions:**
```
APP-MAIN       → Read: YES, Add: NO,  Update: NO,  Delete: NO
APP-ENTITY     → Read: YES, Add: YES, Update: YES, Delete: NO
APP-INSPECT    → Read: YES, Add: YES, Update: YES, Delete: NO
APP-CERT       → Read: YES, Add: YES, Update: YES, Delete: NO
MST-PRODUCT    → Read: YES, Add: NO,  Update: NO,  Delete: NO
MST-LOCATION   → Read: YES, Add: NO,  Update: NO,  Delete: NO
USR-PROFILE    → Read: YES, Add: NO,  Update: YES, Delete: NO
USR-GROUP      → Read: NO,  Add: NO,  Update: NO,  Delete: NO
```

### 3. Border Point Group
**Group ID:** 3
**Group Name:** Border Checkpoint Officer

**Typical Permissions:**
```
APP-MAIN       → Read: YES, Add: NO,  Update: NO,  Delete: NO
APP-ENTITY     → Read: YES, Add: YES, Update: YES, Delete: NO
APP-INSPECT    → Read: YES, Add: YES, Update: YES, Delete: NO
APP-CERT       → Read: YES, Add: YES, Update: NO,  Delete: NO
MST-PRODUCT    → Read: YES, Add: NO,  Update: NO,  Delete: NO
USR-PROFILE    → Read: YES, Add: NO,  Update: YES, Delete: NO
```

### 4. Laboratory Group
**Group ID:** 4
**Group Name:** Laboratory Technician

**Typical Permissions:**
```
APP-MAIN       → Read: YES, Add: NO,  Update: NO,  Delete: NO
APP-INSPECT    → Read: YES, Add: YES, Update: YES, Delete: NO
MST-PRODUCT    → Read: YES, Add: NO,  Update: NO,  Delete: NO
USR-PROFILE    → Read: YES, Add: NO,  Update: YES, Delete: NO
```

### 5. Read-Only/Viewer Group
**Group ID:** 5
**Group Name:** External Viewer

**Typical Permissions:**
```
APP-MAIN       → Read: YES, Add: NO,  Update: NO,  Delete: NO
APP-ENTITY     → Read: YES, Add: NO,  Update: NO,  Delete: NO
APP-INSPECT    → Read: YES, Add: NO,  Update: NO,  Delete: NO
APP-CERT       → Read: YES, Add: NO,  Update: NO,  Delete: NO
```

---

## Implementation Steps

### Step 1: Update Module Codes in Database

```sql
-- Update existing modules with new standardized codes
UPDATE tbmodules SET code = 'APP-MAIN' WHERE code = 'main.php';
UPDATE tbmodules SET code = 'APP-ENTITY' WHERE code = 'app';
UPDATE tbmodules SET code = 'APP-INSPECT' WHERE code = 'insp';
UPDATE tbmodules SET code = 'APP-CERT' WHERE code = 'ctf';
UPDATE tbmodules SET code = 'MST-DATA' WHERE code = 'msd';
UPDATE tbmodules SET code = 'APP-MONITOR' WHERE code = 'mrp';

-- Add new modules if needed
INSERT INTO tbmodules (code, title, "desc", enabled) 
VALUES 
  ('APP-EXPORT', 'Export Management', 'Export entities information', 'yes'),
  ('APP-IMPORT', 'Import Management', 'Import countries information', 'yes'),
  ('USR-MGMT', 'User Management', 'User account management', 'yes');
```

### Step 2: Update Permission Checking Code

The system already checks permissions using the filename. To use module codes instead:

**Current code in main.php:**
```php
$groupPermit = GrouppermitCheck($guid, basename($_SERVER['PHP_SELF']), $con);
```

**Enhanced code (supports both):**
```php
// Define module code for this page
define('MODULE_CODE', 'APP-MAIN'); // Define at top of each file

// Check permission by module code (preferred) or fall back to filename
$groupPermit = GrouppermitCheck($guid, MODULE_CODE, $con);
```

### Step 3: Create Permission Templates for Each Group

Create a setup script to initialize permissions:

```php
<?php
// permission_setup.php
require("php-bin/connection.php");
require("php-bin/supports.php");

// Define permission templates
$permissionTemplates = [
    1 => [ // Administrator - Full access to all
        'APP-MAIN' => ['yes', 'yes', 'yes', 'yes'],
        'APP-ENTITY' => ['yes', 'yes', 'yes', 'yes'],
        'APP-INSPECT' => ['yes', 'yes', 'yes', 'yes'],
        'APP-CERT' => ['yes', 'yes', 'yes', 'yes'],
        'MST-DATA' => ['yes', 'yes', 'yes', 'yes'],
        'USR-MGMT' => ['yes', 'yes', 'yes', 'yes'],
    ],
    2 => [ // Provincial Office
        'APP-MAIN' => ['yes', 'no', 'no', 'no'],
        'APP-ENTITY' => ['yes', 'yes', 'yes', 'no'],
        'APP-INSPECT' => ['yes', 'yes', 'yes', 'no'],
        'APP-CERT' => ['yes', 'yes', 'yes', 'no'],
        'MST-DATA' => ['yes', 'no', 'no', 'no'],
        'USR-MGMT' => ['no', 'no', 'no', 'no'],
    ],
    3 => [ // Border Point
        'APP-MAIN' => ['yes', 'no', 'no', 'no'],
        'APP-ENTITY' => ['yes', 'yes', 'yes', 'no'],
        'APP-INSPECT' => ['yes', 'yes', 'yes', 'no'],
        'APP-CERT' => ['yes', 'yes', 'no', 'no'],
        'MST-DATA' => ['yes', 'no', 'no', 'no'],
    ],
    4 => [ // Laboratory
        'APP-MAIN' => ['yes', 'no', 'no', 'no'],
        'APP-INSPECT' => ['yes', 'yes', 'yes', 'no'],
        'MST-DATA' => ['yes', 'no', 'no', 'no'],
    ]
];

// Apply permissions
foreach ($permissionTemplates as $groupId => $modules) {
    foreach ($modules as $moduleCode => $perms) {
        // Get module ID from code
        $sql = "SELECT id FROM tbmodules WHERE code='$moduleCode' AND enabled='yes'";
        $result = pg_query($con, $sql);
        if ($row = pg_fetch_assoc($result)) {
            $moduleId = $row['id'];
            list($read, $add, $update, $delete) = $perms;
            
            // Add permission (will skip if exists)
            AddGroupPermit($groupId, $moduleId, $read, $add, $update, $delete, $con);
        }
    }
}

echo "Permission templates applied successfully!";
?>
```

### Step 4: Add Module Code Definition to Each PHP File

Add at the top of each main file:

**main.php:**
```php
<?php
define('MODULE_CODE', 'APP-MAIN');
define('MODULE_TITLE', 'Main Dashboard');
session_start();
// ... rest of code
```

**application.php:**
```php
<?php
define('MODULE_CODE', 'APP-ENTITY');
define('MODULE_TITLE', 'Application Processing');
session_start();
// ... rest of code
```

**inspection.php:**
```php
<?php
define('MODULE_CODE', 'APP-INSPECT');
define('MODULE_TITLE', 'Inspection Processing');
session_start();
// ... rest of code
```

---

## Best Practices

### 1. Module Organization
- **Group related functions** - Keep related operations together
- **Use hierarchical codes** - Enable easy filtering and reporting
- **Maintain consistency** - Follow naming conventions strictly

### 2. Permission Assignment
- **Principle of Least Privilege** - Give minimum necessary permissions
- **Regular audits** - Review permissions quarterly
- **Document changes** - Keep track of permission modifications

### 3. Security Considerations
- **Never trust client-side** - Always verify permissions server-side
- **Log access attempts** - Track who accesses what and when
- **Implement fallback** - Deny access if permission check fails

### 4. Testing
```php
// Test permission check function
function testPermissionCheck($userId, $moduleCode) {
    global $con;
    $userInfo = Userdata($userId, $con);
    $groupId = $userInfo['group_id'];
    $permit = GrouppermitCheck($groupId, $moduleCode, $con);
    
    echo "User ID: $userId\n";
    echo "Group ID: $groupId\n";
    echo "Module: $moduleCode\n";
    echo "Read: " . ($permit['pread'] ? 'YES' : 'NO') . "\n";
    echo "Add: " . ($permit['padd'] ? 'YES' : 'NO') . "\n";
    echo "Update: " . ($permit['pupdate'] ? 'YES' : 'NO') . "\n";
    echo "Delete: " . ($permit['pdelete'] ? 'YES' : 'NO') . "\n";
}
```

---

## Migration Checklist

- [ ] Backup current database
- [ ] Design new module code structure
- [ ] Update tbmodules table with new codes
- [ ] Test GrouppermitCheck with new codes
- [ ] Add MODULE_CODE constants to all files
- [ ] Update permission checking logic
- [ ] Create permission templates
- [ ] Apply default permissions to all groups
- [ ] Test each user group's access
- [ ] Update documentation
- [ ] Train users on new system

---

## Example Permission Matrix

| Module Code | Module Title | Admin | PAFO | Border | Lab | Viewer |
|------------|--------------|-------|------|--------|-----|--------|
| APP-MAIN | Main Dashboard | CRUD | R--- | R--- | R--- | R--- |
| APP-ENTITY | Entity Management | CRUD | CRU- | CRU- | ---- | R--- |
| APP-INSPECT | Inspection | CRUD | CRU- | CRU- | CRU- | R--- |
| APP-CERT | Certificate | CRUD | CRU- | CR-- | ---- | R--- |
| APP-MONITOR | Monitoring | CRUD | R--- | R--- | R--- | R--- |
| MST-DATA | Master Data | CRUD | R--- | R--- | R--- | R--- |
| USR-MGMT | User Management | CRUD | ---- | ---- | ---- | ---- |
| USR-PROFILE | User Profile | CRUD | -RU- | -RU- | -RU- | -RU- |

**Legend:** C=Create, R=Read, U=Update, D=Delete, -=No Access

---

## Maintenance

### Regular Tasks
1. **Monthly:** Review access logs for unusual patterns
2. **Quarterly:** Audit user permissions against job roles
3. **Annually:** Review and update permission templates
4. **On Change:** Update permissions when user roles change

### Troubleshooting

**Issue:** User cannot access a page they should have access to
**Solution:**
1. Check user's group assignment
2. Verify module is enabled in tbmodules
3. Check tbgrouppermits for correct mapping
4. Verify MODULE_CODE constant matches database code

**Issue:** Permission check not working
**Solution:**
1. Verify GrouppermitCheck function logic
2. Check module code in file matches database
3. Ensure group_id is correctly set in session
4. Check for typos in module codes

---

## Contact & Support
For questions or issues with permission management, contact the system administrator or refer to the technical documentation in php-bin/supports.php

**Last Updated:** May 5, 2026
**Version:** 1.0
