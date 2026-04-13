<?php
session_start();

// Include database connection and functions
require("php-bin/connection.php");
require("php-bin/supports.php");

// AJAX endpoint for logging certificate prints
if (isset($_POST['action']) && $_POST['action'] == 'log_certificate_print') {
    // Clean output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    $appid = $_POST['appid'] ?? '';
    $certificate_id = $_POST['certificate_id'] ?? '';
    $userid = $_POST['userid'] ?? '';
    
    if (empty($appid) || !is_numeric($appid)) {
        echo json_encode(['success' => false, 'error' => 'Invalid application ID']);
        exit;
    }
    
        // Log to PHP error log instead of trying to output script tag
        error_log("Logging print for Certificate - appid: $appid, certificate_id: $certificate_id, userid: $userid");
        
        try {
            // Get certificate info to retrieve current carbonpaper ID
            $cert_info = CertificateInfo($appid, $con);
            $current_carbonpaper_id = $cert_info['carbonpaper_id'] ?? '';
            
            // Get original carbonpaper ID from the first print log entry, or use current if this is the first print
            $original_sql = "SELECT original_carbonpaper_id FROM tbcertificate_print_log WHERE application_id = $1 ORDER BY id ASC LIMIT 1";
            $original_result = pg_query_params($con, $original_sql, [$appid]);
            
            if ($original_result && pg_num_rows($original_result) > 0) {
                // Use the original from first print log entry
                $original_row = pg_fetch_assoc($original_result);
                $original_carbonpaper_id = $original_row['original_carbonpaper_id'];
            } else {
                // This is the first print, so current becomes original
                $original_carbonpaper_id = $current_carbonpaper_id;
            }
            
            // Log retrieved data
            error_log("Carbon paper IDs - current: $current_carbonpaper_id, original: $original_carbonpaper_id");
            
            // Determine current_status based on whether data exists and if carbon paper changed
            $current_status = 'Printed'; // Default for first print
            
            // Check if any print log exists for this application
            $check_sql = "SELECT current_carbonpaper_id, original_carbonpaper_id FROM tbcertificate_print_log WHERE application_id = $1 LIMIT 1";
            $check_result = pg_query_params($con, $check_sql, [$appid]);
            
            if ($check_result && pg_num_rows($check_result) > 0) {
                // Data exists - check if carbon paper has changed
                if ($current_carbonpaper_id != $original_carbonpaper_id) {
                    $current_status = 'Printed/Updated';
                } else {
                    $current_status = 'Printed';
                }
            }
            
            error_log("Determined current_status: $current_status");
            
            // Get current print count for this application
        $count_sql = "SELECT COALESCE(MAX(print_count), 0) + 1 as next_count FROM tbcertificate_print_log WHERE application_id = $1";
        $count_result = pg_query_params($con, $count_sql, [$appid]);
        $next_print_count = 1;
        if ($count_result && pg_num_rows($count_result) > 0) {
            $count_row = pg_fetch_assoc($count_result);
            $next_print_count = $count_row['next_count'];
        }
        
        error_log("Next print count for appid $appid: $next_print_count");
        
        // Insert into print log using parameterized query for safety (id auto-increments)
        $sql = "INSERT INTO tbcertificate_print_log 
                (application_id, certificate_id, current_status, current_carbonpaper_id, original_carbonpaper_id, updated_at, updated_by, print_count) 
                VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
        
        $result = pg_query_params($con, $sql, [
            $appid,
            $certificate_id,
            $current_status,
            $current_carbonpaper_id,
            $original_carbonpaper_id,
            date('Y-m-d H:i:s'),
            $userid,
            $next_print_count
        ]);
        
        if ($result) {
            error_log("Print logged successfully for appid: $appid");
            echo json_encode(['success' => true, 'message' => 'Print logged successfully']);
        } else {
            $error = pg_last_error($con);
            error_log("Failed to log print: $error");
            echo json_encode(['success' => false, 'error' => $error]);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log("Exception logging print: $error");
        echo json_encode(['success' => false, 'error' => $error]);
    }
    
    exit;
}

$userid = '';
// User data
    $userid = Userconnect(
        isset($_GET['uid']) ? $_GET['uid'] : '',
        isset($_POST['uid']) ? $_POST['uid'] : '',
        isset($_POST['huid']) ? $_POST['huid'] : '',
        isset($_COOKIE['ephyto_uid']) ? $_COOKIE['ephyto_uid'] : '',
        isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
        $con
    );
    $loginuser = Userdata($userid, $con)['name']; // User name
    $guid = Userdata($userid, $con)['group_id'];
    $position = Userdata($userid, $con)['position'];       
    // Get and store user profile image
    $uprofile = Profiledata($userid, $con);
    if (!$uprofile) {
    // Initialize profile if it doesn't exist
    InitializeProfile($userid, $con);
        $uprofile = Profiledata($userid, $con);
    }
    if ($uprofile && isset($uprofile['imgfilepath']) && !empty($uprofile['imgfilepath']) && $uprofile['imgfilepath'] !== 'default_imgfilepath') {
    $uimage = $uprofile['imgfilepath'];
    }

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
$unit_id = $app_info['unit_id'] ?? '';
$unit_name = '';
if ($unit_id) {
    $sql = "SELECT symb FROM tbproduct_unit WHERE id = '$unit_id'";
    $result = pg_query($con, $sql);
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $unit_name = $row['symb'];
    }
}

