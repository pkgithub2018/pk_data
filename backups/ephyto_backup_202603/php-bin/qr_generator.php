<?php
/**
 * QR Code Generator - Working Version
 */

/**
 * Generate QR code using QR Server API
 */
function generateQRCode($certificate_no, $size = 200) {
    // Create verification URL with certificate number
    $data = "https://ephyto.info/certificate_verify.php?cert=" . urlencode($certificate_no);
    
    // Use api.qrserver.com for reliable QR generation
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($data);
    
    // Try to fetch and encode as base64 for better reliability
    $imageContent = @file_get_contents($qrUrl);
    
    if ($imageContent) {
        $base64 = base64_encode($imageContent);
        return '<img src="data:image/png;base64,' . $base64 . '" style="width: 100%; height: 100%;" alt="QR Code">';
    } else {
        // Fallback to direct URL if base64 fails
        return '<img src="' . $qrUrl . '" style="width: 100%; height: 100%;" alt="QR Code">';
    }
}

/**
 * Main function used by certificate_preview_new.php
 */
function ensureCertificateQR($certificate_id, $application_id, $certificate_no, $con) {
    return [
        'success' => true,
        'qr_data' => "CERT:" . $certificate_no,
        'qr_svg' => generateQRCode($certificate_no, 150),
        'exists' => true
    ];
}

// Keep for compatibility
function generateSimpleQRCodeSVG($data) {
    return generateQRCode($data, 150);
}

function getCertificateQR($certificate_id, $con) {
    return null;
}

function generateCertificateQRData($certificate_id, $application_id, $certificate_no, $con) {
    return ['success' => true];
}

?>