<?php
/**
 * Public Certificate Verification Page
 * This page verifies phytosanitary certificates via QR code scanning
 * No authentication required - public access
 */

require("php-bin/connection.php");
require("php-bin/supports.php");

// Get parameters from URL
$certificate_no = isset($_GET['cert']) ? trim($_GET['cert']) : '';
$hash = isset($_GET['hash']) ? trim($_GET['hash']) : '';

$verification_result = null;
$certificate_data = null;
$error_message = null;

if (!empty($certificate_no)) {
    try {
        // Get certificate information
        $cert_sql = "SELECT c.*, a.application_no, a.company_id, a.country_import,
                     a.certificate_type, a.commodity_id,
                     a.place_origin, a.conveyance_id, a.conveyance_sign
                     FROM tbcertificate c
                     INNER JOIN tbapplication a ON c.application_id = a.id
                     WHERE c.certificate_no = $1";
        
        $cert_result = pg_query_params($con, $cert_sql, [$certificate_no]);
        
        if ($cert_result && pg_num_rows($cert_result) > 0) {
            $certificate_data = pg_fetch_assoc($cert_result);
            
            // Get QR code record to verify hash
            $qr_sql = "SELECT * FROM tbcertificate_qr WHERE certificate_id = $1 ORDER BY created_at DESC LIMIT 1";
            $qr_result = pg_query_params($con, $qr_sql, [$certificate_data['id']]);
            
            if ($qr_result && pg_num_rows($qr_result) > 0) {
                $qr_data = pg_fetch_assoc($qr_result);
                
                // Verify hash (if provided)
                if (!empty($hash)) {
                    // Extract hash from stored QR data URL
                    parse_str(parse_url($qr_data['qr_code_data'], PHP_URL_QUERY), $params);
                    $stored_hash = $params['hash'] ?? '';
                    
                    if ($hash === $stored_hash) {
                        $verification_result = 'valid';
                    } else {
                        $verification_result = 'invalid_hash';
                        $error_message = "Security verification failed. This certificate may have been tampered with.";
                    }
                } else {
                    $verification_result = 'no_hash';
                }
                
                // Get additional information
                if ($verification_result === 'valid' || $verification_result === 'no_hash') {
                    $appid = $certificate_data['appid'];
                    
                    // Get exporter info
                    $exporter_info = EntityExportInfo($certificate_data['company_id'], $con);
                    
                    // Get importer info
                    $importer_info = EntityImportInfo($certificate_data['importerid'], $con);
                    
                    // Get country info
                    $import_country = CountryInfo($certificate_data['country_import'], $con);
                    $origin_country = CountryInfo($certificate_data['place_origin'], $con);
                    
                    // Get product info
                    $product_info = ProductInfo($certificate_data['commodity_id'], $con);
                    
                    // Get approver info
                    $approver_info = ApproverInfo($certificate_data['approved_by'], $con);
                }
            } else {
                $verification_result = 'no_qr';
                $error_message = "QR code verification data not found for this certificate.";
            }
        } else {
            $verification_result = 'not_found';
            $error_message = "Certificate not found. Please check the certificate number.";
        }
    } catch (Exception $e) {
        $verification_result = 'error';
        $error_message = "An error occurred during verification: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification - Phytosanitary Certificate System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #4caf50;
            --accent-color: #8bc34a;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .verification-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .verification-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .status-badge {
            display: inline-block;
            padding: 10px 30px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
        }
        
        .status-valid {
            background: #d4edda;
            color: #155724;
            border: 2px solid #28a745;
        }
        
        .status-invalid {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #dc3545;
        }
        
        .status-warning {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffc107;
        }
        
        .info-row {
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #212529;
            font-size: 15px;
        }
        
        .section-title {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 18px;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--accent-color);
        }
        
        .verification-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
        
        .search-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <!-- Header -->
        <div class="text-center mb-4">
            <img src="assets/img/national_logo.jpg" alt="Logo" style="width: 80px; height: 80px; margin-bottom: 15px;">
            <h2 class="text-white">Phytosanitary Certificate Verification</h2>
            <p class="text-white">Department of Agriculture, Lao PDR</p>
        </div>
        
        <!-- Search Form (if no certificate searched yet) -->
        <?php if (empty($certificate_no)): ?>
        <div class="search-form">
            <h4 class="text-center mb-4">Enter Certificate Number</h4>
            <form method="GET" action="verify.php">
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-lg" name="cert" 
                           placeholder="Enter certificate number (e.g., LAO/PC/2024/001)" required>
                    <button class="btn btn-success btn-lg" type="submit">
                        <i class="bi bi-search me-2"></i>Verify
                    </button>
                </div>
            </form>
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Scan the QR code on your certificate or enter the certificate number manually
                </small>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Verification Results -->
        <?php if (!empty($certificate_no)): ?>
        <div class="verification-card">
            <!-- Card Header -->
            <div class="card-header-custom">
                <h3 class="mb-0"><i class="bi bi-shield-check me-2"></i>Certificate Verification</h3>
                
                <?php if ($verification_result === 'valid'): ?>
                    <div class="status-badge status-valid">
                        <i class="bi bi-check-circle-fill me-2"></i>VERIFIED & AUTHENTIC
                    </div>
                <?php elseif ($verification_result === 'no_hash'): ?>
                    <div class="status-badge status-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>CERTIFICATE FOUND
                    </div>
                    <small class="text-white d-block">Security hash not provided - basic verification only</small>
                <?php else: ?>
                    <div class="status-badge status-invalid">
                        <i class="bi bi-x-circle-fill me-2"></i>VERIFICATION FAILED
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Card Body -->
            <div class="card-body p-4">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($verification_result === 'valid' || $verification_result === 'no_hash'): ?>
                    <!-- Certificate Information -->
                    <div class="section-title">
                        <i class="bi bi-file-earmark-text me-2"></i>Certificate Details
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Certificate Number:</div>
                                <div class="info-value">
                                    <strong><?php echo htmlspecialchars($certificate_data['certificate_no']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Application Number:</div>
                                <div class="info-value"><?php echo htmlspecialchars($certificate_data['application_no']); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Date Issued:</div>
                                <div class="info-value">
                                    <?php echo date('d-M-Y', strtotime($certificate_data['date_issued'])); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Place Issued:</div>
                                <div class="info-value"><?php echo htmlspecialchars($certificate_data['place_issued']); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Certificate Type:</div>
                        <div class="info-value">
                            <span class="badge bg-success">
                                <?php echo strtoupper(htmlspecialchars($certificate_data['certificate_type'])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Exporter Information -->
                    <div class="section-title">
                        <i class="bi bi-building me-2"></i>Exporter Information
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Exporter Name:</div>
                        <div class="info-value"><?php echo htmlspecialchars($exporter_info['title'] ?? 'N/A'); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Address:</div>
                        <div class="info-value"><?php echo htmlspecialchars($exporter_info['address'] ?? 'N/A'); ?></div>
                    </div>
                    
                    <!-- Importer Information -->
                    <div class="section-title">
                        <i class="bi bi-globe me-2"></i>Importer Information
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Importer Name:</div>
                        <div class="info-value"><?php echo htmlspecialchars($importer_info['title'] ?? 'N/A'); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Destination Country:</div>
                        <div class="info-value">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                            <?php echo htmlspecialchars($import_country['title'] ?? 'N/A'); ?>
                        </div>
                    </div>
                    
                    <!-- Product Information -->
                    <div class="section-title">
                        <i class="bi bi-box-seam me-2"></i>Product Information
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Product/Commodity:</div>
                        <div class="info-value"><?php echo htmlspecialchars($product_info['name'] ?? 'N/A'); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Place of Origin:</div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($origin_country['title'] ?? 'N/A'); ?>
                        </div>
                    </div>
                    
                    <!-- Authorized Officer -->
                    <div class="section-title">
                        <i class="bi bi-person-badge me-2"></i>Authorized By
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Authorized Officer:</div>
                        <div class="info-value">
                            <?php 
                            $officer_name = trim(($approver_info['name'] ?? '') . ' ' . ($approver_info['surname'] ?? ''));
                            echo htmlspecialchars(strtoupper($officer_name)); 
                            ?>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Position:</div>
                        <div class="info-value"><?php echo htmlspecialchars($certificate_data['position_approved'] ?? 'N/A'); ?></div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="text-center mt-4">
                        <a href="verify.php" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left me-2"></i>Verify Another Certificate
                        </a>
                        <button class="btn btn-success" onclick="window.print()">
                            <i class="bi bi-printer-fill me-2"></i>Print Verification
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Footer -->
            <div class="verification-footer">
                <p class="mb-2">
                    <strong>Department of Agriculture</strong><br>
                    P.O Box 811, Nongbone, Lao PDR<br>
                    Tel: (856) 21 416350 | Email: ppd@doa.gov.la
                </p>
                <small>
                    This verification was performed on <?php echo date('d-M-Y H:i:s'); ?><br>
                    <i class="bi bi-shield-lock me-1"></i>Secure Certificate Verification System
                </small>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Information Section -->
        <div class="text-center mt-4 text-white">
            <small>
                <i class="bi bi-info-circle me-1"></i>
                For assistance, contact the Department of Agriculture or visit our office.
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
