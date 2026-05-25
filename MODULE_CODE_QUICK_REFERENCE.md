# Module Code Quick Reference Card
## ePhytosanitary Certificate System

---

## Module Code Format
```
[PREFIX]-[CATEGORY]-[FUNCTION]
```

---

## All Module Codes

### 📱 Application Processing Modules
| Code | Title | File | Description |
|------|-------|------|-------------|
| **APP-MAIN** | Main Dashboard | main.php | Main dashboard - first page after login |
| **APP-ENTITY** | Application Processing | application.php | Application data processing |
| **APP-INSPECT** | Inspection Processing | inspection.php | Inspection results processing |
| **APP-CERT** | Certificate Issuance | certificate.php | Certificate processing and issuance |
| **APP-MONITOR** | Monitoring & Reporting | monitor_report.php | Data monitoring and reporting |
| **APP-EXPORT** | Export Information | exp | Export entity information |
| **APP-IMPORT** | Import Information | imp | Import countries information |

### 📊 Master Data Modules
| Code | Title | File | Description |
|------|-------|------|-------------|
| **MST-DATA** | Master Data Hub | masterdata.php | Master data management hub |
| **MST-PRODUCT** | Product Management | masterdata.php?part=product | Product catalog management |
| **MST-COUNTRY** | Country Management | masterdata.php?part=countries | Country master data |
| **MST-LOCATION** | Location Management | masterdata.php?part=locations | Location/border points |
| **MST-CONVEY** | Conveyance Management | masterdata.php?part=conveyance | Conveyance types |
| **MST-MODULE** | Module Configuration | masterdata.php?part=modules | System module management |

### 👥 User Management Modules
| Code | Title | File | Description |
|------|-------|------|-------------|
| **USR-PROFILE** | User Profile | users-profile.php | User profile management |
| **USR-GROUP** | User Groups | users.php?part=ugroup | User group management |
| **USR-PERMIT** | Group Permissions | users.php?part=upermits | Permission management |
| **USR-LIST** | User List | users.php?part=userslist | User account management |

---

## Permission Types

| Code | Permission | Description |
|------|-----------|-------------|
| **R** | Read | View/access the module |
| **A** | Add | Create new records |
| **U** | Update | Edit existing records |
| **D** | Delete | Remove records |

---

## User Group Permission Matrix

| Module | Admin | PAFO | Border | Lab | Viewer |
|--------|-------|------|--------|-----|--------|
| APP-MAIN | RAUD | R--- | R--- | R--- | R--- |
| APP-ENTITY | RAUD | RAU- | RAU- | ---- | R--- |
| APP-INSPECT | RAUD | RAU- | RAU- | RAU- | R--- |
| APP-CERT | RAUD | RAU- | RA-- | ---- | R--- |
| APP-MONITOR | RAUD | R--- | R--- | R--- | R--- |
| APP-EXPORT | RAUD | RAU- | RAU- | ---- | ---- |
| APP-IMPORT | RAUD | R--- | R--- | ---- | ---- |
| MST-DATA | RAUD | R--- | R--- | R--- | R--- |
| MST-PRODUCT | RAUD | R--- | R--- | R--- | ---- |
| MST-COUNTRY | RAUD | R--- | R--- | ---- | ---- |
| MST-LOCATION | RAUD | R--- | R--- | ---- | ---- |
| MST-CONVEY | RAUD | R--- | R--- | ---- | ---- |
| MST-MODULE | RAUD | ---- | ---- | ---- | ---- |
| USR-PROFILE | RAUD | -RU- | -RU- | -RU- | -RU- |
| USR-GROUP | RAUD | ---- | ---- | ---- | ---- |
| USR-PERMIT | RAUD | ---- | ---- | ---- | ---- |
| USR-LIST | RAUD | ---- | ---- | ---- | ---- |

**Legend:** R=Read, A=Add, U=Update, D=Delete, -=No Access

---

## User Group Definitions

