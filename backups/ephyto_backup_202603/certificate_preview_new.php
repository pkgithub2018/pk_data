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
$inspection_date = $inspection_info['date_inspection'] ?? '';
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
    <link rel="stylesheet" href="stylecss/certificate_style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @media print {
            .certificate-main {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button class="btn-print" onclick="window.print()">🖨️ Print Certificate</button>
        <button class="btn-save-pdf" onclick="saveAsPDF()">💾 Save As PDF</button>
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
        <div class="certificate-main" style="background-image: url('images/certificate_bg.png'); background-size: cover; background-position: center 150px; background-repeat: no-repeat;">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <img src="assets/img/national_logo.jpg" alt="National Logo">
            </div>
            <p class="title-text-lao" style="font-size: 11pt;">ສາທາລະນະລັດ ປະຊາທິປະໄຕ ປະຊາຊົນລາວ</p>
            <h1>LAO PEOPLE'S DEMOCRATIC REPUBLIC</h1>
            <p class="title-text-lao" style="font-size: 11pt;">ສັນຕິພາບ ເອກະລາດ ປະຊາທິປະໄຕ ເອກະພາບ ວັດທະນາຖາວອນ</p>
            <p style="margin-bottom: 10px;">PEACE INDEPENDENCE DEMOCRACY UNITY PROSPERITY</p>
            <h2>MINISTRY OF AGRICULTURE AND FORESTRY<br>DEPARTMENT OF AGRICULTURE</h2>
        </div>
        
        <!-- Certificate Title -->
        <div class="title-text-lao" style="font-size: 16pt;">ໃບຢັ້ງຢືນ ສຸຂານາໄມ</div>
        <div class="cert-title">
            <span class="title-text">PHYTOSANITARY CERTIFICATE</span>
            <span class="cert-no">No. <?php echo htmlspecialchars($certificate_no); ?></span>
        </div>
        <!-- Main Content -->
        <div style="border: 1px solid #000;">
        <!-- From/To Section -->
        <div style="border-bottom: 1px solid #000;">
            <div class="two-column">
                <div class="column" style="width: 50%;">
                    <span class="field-label-lao" style="padding:2px">ຈາກ:</span><span class="field-value" style="display: inline-block; width:85%; padding:2px; margin-left: 8px; text-align: center;">The National Plant Protection Organization</span>
                </div>
                <div class="column" style="width: 50%; border-left: 1px solid #000;">
                    <span class="field-label-lao" style="padding:2px">ເຖີງ:</span><span class="field-value" style="display: inline-block; width:85%; padding:2px; text-align: center;">The National Plant Protection Organization of</span>
                </div>
            </div>
            <div class="two-column">
                <div class="column" style="width: 50%;">
                    <span class="field-label" style="padding:2px">&nbsp;FROM:</span><span class="field-value" style="display: inline-block; width:85%; padding:2px; text-align: center;"><b>LAO PEOPLE'S DEMOCRATIC REPUBLIC</b></span>
                </div>
                <div class="column" style="width: 50%; border-left: 1px solid #000;">
                    <span class="field-label" style="padding:2px">TO:</span><span class="field-value" style="display: inline-block; width:85%; padding:2px; text-align: center;"><b><?php echo strtoupper(htmlspecialchars($import_country)); ?></b></span>
                </div>
            </div>
        </div>
        
        <!-- Description Header -->
        <div style="border-bottom: 1px solid #000;">
            <div class="section" style="margin: 0; text-align: center;">
                <div class="field-label" style="font-weight: bold;"><span class="field-label-lao">I. ປະເພດສີນຄ້າ</span><span style="font-weight: normal;"> / DESCRIPTION OF CONSIGNMENT</span></div>
            </div>
        </div>
        

        <!-- Names Section -->
        <div class="two-column">
            <div class="column" style="width: 50%; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: center;">
                <div class="field-label" style="font-family: phetsarath OT;">ຊື່ ແລະ ທີ່ຢູ່ ຂອງ ຜູ້ສົ່ງອອກ</div>
                <div class="field-label">Name and address of exporter</div>
            </div>
            <div class="column" style="width: 50%; border-bottom: 1px solid #000; text-align: center;">
                <div class="field-label" style="font-family: phetsarath OT;">ຊື່ ແລະ ທີ່ຢູ່ ຂອງ ຜູ້ຮັບ</div>
                <div class="field-label">Declared name and address of consignee</div>
            </div>
        </div>
        
        <div class="two-column">
            <div class="column" style="width: 50%; border-bottom: 1px solid #000;">
                <div class="field-value" style="margin-left: 10px;"><?php echo strtoupper(htmlspecialchars($exporter_name)); ?><br><?php echo strtoupper(htmlspecialchars($exporter_address)); ?></div>
            </div>
            <div class="column" style="width: 50%; border-left: 1px solid #000; border-bottom: 1px solid #000;">
                <div class="field-value" style="margin-left: 10px;"><?php echo strtoupper(htmlspecialchars($importer_name)); ?><br><?php echo strtoupper(htmlspecialchars($importer_address)); ?></div>
            </div>
        </div>
        
        <!-- Number and Description of Packages / Distinguishing Marks -->
        <div class="two-column">
            <div class="column" style="width: 50%; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: center;">
                <div class="field-label" style="font-family: phetsarath OT;">ຈໍານວນ ແລະ ລັກສະນະການຫຸ້ມຫໍ່</div>
                <div class="field-label">Number and description of packages</div>
            </div>
            <div class="column" style="width: 50%; border-bottom: 1px solid #000; text-align: center;">
                <div class="field-label" style="font-family: phetsarath OT;">ເຄື່ອງໝາຍທີ່ເດັ່ນ</div>
                <div class="field-label">Distinguishing marks</div>
            </div>
        </div>
        
        <div class="two-column">
            <div class="column" style="width: 50%; border-bottom: 1px solid #000;">
                <div class="field-value" style="margin-left: 10px;"><?php echo strtoupper(htmlspecialchars($commodity_description)); ?></div>
            </div>
            <div class="column" style="width: 50%; border-left: 1px solid #000; border-bottom: 1px solid #000;">
                <div class="field-value" style="margin-left: 10px;"><?php echo strtoupper(htmlspecialchars($distinguishing_marks)); ?></div>
            </div>
        </div>
        

        <!-- Place of Origin / Means of Conveyance / Point of Entry -->
        <div style="display: flex;">
            <div class="column" style="width: 25%; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: center;">
                <div class="field-label" style="font-family: phetsarath OT;">ແຫຼ່ງທີ່ມາ ຂອງ ສີນຄ້າ</div>
                <div class="field-label">Place of origin</div>
            </div>
            <div class="column" style="width: 25%; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: center;">
                <div class="field-label" style="font-family: phetsarath OT;">ວິທີການຂົນສົ່ງ</div>
                <div class="field-label">Declared means of conveyance</div>
            </div>
            <div class="column" style="width: 50%; border-bottom: 1px solid #000; text-align: center;">
                <div class="field-label" style="font-family: phetsarath OT;">ຈຸດແຈ້ງ ການນໍາເຂົ້າ</div>
                <div class="field-label">Declared point of entry</div>
            </div>
        </div>
        
        <div style="display: flex;">
            <div class="column" style="width: 25%; border-bottom: 1px solid #000;">
                <div class="field-value" style="text-align: center;"><b><?php echo strtoupper(htmlspecialchars($country_origin)); ?></b></div>
            </div>
            <div class="column" style="width: 25%; border-left: 1px solid #000; border-bottom: 1px solid #000;">
                <div class="field-value" style="text-align: center;">
                    <b><?php echo strtoupper(htmlspecialchars($conveyance_name)); ?></b>
                    <?php if ($conveyance_sign): ?>
                     <b><?php echo strtoupper(htmlspecialchars($conveyance_sign)); ?></b>
                    <?php endif; ?>
                </div>
            </div>
            <div class="column" style="width: 50%; border-left: 1px solid #000; border-bottom: 1px solid #000;">
                <div class="field-value" style="text-align: center;"><b><?php echo strtoupper(htmlspecialchars($import_point)); ?></b></div>
            </div>
        </div>
        

        <!-- Product Name/Quantity and Botanical Name -->
        <div class="two-column">
            <div class="column" style="width: 50%; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: center;">
                <div class="field-label" style="font-family: phetsarath OT;">ຊື່ ຂອງ ສີນຄ້າ ແລະ ຈໍານວນທີ່ແຈ້ງ</div>
                <div class="field-label">Name of product and quantity declared</div>
            </div>
            <div class="column" style="width: 50%; border-bottom: 1px solid #000; text-align: center;">
                <div class="field-label" style="font-family: phetsarath OT;">ຊື່ວິທະຍາສາດ ຂອງ ພືດ</div>
                <div class="field-label">Botanical name of plants</div>
            </div>
        </div>
        
        <div class="two-column">
            <div class="column" style="width: 50%; border-bottom: 1px solid #000;">
                <div class="field-value" style="margin-left: 10px; font-weight: bold;">
                    <?php echo strtoupper(htmlspecialchars($product_name)); ?> - 
                    <?php echo strtoupper(htmlspecialchars($quantity_gross . ' ' . $unit_name)); ?>
                </div>
            </div>
            <div class="column" style="width: 50%; border-left: 1px solid #000; border-bottom: 1px solid #000;">
                <div class="field-value" style="text-align: center; font-weight: bold;"><i><?php echo htmlspecialchars($scientific_name); ?></i></div>
            </div>
        </div>
        
         <!-- Declaration Text -->
        <div class="section" style="margin-top: 15px; font-size: 9pt; text-align: center; border-bottom: 1px solid #000;">
            <div style="font-family: phetsarath OT; margin-bottom: 5px;">
                ຂໍຢັ້ງຢືນວ່າ ພືດ ແລະ ຜະລິດຕະພັນພືດ ຫຼື ວັດຖຸອື່ນຂ້າງເທີງນັ້ນ ໄດ້ຜ່ານການກວດກາ ແລະ ພົບວ່າ ປອດສັດຕູພືດຕ້ອງຫ້າມ ແລະ ສັດຕູພືດອື່ນໆ ທີ່ເປັນອັນຕະລາຍ ເຊີ່ງສອດຄ່ອງກັບ ລະບຽບການປ້ອງກັນພືດ ຂອງ ປະທດ ທີ່ນໍາເຂົ້າ
            </div>
            This is to certify that the plants, plant products or other regulated articles described herein have been inspected and/or tested according to appropriate official
            procedures and are considered to be free from the quarantine pests specified by the importing contracting party and to conform with the current phytosanitary
            requirements of the importing contracting party, including those for regulated non-quarantine pests.
        </div>
        
        <!-- Additional Declaration Header -->
        <div style="border-bottom: 1px solid #000;">
            <div class="section" style="margin: 0; text-align: center;">
                <div class="field-label" style="font-weight: bold;"><span class="field-label-lao">II. ແຈ້ງເພີ່ມເຕີມ(ຖ້າມີ)</span><span style="font-weight: normal;"> / ADDITIONAL DECLARATION</span></div>
            </div>
        </div>
      
        <div style="border-bottom: 1px solid #000; height: 100px;">
            <div class="section">
                <div class="field-value" style="text-align: center; font-weight: bold;"><?php echo strtoupper(htmlspecialchars($additional_declaration)); ?></div>
            </div>
        </div>

        <!-- Treatment Section -->
       <!--
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
        --> 
         <div style="border-bottom: 1px solid #000;">
            <div class="section" style="margin: 0; text-align: center;">
                <div class="field-label" style="font-weight: bold;"><span class="field-label-lao">III. ການເຮັດຄວາມສະອາດ ແລະ ການຂ້າເຊື້ອ</span><span style="font-weight: normal;"> / DISINFESTATION AND/OR DISINFECTION TREATMENT</span></div>
            </div>
        </div>
       
        <div style="display: flex; border-bottom: 1px solid #000;">
            <div class="column" style="width: 20%; text-align: left;">
                <div class="field-label" style="font-family: phetsarath OT;">ວັນທີ ເຮັດຄວາມສະອາດຂ້າເຊື້ອ</div>
                <div class="field-label">Treatment date:</div>
            </div>
            <div class="column" style="width: 30%; border-right: 1px solid #000;">
                <div class="field-value" style="text-align: center; font-weight: bold;">
                    <?php echo $treatment_date ? date('d-M-Y', strtotime($treatment_date)) : 'NIL'; ?>
                </div>
            </div>
            <div class="column" style="width: 20%; text-align: left;">
                <div class="field-label" style="font-family: phetsarath OT;">ວິທີເຮັດຄວາມສະອາດຂ້າເຊື້ອ</div>
                <div class="field-label">Treatment:</div>
            </div>
            <div class="column" style="width: 30%;">
                <div class="field-value" style="text-align: center; font-weight: bold;">
                    <?php echo $treatment_name ? strtoupper(htmlspecialchars($treatment_name)) : 'NIL'; ?>
                </div>
            </div>
        </div>
        
        <div style="display: flex; border-bottom: 1px solid #000;">
            <div class="column" style="width: 22%; text-align: left;">
                <div class="field-label" style="font-family: phetsarath OT;">ສານເຄມີ (ສ່ວນປະກອບທີ່ອອກລິດ)</div>
                <div class="field-label">Chemical (Active ingredient):</div>
            </div>
            <div class="column" style="width: 28%; border-right: 1px solid #000;">
                <div class="field-value" style="text-align: center; font-weight: bold;">
                    <?php echo $treatment_chemical ? htmlspecialchars($treatment_chemical) : 'NIL'; ?>
                </div>
            </div>
            <div class="column" style="width: 20%; text-align: left;">
                <div class="field-label" style="font-family: phetsarath OT;">ໄລຍະເວລາ ແລະ ອຸນຫະພູມ</div>
                <div class="field-label">Duration & temperature:</div>
            </div>
            <div class="column" style="width: 30%;">
                <div class="field-value" style="text-align: center; font-weight: bold;">
                    <?php echo $treatment_duration ? htmlspecialchars($treatment_duration) : 'NIL'; ?>
                </div>
            </div>
        </div>

         <div style="display: flex; border-bottom: 1px solid #000;">
            <div class="column" style="width: 22%; text-align: left;">
                <div class="field-label" style="font-family: phetsarath OT;">ຄວາມເຂັ້ມຂຸ້ນ</div>
                <div class="field-label">Concentration:</div>
            </div>
            <div class="column" style="width: 28%; border-right: 1px solid #000;">
                <div class="field-value" style="text-align: center; font-weight: bold;">
                    <?php echo $treatment_concentration ? htmlspecialchars($treatment_concentration) : 'NIL'; ?>
                </div>
            </div>
            <div class="column" style="width: 20%; text-align: left;">
                <div class="field-label" style="font-family: phetsarath OT;">ຂໍ້ມູນເພີ່ມເຕີມ</div>
                <div class="field-label">Additional Information:</div>
            </div>
            <div class="column" style="width: 30%;">
                <div class="field-value" style="text-align: center; font-weight: bold;">
                    <?php echo $additional_declaration ? htmlspecialchars($additional_declaration) : 'NIL'; ?>
                </div>
            </div>
        </div>
        

        <!-- Signature Section -->
        <div style="display: flex; position: relative;">
            <!-- Left side: Three rows with two columns each -->
            <div style="width: 43%; display: flex; flex-direction: column;">
                <!-- First Row: Date Inspected -->
                <div class="row" style="display: flex; border-bottom: 1px solid #000;">
                    <div class="column" style="width: 40%; text-align: left;">
                        <div class="field-label" style="font-family: phetsarath OT;">ວັນທີ ກວດກາ</div>
                        <div class="field-label">Date Inspected:</div>
                    </div>
                    <div class="column" style="width: 60%;">
                        <div class="field-value" style="text-align: left;">
                            <?php echo isset($inspection_date) && $inspection_date ? date('d-M-Y', strtotime($inspection_date)) : ''; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Second Row: Date Issued -->
                <div class="row" style="display: flex; border-bottom: 1px solid #000;"> 
                   <div class="column" style="width: 40%; text-align: left;">
                        <div class="field-label" style="font-family: phetsarath OT;">ວັນທີ ອອກໃບຢັ້ງຢືນ</div>
                        <div class="field-label">Date Issued:</div>
                    </div>
                    <div class="column" style="width: 60%;">
                        <div class="field-value" style="text-align: left;">
                            <?php echo isset($date_issued) && $date_issued ? date('d-M-Y', strtotime($date_issued)) : ''; ?>
                        </div>
                    </div>
                </div>  
                
                <!-- Third Row: Place of Issued -->
                <div class="row" style="display: flex; border-bottom: 1px solid #000;"> 
                    <div class="column" style="width: 40%; text-align: left;">
                        <div class="field-label" style="font-family: phetsarath OT;">ສະຖານທີ່ ອອກໃບຢັ້ງຢືນ</div>
                        <div class="field-label">Place of Issued:</div>
                    </div>
                    <div class="column" style="width: 60%;">
                        <div class="field-value" style="text-align: left;">
                            <?php echo isset($place_issued) ? htmlspecialchars($place_issued) : ''; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Merged third column (spans all 3 rows) -->
            <div class="column" style="width: 25%; border-bottom: 1px solid #000; border-right: 1px solid #000; border-left: 1px solid #000; text-align: center;">
                <div class="field-value">&nbsp;</div>
            </div>
            
            <!-- Fourth column area -->
            <div style="width: 42%; display: flex; flex-direction: column; border-bottom: 1px solid #000;">
                <div class="column" style="height: 33.33%; width: 100%;">
                    <div class="field-label" style="font-family: phetsarath OT; text-align: center;">ຊື່ ແລະ ລາຍເຊັນເຈົ້າໜ້າທີ່ກັກກັນພືດ</div>
                    <div class="field-label" style="text-align: center;">Name and Signature of Authorized Officer</div>
                </div>
                <div class="column" style="height: 33.33%;">
                    <div class="field-value">&nbsp;</div>
                </div>
                <div class="column" style="height: 33.34%; text-align: center;">
                    <div class="field-value" style="text-align: center;">Officer's name</div>
                </div>
            </div>
        </div> 
        <!-- Footer -->
        <div class="footer">
            Department of Agriculture, P.O Box 811, Nongbone, Lao PDR. Tel: (856) 21 412350, Fax: (856) 21 412349<br>
            Email: pqdlao@yahoo.com
        </div>
       </div> <!-- End Main Content --> 
     </div><!-- End certificate-main -->
    </div><!-- End certificate-container -->
    
    <script>
    function saveAsPDF() {
        // Hide the buttons before generating PDF
        const buttonContainer = document.querySelector('.print-button');
        buttonContainer.style.display = 'none';
        
        // Clone the certificate container for manipulation
        const originalContainer = document.querySelector('.certificate-container');
        const clonedContainer = originalContainer.cloneNode(true);
        
        // Create a temporary wrapper
        const tempWrapper = document.createElement('div');
        tempWrapper.style.position = 'fixed';
        tempWrapper.style.left = '-9999px';
        tempWrapper.style.top = '0';
        tempWrapper.style.width = '210mm';
        tempWrapper.style.background = 'white';
        tempWrapper.style.padding = '2mm';
        document.body.appendChild(tempWrapper);
        
        // Restructure layout for PDF
        const qrBox = clonedContainer.querySelector('.qr-box');
        const certMain = clonedContainer.querySelector('.certificate-main');
        
        if (qrBox && certMain) {
            // Create a wrapper table for side-by-side layout
            const layoutTable = document.createElement('div');
            layoutTable.style.display = 'table';
            layoutTable.style.width = '100%';
            layoutTable.style.tableLayout = 'fixed';
            
            // QR cell
            const qrCell = document.createElement('div');
            qrCell.style.display = 'table-cell';
            qrCell.style.width = '35mm';
            qrCell.style.verticalAlign = 'top';
            qrCell.style.padding = '0';
            
            // Content cell
            const contentCell = document.createElement('div');
            contentCell.style.display = 'table-cell';
            contentCell.style.verticalAlign = 'top';
            contentCell.style.padding = '0';
            contentCell.style.paddingLeft = '2mm';
            
            // Style QR box
            qrBox.style.position = 'static';
            qrBox.style.float = 'none';
            qrBox.style.width = '32mm';
            qrBox.style.height = 'auto';
            qrBox.style.margin = '0';
            qrBox.style.padding = '1mm';
            
            // Style cert main
            certMain.style.margin = '0';
            certMain.style.padding = '0';
            // Preserve background image for PDF
            certMain.style.backgroundImage = "url('images/certificate_bg.png')";
            certMain.style.backgroundSize = 'cover';
            certMain.style.backgroundPosition = 'center 150px';
            certMain.style.backgroundRepeat = 'no-repeat';
            
            // Move elements into cells
            qrCell.appendChild(qrBox);
            contentCell.appendChild(certMain);
            
            // Clear and rebuild container
            clonedContainer.innerHTML = '';
            layoutTable.appendChild(qrCell);
            layoutTable.appendChild(contentCell);
            clonedContainer.appendChild(layoutTable);
        }
        
        tempWrapper.appendChild(clonedContainer);
        
        // Minimal styling to ensure clean PDF output
        clonedContainer.style.boxShadow = 'none';
        clonedContainer.style.maxWidth = '100%';
        clonedContainer.style.background = 'white';
        
        // Wait for rendering
        setTimeout(function() {
            const certificateNo = '<?php echo htmlspecialchars($certificate_no); ?>';
            const filename = 'Phytosanitary_Certificate_' + certificateNo.replace(/[^a-zA-Z0-9]/g, '_') + '.pdf';
            
            const opt = {
                margin: [2, 2, 2, 2],
                filename: filename,
                image: { type: 'jpeg', quality: 0.95 },
                html2canvas: { 
                    scale: 2,
                    useCORS: true, 
                    letterRendering: true,
                    logging: false,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    windowHeight: clonedContainer.scrollHeight,
                    height: clonedContainer.scrollHeight
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'portrait',
                    compress: true
                },
                pagebreak: { mode: 'avoid-all' }
            };
            
            html2pdf().set(opt).from(clonedContainer).save().then(function() {
                // Clean up
                document.body.removeChild(tempWrapper);
                buttonContainer.style.display = 'flex';
            }).catch(function(error) {
                console.error('PDF generation error:', error);
                // Clean up on error
                if (document.body.contains(tempWrapper)) {
                    document.body.removeChild(tempWrapper);
                }
                buttonContainer.style.display = 'flex';
                alert('Error generating PDF. Please try the Print button instead.');
            });
        }, 500);
    }
    </script>
</body>
</html>
