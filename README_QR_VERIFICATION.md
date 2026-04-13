# QR Code Certificate Verification System

## Overview
This system allows anyone to verify the authenticity of phytosanitary certificates by scanning QR codes printed on the certificates.

## How It Works

### 1. **QR Code Generation** (Automatic)
When a certificate is created:
- A unique QR code is automatically generated
- The QR code contains a verification URL: `https://ephyto.info/verify?cert=[certificate_no]&hash=[security_hash]`
- The QR data and security hash are stored in the `tbcertificate_qr` database table
- The QR code is displayed on the certificate preview/PDF

### 2. **Certificate Printing**
- Certificates are printed with the QR code in the left corner
- The QR code label says "Scan to Verify"
- Anyone with a QR code scanner can verify the certificate

### 3. **Certificate Verification** (Public Access)
When someone scans the QR code:

#### Method 1: Scan QR Code (Recommended)
1. Use a smartphone QR code scanner app
2. Scan the QR code on the physical/digital certificate
3. The scanner opens the verification URL automatically
4. The verification page displays certificate details

#### Method 2: Manual Entry
1. Visit: `http://localhost/verify.php` (or your domain)
2. Enter the certificate number manually
3. Click "Verify"
4. View certificate information

## Files Involved

### Core Files
- **certificate_preview_new.php** - Displays certificate with QR code
- **verify.php** - Public verification page (newly created)
- **php-bin/qr_generator.php** - QR code generation functions
- **php-bin/supports.php** - Helper functions for fetching data

### Database Tables
- **tbcertificate** - Certificate information
- **tbcertificate_qr** - QR code data and hashes
- **tbapplication** - Application details
- **tbcompany** - Exporter information
- **tbimporter** - Importer information

## Features

### Security Features
✅ **Hash Verification** - Each QR code contains a unique security hash
✅ **Database Validation** - Certificate data verified against database
✅ **Tamper Detection** - Invalid hashes indicate tampering
✅ **Timestamp Tracking** - Verification time recorded

### Verification Information Displayed
The verification page shows:
- Certificate number and status
- Issue date and place
- Exporter name and address
- Importer and destination country
- Product/commodity information
- Place of origin
- Authorized officer details
- Certificate validity status

### Visual Status Indicators
- **Green Badge** - ✅ VERIFIED & AUTHENTIC (hash matched)
- **Yellow Badge** - ⚠️ CERTIFICATE FOUND (no hash provided)
- **Red Badge** - ❌ VERIFICATION FAILED (not found/invalid)

## Usage Instructions

### For Certificate Holders/Exporters
1. After certificate approval, view/print your certificate
2. The QR code is automatically included on the certificate
3. Share the certificate with importers/customs officials
4. They can scan the QR code to verify authenticity

### For Verification (Customs/Importers)
1. **Using QR Scanner:**
   - Open any QR code scanner app on smartphone
   - Point camera at the QR code on certificate
   - Tap the notification to open verification page
   - Review certificate details

2. **Manual Verification:**
   - Go to: `http://yourdomain.com/verify.php`
   - Enter certificate number (e.g., LAO/PC/2024/001)
   - Click "Verify"
   - Review displayed information

### For Testing Locally
1. Open a certificate: `http://localhost/certificate_preview_new.php?appid=194`
2. Print or save the certificate with QR code
3. Test verification: `http://localhost/verify.php?cert=LAO/PC/2024/001&hash=[hash]`

## QR Code URL Format

```
https://ephyto.info/verify?cert=[CERT_NUMBER]&hash=[SECURITY_HASH]

Example:
https://ephyto.info/verify?cert=LAO/PC/2024/001&hash=5f4dcc3b5aa765d61d8327deb882cf99
```

### URL Parameters
- `cert` (required) - Certificate number
- `hash` (optional but recommended) - Security hash for authentication

## Public Access
The verification page (`verify.php`) is **publicly accessible** without login:
- ✅ No authentication required
- ✅ Works for anyone with certificate number or QR code
- ✅ Mobile-friendly responsive design
- ✅ Can be accessed from anywhere

## Implementation Status

### ✅ Completed
- QR code generation system
- QR code display on certificates
- Database storage for QR data
- Public verification page
- Security hash validation
- Mobile-responsive design
- Search by certificate number
- Print verification results

### 🔄 Optional Enhancements
- Real QR library integration (e.g., endroid/qr-code for production)
- QR code scanning with device camera (JavaScript library)
- Multi-language support for verification page
- API endpoint for programmatic verification
- Verification history/audit log
- Email notifications on verification attempts
- Blockchain integration for immutable records

## Server Configuration

### For Production Deployment
1. Update QR code generation URL in `php-bin/qr_generator.php`:
   ```php
   $qr_data = "https://yourdomain.com/verify?cert=" . urlencode($certificate_no) . "&hash=" . $hash;
   ```

2. Update verification page domain references

3. Ensure database connection works in production

4. Test QR codes with actual scanners

## Troubleshooting

### QR Code Not Displaying
- Check that `tbcertificate_qr` table exists
- Verify QR generation function is called
- Check for PHP errors in `qr_generator.php`

### Verification Page Shows "Not Found"
- Verify certificate exists in database
- Check certificate number is correct
- Ensure database connection is working

### Hash Mismatch Errors
- QR code may be from old certificate version
- Certificate may have been regenerated
- Security hash may have changed

## Contact
For technical support:
- Department of Agriculture, Lao PDR
- Email: ppd@doa.gov.la
- Tel: (856) 21 416350