| ID | Group Name | Description | Typical Users |
|----|-----------|-------------|---------------|
| **1** | Administrator | Full system access | System administrators, IT staff |
| **2** | Provincial Office (PAFO) | Provincial-level operations | PAFO officers |
| **3** | Border Point | Border checkpoint operations | Border inspectors |
| **4** | Laboratory | Laboratory testing | Lab technicians |
| **5** | Viewer/Other | Read-only access | External viewers, auditors |

---

## Quick Implementation Guide

### 1. Add to top of PHP file:
```php
define('MODULE_CODE', 'APP-MAIN'); // Your module code
define('MODULE_TITLE', 'Main Dashboard');
session_start();
```

### 2. Check permissions:
```php
$groupPermit = GrouppermitCheck($guid, MODULE_CODE, $con);
$canRead = $groupPermit['pread'];
$canAdd = $groupPermit['padd'];
$canUpdate = $groupPermit['pupdate'];
$canDelete = $groupPermit['pdelete'];
```

### 3. Block unauthorized access:
```php
if ($groupPermit['exists'] && !$canRead) {
    echo "<script>alert('No permission');</script>";
    echo "<script>window.location.href='main.php';</script>";
    exit();
}
```

### 4. Conditional display:
```php
if ($canAdd) {
    echo '<button>Add New</button>';
}

if ($canUpdate) {
    echo '<button>Edit</button>';
}

if ($canDelete) {
    echo '<button>Delete</button>';
}
```

---

## Common SQL Queries

### View all permissions for a group:
```sql
SELECT m.code, m.title, gp.pread, gp.padd, gp.pupdate, gp.pdelete
FROM tbgrouppermits gp
JOIN tbmodules m ON gp.mid = m.id
WHERE gp.gid = 1 -- Group ID
ORDER BY m.code;
```

### Add permission:
```sql
INSERT INTO tbgrouppermits (gid, mid, pread, padd, pupdate, pdelete)
VALUES (2, 1, 'yes', 'yes', 'yes', 'no');
```

### Update permission:
```sql
UPDATE tbgrouppermits 
SET pread='yes', padd='yes', pupdate='yes', pdelete='no'
WHERE gid = 2 AND mid = 1;
```

### List users by group:
```sql
SELECT u.id, u.name, u.email, ug.title as group_name
FROM tbusers u
JOIN tbusergroup ug ON u.group_id = ug.id
WHERE ug.id = 2;
```

---

## Troubleshooting Checklist

- [ ] Module code is defined at top of file?
- [ ] Module exists in tbmodules table?
- [ ] Module is enabled (enabled='yes')?
- [ ] User group is enabled?
- [ ] Permission exists in tbgrouppermits?
- [ ] Permission check uses correct module code?
- [ ] User's group_id is set correctly?
- [ ] Session is active?

---

## File Locations

| File | Purpose |
|------|---------|
| `MODULE_NAMING_PERMISSIONS_GUIDE.md` | Complete documentation |
| `permission_setup_implementation.php` | Initial setup script |
| `permission_code_templates.php` | Code examples |
| `php-bin/supports.php` | Core permission functions |
| `php-bin/connection.php` | Database connection |

---

## Important Functions

| Function | Purpose | File |
|----------|---------|------|
| `GrouppermitCheck()` | Check user permissions | supports.php |
| `Groupname()` | Get group name | supports.php |
| `Userdata()` | Get user information | supports.php |
| `ModuleName()` | Get module name | supports.php |
| `AddGroupPermit()` | Add new permission | supports.php |
| `UpdateGroupPermit()` | Update permission | supports.php |

---

## Security Best Practices

1. ✅ Always check permissions server-side
2. ✅ Use pg_escape_string() for inputs
3. ✅ Block access if permission check fails
4. ✅ Log permission changes
5. ✅ Review permissions regularly
6. ✅ Follow principle of least privilege
7. ✅ Test with different user groups
8. ✅ Keep this reference updated

---

## Support

For help or questions:
- Review: `MODULE_NAMING_PERMISSIONS_GUIDE.md`
- Check: `permission_code_templates.php`
- Run: `permission_setup_implementation.php`
- Contact: System Administrator

**Last Updated:** May 5, 2026
**Version:** 1.0
