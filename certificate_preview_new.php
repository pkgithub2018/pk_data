<?php
session_start();
require("php-bin/connection.php");
require("php-bin/supports.php");
require("php-bin/qr_generator.php");

// Get application ID
$appid = isset($_GET['appid']) ? (int)$_GET['appid'] : 0;

if ($appid <= 0) {
    die("Invalid application ID");
}

// Get application info
$app_info = ApplicationInfo($appid, $con);
if (!$app_info) {
    die("Application not found");
}

// Get certificate info
$cert_info = CertificateInfo($appid, $con);
if (!$cert_info) {
    die("Certificate not found");
}

// Get certificate ID
$certificate_id = $cert_info['id'] ?? 0;

// Extract application data
$application_no = $app_info['application_no'] ?? '';
$certificate_no = $cert_info['certificate_no'] ?? '';
$certificate_type = $app_info['certificate_type'] ?? 'export';

// Generate or retrieve QR code
$qr_result = ensureCertificateQR($certificate_id, $appid, $certificate_no, $con);
$qr_code_svg = $qr_result['qr_svg'] ?? '';
$qr_code_data = $qr_result['qr_data'] ?? '';
$qr_exists = $qr_result['exists'] ?? false;

// Country information
$import_country_id = $app_info['country_import'] ?? '';
$import_country = $import_country_id ? CountryInfo($import_country_id, $con)['title'] ?? '' : '';
$import_point = $app_info['import_point'] ?? '';

// Exporter information
$exporterid = $app_info['company_id'] ?? '';
$exporter_info = EntityExportInfo($exporterid, $con);
$exporter_name = $exporter_info['title'] ?? '';
$exporter_address = $exporter_info['address'] ?? '';

// Importer information
$app_importerid = $app_info['importerid'] ?? '';
$importer_info = EntityImportInfo($app_importerid, $con);
$importer_name = $importer_info['title'] ?? '';
$importer_address = $importer_info['address'] ?? '';

// Commodity/Product information
$product_id = $app_info['commodity_id'] ?? '';
$product_info = ProductInfo($product_id, $con);
$product_name = $product_info['name'] ?? '';
$scientific_name = $app_info['name_scientific'] ?? '';

// Quantities and descriptions
$commodity_description = $app_info['commodity_description'] ?? '';
$quantity_net = $app_info['quantity_net'] ?? '';
$quantity_gross = $app_info['quantity_gross'] ?? '';
$unit_id = $app_info['unit_id'] ?? '';
$unit_name = $unit_id ? UnitSymbol($unit_id, $con) ?? '' : '';

// Marks and origin
$distinguishing_marks = $app_info['marks_item'] ?? '';
$country_origin_id = $app_info['place_origin'] ?? '';
$country_origin = $country_origin_id ? CountryInfo($country_origin_id, $con)['title'] ?? '' : '';

// Conveyance
$conveyance_id = $app_info['conveyance_id'] ?? '';
$conveyance_info = ConveyanceType($conveyance_id, $con);
$conveyance_name = $conveyance_info['title'] ?? '';
$conveyance_sign = $app_info['conveyance_sign'] ?? '';

// Export point
$export_point_id = $app_info['export_point'] ?? '';
$export_point = $export_point_id ? Locationname($export_point_id, $con) : '';

// Certificate details
$date_issued = $cert_info['date_issued'] ?? date('Y-m-d');
$place_issued = $cert_info['place_issued'] ?? '';

// Inspection information
$inspection_info = InspectionInfo($appid, $con);
$treatment_date = $inspection_info['date_treatment'] ?? '';
$treatment_method_id = $inspection_info['treatment_id'] ?? '';
$treatment_info = TreatmentMethodInfo($treatment_method_id, $con);
$treatment_name = $treatment_info['title'] ?? '';
$treatment_chemical = $inspection_info['chemical_used'] ?? '';
$treatment_duration = $inspection_info['duration_temp'] ?? '';
$treatment_concentration = $inspection_info['concentration'] ?? '';
$additional_declaration = $cert_info['additional_declaration'] ?? '';

