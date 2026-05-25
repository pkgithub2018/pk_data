# Custom Permission System Documentation

## Overview

This project implements a sophisticated role-based permission system that controls user access to different modules based on:
1. **User Group** (from `tbusergroup` table) - 4 groups: admin, Data Officer, LAB, VIEWER
2. **Group Admin Flag** (from `tbusers.group_admin` column) - Determines role within Data Officer group

## User Groups (4 Groups)

The system has **4 user groups**:
1. **admin** - System administrators
2. **Data Officer** - Main user group (role determined by group_admin flag)
3. **LAB** - Laboratory officers
4. **VIEWER** - Guest viewers

## User Roles (5 Roles)

The `group_admin` flag creates **5 distinct roles** from 4 groups:

### 1. System Administrator
- **User Group**: `tbusergroup.title='admin'`
- **Access Level**: Full access to ALL modules
- **Permissions**: Read, Add, Update, Delete on all modules
- **Notes**: Highest privilege level, bypasses all restrictions

### 2. Group Admin (Role, not a group!)
- **User Group**: `tbusergroup.title='Data Officer'`
- **Flag**: `tbusers.group_admin='yes'`
- **Access Level**: Read-only for most modules, Process for monitoring
- **Permissions**:
  - **Read-Only**: PG-MAIN, FRM - ENTITY, FRM - DATA PROCESSING, PG-APPLICATION, PG-INSPECTION, PG-CERTIFICATE, FRM - MASTER DATA
  - **Process** (Read, Add, Update, no Delete): PG-MR (Monitoring and Reporting)
  - **Full Access** (Read, Add, Update, Delete): FRM-USERPROFILE
  - **Conditional**: APP-LAB (read-only if user also belongs to LAB group)

### 3. Data Officer (Role, not just a group!)
- **User Group**: `tbusergroup.title='Data Officer'`
- **Flag**: `tbusers.group_admin='no'`
- **Access Level**: Full access to data processing modules
- **Permissions**:
  - **Full Access**: PG-MAIN, FRM - ENTITY, FRM - DATA PROCESSING, PG-MR, PG-APPLICATION, PG-INSPECTION, PG-CERTIFICATE, FRM-USERPROFILE
  - **Read-Only**: FRM - MASTER DATA
  - **No Access**: APP-LAB, FRM - USERS_PERMIT (if not in admin group)

### 4. Lab Officer
- **User Group**: `tbusergroup.title='LAB'`
- **Flag**: group_admin flag not applicable
- **Access Level**: Limited to entity viewing and lab operations
- **Permissions**:
  - **Read-Only**: FRM - ENTITY
  - **Full Access**: FRM-USERPROFILE, APP-LAB
  - **No Access**: All other modules

### 5. Viewer (Guest)
- **User Group**: `tbusergroup.title='VIEWER'`
- **Flag**: group_admin flag not applicable
- **Access Level**: Read-only guest access to view system data
- **Permissions**:
  - **Read-Only**: PG-MAIN, FRM - ENTITY, FRM - DATA PROCESSING, PG-APPLICATION, PG-INSPECTION, PG-CERTIFICATE, FRM - MASTER DATA, PG-MR, APP-LAB
  - **Full Access**: FRM-USERPROFILE (their own profile)
  - **No Access**: FRM - USERS_PERMIT (user management)

## Module Codes

| Module Code | Module Name | Description |
|------------|-------------|-------------|
| PG-MAIN | Main dashboard | System dashboard and overview |
| FRM - ENTITY | Import and Export entity | Entity management |
| FRM - DATA PROCESSING | Application/Inspection/Certificate | Main data processing |
| PG-APPLICATION | Application module | Application management |
| PG-INSPECTION | Inspection module | Inspection management |
| PG-CERTIFICATE | Certificate module | Certificate management |
| FRM - MASTER DATA | Master data | System master data |
| PG-MR | Monitoring and reporting | Reports and monitoring |
| FRM - USERS_PERMIT | User and group management | User and permission management |
| FRM-USERPROFILE | User profile | User profile management |
| APP-LAB | Laboratory | Laboratory operations |

## Permission Levels

