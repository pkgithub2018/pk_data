# QR Code Verification - Quick Start Guide

## 📱 For Certificate Verification (Anyone)

### Method 1: Scan QR Code (Easiest)

1. **Get a QR Scanner App** (if not built into your phone)
   - iPhone: Use built-in Camera app
   - Android: Use built-in Camera or download "QR Code Reader"

2. **Scan the Certificate**
   ```
   📱 Open Camera/QR Scanner
         ↓
   📷 Point at QR code on certificate
         ↓
   👆 Tap notification/link that appears
         ↓
   ✅ View verification results
   ```

3. **Review Certificate Details**
   - Check the green "VERIFIED & AUTHENTIC" badge
   - Review exporter, importer, product details
   - Verify issue date and authorized officer

### Method 2: Manual Entry

1. Go to: `http://yourdomain.com/verify.php`
2. Enter certificate number (e.g., `LAO/PC/2024/001`)
3. Click "Verify"
4. View results

---

## 🖨️ For Certificate Printing (System Users)

### The QR code is automatically included on certificates

When you view/print a certificate:
1. Go to: Applications → View Certificate
2. The QR code appears in the left corner
3. Print the certificate with Print button
4. The QR code will be included in the printout

**QR Code Location on Certificate:**
```
┌─────────────────────────────────────┐
│  ┏━━━━━━━┓                          │
│  ┃ QR    ┃  CERTIFICATE HEADER      │
│  ┃ CODE  ┃                          │
│  ┃       ┃  Certificate Details...  │
│  ┗━━━━━━━┛                          │
│   Scan to     Exporter Info...      │
│    Verify                           │
│              Product Info...        │
│                                     │
│              Signature...           │
└─────────────────────────────────────┘
```

---

## 🔍 What Information Is Verified?

When scanning the QR code, the following is displayed:

### ✅ Certificate Information
- Certificate number
- Issue date and place
- Certificate type (Export/Import)

### ✅ Exporter Details
- Company name
- Company address

### ✅ Importer Details
- Importer name
- Destination country

### ✅ Product Information
- Product/commodity name
- Place of origin

### ✅ Authorization
- Authorized officer name
- Officer position
- Verification status

---

## 🚦 Understanding Status Badges

### ✅ Green Badge: "VERIFIED & AUTHENTIC"
- Certificate found in database ✓
- Security hash matches ✓
- Certificate is valid and authentic ✓
- **Action:** Certificate is genuine, proceed with transaction

### ⚠️ Yellow Badge: "CERTIFICATE FOUND"
- Certificate found in database ✓
- No security hash provided ⚠️
- Basic verification only
- **Action:** Certificate exists but not fully authenticated

### ❌ Red Badge: "VERIFICATION FAILED"
- Certificate not found in database ✗
- OR security hash doesn't match ✗
- **Action:** Do not accept certificate, contact authorities

---

## 🔐 Security Features

1. **Unique Security Hash**
   - Each QR code has a unique hash
   - Hash is validated against database
   - Prevents counterfeiting

2. **Real-time Database Check**
   - Every scan checks live database
   - Ensures certificate is current and valid

3. **Tamper Detection**
   - Modified certificates won't match hash
   - System alerts on hash mismatch

---

## 📞 Support & Contact

**If verification fails or you have questions:**

📍 Department of Agriculture, Lao PDR  
📮 P.O Box 811, Nongbone, Lao PDR  
📞 Tel: (856) 21 416350  
📠 Fax: (856) 21 415349  
📧 Email: ppd@doa.gov.la

---

## 🧪 Testing the System

### Test URL (Local Development):
```
http://localhost/qr_verification_demo.html
```

### Test a Certificate:
```
http://localhost/certificate_preview_new.php?appid=194
```

### Test Verification:
```
http://localhost/verify.php
```

---

## 🌐 Production URLs

When deployed to production, update these URLs:

**Verification Page:**
```
https://yourdomain.com/verify.php
```

**QR Code URL Pattern:**
```
https://yourdomain.com/verify?cert=[CERT_NO]&hash=[HASH]
```

**Demo Page:**
```
https://yourdomain.com/qr_verification_demo.html
```

---

## 💡 Tips for Best Results

### For Certificate Holders:
✅ Print certificates in high quality  
✅ Ensure QR code is clearly visible  
✅ Don't fold or damage QR code area  
✅ Test QR code before shipping certificates  

### For Verifiers:
✅ Use good lighting when scanning  
✅ Hold phone steady when scanning  
✅ Try different angles if scan fails  
✅ Use manual entry if QR code damaged  

### For System Admins:
✅ Test QR codes regularly  
✅ Monitor verification logs  
✅ Keep database backups  
✅ Update verification URL for production  

---

## 🔄 Workflow Example

### Typical Use Case:

1. **Exporter** applies for phytosanitary certificate
2. **Inspector** reviews and approves application
3. **System** generates certificate with QR code
4. **Exporter** prints certificate for shipment
5. **Customs/Importer** scans QR code at border
6. **System** verifies certificate authenticity
7. **Goods** cleared for import (if verified)

---

## 📋 Checklist for Implementation

- [x] QR code generation system implemented
- [x] QR codes display on certificates
- [x] Public verification page created
- [x] Security hash validation working
- [x] Database tables configured
- [ ] Update production URLs
- [ ] Test with actual QR scanners
- [ ] Train staff on verification process
- [ ] Publish verification page URL
- [ ] Add verification link to certificates

---

## ❓ Frequently Asked Questions

**Q: Do I need to log in to verify a certificate?**  
A: No, verification is public and requires no login.

**Q: Can I verify certificates from my phone?**  
A: Yes! The verification page is mobile-friendly.

**Q: What if the QR code is damaged?**  
A: Use manual entry on the verification page with the certificate number.

**Q: Is the verification real-time?**  
A: Yes, it checks the live database instantly.

**Q: Can I print verification results?**  
A: Yes, use the "Print Verification" button on results page.

**Q: How long are certificates valid?**  
A: Check the issue date and validity period on the certificate.

---

## 📚 Related Documentation

- `README_QR_VERIFICATION.md` - Complete technical documentation
- `qr_verification_demo.html` - Interactive demo page
- `verify.php` - Verification page source code
- `php-bin/qr_generator.php` - QR generation functions

---

**Last Updated:** January 2026  
**Version:** 1.0  
**System:** Phytosanitary Certificate Management
