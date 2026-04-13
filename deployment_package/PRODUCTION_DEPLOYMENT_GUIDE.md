# 🚀 Production Deployment Guide - ephyto.info

## Files to Upload to Your Server

### ✅ Required Files:

#### 1. Main Verification File
```
verify.php                    (Main verification page)
```

#### 2. Required PHP Dependencies
```
php-bin/
  ├── connection.php          (Database connection)
  ├── supports.php            (Helper functions)
  └── qr_generator.php        (QR code functions - updated for production)
```

#### 3. Assets (if needed for logo)
```
assets/
  └── img/
      └── national_logo.jpg   (National logo for verification page)
```

---

## 📋 Deployment Steps

### Step 1: Update Database Connection

**Edit `php-bin/connection.php` on your server:**

Make sure it has your production database credentials:
```php
<?php
$host = "your-production-db-host";
$port = "5432";
$dbname = "your-production-db-name";
$user = "your-production-db-user";
$password = "your-production-db-password";

$con = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
?>
```

### Step 2: Upload Files via FTP/SFTP

**Using FileZilla or your hosting control panel:**

Upload these files to your `ephyto.info` root directory:
```
ephyto.info/
├── verify.php
├── php-bin/
│   ├── connection.php
│   ├── supports.php
│   └── qr_generator.php
└── assets/
    └── img/
        └── national_logo.jpg
```

### Step 3: Set File Permissions

Make sure PHP files are executable:
```bash
chmod 644 verify.php
chmod 644 php-bin/*.php
```

### Step 4: Test the Verification Page

Visit in your browser:
```
https://ephyto.info/verify.php
```

You should see the verification form.

### Step 5: Test Manual Verification

Enter a certificate number:
```
https://ephyto.info/verify.php?cert=000042/25/00
```

Should display certificate details.

### Step 6: Scan Your Printed QR Code! 📱

Now your existing printed certificate QR code will work because it points to:
```
https://ephyto.info/verify?cert=000042/25/00&hash=...
```

Use your Samsung QR Scanner:
1. Open QR Scanner app
2. Scan the QR code on your printed certificate
3. It should open the verification page automatically!
4. View the certificate details ✅

---

## 🔐 Security Checklist

- [ ] SSL Certificate installed (https:// not http://)
- [ ] Database credentials secured
- [ ] File permissions set correctly
- [ ] Test verification works
- [ ] Test QR code scanning works
- [ ] Firewall allows web traffic

---

## 📊 Database Requirements

Make sure these tables exist on production:
- `tbcertificate`
- `tbcertificate_qr`
- `tbapplication`
- `tbcompany` (exporters)
- `tbimporter`
- `tbcountry`
- `tbcommodity`
- `tbapprover`

---

## 🧪 Testing Checklist

After deployment:

### Test 1: Direct URL Access
```
✅ https://ephyto.info/verify.php loads
```

### Test 2: Manual Certificate Entry
```
✅ Enter certificate number
✅ Click Verify
✅ See certificate details
```

### Test 3: URL with Parameters
```
✅ https://ephyto.info/verify.php?cert=000042/25/00
✅ Shows certificate directly
```

### Test 4: QR Code Scanning
```
✅ Scan QR code with phone
✅ Opens verification page
✅ Shows certificate details
✅ Green "VERIFIED" badge appears
```

---

## 🔄 For Future Certificates

Now that production is set up:

1. **Generate new certificates** - they will automatically have QR codes pointing to `https://ephyto.info/verify.php`
2. **Print certificates** - QR codes work immediately
3. **Anyone can scan** - no special setup needed

---

## 📱 User Instructions (Share with Customs/Importers)

**To verify a certificate:**

**Method 1: Scan QR Code**
1. Open any QR scanner app
2. Scan the QR code on the certificate
3. View verification results

**Method 2: Manual Entry**
1. Visit: https://ephyto.info/verify.php
2. Enter certificate number
3. Click Verify

---

## 🆘 Troubleshooting

### Issue: Page shows blank/error
- Check PHP version (needs 7.0+)
- Check PostgreSQL extension is enabled
- Review server error logs

### Issue: Database connection failed
- Verify database credentials in connection.php
- Check database server is running
- Verify firewall allows database connections

### Issue: Certificate not found
- Verify certificate exists in database
- Check certificate number is correct
- Verify database connection works

### Issue: QR code doesn't work
- Verify SSL certificate is installed (https)
- Check verify.php is accessible
- Test manual URL first

---

## 📞 Support Information

**Production URL:** https://ephyto.info/verify.php

**For technical support:**
- Department of Agriculture, Lao PDR
- Email: ppd@doa.gov.la
- Tel: (856) 21 416350

---

## ✅ Deployment Complete!

Once deployed, your QR code verification system will be fully operational:

- ✅ Public access (no login required)
- ✅ Mobile-friendly
- ✅ Real-time database verification
- ✅ Security hash validation
- ✅ QR code scanning works from anywhere

**Your existing printed certificate will now work when scanned!** 🎉
