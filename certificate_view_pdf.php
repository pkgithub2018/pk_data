<?php
// This file is included by the PDF generation function
// Do not call directly - variables are passed from the calling function

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
    $sql = "SELECT name, surname FROM tbapprovers WHERE id = '$approved_byid'";
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
            background: white;
            font-size: 10pt;
            line-height: 1.2;
        }
        
        .certificate-container {
            width: 210mm;
            height: 297mm;
            position: relative;
            background: #F5F5DC; /* Beige background like the image */
            background-image: url('assets/img/certificate_draft.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            overflow: hidden;
            page-break-inside: avoid;
            padding: 15mm;
            box-sizing: border-box;
        }
        
        .header-section {
            text-align: center;
            margin-bottom: 15mm;
        }
        
        .coat-of-arms {
            width: 60mm;
            height: 60mm;
            margin: 0 auto 10mm auto;
            background: url('assets/img/lao_coat_of_arms.png') center center no-repeat;
            background-size: contain;
        }
        
        .country-title {
            font-size: 11pt;
            font-weight: bold;
            margin: 2mm 0;
            color: #000;
        }
        
        .ministry-title {
            font-size: 9pt;
            margin: 1mm 0;
            color: #000;
        }
        
        .certificate-title {
            font-size: 18pt;
            font-weight: bold;
            margin: 8mm 0 5mm 0;
            color: #000;
        }
        
        .certificate-number {
            position: absolute;
            top: 85mm;
            right: 20mm;
            font-size: 14pt;
            font-weight: bold;
            color: #000;
        }
        
        .original-stamp {
            position: absolute;
            top: 75mm;
            right: 15mm;
            border: 2px solid red;
            color: red;
            padding: 3mm;
            font-weight: bold;
            font-size: 10pt;
        }
        
        .certificate-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10mm;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .certificate-table td, .certificate-table th {
            border: 1px solid #000;
            padding: 3mm;
            vertical-align: top;
            font-size: 9pt;
            line-height: 1.3;
        }
        
        .section-header {
            background-color: #E6E6E6;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
            padding: 2mm;
        }
        
        .field-label {
            font-weight: bold;
            font-size: 8pt;
            color: #333;
        }
        
        .field-value {
            font-size: 9pt;
            color: #000;
        }
        
        .scientific-name {
            font-style: italic;
        }
        
        .two-column {
            width: 50%;
        }
        
        .declaration-section {
            margin-top: 5mm;
            padding: 3mm;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #000;
            font-size: 8pt;
            line-height: 1.4;
        }
        
        .signature-section {
            margin-top: 5mm;
            text-align: right;
            font-size: 9pt;
        }
        
        .uppercase {
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="coat-of-arms"></div>
            <div class="country-title">LAO PEOPLE'S DEMOCRATIC REPUBLIC</div>
            <div class="ministry-title">PEACE INDEPENDENCE DEMOCRACY UNITY PROSPERITY</div>
            <div class="ministry-title">MINISTRY OF AGRICULTURE AND FORESTRY</div>
            <div class="ministry-title">DEPARTMENT OF AGRICULTURE</div>
            <div class="certificate-title">ໃບຢັ້ງຢືນ ກູ້ຄືນໂພດິ໌<br>PHYTOSANITARY CERTIFICATE</div>
        </div>
        
        <!-- Certificate Number -->
        <div class="certificate-number">
            <?php echo strtoupper(htmlspecialchars($cert_info['certificate_no'] ?? '')); ?>
        </div>
        
        <!-- Original Stamp -->
        <div class="original-stamp">ORIGINAL</div>
        
        <!-- Main Certificate Table -->
        <table class="certificate-table">
            <!-- FROM/TO Row -->
            <tr>
                <td class="two-column">
                    <div class="field-label">ຈາກ:<br>FROM:</div>
                    <div class="field-value uppercase"><?php echo strtoupper(htmlspecialchars($place_origin)); ?></div>
                </td>
                <td class="two-column">
                    <div class="field-label">ເຖິງ:<br>TO:</div>
                    <div class="field-value uppercase"><?php echo strtoupper(htmlspecialchars($import_country)); ?></div>
                </td>
            </tr>
            
            <!-- Description Section Header -->
            <tr>
                <td colspan="2" class="section-header">I. ລາຍລະອຽດສິນຄ້າ / DESCRIPTION OF CONSIGNMENT</td>
            </tr>
            
            <!-- Name and Address Row -->
            <tr>
                <td class="two-column">
                    <div class="field-label">ຊື່ ແລະ ທີ່ຢູ່ ຜູ້ສົ່ງອອກ<br>Name and address of exporter</div>
                    <div class="field-value uppercase">
                        <?php echo strtoupper(htmlspecialchars($exporter_info['title'] ?? '')); ?><br>
                        <?php echo strtoupper(nl2br(htmlspecialchars($exporter_info['address'] ?? ''))); ?>
                    </div>
                </td>
                <td class="two-column">
                    <div class="field-label">ຊື່ ແລະ ທີ່ຢູ່ ຜູ້ຮັບ<br>Declared name and address of consignee</div>
                    <div class="field-value uppercase">
                        <?php echo strtoupper(htmlspecialchars($importer_info['title'] ?? '')); ?><br>
                        <?php echo strtoupper(nl2br(htmlspecialchars($importer_info['address'] ?? ''))); ?>
                    </div>
                </td>
            </tr>
            
            <!-- Number and Description of Packages Row -->
            <tr>
                <td class="two-column">
                    <div class="field-label">ຈຳນວນ ແລະ ລາຍລະອຽດຫຸ້ມຫໍ່<br>Number and description of packages</div>
                    <div class="field-value uppercase">
                        <?php echo strtoupper(htmlspecialchars($app_info['commodity_description'] ?? '')); ?>
                    </div>
                </td>
                <td class="two-column">
                    <div class="field-label">ເຄື່ອງໝາຍທີ່ມີຢູ່<br>Distinguishing marks</div>
                    <div class="field-value uppercase">
                        <?php echo strtoupper(htmlspecialchars($app_info['marks_item'] ?? '')); ?>
                    </div>
                </td>
            </tr>
            
            <!-- Place of Origin and Transport Row -->
            <tr>
                <td style="width: 33.33%">
                    <div class="field-label">ແຫຼ່ງຜະລິດ ຫຼື ຕົ້ນກຳເນີດ<br>Place of origin</div>
                    <div class="field-value uppercase"><?php echo strtoupper(htmlspecialchars($place_origin)); ?></div>
                </td>
                <td style="width: 33.33%">
                    <div class="field-label">ວິທີການຂົນສົ່ງທີ່ໄດ້ແຈ້ງ<br>Declared means of conveyance</div>
                    <div class="field-value uppercase"><?php echo strtoupper(htmlspecialchars($conveyance_name)); ?></div>
                </td>
                <td style="width: 33.33%">
                    <div class="field-label">ຈຸດປ່ອຍ ຫຼື ທາງເຂົ້າ<br>Declared point of entry</div>
                    <div class="field-value uppercase"><?php echo strtoupper(htmlspecialchars($app_info['import_point'] ?? '')); ?></div>
                </td>
            </tr>
            
            <!-- Product Name and Scientific Name Row -->
            <tr>
                <td class="two-column">
                    <div class="field-label">ຊື່ ຫຼື ຊະນິດ ແລະ ປະລິມານທີ່ໄດ້ແຈ້ງ<br>Name of product and quantity declared</div>
                    <div class="field-value uppercase">
                        <?php echo $commodity_name ? strtoupper(htmlspecialchars($commodity_name)) . '<br>' : ''; ?>
                        <?php echo $commodity_quantity ? strtoupper(htmlspecialchars($commodity_quantity)) : ''; ?>
                    </div>
                </td>
                <td class="two-column">
                    <div class="field-label">ຊື່ສຳຜັດທາງວິທະຍາສາດ<br>Botanical name of plants</div>
                    <div class="field-value scientific-name">
                        <?php echo htmlspecialchars($app_info['name_scientific'] ?? ''); ?>
                    </div>
                </td>
            </tr>
        </table>
        
        <!-- Declaration Section -->
        <?php if (!empty($cert_info['additional_declaration'])): ?>
        <div class="declaration-section">
            <strong>II. ຄຳແຈ້ງເພີ່ມເຕີມ / ADDITIONAL DECLARATION</strong><br>
            <?php echo nl2br(htmlspecialchars($cert_info['additional_declaration'])); ?>
        </div>
        <?php endif; ?>
        
        <!-- Standard Declaration -->
        <div class="declaration-section">
            <strong>This is to certify that the plants and plant products or other regulated articles described herein have been inspected and/or tested according to appropriate official procedures and are considered to be free from the quarantine pests specified by the importing contracting party and to conform with the current phytosanitary requirements of the importing contracting party, including those for regulated non-quarantine pests.</strong>
        </div>
        
        <!-- Treatment Information Table -->
        <?php 
        $inspection_info = InspectionInfo($appid, $con);
        if ($inspection_info): 
        ?>
        <table class="certificate-table" style="margin-top: 5mm;">
            <tr>
                <td colspan="4" class="section-header">III. ການປົວແປງ / TREATMENT</td>
            </tr>
            <tr>
                <td style="width: 25%">
                    <div class="field-label">ວັນທີປົວແປງ<br>Date</div>
                    <div class="field-value">
                        <?php 
                        if (!empty($inspection_info['treatment_date']) && $inspection_info['treatment_date'] !== '0000-00-00') {
                            echo date('d-M-Y', strtotime($inspection_info['treatment_date']));
                        }
                        ?>
                    </div>
                </td>
                <td style="width: 25%">
                    <div class="field-label">ການປິ່ນປົວ<br>Treatment</div>
                    <div class="field-value">
                        <?php
                        if (!empty($inspection_info['treatment_method'])) {
                            $treatment_method_info = TreatmentMethodInfo($inspection_info['treatment_method'], $con);
                            echo htmlspecialchars($treatment_method_info['title'] ?? '');
                        }
                        ?>
                    </div>
                </td>
                <td style="width: 25%">
                    <div class="field-label">ສານເຄມີທີ່ໃຊ້<br>Chemical (active ingredient)</div>
                    <div class="field-value">
                        <?php echo htmlspecialchars($inspection_info['chemical_used'] ?? ''); ?>
                    </div>
                </td>
                <td style="width: 25%">
                    <div class="field-label">ໄລຍະເວລາ ແລະ ອຸນຫະພູມ<br>Duration and temperature</div>
                    <div class="field-value">
                        <?php echo htmlspecialchars($inspection_info['duration_temp'] ?? ''); ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 25%">
                    <div class="field-label">ປະລິມານ<br>Concentration</div>
                    <div class="field-value">
                        <?php echo htmlspecialchars($inspection_info['concentration'] ?? ''); ?>
                    </div>
                </td>
                <td colspan="3">
                    <div class="field-label">ຂໍ້ມູນເພີ່ມເຕີມ<br>Additional information</div>
                    <div class="field-value">
                        <?php echo htmlspecialchars($inspection_info['additional_info'] ?? ''); ?>
                    </div>
                </td>
            </tr>
        </table>
        <?php endif; ?>
        
        <!-- Final Information Table -->
        <table class="certificate-table" style="margin-top: 5mm;">
            <tr>
                <td style="width: 33.33%">
                    <div class="field-label">ວັນທີກວດກາ<br>Date of inspection</div>
                    <div class="field-value">
                        <?php 
                        if ($inspection_info) {
                            $inspection_date = $inspection_info['inspection_date'] ?? '';
                            if ($inspection_date && $inspection_date !== '0000-00-00') {
                                echo date('d-M-Y', strtotime($inspection_date));
                            }
                        }
                        ?>
                    </div>
                </td>
                <td style="width: 33.33%">
                    <div class="field-label">ວັນທີອອກໃບຢັ້ງຢືນ<br>Date of issue</div>
                    <div class="field-value"><?php echo htmlspecialchars($date_issued); ?></div>
                </td>
                <td style="width: 33.33%">
                    <div class="field-label">ສະຖານທີ່ອອກໃບຢັ້ງຢືນ<br>Place of issue</div>
                    <div class="field-value"><?php echo htmlspecialchars($cert_info['place_issued'] ?? ''); ?></div>
                </td>
            </tr>
        </table>
        
        <!-- Signature Section -->
        <div class="signature-section" style="margin-top: 10mm;">
            <div style="float: right; width: 80mm; text-align: center;">
                <div style="height: 20mm; border-bottom: 1px solid #000; margin-bottom: 2mm;"></div>
                <div style="font-weight: bold;">
                    <?php 
                    if ($approver_info) {
                        echo htmlspecialchars($approver_info['name'] . ' ' . $approver_info['surname']);
                    }
                    ?>
                </div>
                <div style="margin-top: 2mm;">
                    <strong>Inspector / Authorized Officer</strong>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</body>
</html>