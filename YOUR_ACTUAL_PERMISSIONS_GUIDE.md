# Your Actual Module Codes & Permissions
## ePhytosanitary Certificate System - Quick Reference

---

## Your Module Codes (As Implemented)

| No | Code | Module Name | Description |
|----|------|-------------|-------------|
| 1 | **PG-MAIN** | Main dashboard | Main dashboard - first page after login |
| 2 | **FRM - ENTITY** | Import and Export entity's information | Import & Export entity's information - data form |
| 3 | **FRM - DATA PROCESSING** | Application, inspection and certificate data processing | Application, inspection and certificate data processing - data forms |
| 4 | **FRM - MASTER DATA** | Master data | Master data - all data forms |
| 5 | **PG-MR** | Monitoring and data reporting | Monitoring and data reporting |
| 6 | **FRM - USERS_PERMIT** | User and user group management, and group permits | User and user group management, and group permits |
| 7 | **FRM-USERPROFILE** | User profile | User profile |
| 8 | **APP-LAB** | Laboratory | Laboratory - PPC and ATC |

---

## Your User Groups

| ID | Group Name | Description |
|----|-----------|-------------|
| 1 | **System Admin (DOA)** | Full system access - Department of Agriculture |
| 2 | **Group Admin** | Limited administrative access |
| 3 | **Data Officer (DOA/PAEO)** | Process their own data - DOA/Provincial offices |
| 4 | **LAB Officer (PPC and ATC)** | Laboratory operations - PPC and ATC |
| 5 | **Viewer/Guest** | Read-only access for external viewers |

---

## Complete Permission Matrix

### Legend
- ✓✓✓✓ = Full Access (Create, Read, Update, Delete)
- ✓✓✓ = Process (Create, Read, Update)
- ✓ = Read-Only
- ✗ = No Access

| Module | System Admin | Group Admin | Data Officer | LAB Officer | Viewer |
|--------|--------------|-------------|--------------|-------------|--------|
| **PG-MAIN** | ✓✓✓✓ | ✓ | ✓✓✓ (own data) | ✗ | ✓ |
| **FRM - ENTITY** | ✓✓✓✓ | ✓ | ✓✓✓ (own data) | ✗ | ✓ |
| **FRM - DATA PROCESSING** | ✓✓✓✓ | ✓ | ✓✓✓ (own data) | ✗ | ✓ |
| **FRM - MASTER DATA** | ✓✓✓✓ | ✓ | ✓ | ✓ | ✓ |
| **PG-MR** | ✓✓✓✓ | ✓✓✓ | ✓✓✓ | ✗ | ✓ |
| **FRM - USERS_PERMIT** | ✓✓✓✓ | ✗ | ✗ | ✗ | ✗ |
| **FRM-USERPROFILE** | ✓✓✓✓ | ✓✓✓✓ (own) | ✓✓✓✓ (own) | ✓✓✓✓ (own) | ✗ |
| **APP-LAB** | ✓✓✓✓ | ✓ | ✗ | ✓✓✓ | ✓ |

---

## Detailed Permission Breakdown

### 1. System Admin (DOA)
**Full Access to Everything**
```
PG-MAIN:              Read=Yes, Add=Yes, Update=Yes, Delete=Yes
FRM - ENTITY:         Read=Yes, Add=Yes, Update=Yes, Delete=Yes
FRM - DATA PROCESSING: Read=Yes, Add=Yes, Update=Yes, Delete=Yes
FRM - MASTER DATA:    Read=Yes, Add=Yes, Update=Yes, Delete=Yes
PG-MR:                Read=Yes, Add=Yes, Update=Yes, Delete=Yes
FRM - USERS_PERMIT:   Read=Yes, Add=Yes, Update=Yes, Delete=Yes
FRM-USERPROFILE:      Read=Yes, Add=Yes, Update=Yes, Delete=Yes
APP-LAB:              Read=Yes, Add=Yes, Update=Yes, Delete=Yes
```