| Level | Read | Add | Update | Delete | Description |
|-------|------|-----|--------|--------|-------------|
| Full Access | ✓ | ✓ | ✓ | ✓ | Complete control |
| Process | ✓ | ✓ | ✓ | ✗ | Can modify but not delete |
| Read-Only | ✓ | ✗ | ✗ | ✗ | View only |
| No Access | ✗ | ✗ | ✗ | ✗ | Cannot access |

## Database Structure

### tbusers
```sql
- id: User ID
- group_id: References tbusergroup.id
- group_admin: 'yes' or 'no' (distinguishes Group Admin from Data Officer)
- enabled: 'yes' or 'no'
```

### tbusergroup
```sql
- id: Group ID
- title: Group name ('admin', 'LAB', 'Data Officer', etc.)
- desc: Group description
- enabled: 'yes' or 'no'
```

### tbgrouppermits
```sql
- id: Permission ID
- gid: References tbusergroup.id
- mid: References tbmodules.id
- pread: 'yes' or 'no'
- padd: 'yes' or 'no'
- pupdate: 'yes' or 'no'
- pdelete: 'yes' or 'no'
```

### tbmodules
```sql
- id: Module ID
- code: Module code (e.g., 'PG-MAIN')
- title: Module title
- desc: Module description
- enabled: 'yes' or 'no'
```

## PHP Functions

### 1. GrouppermitCheck($groupid, $moduleRef, $con)
**Purpose**: Check permissions based on user group only

**Parameters**:
- `$groupid`: User's group ID from tbusergroup
- `$moduleRef`: Module ID, code, title, or filename
- `$con`: Database connection

**Returns**: Array with permission details
```php
[
    'exists' => true/false,
    'mid' => module_id,
    'module_code' => 'PG-MAIN',
    'module_title' => 'Main dashboard',
    'pread' => true/false,
    'padd' => true/false,
    'pupdate' => true/false,
    'pdelete' => true/false,
    'granted' => true/false
]
```

### 2. UserPermitCheck($userid, $moduleRef, $con) [NEW]
**Purpose**: Enhanced permission check considering user's group, group_admin flag, and group title

**Parameters**:
- `$userid`: User's ID from tbusers
- `$moduleRef`: Module ID, code, title, or filename
- `$con`: Database connection

**Returns**: Array with permission details including reason
```php
[
    'exists' => true/false,
    'mid' => module_id,
    'module_code' => 'PG-MAIN',
    'module_title' => 'Main dashboard',
    'pread' => true/false,
    'padd' => true/false,
    'pupdate' => true/false,
    'pdelete' => true/false,
    'granted' => true/false,
    'reason' => 'Group Admin - Read Only'
]
```

## Implementation Examples

### Example 1: Check if user can edit entity data
```php
// Get user permissions
$permissions = UserPermitCheck($userid, 'FRM - ENTITY', $con);

// Check specific permission
if ($permissions['pupdate']) {
    echo "User can update entity data";
} else {
    echo "User cannot update entity data";
}
```

### Example 2: Disable form fields for read-only users
```php
$permissions = UserPermitCheck($userid, 'FRM - DATA PROCESSING', $con);

$formDisabled = ($permissions['padd'] || $permissions['pupdate']) ? '' : 'disabled';
$showSubmitButton = ($permissions['padd'] || $permissions['pupdate']);

// In HTML
<input type="text" name="field1" <?php echo $formDisabled; ?>>
<?php if ($showSubmitButton): ?>
    <button type="submit">Submit</button>
<?php endif; ?>
```

### Example 3: Show warning for read-only access
```php
$permissions = UserPermitCheck($userid, 'PG-MAIN', $con);

if ($permissions['pread'] && !$permissions['padd'] && !$permissions['pupdate']) {
    echo '<div class="alert alert-info">';
    echo 'You have read-only access to this module.';
    echo '</div>';
}
```

## Setup Instructions

### Step 1: Run Permission Setup Script
```
http://localhost/setup_custom_permissions.php
```

This script will:
1. Create/verify 4 user groups: admin, Data Officer, LAB, VIEWER
2. Verify all module codes
3. Set up the permission matrix according to specifications
4. Insert/update permissions in tbgrouppermits table

