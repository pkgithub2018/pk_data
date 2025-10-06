<?php
session_start();

// Include database connection and functions
require("php-bin/connection.php");
require("php-bin/supports.php");

// Get application ID from URL parameter
$appid = isset($_GET['appid']) ? (int)$_GET['appid'] : 0;

if ($appid === 0) {
    die("Invalid application ID");
}

// Get application information
$app_info = ApplicationInfo($appid, $con);
if (!$app_info) {
    die("Application not found");
}

// Get certificate information
$cert_info = CertificateInfo($appid, $con);
if (!$cert_info) {
    die("Certificate not found");
}

// Get exporter information
$exporterid = $app_info['company_id'] ?? '';
$exporter_info = null;
if ($exporterid) {
    $exporter_info = EntityExportInfo($exporterid, $con);
}

// Get importer information
$importerid = $app_info['importerid'] ?? '';
$importer_info = null;
if ($importerid) {
    $importer_info = EntityImportInfo($importerid, $con);
}

// Get additional data
$import_country_id = $app_info['country_import'] ?? '';
$import_country = '';
if ($import_country_id) {
    $country_info = CountryInfo($import_country_id, $con);
    $import_country = $country_info ? $country_info['title'] : 'Unknown Country';
}

// Get place of origin
$place_origin_id = $app_info['place_origin'] ?? '';
$place_origin = '';
if ($place_origin_id) {
    $origin_info = CountryInfo($place_origin_id, $con);
    $place_origin = $origin_info ? $origin_info['title'] : 'Lao PDR';
} else {
    $place_origin = 'Lao PDR';
}

// Get conveyance type
$conveyance_id = $app_info['conveyance_id'] ?? '';
$conveyance_name = '';
if ($conveyance_id) {
    $conveyance_name = ConveyanceType($conveyance_id, $con);
}

// Get commodity name
$commodity_id = $app_info['commodity_id'] ?? '';
$commodity_name = '';
if ($commodity_id) {
    $sql = "SELECT name FROM tbproduct WHERE id = '$commodity_id'";
    $result = pg_query($con, $sql);
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $commodity_name = $row['name'];
    }
}

// Get unit information
$unit_id = $app_info['unit'] ?? '';
$unit_name = '';
if ($unit_id) {
    $sql = "SELECT unit FROM tbunit WHERE id = '$unit_id'";
    $result = pg_query($con, $sql);
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $unit_name = $row['unit'];
    }
}

// Get approver information
$approved_by_id = $cert_info['approved_by'] ?? '';
$approver_info = null;
if ($approved_by_id) {
    $sql = "SELECT name, surname FROM tbapprovers WHERE id = '$approved_by_id'";
    $result = pg_query($con, $sql);
    if ($result && pg_num_rows($result) > 0) {
        $approver_info = pg_fetch_assoc($result);
    }
}