### 2. Group Admin
**Read-Only + Process Monitoring + Own Profile**
```
PG-MAIN:              Read=Yes, Add=No,  Update=No,  Delete=No
FRM - ENTITY:         Read=Yes, Add=No,  Update=No,  Delete=No
FRM - DATA PROCESSING: Read=Yes, Add=No,  Update=No,  Delete=No
FRM - MASTER DATA:    Read=Yes, Add=No,  Update=No,  Delete=No
PG-MR:                Read=Yes, Add=Yes, Update=Yes, Delete=No
FRM - USERS_PERMIT:   Read=No,  Add=No,  Update=No,  Delete=No
FRM-USERPROFILE:      Read=Yes, Add=Yes, Update=Yes, Delete=Yes (own only)
APP-LAB:              Read=Yes, Add=No,  Update=No,  Delete=No
```

### 3. Data Officer (DOA/PAEO)
**Process Own Data + Read Master Data**
```
PG-MAIN:              Read=Yes, Add=Yes, Update=Yes, Delete=No  (own data)
FRM - ENTITY:         Read=Yes, Add=Yes, Update=Yes, Delete=No  (own data)
FRM - DATA PROCESSING: Read=Yes, Add=Yes, Update=Yes, Delete=No  (own data)
FRM - MASTER DATA:    Read=Yes, Add=No,  Update=No,  Delete=No
PG-MR:                Read=Yes, Add=Yes, Update=Yes, Delete=No
FRM - USERS_PERMIT:   Read=No,  Add=No,  Update=No,  Delete=No
FRM-USERPROFILE:      Read=Yes, Add=Yes, Update=Yes, Delete=Yes (own only)
APP-LAB:              Read=No,  Add=No,  Update=No,  Delete=No
```

### 4. LAB Officer (PPC and ATC)
**Laboratory Process + Read Master Data**
```
PG-MAIN:              Read=No,  Add=No,  Update=No,  Delete=No
FRM - ENTITY:         Read=No,  Add=No,  Update=No,  Delete=No
FRM - DATA PROCESSING: Read=No,  Add=No,  Update=No,  Delete=No
FRM - MASTER DATA:    Read=Yes, Add=No,  Update=No,  Delete=No
PG-MR:                Read=No,  Add=No,  Update=No,  Delete=No
FRM - USERS_PERMIT:   Read=No,  Add=No,  Update=No,  Delete=No
FRM-USERPROFILE:      Read=Yes, Add=Yes, Update=Yes, Delete=Yes (own only)
APP-LAB:              Read=Yes, Add=Yes, Update=Yes, Delete=No
```

### 5. Viewer/Guest
**Read-Only Access (No User Management)**
```
PG-MAIN:              Read=Yes, Add=No,  Update=No,  Delete=No
FRM - ENTITY:         Read=Yes, Add=No,  Update=No,  Delete=No
FRM - DATA PROCESSING: Read=Yes, Add=No,  Update=No,  Delete=No
FRM - MASTER DATA:    Read=Yes, Add=No,  Update=No,  Delete=No
PG-MR:                Read=Yes, Add=No,  Update=No,  Delete=No
FRM - USERS_PERMIT:   Read=No,  Add=No,  Update=No,  Delete=No
FRM-USERPROFILE:      Read=No,  Add=No,  Update=No,  Delete=No
APP-LAB:              Read=Yes, Add=No,  Update=No,  Delete=No
```

---

## Implementation Code for Each Module

### PG-MAIN (main.php)
```php
<?php
define('MODULE_CODE', 'PG-MAIN');
define('MODULE_TITLE', 'Main Dashboard');
session_start();
// ... rest of code
```

### FRM - ENTITY (Import/Export - likely entity.php or similar)
```php
<?php
define('MODULE_CODE', 'FRM - ENTITY');
define('MODULE_TITLE', 'Import and Export Entity Information');
session_start();
// ... rest of code
```

### FRM - DATA PROCESSING (application.php)
```php
<?php
define('MODULE_CODE', 'FRM - DATA PROCESSING');
define('MODULE_TITLE', 'Application, Inspection and Certificate Data Processing');
session_start();
// ... rest of code
```

