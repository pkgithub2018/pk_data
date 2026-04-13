# Upload These Files to ephyto.info

## 📦 Required Files for Verification System

### Core Files (Must Upload):

1. **verify.php** ← Main verification page
2. **php-bin/connection.php** ← Database connection (update credentials!)
3. **php-bin/supports.php** ← Helper functions
4. **php-bin/qr_generator.php** ← QR functions (already updated for production)

### Optional (for logo display):

5. **assets/img/national_logo.jpg** ← National logo

---

## ⚡ Quick Start

### Step 1: Update Database Credentials

Edit `php-bin/connection.php` with your production database info before uploading.

### Step 2: Upload via FTP/cPanel

Upload all files maintaining the folder structure:
```
ephyto.info/
├── verify.php
├── php-bin/
│   ├── connection.php
│   ├── supports.php
│   └── qr_generator.php
└── assets/img/national_logo.jpg
```

### Step 3: Test

Visit: `https://ephyto.info/verify.php`

### Step 4: Scan Your QR Code! 📱

Use your Samsung QR Scanner to scan the QR code on your printed certificate - it will now work!

---

## ✅ What's Already Done:

- ✅ QR generator updated to use `https://ephyto.info/verify.php`
- ✅ Verification page ready
- ✅ Security hash validation implemented
- ✅ Mobile-responsive design
- ✅ Public access (no login required)

---

## 🎯 After Upload:

Your existing printed certificate with QR code will work immediately because it already points to `ephyto.info`!

See [PRODUCTION_DEPLOYMENT_GUIDE.md](PRODUCTION_DEPLOYMENT_GUIDE.md) for detailed instructions.