// Approver information
$approver_id = $cert_info['approved_by'] ?? '';
$approver_info = ApproverInfo($approver_id, $con);
$authorized_name = $approver_info['name'] ?? '';
$authorized_surname = $approver_info['surname'] ?? '';
$authorized_officer = trim($authorized_name . ' ' . $authorized_surname);
$authorized_officer = strtoupper($authorized_officer);
$approver_position = $cert_info['position_approved'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phytosanitary Certificate - <?php echo htmlspecialchars($certificate_no); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .certificate-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 15mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            /* border-bottom: 2px solid #000; */
            padding-bottom: 10px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px auto;
            display: block;
            text-align: center;
        }
        
        .logo img {
            max-width: 100%;
            max-height: 100%;
            display: block;
            margin: 0 auto;
        }
        
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .header h2 {
            font-size: 12pt;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 9pt;
            margin: 2px 0;
        }
        
        .cert-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 15px 0;
            border-bottom: 2px solid #000;
            text-decoration: none;
        }
        
        .cert-number {
            text-align: right;
            font-size: 10pt;
            margin-bottom: 10px;
        }
        
        .section {
            margin-bottom: 10px;
        }
        
        .two-column {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            padding: 5px;
            vertical-align: top;
        }
        
        .field-label {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 3px;
        }
        
        .field-value {
            font-size: 10pt;
            padding: 3px;
            border-bottom: 1px solid #333;
            min-height: 20px;
        }
        
        .table-section {
            border: 1px solid #000;
            margin: 10px 0;
        }
        
        .table-header {
            background: #e0e0e0;
            padding: 5px;
            font-weight: bold;
            font-size: 10pt;
            border-bottom: 1px solid #000;
        }
        
        .table-content {
            padding: 8px;
            min-height: 40px;
        }
        
        .treatment-section {
            border: 1px solid #000;
            padding: 8px;
            margin: 10px 0;
        }
        
        .treatment-title {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 5px;
            text-decoration: underline;
        }
        
        .treatment-details {
            display: table;
            width: 100%;
        }
        
        .treatment-row {
            display: table-row;
        }
        
        .treatment-label {
            display: table-cell;
            width: 30%;
            padding: 3px;
            font-size: 9pt;
        }
        
        .treatment-value {
            display: table-cell;
            padding: 3px;
            font-size: 10pt;
        }
        
        .signature-section {
            margin-top: 30px;
            padding: 10px;
            border: 1px solid #000;
        }
        
        .signature-row {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        
        .signature-col {
            display: table-cell;
            width: 50%;
            padding: 5px;
        }
        
        .signature-label {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .signature-value {
            font-size: 10pt;
            min-height: 50px;
            border-bottom: 1px solid #333;
        }
        
        .stamp-area {
            text-align: center;
            padding: 20px;
            margin-top: 10px;
            border: 2px dashed #999;
            font-size: 9pt;
            color: #666;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        
        .print-button {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn-print {
            background: #4CAF50;
            color: white;
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-print:hover {
            background: #45a049;
        }
        
        /* QR Code Box Styles */
        .qr-box {
            position: absolute;
            left: 32mm;
            top: 55mm;
            width: 45mm;
            height: 60mm;
           /* border: 3px solid #4A148C; */
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 5mm;
        }
        
        .qr-box::before,
        .qr-box::after {
            content: '';
            position: absolute;
            background: #4A148C;
        }
        
        /* Diagonal cross lines */
        .qr-box::before {
            width: 100%;
            height: 3px;
            transform: rotate(45deg);
            transform-origin: center;
        }
        
        .qr-box::after {
            width: 100%;
            height: 3px;
            transform: rotate(-45deg);
            transform-origin: center;
        }
        
        .qr-code-display {
            width: 30mm;
            height: 30mm;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            z-index: 1;
            position: relative;
        }
        
        .qr-code-display svg {
            width: 100%;
            height: 100%;
        }
        
        .qr-label {
            margin-top: 5px;
            font-size: 8pt;
            text-align: center;
            font-weight: bold;
            z-index: 1;
            position: relative;
        }
        
        .certificate-main {
            position: relative;
        }
        
        @media print {
            @page {
                size: A4;
                margin: 5mm;
            }
            
            body {
                background: white;
                padding: 0;
                margin: 0;
                font-size: 9pt;
            }
            
            .certificate-container {
                box-shadow: none;
                padding: 3mm 5mm 5mm 5mm;
                max-width: 100%;
                margin: 0;
            }
            
            .print-button {
                display: none;
            }
            
            .header {
                margin-bottom: 2px;
                padding-bottom: 0;
                margin-top: 0;
            }
            
            .header h1 {
                font-size: 11pt;
                margin-bottom: 1px;
            }
            
            .header h2 {
                font-size: 9pt;
                margin-bottom: 1px;
            }
            
            .header p {
                font-size: 7pt;
                margin: 0;
            }
            
            .logo {
                width: 50px;
                height: 50px;
                margin: -15px auto 0 auto;
                display: block;
                text-align: center;
            }
            
            .logo img {
                max-width: 100%;
                max-height: 100%;
                display: block;
                margin: 0 auto;
            }
            
            .cert-title {
                font-size: 12pt;
                margin: 5px 0;
                padding: 3px 0;
            }
            
            .cert-number {
                font-size: 8pt;
                margin-bottom: 4px;
            }
            
            .section {
                margin-bottom: 4px;
            }
            
            .two-column {
                margin-bottom: 4px;
            }
            
            .column {
                padding: 2px;
            }
            
            .field-label {
                font-size: 7pt;
                margin-bottom: 1px;
            }
            
            .field-value {
                font-size: 8pt;
                padding: 1px 2px;
                min-height: 14px;
            }
            
            .table-section {
                margin: 4px 0;
            }
            
            .table-header {
                padding: 2px;
                font-size: 8pt;
            }
            
            .table-content {
                padding: 3px;
                min-height: 20px;
            }
            
            .treatment-section {
                padding: 4px;
                margin: 4px 0;
            }
            
            .treatment-title {
                font-size: 8pt;
                margin-bottom: 2px;
            }
            
            .treatment-label {
                font-size: 7pt;
                padding: 1px;
            }
            
            .treatment-value {
                font-size: 8pt;
                padding: 1px;
            }
            
            .signature-section {
                margin-top: 8px;
                padding: 4px;
            }
            
            .signature-row {
                margin-top: 4px;
            }
            
            .signature-col {
                padding: 2px;
            }
            
            .signature-label {
                font-size: 7pt;
                margin-bottom: 1px;
            }
            
            .signature-value {
                font-size: 8pt;
                min-height: 20px;
                padding-top: 8px;
            }
            
            .stamp-area {
                padding: 8px;
                margin-top: 4px;
                font-size: 7pt;
            }
            
            .footer {
                margin-top: 6px;
                font-size: 6pt;
                padding-top: 3px;
            }
            
            .qr-box {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
                position: absolute;
                left: 5mm;
                top: 10mm;
                width: 28mm;
                height: 40mm;
                padding: 2mm;
                border-width: 2px;
            }
            
            .qr-box::before,
            .qr-box::after {
                height: 2px;
            }
            
            .qr-code-display {
                width: 24mm;
                height: 24mm;
            }
            
            .qr-label {
                font-size: 6pt;
                margin-top: 2px;
            }
            
            .certificate-main {
                position: relative;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button class="btn-print" onclick="window.print()">🖨️ Print Certificate</button>
    </div>
    
    <div class="certificate-container">
        <!-- QR Code Box (Left side with purple border and cross) -->
        <div class="qr-box">
            <div class="qr-code-display">
                <?php if (!empty($qr_code_svg)): ?>
                    <?php echo $qr_code_svg; ?>
                <?php else: ?>
                    <div style="text-align: center; font-size: 8pt; color: #999;">
                        QR Code<br>Not Available
                    </div>
                <?php endif; ?>
            </div>
            <div class="qr-label">Scan to Verify</div>
        </div>
        
        <!-- Main Certificate Content -->
        <div class="certificate-main">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <img src="assets/img/national_logo.jpg" alt="National Logo">
            </div>
            <p>ສາທາລະນະລັດ ປະຊາທິປະໄຕ ປະຊາຊົນລາວ</p>
            <h1>LAO PEOPLE'S DEMOCRATIC REPUBLIC</h1>
            <p>ສັນຕິພາບ ເອກະລາດ ປະຊາທິປະໄຕ ເອກະພາບ ວັດທະນາຖາວອນ</p>
            <p style="margin-bottom: 10px;">PEACE INDEPENDENCE DEMOCRACY UNITY PROSPERITY</p>
            <h2>MINISTRY OF AGRICULTURE AND FORESTRY</h2>
            <h2>DEPARTMENT OF AGRICULTURE</h2>
        </div>
        
        <!-- Certificate Title -->
        <div class="cert-title">PHYTOSANITARY CERTIFICATE</div>
        
        <!-- Certificate Number -->
        <div class="cert-number">
            <strong>No. <?php echo htmlspecialchars($certificate_no); ?></strong>
        </div>
        
        <!-- From/To Section -->
        <div class="two-column">
            <div class="column">
                <div class="field-label">FROM:</div>
                <div class="field-value">The National Plant Protection Organization</div>
                <div class="field-value">LAO PEOPLE'S DEMOCRATIC REPUBLIC</div>
            </div>
            <div class="column">
                <div class="field-label">TO:</div>
                <div class="field-value">The National Plant Protection Organization of</div>
                <div class="field-value"><?php echo htmlspecialchars($import_country); ?></div>
            </div>
        </div>
        
        <!-- Names Section -->
        <div class="section">
            <div class="field-label">Name and address of exporter:</div>
            <div class="field-value"><?php echo htmlspecialchars($exporter_name); ?></div>
            <div class="field-value"><?php echo htmlspecialchars($exporter_address); ?></div>
        </div>
        
        <div class="section">
            <div class="field-label">Declared name and address of consignee:</div>
            <div class="field-value"><?php echo htmlspecialchars($importer_name); ?></div>
            <div class="field-value"><?php echo htmlspecialchars($importer_address); ?></div>
        </div>
        
        <!-- Description of Consignment -->
        <div class="table-section">
            <div class="table-header">DESCRIPTION OF CONSIGNMENT</div>
            <div class="table-content">
                <div class="field-label">Name of product and quantity declared:</div>
                <div class="field-value">
                    <?php echo htmlspecialchars($product_name); ?> - 
                    <?php echo htmlspecialchars($quantity_gross . ' ' . $unit_name); ?>
                </div>
            </div>
        </div>
        
        <!-- Number and Description of Packages -->
        <div class="section">
            <div class="field-label">Number and description of packages:</div>
            <div class="field-value"><?php echo htmlspecialchars($commodity_description); ?></div>
        </div>
        
        <!-- Distinguishing Marks -->
        <div class="section">
            <div class="field-label">Distinguishing marks:</div>
            <div class="field-value"><?php echo htmlspecialchars($distinguishing_marks); ?></div>
        </div>
        
        <!-- Place of Origin and Entry Point -->
        <div class="two-column">
            <div class="column">
                <div class="field-label">Place of origin:</div>
                <div class="field-value"><?php echo htmlspecialchars($country_origin); ?></div>
            </div>
            <div class="column">
                <div class="field-label">Declared point of entry:</div>
                <div class="field-value"><?php echo htmlspecialchars($import_point); ?></div>
            </div>
        </div>
        
        <!-- Means of Conveyance -->
        <div class="section">
            <div class="field-label">Means of conveyance:</div>
            <div class="field-value">
                <?php echo htmlspecialchars($conveyance_name); ?>
                <?php if ($conveyance_sign): ?>
                    - <?php echo htmlspecialchars($conveyance_sign); ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Botanical Name -->
        <div class="section">
            <div class="field-label">Botanical name of plants:</div>
            <div class="field-value"><?php echo htmlspecialchars($scientific_name); ?></div>
        </div>
        
        <!-- Treatment Section -->
        <?php if ($treatment_name): ?>
        <div class="treatment-section">
            <div class="treatment-title">III. DISINFESTATION AND/OR DISINFECTION TREATMENT</div>
            <div class="treatment-details">
                <div class="treatment-row">
                    <div class="treatment-label">Treatment Date:</div>
                    <div class="treatment-value">
                        <?php echo $treatment_date ? date('d-M-Y', strtotime($treatment_date)) : ''; ?>
                    </div>
                    <div class="treatment-label">Treatment:</div>
                    <div class="treatment-value"><?php echo htmlspecialchars($treatment_name); ?></div>
                </div>
                <div class="treatment-row">
                    <div class="treatment-label">Chemical (Active ingredient):</div>
                    <div class="treatment-value"><?php echo htmlspecialchars($treatment_chemical); ?></div>
                </div>
                <div class="treatment-row">
                    <div class="treatment-label">Duration & temperature:</div>
                    <div class="treatment-value"><?php echo htmlspecialchars($treatment_duration); ?></div>
                    <div class="treatment-label">Concentration:</div>
                    <div class="treatment-value"><?php echo htmlspecialchars($treatment_concentration); ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Additional Declaration -->
        <?php if ($additional_declaration): ?>
        <div class="section">
            <div class="field-label">Additional Declaration:</div>
            <div class="field-value"><?php echo htmlspecialchars($additional_declaration); ?></div>
        </div>
        <?php endif; ?>
        
        <!-- Declaration Text -->
        <div class="section" style="margin-top: 15px; font-size: 9pt; text-align: justify;">
            This is to certify that the plants, plant products or other regulated articles described herein have been inspected and/or tested according to appropriate official
            procedures and are considered to be free from the quarantine pests specified by the importing contracting party and to conform with the current phytosanitary
            requirements of the importing contracting party, including those for regulated non-quarantine pests.
        </div>
        
        <div style="text-align: center; margin: 10px 0; font-weight: bold;">
            NO MORE DECLARATION_TEST
        </div>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-row">
                <div class="signature-col">
                    <div class="signature-label">Date Inspected:</div>
                    <div class="signature-value">
                        <?php echo $treatment_date ? date('d-M-Y', strtotime($treatment_date)) : ''; ?>
                    </div>
                </div>
                <div class="signature-col">
                    <div class="signature-label">Date Issued:</div>
                    <div class="signature-value">
                        <?php echo date('d-M-Y', strtotime($date_issued)); ?>
                    </div>
                </div>
            </div>
            
            <div class="signature-row">
                <div class="signature-col">
                    <div class="signature-label">Place of Issue:</div>
                    <div class="signature-value"><?php echo htmlspecialchars($place_issued); ?></div>
                </div>
            </div>
            
            <div class="stamp-area">
                <strong>STAMP OF AUTHORIZED ORGANIZATION</strong><br>
                (Official Stamp/Seal Area)
            </div>
            
            <div class="signature-row">
                <div class="signature-col" style="width: 100%;">
                    <div class="signature-label">Name and Signature of Authorized Officer:</div>
                    <div class="signature-value" style="text-align: center; padding-top: 20px;">
                        <strong><?php echo htmlspecialchars($authorized_officer); ?></strong><br>
                        <span style="font-size: 9pt;"><?php echo htmlspecialchars($approver_position); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            Department of Agriculture, P.O Box 811, Nongbone, Lao PDR. Tel: (856) 21 416350, Fax: (856) 21 415349<br>
            Email: ppd@doa.gov.la
        </div>
        
        </div><!-- End certificate-main -->
    </div><!-- End certificate-container -->
</body>
</html>
