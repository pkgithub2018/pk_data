# VIEWER Permission Fix - Quick Guide

## What Was Fixed? 🔧

**Problem:** VIEWER users could edit and update data in Application/Inspection/Certificate forms even though they should only have read-only access.

**Root Cause:** The `transaction.php` file (which handles all forms) was using incorrect module codes:
- Used: `APP-ENTITY`, `APP-INSPECT`, `APP-CERT`
- Should be: `PG-APPLICATION`, `PG-INSPECTION`, `PG-CERTIFICATE`

Because of this mismatch, the permission check failed and the system defaulted to allowing full access! ❌

## What Changed? ✅

**Files Updated:**
1. **transaction.php** (CRITICAL) - Fixed module codes for form permission checks
2. **main.php** - Fixed module codes for dashboard permission checks
3. **monitor_report.php** - Fixed module codes
4. **entity.php** - Fixed module codes
5. **masterdata.php** - Fixed module codes

## How to Verify the Fix? 🧪

### Option 1: Browser Test (Recommended)
1. Login as a VIEWER user (e.g., "Keota_viewer" from your screenshot)
2. Go to any Application/Inspection/Certificate form
3. Try to edit a field → Should be **disabled/readonly** ✅
4. Try to click Update button → Should be **disabled/grayed out** ✅

### Option 2: Command Line Test
```bash
cd c:\xampp\htdocs
php test_transaction_viewer_permissions.php
```

Should show:
```
✅ ✅ ✅ ALL CHECKS PASSED!
VIEWER users will now have READ-ONLY access to forms.
```

### Option 3: Visual Browser Test Page
```
http://localhost/viewer_permission_test.php?uid=33
```
(Replace `33` with your VIEWER user ID)

Shows a nice formatted page with all permission checks.

## Expected Behavior Now 👁️

### VIEWER users CAN:
- ✅ View all dashboards (Main, Application, Inspection, Certificate)
- ✅ View Entity data (Import/Export)
- ✅ View Master Data
- ✅ View Monitoring & Reporting
- ✅ Edit their own profile
- ✅ **View form data** (can see all information)

### VIEWER users CANNOT:
- ❌ Edit any form fields (all fields are readonly)
- ❌ Change dropdown selections (all selects are disabled)
- ❌ Submit or Update forms (buttons are disabled)
- ❌ Add new records
- ❌ Delete records
- ❌ Access User Management section

## Visual Confirmation 📸

When a VIEWER user opens a form, they should see:

### Text Fields:
```html
<input type="text" readonly>
```
→ Background is grayed, cannot type

### Dropdowns:
```html
<select disabled>
```
→ Cannot open dropdown, selection is grayed

### Update Button:
```html
<button disabled>Update</button>
```
→ Button is grayed out, cannot click

## Module Code Reference 📋

### Correct Module Codes (Database Format):
```
PG-MAIN                 → Main Dashboard
PG-APPLICATION          → Application Module
PG-INSPECTION           → Inspection Module
PG-CERTIFICATE          → Certificate Module
FRM - ENTITY            → Entity Forms (note the spaces!)
FRM - MASTER DATA       → Master Data (note the spaces!)
FRM - USERS_PERMIT      → User Management (note the spaces!)
FRM - MODULE            → Module Management (note the spaces!)
FRM-USERPROFILE         → User Profile (no spaces)
```

### ❌ Incorrect Codes That Were Fixed:
```
APP-ENTITY              → Wrong! Should be PG-APPLICATION
APP-INSPECT             → Wrong! Should be PG-INSPECTION
APP-CERT                → Wrong! Should be PG-CERTIFICATE
FRM-MASTER DATA         → Wrong! Should be FRM - MASTER DATA (with spaces)
FRM-USERGROUP           → Wrong! Should be FRM - USERS_PERMIT
FRM-USERS               → Wrong! Should be FRM - USERS_PERMIT
```

## Common Issues & Solutions 🔍

### Issue: VIEWER can still edit forms
**Check:**
1. Clear browser cache and reload page
2. Verify VIEWER user is in correct group:
   ```sql
   SELECT u.name, g.title 
   FROM tbusers u 
   JOIN tbusergroup g ON g.id = u.group_id 
   WHERE u.email = 'viewer@example.com'
   ```
3. Run test script to verify permissions

### Issue: Module not found
**Check:**
1. Verify module exists with correct code:
   ```sql
   SELECT code, title FROM tbmodules ORDER BY code
   ```
2. Check for extra spaces in module code
3. Verify module is enabled: `enabled='yes'`

### Issue: Permission check returns wrong result
**Check:**
1. Verify group permission exists:
   ```sql
   SELECT g.title, m.code, gp.pread, gp.pupdate 
   FROM tbgrouppermits gp
   JOIN tbusergroup g ON g.id = gp.gid
   JOIN tbmodules m ON m.id = gp.mid
   WHERE g.title = 'VIEWER'
   ```
2. Run diagnostic script: `php check_viewer_permissions.php`

## Technical Details 🔧

### Permission Check Flow:
1. User logs in → userid stored in session
2. Page loads → calls `UserPermitCheck($userid, 'PG-APPLICATION', $con)`
3. Function checks:
   - User's group (VIEWER)
   - Module permissions in database
   - Applies VIEWER-specific rules (read-only)
4. Returns permission array: `['pread' => true, 'pupdate' => false, ...]`
5. Form renders with appropriate restrictions

### Form Restriction Logic (transaction.php lines 1242-1244):
```php
// For edit mode, need update permission
$requiredPermission = $isEditMode ? $canAppUpdate : $canAppAdd;

// If user has read but not update, make readonly
$appFormReadOnly = (!$requiredPermission && $canAppRead) ? 'readonly' : '';
$appFormDisabled = (!$requiredPermission && $canAppRead) ? 'disabled' : '';
$appSubmitDisabled = (!$requiredPermission && $canAppRead) ? 'disabled' : '';
```

For VIEWER users:
- `$canAppRead = true` (can view)
- `$canAppUpdate = false` (cannot edit)
- `$requiredPermission = false` (no update permission)
- Result: `readonly` and `disabled` attributes applied ✅

## Files Reference 📁

### Production Files (Modified):
- `transaction.php` - Form rendering and permission checks
- `main.php` - Dashboard and permission setup
- `monitor_report.php` - Monitoring page permissions
- `entity.php` - Entity page permissions
- `masterdata.php` - Master data page permissions

### Testing Files (Created):
- `test_transaction_viewer_permissions.php` - Form behavior test
- `test_viewer_permissions.php` - Permission check test
- `viewer_permission_test.php` - Browser visual test
- `check_viewer_permissions.php` - Database diagnostic
- `check_module_codes.php` - Module code diagnostic

### Documentation:
- `VIEWER_PERMISSION_FIX_SUMMARY.md` - Detailed technical summary
- `VIEWER_PERMISSION_FIX_QUICK_GUIDE.md` - This file

## Support 💡

If you encounter issues:
1. Run the test scripts to diagnose
2. Check browser console for JavaScript errors
3. Verify database permissions with diagnostic scripts
4. Check server error logs for PHP errors
5. Compare your module codes with the reference above

---

**Last Updated:** May 22, 2026  
**Status:** ✅ All fixes applied and tested  
**Impact:** VIEWER users now properly restricted to read-only access