// Format date
$date_issued = $cert_info['date_issued'] ?? '';
if ($date_issued && $date_issued !== '0000-00-00') {
    $date_issued = date('d F Y', strtotime($date_issued));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phytosanitary Certificate - <?php echo htmlspecialchars($cert_info['certificate_no']); ?></title>
    
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        
        .certificate-container {
            width: 210mm;
            height: 297mm;
            margin: 20px auto;
            position: relative;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-image: url('assets/img/certificate_draft.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .certificate-content {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 40mm 25mm;
            color: #000;
            font-size: 12pt;
            line-height: 1.4;
        }
        
        .certificate-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .certificate-title {
            font-size: 24pt;
            font-weight: bold;
            margin: 20px 0;
            color: #2c5aa0;
        }
        
        .certificate-no {
            position: absolute;
            top: 60mm;  /* Position 1 - Certificate No at top right */
            right: 15mm;
            font-size: 14pt;
            font-weight: bold;
            color: #000;
        }
        
        .certificate-field {
            position: absolute;
            display: flex;
            align-items: flex-start;
            color: #000;
            font-size: 14pt;
        }
        
        /* Position 2 - Export Country (FROM field) */
        .field-export-country {
            top: 70mm;
            left: 30mm;
            width: 70mm;
        }
        
        /* Position 3 - Import Country (TO field) */
        .field-import-country {
            top: 70mm;
            right: 25mm;
            width: 70mm;
        }
        
        /* Position 4 - Name and address of exporter */
        .field-exporter {
            top: 95mm;
            left: 25mm;
            width: 85mm;
        }
        
        /* Position 5 - Name and address of importer */
        .field-importer {
            top: 95mm;
            right: 15mm;
            width: 85mm;
        }
        
        /* Position 6 - Number and description of packages */
        .field-packages {
            top: 125mm;
            left: 25mm;
            width: 85mm;
        }
        
        /* Position 7 - Distinguishing marks */
        .field-distinguishing-marks {
            top: 125mm;
            right: 15mm;
            width: 85mm;
        }
        
        /* Position 8 - Place of origin */
        .field-place-origin {
            top: 145mm;
            left: 25mm;
            width: 85mm;
        }

        /* Position 8 - Conveyance method */
        .field-conveyance-method {
            top: 145mm;
            left: 70mm;
            width: 85mm;
        }
        
        /* Position 9 - Entry point */
        .field-entry-point {
            top: 145mm;
            right: 15mm;
            width: 85mm;
        }
        
        /* Position 10 - Quantity and unit */
        .field-quantity {
            top: 165mm;
            left: 15mm;
            width: 85mm;
        }
        
        /* Position 11 - Scientific name */
        .field-scientific-name {
            top: 165mm;
            right: 15mm;
            width: 85mm;
        }
        
        /* Position 12 - Additional declaration */
        .field-additional-declaration {
            top: 170mm;
            left: 15mm;
            width: 175mm;
        }
        
        /* Position 13 - Treatment date */
        .field-treatment-date {
            top: 210mm;
            left: 15mm;
            width: 55mm;
        }
        
        /* Position 14 - Treatment method */
        .field-treatment-method {
            top: 210mm;
            left: 75mm;
            width: 55mm;
        }
        
        /* Position 15 - Duration and temperature */
        .field-duration-temp {
            top: 210mm;
            right: 15mm;
            width: 55mm;
        }
        
        /* Position 16 - Additional information */
        .field-additional-info {
            top: 235mm;
            left: 15mm;
            width: 85mm;
        }
        
        /* Position 17 - Date inspected */
        .field-date-inspected {
            top: 235mm;
            left: 105mm;
            width: 40mm;
        }
        
        /* Position 18 - Date issued */
        .field-date-issued {
            top: 260mm;
            left: 15mm;
            width: 55mm;
        }
        
        /* Position 19 - Place of issue */
        .field-place-issued {
            top: 260mm;
            right: 15mm;
            width: 85mm;
        }
        
        .field-label {
            font-weight: bold;
            margin-right: 5px;
            font-size: 8pt;
        }
        
        .field-value {
            flex: 1;
            min-height: 15px;
            padding: 1px 3px;
            font-size: 9pt;
            line-height: 1.2;
            background: transparent; /* No background - transparent over certificate image */
        }
        
        /* Special styling for text areas */
        .field-value.textarea {
            min-height: 25mm;
            padding: 3px;
            background: transparent; /* No background for textarea fields */
        }
        
        /* Remove borders for clean look on background */
        .field-value {
            border: none;
        }
        
        .signature-section {
            position: absolute;
            bottom: 20mm;
            right: 25mm;
            width: 60mm;
            text-align: center;
            font-size: 9pt;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 20px;
            padding-top: 5px;
            font-size: 8pt;
        }
        
        .signature-section {
            position: absolute;
            bottom: 80mm;
            right: 25mm;
            width: 60mm;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 10pt;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
        
        @media print {
            body {
                background: white;
            }
            
            .certificate-container {
                width: 100%;
                height: 100vh;
                margin: 0;
                box-shadow: none;
            }
            
            .print-button {
                display: none;
            }
        }
        
        .two-column {
            display: flex;
            gap: 20px;
        }
        
        .column {
            flex: 1;
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">🖨️ Print Certificate</button>
    
    <div class="certificate-container">
        <div class="certificate-content">
            <!-- Position 1: Certificate Number -->
            <div class="certificate-no">
                <?php echo htmlspecialchars($cert_info['certificate_no'] ?? ''); ?>
            </div>
            
            <!-- Position 2: Export Country (FROM) -->
            <div class="certificate-field field-export-country">
                <span class="field-value"><?php echo htmlspecialchars($place_origin); ?></span>
            </div>
            
            <!-- Position 3: Import Country (TO) -->
            <div class="certificate-field field-import-country">
                <span class="field-value"><?php echo htmlspecialchars($import_country); ?></span>
            </div>
            
            <!-- Position 4: Name and address of exporter -->
            <div class="certificate-field field-exporter">
                <span class="field-value textarea">
                    <?php echo htmlspecialchars($exporter_info['title'] ?? ''); ?><br>
                    <?php echo nl2br(htmlspecialchars($exporter_info['address'] ?? '')); ?>
                </span>
            </div>
            
            <!-- Position 5: Name and address of importer -->
            <div class="certificate-field field-importer">
                <span class="field-value textarea">
                    <?php echo htmlspecialchars($importer_info['title'] ?? ''); ?><br>
                    <?php echo nl2br(htmlspecialchars($importer_info['address'] ?? '')); ?>
                </span>
            </div>
            
            <!-- Position 6: Number and description of packages -->
            <div class="certificate-field field-packages">
                <span class="field-value">
                    <?php echo htmlspecialchars($app_info['commodity_description'] ?? ''); ?>
                </span>
            </div>
            
            <!-- Position 7: Distinguishing marks -->
            <div class="certificate-field field-distinguishing-marks">
                <span class="field-value">
                    <?php echo htmlspecialchars($app_info['marks_item'] ?? ''); ?>
                </span>
            </div>
            
            <!-- Position 8: Place of origin -->
            <div class="certificate-field field-place-origin">
                <span class="field-value"><?php echo htmlspecialchars($place_origin); ?></span>
            </div>
            <!-- Position 8: Conveyance method -->
            <div class="certificate-field field-conveyance-method">
                <span class="field-value"><?php echo htmlspecialchars($conveyance_name); ?></span>
            </div>

            <!-- Position 9: Entry point -->
            <div class="certificate-field field-entry-point">
                <span class="field-value"><?php echo htmlspecialchars($app_info['import_point'] ?? ''); ?></span>
            </div>
            
            <!-- Position 10: Quantity and unit -->
            <div class="certificate-field field-quantity">
                <span class="field-value">
                    Net: <?php echo htmlspecialchars($app_info['nquantity'] ?? '0'); ?> <?php echo htmlspecialchars($unit_name); ?><br>
                    Gross: <?php echo htmlspecialchars($app_info['gquantity'] ?? '0'); ?> <?php echo htmlspecialchars($unit_name); ?>
                </span>
            </div>
            
            <!-- Position 11: Scientific name -->
            <div class="certificate-field field-scientific-name">
                <span class="field-value">
                    <?php echo htmlspecialchars($app_info['scientific_name'] ?? ''); ?>
                    <?php if (!empty($cert_info['additional_scientificname'])): ?>
                        <br><?php echo htmlspecialchars($cert_info['additional_scientificname']); ?>
                    <?php endif; ?>
                </span>
            </div>
            
            <!-- Position 12: Additional declaration -->
            <?php if (!empty($cert_info['additional_declaration'])): ?>
            <div class="certificate-field field-additional-declaration">
                <span class="field-value textarea">
                    <?php echo nl2br(htmlspecialchars($cert_info['additional_declaration'])); ?>
                </span>
            </div>
            <?php endif; ?>
            
            <!-- Position 13: Treatment date -->
            <?php 
            $inspection_info = null;
            $sql = "SELECT * FROM tbinspection WHERE application_id = '$appid'";
            $result = pg_query($con, $sql);
            if ($result && pg_num_rows($result) > 0) {
                $inspection_info = pg_fetch_assoc($result);
            }
            ?>
            
            <?php if ($inspection_info): ?>
            <div class="certificate-field field-treatment-date">
                <span class="field-value">
                    <?php 
                    $treatment_date = $inspection_info['treatment_date'] ?? '';
                    if ($treatment_date && $treatment_date !== '0000-00-00') {
                        echo date('d/m/Y', strtotime($treatment_date));
                    }
                    ?>
                </span>
            </div>
            
            <!-- Position 14: Treatment method -->
            <div class="certificate-field field-treatment-method">
                <span class="field-value">
                    <?php
                    $treatment_method_id = $inspection_info['treatment_method'] ?? '';
                    if ($treatment_method_id) {
                        $sql = "SELECT method FROM tbtreatment_method WHERE id = '$treatment_method_id'";
                        $result = pg_query($con, $sql);
                        if ($result && pg_num_rows($result) > 0) {
                            $row = pg_fetch_assoc($result);
                            echo htmlspecialchars($row['method']);
                        }
                    }
                    ?>
                </span>
            </div>
            
            <!-- Position 15: Duration and temperature -->
            <div class="certificate-field field-duration-temp">
                <span class="field-value">
                    <?php echo htmlspecialchars($inspection_info['duration_temp'] ?? ''); ?>
                </span>
            </div>
            
            <!-- Position 16: Additional information -->
            <div class="certificate-field field-additional-info">
                <span class="field-value">
                    <?php echo htmlspecialchars($inspection_info['additional_info'] ?? ''); ?>
                </span>
            </div>
            
            <!-- Position 17: Date inspected -->
            <div class="certificate-field field-date-inspected">
                <span class="field-value">
                    <?php 
                    $inspection_date = $inspection_info['inspection_date'] ?? '';
                    if ($inspection_date && $inspection_date !== '0000-00-00') {
                        echo date('d/m/Y', strtotime($inspection_date));
                    }
                    ?>
                </span>
            </div>
            <?php endif; ?>
            
            <!-- Position 18: Date issued -->
            <div class="certificate-field field-date-issued">
                <span class="field-value"><?php echo htmlspecialchars($date_issued); ?></span>
            </div>
            
            <!-- Position 19: Place of issue -->
            <div class="certificate-field field-place-issued">
                <span class="field-value"><?php echo htmlspecialchars($cert_info['place_issued'] ?? ''); ?></span>
            </div>
            
            <!-- Signature Section -->
            <div class="signature-section">
                <div style="margin-bottom: 20px;">
                    <?php if ($approver_info): ?>
                        <div style="font-weight: bold;"><?php echo htmlspecialchars($approver_info['name'] . ' ' . $approver_info['surname']); ?></div>
                    <?php endif; ?>
                    <div><?php echo htmlspecialchars($cert_info['position_approved'] ?? ''); ?></div>
                </div>
                <div class="signature-line">
                    Authorized Officer
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-focus for printing
        window.onload = function() {
            // Optional: Auto-print when page loads (uncomment if desired)
            // window.print();
        };
        
        // Keyboard shortcut for printing
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>