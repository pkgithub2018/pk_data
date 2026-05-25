# Files to Upload - VIEWER Permission Fixes

## 📦 Production Files (MUST UPLOAD)

Upload these **8 modified files** to fix VIEWER permission issues:

### 🚨 CRITICAL FILES (Dashboard Access Fix):
1. **application.php** - Allows VIEWER access to Application dashboard
2. **inspection.php** - Allows VIEWER access to Inspection dashboard  
3. **certificate.php** - Allows VIEWER access to Certificate dashboard

### 🚨 CRITICAL FILE (Form Read-Only Fix):
4. **transaction.php** - Enforces read-only forms for VIEWER users

### Supporting Files:
5. **main.php** - Menu display and permission setup
6. **monitor_report.php** - Monitoring page permissions
7. **entity.php** - Entity page permissions
8. **masterdata.php** - Master data page permissions

---

## ⚠️ DO NOT UPLOAD (Testing/Documentation Only)

These files are for **local testing only**, NOT for production:
- ❌ test_dashboard_access.php
- ❌ test_transaction_viewer_permissions.php
- ❌ test_viewer_permissions.php
- ❌ check_viewer_permissions.php
- ❌ check_module_codes.php
- ❌ viewer_permission_test.php
- ❌ VIEWER_PERMISSION_*.md (documentation files)

---

## 📋 Upload Checklist

```
Cloud Server: [Your production server]
Method: FTP / cPanel File Manager / Git

Files to Upload:
☐ application.php
☐ certificate.php
☐ inspection.php
☐ transaction.php
☐ main.php
☐ monitor_report.php
☐ entity.php
☐ masterdata.php
```

---

## 🔧 Changes Made in Each File

### application.php
- **Line 117**: `APP-ENTITY` → `PG-APPLICATION`
- **Lines 125-129**: Fixed module code spacing

### inspection.php
- **Line 113**: `APP-INSPECT` → `PG-INSPECTION`
- **Lines 121-126**: Fixed module code spacing

### certificate.php
- **Line 118**: `APP-CERT` → `PG-CERTIFICATE`
- **Lines 126-131**: Fixed module code spacing

### transaction.php
- **Lines 616-628**: Fixed module codes for form permission checks
  - `APP-ENTITY` → `PG-APPLICATION`
  - `APP-INSPECT` → `PG-INSPECTION`
  - `APP-CERT` → `PG-CERTIFICATE`
  - Fixed spacing: `FRM-MASTER DATA` → `FRM - MASTER DATA`

### main.php
- **Lines 340-360**: Fixed module codes for Application/Inspection/Certificate
- **Lines 384-386**: Fixed module code spacing

### monitor_report.php
- **Lines 105-108**: Fixed module code spacing

### entity.php
- **Lines 70-73**: Fixed module code spacing

### masterdata.php
- **Lines 51, 56**: Fixed module code spacing

---

## ✅ After Upload - Verify These Work:

### Test 1: Dashboard Access
1. Login as VIEWER user (e.g., Keota_viewer)
2. Click "Application" menu link
3. ✅ Should load dashboard (no "Access Denied")
4. Click "Inspection" menu link
5. ✅ Should load dashboard (no "Access Denied")
6. Click "Certificate" menu link
7. ✅ Should load dashboard (no "Access Denied")

### Test 2: Form Read-Only Mode
1. From any dashboard, click to view/edit a record
2. ✅ Form fields should be grayed out (readonly)
3. ✅ Dropdowns should be disabled
4. ✅ Update/Submit button should be grayed out (disabled)

---

## 🚀 Upload Methods

### Method 1: FTP (Recommended)
```bash
# Using FileZilla or similar:
- Connect to your server
- Navigate to application directory
- Upload the 8 files (overwrite existing)
- Verify file permissions (644 for .php files)
```

### Method 2: cPanel File Manager
1. Login to cPanel
2. Open File Manager
3. Navigate to public_html or application folder
4. Upload each file (select "Overwrite")
5. Done!

### Method 3: Git (If using version control)
```bash
# Commit changes
git add application.php inspection.php certificate.php transaction.php
git add main.php monitor_report.php entity.php masterdata.php
git commit -m "Fix VIEWER permission issues - dashboard access and form restrictions"
git push origin master

# Then pull on server
ssh user@yourserver
cd /path/to/application
git pull
```

---

## ⚠️ Important Notes

1. **Backup First**: Before uploading, backup your current files on the cloud server
2. **Database**: No database changes needed - all fixes are in PHP code only
3. **Cache**: Clear browser cache after upload (Ctrl+Shift+Delete)
4. **Session**: Users may need to logout/login again to see changes
5. **Test**: Test with a VIEWER account immediately after upload

---

## 🆘 Rollback Plan

If issues occur after upload:
1. Restore backup files
2. Check server error logs: `/var/log/apache2/error.log` or cPanel error logs
3. Verify file permissions: should be `644` for .php files
4. Check PHP version compatibility (requires PHP 7+)

---

## 📊 Expected Impact

**Before Fix:**
- ❌ VIEWER users got "Access Denied" on dashboards
- ❌ VIEWER users could edit/update/delete data in forms

**After Fix:**
- ✅ VIEWER users can access all dashboards
- ✅ VIEWER users have read-only access (cannot edit)
- ✅ Forms show data but fields are disabled
- ✅ Update/Submit buttons are grayed out

---

## 📞 Support

If you encounter issues after upload:
1. Check PHP error logs on server
2. Verify module codes in database match PHP files:
   ```sql
   SELECT code, title FROM tbmodules WHERE code LIKE 'PG-%' OR code LIKE 'FRM%'
   ```
3. Run test scripts locally first to verify fixes
4. Contact system administrator

---

**Created:** May 22, 2026  
**Total Files:** 8  
**Estimated Upload Time:** 2-3 minutes  
**Impact:** HIGH - Fixes critical permission issues  
**Risk:** LOW - Only permission check logic changed, no data modifications