### FRM - MASTER DATA (masterdata.php)
```php
<?php
define('MODULE_CODE', 'FRM - MASTER DATA');
define('MODULE_TITLE', 'Master Data');
session_start();
// ... rest of code
```

### PG-MR (monitor_report.php)
```php
<?php
define('MODULE_CODE', 'PG-MR');
define('MODULE_TITLE', 'Monitoring and Data Reporting');
session_start();
// ... rest of code
```

### FRM - USERS_PERMIT (users.php for permissions section)
```php
<?php
define('MODULE_CODE', 'FRM - USERS_PERMIT');
define('MODULE_TITLE', 'User and User Group Management');
session_start();
// ... rest of code
```

### FRM-USERPROFILE (users-profile.php)
```php
<?php
define('MODULE_CODE', 'FRM-USERPROFILE');
define('MODULE_TITLE', 'User Profile');
session_start();
// ... rest of code
```

### APP-LAB (inspection.php or lab module)
```php
<?php
define('MODULE_CODE', 'APP-LAB');
define('MODULE_TITLE', 'Laboratory');
session_start();
// ... rest of code
```

---

## Special Implementation: "Process Own Data"

For Data Officers who can "Process its own data", add this check:

```php
// After permission check
if ($canUpdate || $canDelete) {
    // Check if record belongs to current user
    $recordOwner = $recordData['created_by_uid']; // or uid field
    
    if ($recordOwner != $userid) {
        echo "<script>alert('You can only edit your own records.');</script>";
        exit();
    }
}

// For displaying records - filter by user
$sql = "SELECT * FROM tbapplication WHERE uid = '$userid'";
```

### Example: Edit Button with Owner Check
```php
<?php
if ($canUpdate) {
    // Check if current user owns this record
    if ($row['uid'] == $userid) {
        echo '<button class="btn btn-primary" onclick="editRecord('.$row['id'].')">
                <i class="bi bi-pencil"></i> Edit
              </button>';
    } else {
        echo '<button class="btn btn-secondary" disabled>
                <i class="bi bi-lock"></i> Not Your Record
              </button>';
    }
}
?>
```

---

## Setup Instructions

1. **Run the setup script:**
   ```
   http://localhost/setup_actual_permissions.php
   ```

2. **Verify permissions in database:**
   ```sql
   SELECT ug.title, m.code, gp.pread, gp.padd, gp.pupdate, gp.pdelete
   FROM tbgrouppermits gp
   JOIN tbusergroup ug ON gp.gid = ug.id
   JOIN tbmodules m ON gp.mid = m.id
   ORDER BY ug.id, m.id;
   ```

3. **Add MODULE_CODE to your PHP files** (see examples above)

4. **Test with each user group:**
   - Login as System Admin → Verify full access
   - Login as Group Admin → Verify can only edit monitoring & profile
   - Login as Data Officer → Verify can only edit own data
   - Login as LAB Officer → Verify can only access lab & profile
   - Login as Viewer → Verify read-only access

---

## File Mapping (Update these files)

| Module Code | Likely Files to Update |
|-------------|------------------------|
| PG-MAIN | main.php |
| FRM - ENTITY | entity.php, import/export related files |
| FRM - DATA PROCESSING | application.php, inspection.php, certificate.php |
| FRM - MASTER DATA | masterdata.php |
| PG-MR | monitor_report.php |
| FRM - USERS_PERMIT | users.php (permissions section) |
| FRM-USERPROFILE | users-profile.php |
| APP-LAB | inspection.php or separate lab file |

---

## Troubleshooting

**Issue:** User can't access a module they should see
**Fix:** Check:
1. User's group_id in tbusers
2. Permission exists in tbgrouppermits
3. Module is enabled in tbmodules
4. MODULE_CODE in file matches database

**Issue:** Data Officer can edit others' data
**Fix:** Add owner check:
```php
if ($record['uid'] != $userid) {
    echo "Not authorized";
    exit();
}
```

---

**Last Updated:** May 6, 2026
**Status:** Configured for your actual module codes