// Get quantity (Weight) - Net and Gross along with unit
$net_weight = $app_info['quantity_net'] ?? 0;
$gross_weight = $app_info['quantity_gross'] ?? 0;
$tq_net = $net_weight." ".$unit_name;
$tg_gross = $gross_weight." ".$unit_name;
$commodity_quantity = '';
$commodity_quantity = "G.W: ".$tg_gross." N.W: ".$tq_net;

// Get approver information
$approved_byid = $cert_info['approved_by'] ?? '';
$approver_info = null;
if ($approved_byid) {
    $sql = "SELECT * FROM tbapprovers WHERE id = '$approved_byid'";
    $result = pg_query($con, $sql);
    if ($result && pg_num_rows($result) > 0) {
        $approver_info = pg_fetch_assoc($result);
    }
}

// Format date
$date_issued = $app_info['date_certificate'] ?? '';
if ($date_issued && $date_issued !== '0000-00-00') {
    $date_issued = date('d-M-Y', strtotime($date_issued));
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
            /*margin: 20px auto; */
            margin-left: 17mm;
            margin-right: 17mm;
            position: relative;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-image: url('assets/img/certificate_draft.jpg');
            background-size: contain; /* Fit entire image within A4 page */
            background-position: center;
            background-repeat: no-repeat;
            overflow: hidden;
            page-break-inside: avoid;
        }
        
        .certificate-content {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 20mm 25mm;
            color: #000;
            font-size: 14pt;
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
            top: 70mm;  /* Position 1 - Certificate No at top right */
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
            font-size: 12pt;
            font-weight: bold;
        }
        
         
        .field-label {
            position: absolute;
            font-weight: normal;
            margin-right: 5px;
            font-size: 9pt;
        }
        
        /* Specific positioning for "The National Plant Protection Organization of" label */
        .field-export-country .field-label {
            /* position: relative; */
            top: -15px; /* Position above the country name */
            left: 0;
            width: 100%;
           /* border: 1px solid #100b0bff; */
        }
        
        /* Specific positioning for import country label */
        .field-import-country .field-label {
            /* position: relative; */
            top: -15px; /* Position above the country name */
            left: 0;
            width: 100%;
        }

        /* Position 2 - Export Country (FROM field) */
        .field-export-country {
            top: 82mm;
            left: 30mm;
            width: 85mm;
            font-weight: bold;
        }
        
        /* Position 3 - Import Country (TO field) */
        .field-import-country {
            top: 82mm;
            right: 15mm;
            width: 85mm;
        }
        
        /* Position 4 - Name and address of exporter */
        .field-exporter {
            top: 107mm;
            left: 10mm;
            width: 85mm;
            font-weight: normal;
        }
        
        /* Position 5 - Name and address of importer */
        .field-importer {
            top: 107mm;
            right: 25mm;
            width: 85mm;
            font-weight: normal;
        }
        
        /* Position 6 - Number and description of packages */
        .field-packages {
            top: 150mm;
            left: 25mm;
            width: 85mm;
            font-weight: normal;
        }
        
        /* Position 7 - Distinguishing marks */
        .field-distinguishing-marks {
            top: 150mm;
            right: 15mm;
            width: 100mm;
            font-weight: normal;
        }
        
        /* Position 8 - Place of origin- Two rows */
        .field-place-origin {
            top: 175mm;
            left: 7mm;
            width: 60mm;
            font-weight: normal;
        }

        /* Position 8 - Conveyance method */
        .field-conveyance-method {
            top: 177mm;
            left: 80mm;
            width: 85mm;
            font-weight: normal;
        }
        
        /* Position 9 - Entry point */
        .field-entry-point {
            top: 177mm;
            right: 5mm;
            width: 85mm;
            font-weight: normal;
        }
        
        /* Position 10 - Product name, Quantity and unit - two rows */
        .field-quantity {
            top: 198mm;
            left: 10mm;
            width: 85mm;
            font-weight: normal;
        }
        
        /* Position 11 - Scientific name */
        .field-scientific-name {
            top: 200mm;
            right: 20mm;
            width: 85mm;
            font-weight: normal;
            font-style: italic;
        }
        
        /* Position 12 - Additional declaration */
        .field-additional-declaration {
            top: 245mm;
            left: 30mm;
            width: 175mm;
            font-weight: normal;
        }
        
        /* Position 13 - Treatment date */
        .field-treatment-date {
            top: 278mm;
            left: 75mm;
            width: 55mm;
            font-weight: normal;
        }

          /* Position 14 - Treatment method */
        .field-treatment-method {
            top: 278mm;
            left: 165mm;
            width: 55mm;
            font-weight: normal;
        }

        /* Position 16 - Chemical used */
        .field-chemical-used {
            top: 290mm;
            left: 60mm;
            width: 55mm;
            font-weight: normal;
        }

        
        /* Position 15 - Duration and temperature */
        .field-duration-temp {
            top: 290mm;
            right: 10mm;
            width: 55mm;
            font-weight: normal;
        }

           /* Position 16 - Concentration */
        .field-concentration {
            top: 300mm;
            left: 45mm;
            width: 55mm;
            font-weight: normal;
        }

        /* Position 17 - Additional information */
        .field-additional-info {
            top: 300mm;
            left: 165mm;
            width: 85mm;
            font-weight: normal;
        }
        
        /* Position 18 - Date inspected */
        .field-date-inspected {
            top: 315mm;
            left: 45mm;
            width: 40mm;
            font-weight: normal;
        }
        
        /* Position 19 - Date issued */
        .field-date-issued {
            top: 325mm;
            left: 45mm;
            width: 35mm;
            font-weight: normal;
        }
        
        /* Position 20 - Place of issue */
        .field-place-issued {
            top: 339mm;
            left: 45mm;
            width: 50mm;
        }

        /* Position 21 - Approver name */
        .field-approver-name {
            top: 336mm;
            right: 15mm;
            width: 80mm;
            text-align: right;
        }

        /* Position 22 - Approver position */
        .field-approver-position {
            top: 341mm;
            right: 10mm;
            width: 80mm;
            text-align: right;
            font-weight: normal;
        }
        
        .field-value {
            flex: 1;
            min-height: 15px;
            padding: 1px 3px;
            font-size: 12pt;
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
            top: 180mm;
            right: 10mm;
            width: 80mm;
            text-align: right;
            font-size: 12pt;
        }
        
     
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 12pt;
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
        
        .pdf-button {
            position: fixed;
            top: 20px;
            right: 180px;
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        
        .position-controls {
            position: fixed;
            top: 80px;
            right: 20px;
            background: rgba(0, 0, 0, 0.1);
            padding: 10px;
            border-radius: 5px;
            z-index: 1000;
        }
        
        .position-btn {
            display: block;
            width: 100px;
            margin: 5px 0;
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .position-btn:hover {
            background: #218838;
        }
        
        .position-info {
            background: #f8f9fa;
            padding: 5px;
            border-radius: 3px;
            font-size: 11px;
            text-align: center;
            margin: 5px 0;
            color: #333;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
        
        .pdf-button:hover {
            background: #c82333;
        }
        
        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }
            
            * {
                box-sizing: border-box !important;
            }
            
            body {
                background: white !important;
                margin: 0;
                padding: 0;
                font-size: 12pt !important;
                line-height: 1.1 !important;
            }
            
            .certificate-container {
                width: 100% !important;
                height: auto !important;
                max-height: 277mm !important;
                margin: 0 !important;
                box-shadow: none !important;
                background: white !important;
                background-image: none !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                overflow: visible !important;
            }
            
            .certificate-content {
                background: transparent !important;
                padding: 3mm !important;
                font-size: 12pt !important;
                line-height: 1.0 !important;
            }
            
            .certificate-field {
                background: transparent !important;
                font-size: 12pt !important;
                margin-bottom: 1mm !important;
                line-height: 1.1 !important;
                color: black !important;
                font-weight: bold !important;
            }
            
            .field-value {
                background: transparent !important;
                padding: 1px 2px !important;
                border-radius: 0 !important;
                font-size: 12pt !important;
                line-height: 1.1 !important;
                color: black !important;
            }
            
            .certificate-no {
                font-size: 14pt !important;
                color: black !important;
                font-weight: bold !important;
            }
            
            /* Make field values more visible */
            .field-exporter,
            .field-importer,
            .field-packages,
            .field-distinguishing-marks,
            .field-place-origin,
            .field-conveyance-method,
            .field-entry-point,
            .field-quantity,
            .field-scientific-name,
            .field-additional-declaration,
            .field-treatment-date,
            .field-treatment-method,
            .field-chemical-used,
            .field-duration-temp,
            .field-concentration,
            .field-additional-info,
            .field-date-inspected,
            .field-date-issued,
            .field-place-issued,
            .field-approver-name,
            .field-approver-position {
                font-size: 12pt !important;
                line-height: 1.0 !important;
                margin-bottom: 1mm !important;
                color: black !important;
            }
            
            /* Set normal font-weight for most field values */
            .field-exporter .field-value,
            .field-importer .field-value,
            .field-packages .field-value,
            .field-distinguishing-marks .field-value,
            .field-place-origin .field-value,
            .field-conveyance-method .field-value,
            .field-entry-point .field-value,
            .field-quantity .field-value,
            .field-scientific-name .field-value,
            .field-additional-declaration .field-value,
            .field-treatment-date .field-value,
            .field-treatment-method .field-value,
            .field-chemical-used .field-value,
            .field-duration-temp .field-value,
            .field-concentration .field-value,
            .field-additional-info .field-value,
            .field-date-inspected .field-value,
            .field-date-issued .field-value,
            .field-approver-position .field-value {
                font-size: 12pt !important;
                color: black !important;
                font-weight: normal !important;
            }
            
            /* Keep bold font-weight for specific fields */
            .field-export-country .field-value,
            .field-import-country .field-value,
            .field-place-issued .field-value,
            .field-approver-name .field-value {
                font-size: 12pt !important;
                color: black !important;
                font-weight: bold !important;
            }
            
            .signature-section {
                margin-top: 5mm !important;
                font-size: 12pt !important;
            }
            
            .print-button {
                display: none !important;
            }
            
            .pdf-button {
                display: none !important;
            }
            
            .position-controls {
                display: none !important;
            }
        }
        
        .two-column {
            display: flex;
            gap: 20px;
        }
        
        .column {
            flex: 1;
        }
        
        /* Hide print-only section by default */
    </style>
</head>
<body>
    <button class="print-button" onclick="trackAndPrint()">🖨️ Print Certificate</button>
   <!-- <button class="pdf-button" onclick="generatePDF()">📄 Generate PDF</button> -->
    
    <!-- Position Control Buttons -->
    <div class="position-controls">
        <div class="position-info">
            Position Control
        </div>
        <button class="position-btn" onclick="moveContent('up')" title="Move content up">
            ⬆️ Move Up
        </button>
        <button class="position-btn" onclick="moveContent('down')" title="Move content down">
            ⬇️ Move Down
        </button>
        <div class="position-info" id="position-display">
            Top: 0px
        </div>
        <button class="position-btn" onclick="resetPosition()" title="Reset to original position">
            🔄 Reset
        </button>
    </div>

    
    <div class="certificate-container">
        <div class="certificate-content">
            <!-- Position 1: Certificate Number -->
            <div class="certificate-no">
                <?php echo "No. " . strtoupper(htmlspecialchars($cert_info['certificate_no'] ?? '')); ?>
            </div>
            
            <!-- Position 2: Export Country (FROM) -->
            <div class="certificate-field field-export-country">
                <span class="field-label">The National Plant Protection Organization of</span>
                <span class="field-value"><?php echo strtoupper(htmlspecialchars($place_origin)); ?></span>
            </div>
            
            <!-- Position 3: Import Country (TO) -->
            <div class="certificate-field field-import-country">
                <span class="field-label">The National Plant Protection Organization(s) of</span>
                <span class="field-value"><?php echo strtoupper(htmlspecialchars($import_country)); ?></span>
            </div>
            
            <!-- Position 4: Name and address of exporter -->
            <div class="certificate-field field-exporter">
                <span class="field-value textarea">
                    <?php echo strtoupper(htmlspecialchars($exporter_info['title'] ?? '')); ?><br>
                    <?php echo strtoupper(nl2br(htmlspecialchars($exporter_info['address'] ?? ''))); ?>
                </span>
            </div>
            
            <!-- Position 5: Name and address of importer -->
            <div class="certificate-field field-importer">
                <span class="field-value textarea">
                    <?php echo strtoupper(htmlspecialchars($importer_info['title'] ?? '')); ?><br>
                    <?php echo strtoupper(nl2br(htmlspecialchars($importer_info['address'] ?? ''))); ?>
                </span>
            </div>
            
            <!-- Position 6: Number and description of packages -->
            <div class="certificate-field field-packages">
                <span class="field-value">
                    <?php echo strtoupper(htmlspecialchars($app_info['commodity_description'] ?? '')); ?>
                </span>
            </div>
            
            <!-- Position 7: Distinguishing marks -->
            <div class="certificate-field field-distinguishing-marks">
                <span class="field-value">
                    <?php echo strtoupper(htmlspecialchars($app_info['marks_item'] ?? '')); ?>
                </span>
            </div>
            
            <!-- Position 8: Place of origin -->
            <div class="certificate-field field-place-origin">
                <span class="field-value"><?php echo strtoupper(htmlspecialchars($place_origin)); ?></span>
            </div>
            <!-- Position 8: Conveyance method -->
            <div class="certificate-field field-conveyance-method">
                <span class="field-value"><?php echo strtoupper(htmlspecialchars($conveyance_name)); ?></span>
            </div>

            <!-- Position 9: Entry point -->
            <div class="certificate-field field-entry-point">
                <span class="field-value"><?php echo strtoupper(htmlspecialchars($app_info['import_point'] ?? '')); ?></span>
            </div>
            
            <!-- Position 10: Product name,  Quantity and unit declared -->
            <div class="certificate-field field-quantity">
                <span class="field-value">
                    <?php echo $commodity_name ? strtoupper(htmlspecialchars($commodity_name)) . '<br>' : ''; ?>
                    <?php echo $commodity_quantity ? strtoupper(htmlspecialchars($commodity_quantity)) : ''; ?>
                </span>
            </div>
            
            <!-- Position 11: Scientific name -->
            <div class="certificate-field field-scientific-name">
                <span class="field-value">
                    <?php echo htmlspecialchars($app_info['name_scientific'] ?? ''); ?>
                </span>
            </div>
            
            <!-- Position 12: Additional declaration -->
            <?php if (!empty($cert_info['additional_declaration'])): ?>
            <div class="certificate-field field-additional-declaration">
                <span class="field-value textarea">
                    <?php echo nl2br(strtoupper(htmlspecialchars($cert_info['additional_declaration']))); ?>
                </span>
            </div>
            <?php endif; ?>
            
            <!-- Position 13: Treatment date -->
           
            <?php // if ($inspection_info): ?>
            <div class="certificate-field field-treatment-date">
                <span class="field-value">
                    <?php 
                    $inspection_info = InspectionInfo($appid, $con);
                    if ($inspection_info) {
                        // Debug: Show what's in the inspection_info
                        echo "<!-- Debug inspection_info: " . print_r($inspection_info, true) . " -->";
                        if (!empty($inspection_info['treatment_date']) && $inspection_info['treatment_date'] !== '0000-00-00') {
                            echo strtoupper(date('d-M-Y', strtotime($inspection_info['treatment_date'])));
                        } else {
                            echo "<!-- No treatment date found in inspection_info -->";
                        }
                    } else {
                        echo "<!-- No inspection info found for appid: $appid -->";
                    }
                    ?>
                </span>
            </div>
            
            <!-- Position 14: Treatment method -->
            <div class="certificate-field field-treatment-method">
                <span class="field-value">
                    <?php
                    $inspection_info = InspectionInfo($appid, $con);
                    if ($inspection_info && !empty($inspection_info['treatment_method'])) {
                        $treatment_method_info = TreatmentMethodInfo($inspection_info['treatment_method'], $con);
                        echo strtoupper(htmlspecialchars($treatment_method_info['title'] ?? ''));
                    }
                    ?>
                </span>
            </div>
            
            <!-- Position 16: Chemical used -->
            <div class="certificate-field field-chemical-used">
                <span class="field-value">
                    <?php 
                    $inspection_info = InspectionInfo($appid, $con);
                    echo strtoupper(htmlspecialchars($inspection_info['chemical_used'] ?? '')); 
                    ?>
                </span>
            </div>

            <!-- Position 15: Duration and temperature -->
            <div class="certificate-field field-duration-temp">
                <span class="field-value">
                    <?php 
                    $inspection_info = InspectionInfo($appid, $con);
                    echo htmlspecialchars($inspection_info['duration_temp'] ?? ''); 
                    ?>
                </span>
            </div>
            
            <!-- Position 15: Concentration -->
            <div class="certificate-field field-concentration">
                <span class="field-value">
                    <?php 
                    $inspection_info = InspectionInfo($appid, $con);
                    echo htmlspecialchars($inspection_info['concentration'] ?? ''); 
                    ?>
                </span>
            </div>

            <!-- Position 16: Additional information -->
            <div class="certificate-field field-additional-info">
                <span class="field-value">
                    <?php 
                    $inspection_info = InspectionInfo($appid, $con);
                    echo htmlspecialchars($inspection_info['additional_info'] ?? ''); 
                    ?>
                </span>
            </div>
            
            <!-- Position 17: Date inspected -->
            <div class="certificate-field field-date-inspected">
                <span class="field-value">
                    <?php 
                    $inspection_info = InspectionInfo($appid, $con);
                    if ($inspection_info) {
                        $inspection_date = $inspection_info['inspection_date'] ?? '';
                        echo "<!-- Debug inspection_date: " . $inspection_date . " -->";
                        if ($inspection_date && $inspection_date !== '0000-00-00') {
                            echo strtoupper(date('d-M-Y', strtotime($inspection_date)));
                        } else {
                            echo "<!-- No inspection date found -->";
                        }
                    } else {
                        echo "<!-- No inspection info found -->";
                    }
                    ?>
                </span>
            </div>

     
            <!-- Position 18: Date issued -->
            <div class="certificate-field field-date-issued">
                <span class="field-value"><?php echo strtoupper(htmlspecialchars($date_issued)); ?></span>
            </div>
            
            <!-- Position 19: Place of issue: field-place-issued -->
            <div class="certificate-field field-place-issued">
                <span class="field-value"><?php echo strtoupper(htmlspecialchars($cert_info['place_issued'] ?? '')); ?></span>
            </div>

            <!-- Position 20: Approver name -->
            <div class="certificate-field field-approver-name">
                <span class="field-value">
                    <?php 
                    if ($approver_info) {
                        echo strtoupper(htmlspecialchars($approver_info['name'] . ' ' . $approver_info['surname']));
                    } else {
                        echo '';
                    }
                    ?>
                </span>
            </div>
            <!-- Position 21: Approver position -->
            <div class="certificate-field field-approver-position">
                <span class="field-value" style="text-align: center;">
                    <?php 
                    if ($approver_info) {
                        echo strtoupper(htmlspecialchars($approver_info['position'] ?? ''));
                    } else {
                        echo '';
                    }
                    ?>
                </span>
        </div>
    </div>
    
    <!-- Print-only compact layout -->
    <div class="print-only" style="display: none;">
        <div style="text-align: center; margin-bottom: 5mm;">
            <h3>LAO PEOPLE'S DEMOCRATIC REPUBLIC</h3>
            <h4>MINISTRY OF AGRICULTURE AND FORESTRY</h4>
            <h4>DEPARTMENT OF AGRICULTURE</h4>
            <h3>PHYTOSANITARY CERTIFICATE</h3>
        </div>
        
        <div style="text-align: right; font-weight: bold; font-size: 12pt; margin-bottom: 3mm;">
            <?php echo strtoupper(htmlspecialchars($cert_info['certificate_no'] ?? '')); ?>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; font-size: 8pt;">
            <tr>
                <td style="border: 1px solid #000; padding: 2mm; width: 50%;">
                    <strong>FROM:</strong> <?php echo strtoupper(htmlspecialchars($place_origin)); ?>
                </td>
                <td style="border: 1px solid #000; padding: 2mm; width: 50%;">
                    <strong>TO:</strong> <?php echo strtoupper(htmlspecialchars($import_country)); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid #000; padding: 2mm; text-align: center; background: #f0f0f0;">
                    <strong>I. DESCRIPTION OF CONSIGNMENT</strong>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 2mm;">
                    <strong>Name and address of exporter:</strong><br>
                    <?php echo strtoupper(htmlspecialchars($exporter_info['title'] ?? '')); ?><br>
                    <?php echo strtoupper(nl2br(htmlspecialchars($exporter_info['address'] ?? ''))); ?>
                </td>
                <td style="border: 1px solid #000; padding: 2mm;">
                    <strong>Declared name and address of consignee:</strong><br>
                    <?php echo strtoupper(htmlspecialchars($importer_info['title'] ?? '')); ?><br>
                    <?php echo strtoupper(nl2br(htmlspecialchars($importer_info['address'] ?? ''))); ?>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 2mm;">
                    <strong>Number and description of packages:</strong><br>
                    <?php echo strtoupper(htmlspecialchars($app_info['commodity_description'] ?? '')); ?>
                </td>
                <td style="border: 1px solid #000; padding: 2mm;">
                    <strong>Distinguishing marks:</strong><br>
                    <?php echo strtoupper(htmlspecialchars($app_info['marks_item'] ?? '')); ?>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 2mm;">
                    <strong>Place of origin:</strong><br>
                    <?php echo strtoupper(htmlspecialchars($place_origin)); ?>
                </td>
                <td style="border: 1px solid #000; padding: 2mm;">
                    <strong>Declared means of conveyance:</strong><br>
                    <?php echo strtoupper(htmlspecialchars($conveyance_name)); ?>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 2mm;">
                    <strong>Name of product and quantity declared:</strong><br>
                    <?php echo $commodity_name ? strtoupper(htmlspecialchars($commodity_name)) : ''; ?>
                    <?php echo $commodity_quantity ? '<br>' . strtoupper(htmlspecialchars($commodity_quantity)) : ''; ?>
                </td>
                <td style="border: 1px solid #000; padding: 2mm;">
                    <strong>Botanical name of plants:</strong><br>
                    <em><?php echo htmlspecialchars($app_info['name_scientific'] ?? ''); ?></em>
                </td>
            </tr>
        </table>
        
        <div style="margin: 3mm 0; font-size: 7pt; text-align: justify;">
            <strong>This is to certify that the plants and plant products or other regulated articles described herein have been inspected and/or tested according to appropriate official procedures and are considered to be free from the quarantine pests specified by the importing contracting party and to conform with the current phytosanitary requirements of the importing contracting party, including those for regulated non-quarantine pests.</strong>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; font-size: 7pt; margin-top: 3mm;">
            <tr>
                <td style="border: 1px solid #000; padding: 2mm; width: 33%;">
                    <strong>Date of inspection:</strong><br>
                    <?php 
                    $inspection_info = InspectionInfo($appid, $con);
                    if ($inspection_info) {
                        $inspection_date = $inspection_info['inspection_date'] ?? '';
                        if ($inspection_date && $inspection_date !== '0000-00-00') {
                            echo date('d-M-Y', strtotime($inspection_date));
                        }
                    }
                    ?>
                </td>
                <td style="border: 1px solid #000; padding: 2mm; width: 33%;">
                    <strong>Date of issue:</strong><br>
                    <?php echo htmlspecialchars($date_issued); ?>
                </td>
                <td style="border: 1px solid #000; padding: 2mm; width: 34%;">
                    <strong>Place of issue:</strong><br>
                    <?php echo htmlspecialchars($cert_info['place_issued'] ?? ''); ?>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid #000; padding: 5mm; text-align: center;">
                    <div style="border-bottom: 1px solid #000; height: 15mm; margin-bottom: 2mm;"></div>
                    <strong>Inspector / Authorized Officer</strong><br>
                    <?php 
                    if ($approver_info) {
                        echo htmlspecialchars($approver_info['name'] . ' ' . $approver_info['surname']);
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>
    
    <script>
        // Position control variables
        let currentTopOffset = 0;
        const moveIncrement = 2; // Move in 2px increments
        
        // Function to move certificate content
        function moveContent(direction) {
            const certificateContent = document.querySelector('.certificate-content');
            const positionDisplay = document.getElementById('position-display');
            
            if (direction === 'up') {
                currentTopOffset -= moveIncrement;
            } else if (direction === 'down') {
                currentTopOffset += moveIncrement;
            }
            
            // Apply the new position
            certificateContent.style.transform = `translateY(${currentTopOffset}px)`;
            
            // Update position display
            positionDisplay.textContent = `Top: ${currentTopOffset}px`;
        }
        
        // Function to reset position
        function resetPosition() {
            const certificateContent = document.querySelector('.certificate-content');
            const positionDisplay = document.getElementById('position-display');
            
            currentTopOffset = 0;
            certificateContent.style.transform = 'translateY(0px)';
            positionDisplay.textContent = 'Top: 0px';
        }
        
        // Track and log print action
        function trackAndPrint() {
            const appid = <?php echo $appid; ?>;
            const certificate_id = <?php echo $cert_info['id'] ?? 0; ?>;
            const userid = <?php echo $userid; ?>;
           // alert('Printing certificate. This action will be logged.' + appid + ', ' + certificate_id + ', ' + userid);
            
            // Log print action to database
            fetch('certificate_view.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'log_certificate_print',
                    appid: appid,
                    certificate_id: certificate_id,
                    userid: userid
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Print action logged successfully');
                } else {
                    console.error('Failed to log print action:', data.error);
                }
            })
            .catch(error => {
                console.error('Error logging print action:', error);
            })
            .finally(() => {
                // Open print dialog regardless of logging result
                window.print();
                
                // Redirect to main.php after print dialog is closed
                window.addEventListener('afterprint', function() {
                    window.location.href = 'main.php?uid=<?php echo $userid; ?>';
                }, { once: true });
            });
        }
        
        // Keyboard controls for fine positioning
        document.addEventListener('keydown', function(e) {
            // Only work when not in input fields
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                return;
            }
            
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                trackAndPrint();
            } else if (e.key === 'ArrowUp' && e.shiftKey) {
                e.preventDefault();
                moveContent('up');
            } else if (e.key === 'ArrowDown' && e.shiftKey) {
                e.preventDefault();
                moveContent('down');
            } else if (e.key === 'r' && e.ctrlKey && e.shiftKey) {
                e.preventDefault();
                resetPosition();
            }
        });
        
        // Auto-focus for printing
        window.onload = function() {
            // Optional: Auto-print when page loads (uncomment if desired)
            // window.print();
        };
        
        // PDF Generation function
        function saveAsPDF() {
            // Add print-specific styling
            const style = document.createElement('style');
            style.innerHTML = `
                @media print {
                    @page {
                        size: A4;
                        margin: 10mm;
                    }
                    
                    body {
                        background: white !important;
                        -webkit-print-color-adjust: exact;
                        color-adjust: exact;
                    }
                    
                    .certificate-container {
                        width: 190mm !important;
                        height: 277mm !important;
                        margin: 0 !important;
                        box-shadow: none !important;
                        background-size: cover !important;
                        background-repeat: no-repeat !important;
                        page-break-inside: avoid !important;
                    }
                    
                    .print-button, .pdf-button {
                        display: none !important;
                    }
                    
                    .certificate-field {
                        font-size: 10pt !important;
                    }
                    
                    .field-value {
                        background: rgba(255, 255, 255, 0.8) !important;
                        padding: 2px 4px !important;
                        border-radius: 2px !important;
                    }
                }
            `;
            document.head.appendChild(style);
            
            // Trigger print dialog (user can choose "Save as PDF")
            window.print();
            
            // Remove the style after printing
            setTimeout(() => {
                document.head.removeChild(style);
            }, 1000);
        }
        
        function generatePDF() {
            const appid = <?php echo $appid; ?>;
            const generateBtn = document.querySelector('.pdf-button');
            
            // Disable button and show loading
            generateBtn.disabled = true;
            generateBtn.innerHTML = '⏳ Generating PDF...';
            
            // Make AJAX request to generate PDF
            fetch(`certificate_pdf.php?action=generate&appid=${appid}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message and offer download
                        generateBtn.innerHTML = '✅ PDF Generated';
                        
                        // Create download link
                        const downloadLink = document.createElement('a');
                        downloadLink.href = data.download_url;
                        downloadLink.download = data.filename;
                        downloadLink.style.display = 'none';
                        document.body.appendChild(downloadLink);
                        downloadLink.click();
                        document.body.removeChild(downloadLink);
                        
                        // Reset button after 3 seconds
                        setTimeout(() => {
                            generateBtn.disabled = false;
                            generateBtn.innerHTML = '📄 Generate PDF';
                        }, 3000);
                        
                    } else {
                        // Show error message
                        generateBtn.innerHTML = '❌ Generation Failed';
                        alert('Error: ' + data.error);
                        
                        // Reset button after 3 seconds
                        setTimeout(() => {
                            generateBtn.disabled = false;
                            generateBtn.innerHTML = '📄 Generate PDF';
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    generateBtn.innerHTML = '❌ Generation Failed';
                    alert('Error generating PDF. Please try again.');
                    
                    // Reset button after 3 seconds
                    setTimeout(() => {
                        generateBtn.disabled = false;
                        generateBtn.innerHTML = '📄 Generate PDF';
                    }, 3000);
                });
        }
    </script>
</body>
</html>