# Certificate PDF Generation System

This system converts phytosanitary certificate views to PDF files and stores them with database tracking.

## Setup Instructions

### 1. Database Setup
Run the SQL script to create the required table:
```sql
-- Execute this in your PostgreSQL database
\i schemas/create_tbcertificate_sources.sql
```

### 2. Dependencies
The system uses mPDF for PDF generation, which has been installed via Composer:
```bash
composer require mpdf/mpdf
```

### 3. Directory Structure
The following files have been created/modified:
- `certificate_sources/` - Directory for storing PDF files
- `schemas/create_tbcertificate_sources.sql` - Database table creation script
- `certificate_view_pdf.php` - PDF-optimized certificate template
- `certificate_pdf.php` - PDF generation endpoint
- `php-bin/supports.php` - Added PDF generation functions
- `certificate_view.php` - Updated with PDF generation button

## Usage

### Generate PDF
1. Open a certificate view: `certificate_view.php?appid=123`
2. Click the "📄 Generate PDF" button
3. The PDF will be automatically generated and downloaded
4. A record is saved in the `tbcertificate_sources` table

### API Endpoints
The `certificate_pdf.php` file provides several endpoints:

#### Generate PDF
```
GET certificate_pdf.php?action=generate&appid=123
```
Returns JSON response with success/error status.

#### Download PDF
```
GET certificate_pdf.php?action=download&appid=123
```
Downloads the latest PDF for the application.

#### View PDF
```
GET certificate_pdf.php?action=view&appid=123
```
Opens the PDF inline in the browser.

#### List PDFs
```
GET certificate_pdf.php?action=list&appid=123
```
Returns JSON list of all PDFs for the application.

## Database Table: tbcertificate_sources

| Column | Type | Description |
|--------|------|-------------|
| id | SERIAL PRIMARY KEY | Auto-increment ID |
| application_id | INTEGER | Reference to tbapplication |
| certificate_id | INTEGER | Reference to tbcertificate |
| created_at | TIMESTAMP | Creation time |
| updated_at | TIMESTAMP | Last update time |
| uid | VARCHAR(50) | User ID who generated |
| gid | VARCHAR(50) | Group ID |
| filelink | VARCHAR(255) | PDF filename |
| enabled | VARCHAR(3) | Active status (yes/no) |

## PHP Functions Added to supports.php

### GenerateCertificatePDF($appid, $uid, $gid, $con)
Generates PDF from certificate data and saves it to the filesystem and database.

**Parameters:**
- `$appid` - Application ID
- `$uid` - User ID
- `$gid` - Group ID
- `$con` - Database connection

**Returns:**
```php
[
    'success' => true/false,
    'filename' => 'certificate_xxx.pdf',
    'filepath' => '/full/path/to/file.pdf',
    'filelink' => 'certificate_sources/file.pdf',
    'db_id' => 123,
    'error' => 'error message if failed'
]
```

### SaveCertificateSource($application_id, $certificate_id, $uid, $gid, $filename, $con)
Saves PDF record to database.

### GetCertificateSourceInfo($application_id, $con)
Gets the latest PDF record for an application.

### GetAllCertificateSources($application_id, $con)
Gets all PDF records for an application.

## Features

1. **Automatic PDF Generation**: Converts HTML certificate to PDF using mPDF
2. **Database Tracking**: Records all generated PDFs in the database
3. **File Storage**: Stores PDFs in `certificate_sources/` directory
4. **User Interface**: Added PDF generation button to certificate view
5. **Error Handling**: Comprehensive error handling and user feedback
6. **Security**: Session-based access control
7. **Multiple Actions**: Generate, download, view, and list PDFs

## File Naming Convention
Generated PDFs are named as:
```
certificate_{certificate_no}_{YYYY-MM-DD_HH-mm-ss}.pdf
```

## Security Notes
- PDF generation requires active user session
- Files are stored outside web root in `certificate_sources/`
- Database stores user and group information for audit trail
- Access controlled through session validation

## Troubleshooting

### Common Issues:
1. **PDF Generation Fails**: Check mPDF installation and PHP memory limits
2. **File Not Found**: Verify `certificate_sources/` directory permissions
3. **Database Errors**: Ensure `tbcertificate_sources` table exists
4. **Permission Denied**: Check web server write permissions to `certificate_sources/`

### Debug Tips:
- Check browser console for JavaScript errors
- Monitor PHP error logs for server-side issues
- Verify database connection and table structure
- Test with simple certificate data first