**Important:** Group Admin is NOT created as a separate user group - it's a role within Data Officer determined by the group_admin flag.

### Step 2: Assign Users to Groups
1. Go to User Management page
2. For each user, set:
   - **group_id**: Assign to appropriate user group (admin, Data Officer, LAB, or VIEWER)
   - **group_admin**: 
     - Set to 'yes' for users in Data Officer group who should be Group Admins
     - Set to 'no' for users in Data Officer group who should be Data Officers
     - Not applicable for admin, LAB, or VIEWER groups

### Step 3: Update PHP Pages
Replace `GrouppermitCheck()` with `UserPermitCheck()` in pages that need sophisticated permission checking:

```php
// OLD (group-based only)
$permissions = GrouppermitCheck($groupid, 'MODULE_CODE', $con);

// NEW (considers group_admin flag)
$permissions = UserPermitCheck($userid, 'MODULE_CODE', $con);
```

## Permission Matrix Summary

**4 User Groups** (admin, Data Officer, LAB, VIEWER) with **5 Roles** (group_admin flag creates 2 roles within Data Officer group):

| Role | Group | Flag | PG-MAIN | FRM-ENTITY | FRM-DATA | PG-APP | PG-INSP | PG-CERT | MASTER | PG-MR | USERS | PROFILE | LAB |
|------|-------|------|---------|------------|----------|--------|---------|---------|--------|-------|-------|---------|-----|
| **System Admin** | admin | n/a | Full | Full | Full | Full | Full | Full | Full | Full | Full | Full | Full |
| **Group Admin** | Data Officer | yes | RO | RO | RO | RO | RO | RO | RO | Proc | RO | Full | RO* |
| **Data Officer** | Data Officer | no | Full | Full | Full | Full | Full | Full | RO | Full | No | Full | No |
| **Lab Officer** | LAB | n/a | No | RO | No | No | No | No | No | No | No | Full | Full |
| **Viewer** | VIEWER | n/a | RO | RO | RO | RO | RO | RO | RO | RO | No | Full | RO |

**Legend:**
- Full = Full Access (Read, Add, Update, Delete)
- Proc = Process (Read, Add, Update)
- RO = Read-Only (Read)
- No = No Access
- RO* = Read-Only if user also belongs to LAB group
- n/a = Not applicable (flag not used for this group)
- USERS = FRM - USERS_PERMIT (User and Permission Management)

**Key Point:** Group Admin is NOT a user group - it's a role within the Data Officer group!

## Troubleshooting

### Issue: User cannot access module they should have access to
**Solution**: 
1. Check if user's group has permission in tbgrouppermits
2. Verify user's group_id in tbusers matches tbusergroup.id
3. Ensure both user and group are enabled
4. Check module is enabled in tbmodules

### Issue: Group Admin has full access when they should have read-only
**Solution**: 
1. Verify page is using `UserPermitCheck()` not `GrouppermitCheck()`
2. Check user's group_admin field is set to 'yes'
3. Review permission setup in tbgrouppermits

### Issue: Permission changes not taking effect
**Solution**:
1. Clear browser cache
2. Re-run setup_custom_permissions.php
3. Log out and log back in
4. Check database directly: `SELECT * FROM tbgrouppermits WHERE gid=X AND mid=Y`

## Best Practices

1. **Always use UserPermitCheck()** for user-specific permissions
2. **Check permissions on every page** that displays or modifies data
3. **Disable form controls** for users without write permissions
4. **Show clear messages** when access is denied
5. **Log permission denials** for security auditing
6. **Regularly review** user group assignments
7. **Test permissions** after any changes to user groups or modules

## Security Considerations

1. Permissions are enforced at the PHP application level
2. Database-level security should also be implemented
3. Always validate and sanitize user inputs
4. Use prepared statements for SQL queries (consider migrating from pg_query to pg_query_params)
5. Implement logging for sensitive operations
6. Regular security audits of permission assignments
7. Implement session management properly

## Support and Maintenance

For issues or questions:
1. Check this documentation first
2. Review the setup script: setup_custom_permissions.php
3. Examine the UserPermitCheck() function in php-bin/supports.php
4. Test with the permission management interface at users.php?part=upermits

Last Updated: May 7, 2026
