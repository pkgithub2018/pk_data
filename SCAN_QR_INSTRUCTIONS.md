# 📱 How to Scan Your Certificate QR Code

## Your Certificate: No. 000042/25/00

### ✅ Step-by-Step Scanning Guide

#### **Step 1: Prepare Your Phone**

**For iPhone:**
- Use the built-in **Camera** app (iOS 11+)
- No additional app needed!

**For Android:**
- Use the built-in **Camera** app, OR
- Download **"QR Code Reader"** from Play Store
- Or use Google Lens

---

#### **Step 2: Connect to Same Network**

Make sure your phone is connected to the **same WiFi network** as your computer running XAMPP.

Your computer's local IP: **192.168.100.9**

---

#### **Step 3: Scan the QR Code**

1. 📱 Open your phone's Camera app
2. 📷 Point it at the QR code on your printed certificate
3. ⏱️ Hold steady for 1-2 seconds
4. 🔔 A notification will appear at the top
5. 👆 Tap the notification to open the verification page

**The QR code will open:**
```
http://192.168.100.9/verify.php?cert=000042/25/00&hash=...
```

---

#### **Step 4: View Verification Results**

You should see:
- ✅ **Green badge**: "VERIFIED & AUTHENTIC"
- Certificate details (exporter, importer, product)
- Issue date and authorized officer
- Full certificate information

---

## 🔄 Alternative Methods

### Method A: Manual URL Entry on Phone

If QR scanning doesn't work:

1. Open browser on your phone
2. Type: `http://192.168.100.9/verify.php`
3. Enter certificate number: `000042/25/00`
4. Click "Verify"

### Method B: Desktop Testing

On your computer:
1. Open browser
2. Go to: `http://localhost/verify.php`
3. Enter certificate number: `000042/25/00`
4. Click "Verify"

---

## 🎯 Quick Test Links

**From your phone (on same WiFi):**
```
http://192.168.100.9/verify.php
```

**From your computer:**
```
http://localhost/verify.php
```

**View the certificate:**
```
http://localhost/certificate_preview_new.php?appid=194
```

---

## ⚠️ Troubleshooting

### QR Code doesn't scan?
- ✅ Ensure phone is on same WiFi network
- ✅ Check QR code is clear (not blurry/folded)
- ✅ Try different lighting/angle
- ✅ Hold phone 6-8 inches from QR code
- ✅ Use manual entry method instead

### Page doesn't load?
- ✅ Verify XAMPP is running (Apache + PostgreSQL)
- ✅ Check your computer's IP: `ipconfig` in terminal
- ✅ Update IP in this guide if it changed
- ✅ Try accessing from computer first: `http://localhost/verify.php`

### Certificate not found?
- ✅ Make sure certificate exists in database
- ✅ Check certificate number matches exactly
- ✅ Verify database connection is working

### Firewall blocking?
- ✅ Allow Apache through Windows Firewall
- ✅ XAMPP → Apache → Config → httpd.conf
- ✅ Check `Listen 80` is enabled

---

## 🔐 What Gets Verified

When you scan, the system checks:

✅ Certificate exists in database  
✅ Certificate number matches  
✅ Security hash is valid  
✅ Certificate details are authentic  

And displays:
- Certificate & Application numbers
- Exporter: Domestic Agriculture for Export Co., Ltd
- Importer & Destination country
- Product/commodity information
- Issue date and location
- Authorized officer details

---

## 🚀 For Production Deployment

When ready to go live, update the URL in:

**File:** `php-bin/qr_generator.php` (Line 69)

**Change from:**
```php
$base_url = "http://192.168.100.9/verify.php";
```

**Change to:**
```php
$base_url = "https://yourdomain.com/verify.php";
```

Then regenerate QR codes for all certificates.

---

## 📞 Need Help?

If scanning still doesn't work:

1. **Check XAMPP Status**
   - Apache: ✅ Running (green)
   - PostgreSQL: ✅ Running (green)

2. **Test verification page works:**
   ```
   http://localhost/verify.php
   ```

3. **Test from phone browser:**
   ```
   http://192.168.100.9/verify.php
   ```

4. **Contact Support:**
   - Email: ppd@doa.gov.la
   - Tel: (856) 21 416350

---

## ✨ Success Indicators

**You'll know it's working when:**

1. 📱 Phone camera recognizes QR code
2. 🔔 Notification appears with link
3. 🌐 Browser opens automatically
4. 🟢 Green "VERIFIED & AUTHENTIC" badge shows
5. 📋 All certificate details display correctly

---

**Current Setup:**
- ✅ QR codes generated with local IP
- ✅ Verification page ready
- ✅ Database connected
- ✅ Ready to scan!

**Try scanning now!** 📱➡️📄
