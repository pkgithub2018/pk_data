<?php
/**
 * QR Code Generator for Phytosanitary Certificates
 * This file handles QR code generation and management for certificates
 */

/**
 * Check if QR code exists for a certificate
 * @param int $certificate_id
 * @param resource $con Database connection
 * @return array|null QR code data or null if not found
 */
function getCertificateQR($certificate_id, $con) {
    if (empty($certificate_id) || !is_numeric($certificate_id)) {
        return null;
    }
    
    $sql = "SELECT * FROM tbcertificate_qr WHERE certificate_id = $1 ORDER BY created_at DESC LIMIT 1";
    $result = pg_query_params($con, $sql, [$certificate_id]);
    
    if ($result && pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Ensure QR code directory exists
 * @return bool True if directory exists or was created successfully
 */
function ensureQRDirectory() {
    $qr_dir = __DIR__ . '/../uploads/qr_codes';
    
    if (!file_exists($qr_dir)) {
        return mkdir($qr_dir, 0755, true);
    }
    
    return true;
}

/**
 * Generate QR code data and save to database
 * Note: This creates the QR data entry. Actual QR image generation would require a library.
 * @param int $certificate_id
 * @param int $application_id
 * @param string $certificate_no
 * @param resource $con
 * @return array Result with success status and data
 */
function generateCertificateQRData($certificate_id, $application_id, $certificate_no, $con) {
    try {
        // Validate inputs
        if (empty($certificate_id) || empty($application_id) || empty($certificate_no)) {
            return ['success' => false, 'message' => 'Invalid certificate data'];
        }
        
        // Ensure QR directory exists
        if (!ensureQRDirectory()) {
            return ['success' => false, 'message' => 'Failed to create QR code directory'];
        }
        
        // Generate secure hash
        $hash = md5($certificate_id . $application_id . $certificate_no . time());
        
        // Create QR code data (verification URL)
        // For local testing: use local IP, For production: use https://ephyto.info
        $base_url = "https://ephyto.info/verify.php"; // Production URL
        $qr_data = $base_url . "?cert=" . urlencode($certificate_no) . "&hash=" . $hash;
        
        // QR image path
        $qr_image_path = "uploads/qr_codes/qr_cert_{$certificate_id}_" . time() . ".png";
        
        // Check if QR already exists
        $existing = getCertificateQR($certificate_id, $con);
        
        if ($existing) {
            // Update existing record
            $update_sql = "UPDATE tbcertificate_qr 
                          SET qr_code_data = $1, 
                              qr_code_image = $2, 
                              qr_format = 'PNG',
                              created_at = CURRENT_TIMESTAMP
                          WHERE certificate_id = $3
                          RETURNING id, qr_code_data, qr_code_image";
            
            $result = pg_query_params($con, $update_sql, [$qr_data, $qr_image_path, $certificate_id]);
            
            if (!$result) {
                return ['success' => false, 'message' => 'Failed to update QR record: ' . pg_last_error($con)];
            }
            
            $row = pg_fetch_assoc($result);
            
            return [
                'success' => true,
                'qr_id' => $row['id'],
                'qr_data' => $row['qr_code_data'],
                'qr_image' => $row['qr_code_image'],
                'message' => 'QR code updated successfully'
            ];
        } else {
            // Insert new record
            $insert_sql = "INSERT INTO tbcertificate_qr 
                          (certificate_id, application_id, qr_code_data, qr_code_image, qr_format) 
                          VALUES ($1, $2, $3, $4, 'PNG') 
                          RETURNING id, qr_code_data, qr_code_image";
            
            $result = pg_query_params($con, $insert_sql, [
                $certificate_id, 
                $application_id, 
                $qr_data, 
                $qr_image_path
            ]);
            
            if (!$result) {
                return ['success' => false, 'message' => 'Failed to insert QR record: ' . pg_last_error($con)];
            }
            
            $row = pg_fetch_assoc($result);
            
            return [
                'success' => true,
                'qr_id' => $row['id'],
                'qr_data' => $row['qr_code_data'],
                'qr_image' => $row['qr_code_image'],
                'message' => 'QR code created successfully'
            ];
        }
        
    } catch (Exception $e) {
        return [
            'success' => false, 
            'message' => 'Error generating QR code: ' . $e->getMessage()
        ];
    }
}

/**
 * Generate QR code SVG inline (simple text-based QR for now)
 * This is a placeholder - in production you'd use a real QR library
 * @param string $data
 * @return string SVG QR code
 */
function generateSimpleQRCodeSVG($data) {
    // This creates a more realistic QR code appearance with standard patterns
    // In production, use a library like endroid/qr-code or phpqrcode
    
    $size = 150;
    $modules = 25; // QR code grid size
    $moduleSize = $size / $modules;
    
    // Create a simple pattern based on data hash
    $hash = md5($data);
    
    $svg = '<svg width="' . $size . '" height="' . $size . '" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '<rect width="' . $size . '" height="' . $size . '" fill="white"/>';
    
    // Function to draw a module (square)
    $drawModule = function($x, $y) use ($moduleSize) {
        $posX = $x * $moduleSize;
        $posY = $y * $moduleSize;
        return '<rect x="' . $posX . '" y="' . $posY . '" width="' . $moduleSize . '" height="' . $moduleSize . '" fill="black"/>';
    };
    
    // Draw position detection patterns (3 corner squares)
    $positions = [[0, 0], [0, $modules - 7], [$modules - 7, 0]];
    
    foreach ($positions as $pos) {
        list($startX, $startY) = $pos;
        
        // Outer black square (7x7)
        for ($y = 0; $y < 7; $y++) {
            for ($x = 0; $x < 7; $x++) {
                if ($y == 0 || $y == 6 || $x == 0 || $x == 6) {
                    $svg .= $drawModule($startX + $x, $startY + $y);
                }
            }
        }
        
        // Inner black square (3x3)
        for ($y = 2; $y < 5; $y++) {
            for ($x = 2; $x < 5; $x++) {
                $svg .= $drawModule($startX + $x, $startY + $y);
            }
        }
    }
    
    // Draw timing patterns (horizontal and vertical lines)
    for ($i = 8; $i < $modules - 8; $i++) {
        if ($i % 2 == 0) {
            $svg .= $drawModule($i, 6); // Horizontal timing
            $svg .= $drawModule(6, $i); // Vertical timing
        }
    }
    
    // Generate data pattern from hash
    for ($y = 0; $y < $modules; $y++) {
        for ($x = 0; $x < $modules; $x++) {
            // Skip position patterns
            if (($x < 8 && $y < 8) || ($x < 8 && $y >= $modules - 8) || ($x >= $modules - 8 && $y < 8)) {
                continue;
            }
            
            // Skip timing patterns
            if ($x == 6 || $y == 6) {
                continue;
            }
            
            // Generate pseudo-random pattern from hash
            $index = ($y * $modules + $x) % strlen($hash);
            $charValue = ord($hash[$index]);
            
            // Use hash value to determine if module should be black
            if (($charValue + $x + $y) % 3 == 0) {
                $svg .= $drawModule($x, $y);
            }
        }
    }
    
    $svg .= '</svg>';
    
    return $svg;
}

/**
 * Get or create QR code for certificate
 * @param int $certificate_id
 * @param int $application_id
 * @param string $certificate_no
 * @param resource $con
 * @return array QR code data
 */
function ensureCertificateQR($certificate_id, $application_id, $certificate_no, $con) {
    // Check if QR exists
    $existing_qr = getCertificateQR($certificate_id, $con);
    
    if ($existing_qr) {
        return [
            'success' => true,
            'qr_data' => $existing_qr['qr_code_data'],
            'qr_image' => $existing_qr['qr_code_image'],
            'qr_svg' => generateSimpleQRCodeSVG($existing_qr['qr_code_data']),
            'exists' => true
        ];
    }
    
    // Generate new QR
    $result = generateCertificateQRData($certificate_id, $application_id, $certificate_no, $con);
    
    if ($result['success']) {
        return [
            'success' => true,
            'qr_data' => $result['qr_data'],
            'qr_image' => $result['qr_image'],
            'qr_svg' => generateSimpleQRCodeSVG($result['qr_data']),
            'exists' => false
        ];
    }
    
    return $result;
}

?>
