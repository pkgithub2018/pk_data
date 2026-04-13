<?php
session_start();

// Include database connection and functions
require("php-bin/connection.php");
require("php-bin/supports.php");

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header('HTTP/1.0 403 Forbidden');
    die("Access denied. Please log in.");
}

// Get parameters
$appid = isset($_GET['appid']) ? (int)$_GET['appid'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';
$uid = isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : 0;
$gid = isset($_SESSION['gid']) ? (int)$_SESSION['gid'] : 0;

if ($appid === 0) {
    header('HTTP/1.0 400 Bad Request');
    die("Invalid application ID");
}

switch ($action) {
    case 'generate':
        // Generate new PDF
        $result = GenerateCertificatePDF($appid, $uid, $gid, $con);
        
        if ($result['success']) {
            // Return JSON response with success
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'PDF generated successfully',
                'filename' => $result['filename'],
                'download_url' => 'certificate_pdf.php?action=download&appid=' . $appid,
                'view_url' => 'certificate_pdf.php?action=view&appid=' . $appid
            ]);
        } else {
            header('HTTP/1.0 500 Internal Server Error');
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $result['error']
            ]);
        }
        break;
        
    case 'download':
        // Download the latest PDF
        $source_info = GetCertificateSourceInfo($appid, $con);
        
        if ($source_info) {
            $filepath = __DIR__ . '/certificate_sources/' . $source_info['filelink'];
            
            if (file_exists($filepath)) {
                // Set headers for file download
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $source_info['filelink'] . '"');
                header('Content-Length: ' . filesize($filepath));
                header('Cache-Control: private');
                
                // Output file content
                readfile($filepath);
                exit;
            } else {
                header('HTTP/1.0 404 Not Found');
                die("PDF file not found");
            }
        } else {
            header('HTTP/1.0 404 Not Found');
            die("No PDF found for this certificate. Please generate one first.");
        }
        break;
        
    case 'view':
        // View the PDF in browser
        $source_info = GetCertificateSourceInfo($appid, $con);
        
        if ($source_info) {
            $filepath = __DIR__ . '/certificate_sources/' . $source_info['filelink'];
            
            if (file_exists($filepath)) {
                // Set headers for inline viewing
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . $source_info['filelink'] . '"');
                header('Content-Length: ' . filesize($filepath));
                header('Cache-Control: private');
                
                // Output file content
                readfile($filepath);
                exit;
            } else {
                header('HTTP/1.0 404 Not Found');
                die("PDF file not found");
            }
        } else {
            header('HTTP/1.0 404 Not Found');
            die("No PDF found for this certificate. Please generate one first.");
        }
        break;
        
    case 'list':
        // List all PDFs for this application
        $sources = GetAllCertificateSources($appid, $con);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'sources' => $sources
        ]);
        break;
        
    default:
        header('HTTP/1.0 400 Bad Request');
        die("Invalid action. Supported actions: generate, download, view, list");
}
?>