<?php 
session_start();

// Check if a language is selected via the query parameter
if (isset($_GET['lang'])) {
  $selectedLang = $_GET['lang'];
  $_SESSION['lang'] = $selectedLang; // Store the selected language in the session
} else {
  // Default to English if no language is selected
  if (!isset($_SESSION['lang'])) {
      $_SESSION['lang'] = 'en';
  }
}

// Include the appropriate language file
$langFile = "php-bin/lang_" . $_SESSION['lang'] . ".php";
if (file_exists($langFile)) {
  $translations = include($langFile);
} else {
  die("Language file not found.");
}
// connection to database
 require("php-bin/connection.php");
 require("php-bin/supports.php");
 
 $userid = isset($_SESSION["uid"]) ? $_SESSION["uid"] : ''; // use user id
 $guid = isset($_SESSION["groupid"]) ? $_SESSION["groupid"] : ''; // use group id
 if(empty($userid)){
    // If user ID is not set, redirect to login page
    echo "<script>alert('You are not logged in. Please log in to access this page.');</script>"; 
 }
 
 // CANCEL/DELETE Application
 if (isset($_GET['btn']) && $_GET['btn'] === 'cancelApp') {
   // Handle the cancellation logic here
    $appid = $_GET['appid']; // Get the application ID from the query parameter
   echo "<script>alert('Application cancelled successfully. ID: " . $appid . "');</script>";
    $del = DeleteApplication($appid, $con);
 }

 // SUBMIT/SAVE application by UPDATING tbapplication with the form data - CLICK ON SUBMIT BUTTON
    if (isset($_POST['btnsubApplication_save']) && (isset($_POST['btnsubApplication_save']) === "submit") || (isset($_POST['btnsubApplication_save']) === "update")) {  // Submit from application form in transaction.php
        $app_id = isset($_POST['app_id']) ? $_POST['app_id'] : ''; // hidden input
        
        $app_no = isset($_POST['application_no']) ? $_POST['application_no'] : '';
        $reg_no = isset($_POST['reg_no']) ? $_POST['reg_no'] : '';
        $entry_point = isset($_POST['entry_point']) ? $_POST['entry_point'] : '';
        $applicant_name = isset($_POST['applicant_name']) ? $_POST['applicant_name'] : '';
        $address = isset($_POST['address']) ? $_POST['address'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';


        $import_country = isset($_POST['import_country']) ? $_POST['import_country'] : '';
        $import_point = isset($_POST['import_point']) ? $_POST['import_point'] : '';
        $export_certificate = isset($_POST['export_certificate']) ? 1 : 0;
        $transit_certificate = isset($_POST['transit_certificate']) ? 1 : 0;
        if($export_certificate==1){
          $certificate_type = 'export';
        } elseif($transit_certificate==1) {
          $certificate_type = 'transit';
        }
        $check_multiple = isset($_POST['multiple_commodities']) ? 1 : 0;
        $check_support_document = isset($_POST['support_document']) ? 1 : 0;
        $product_name = isset($_POST['proname']) ? $_POST['proname'] : '';
        $name_oncertificate = isset($_POST['name_oncertificate']) ? $_POST['name_oncertificate'] : '';
        $scientific_name = isset($_POST['scientific_name']) ? $_POST['scientific_name'] : '';

        $product_id = ProductId($product_name, $scientific_name, $con);

        $commodity_description = isset($_POST['number_description']) ? $_POST['number_description'] : '';
        $nquantity = isset($_POST['nquantity']) ? $_POST['nquantity'] : '';
        $gquantity = isset($_POST['gquantity']) ? $_POST['gquantity'] : '';

        $unit = isset($_POST['unit']) ? $_POST['unit'] : '';
        $marks = isset($_POST['marks']) ? $_POST['marks'] : '';

        $place_origin = isset($_POST['place_origin']) ? $_POST['place_origin'] : null;  // data type - integer could not accept ''
        $conveyance = isset($_POST['conveyance']) ? $_POST['conveyance'] : null;
        $conveyance_sign = isset($_POST['conveyance_sign']) ? $_POST['conveyance_sign'] : '';

        $exporter_address = isset($_POST['exporter_address']) ? $_POST['exporter_address'] : '';
        $importer_address = isset($_POST['importer_address']) ? $_POST['importer_address'] : '';

        $purpose = isset($_POST['purpose']) ? $_POST['purpose'] : '';
     
         $place_quarantine = isset($_POST['place_quarantine']) && $_POST['place_quarantine'] !== '' ? (int)$_POST['place_quarantine'] : null;

        //$place_treatment = isset($_POST['place_treatment']) ? $_POST['place_treatment'] : '';
        $place_treatment = isset($_POST['place_treatment']) && $_POST['place_treatment'] !== '' ? (int)$_POST['place_treatment'] : null;

        $place_quarantine_other = isset($_POST['place_quarantine_other']) ? $_POST['place_quarantine_other'] : '';
        $place_treatment_other = isset($_POST['place_treatment_other']) ? $_POST['place_treatment_other'] : '';
        $certificate_date = isset($_POST['certificate_date']) ? $_POST['certificate_date'] : '';
        //$guid = isset($_POST['guid']) ? $_POST['guid'] : '';

            // Put them into $data array (keys = DB column names)
        $data = [
            'reg_no'             => $reg_no,
            'export_point'       => $entry_point,
            'contact_person'     => $applicant_name,
            'address_person'     => $address,
            'phone'              => $phone,
           
            'country_import'     => $import_country,
            'import_point'       => $import_point,
            'certificate_type'   => $certificate_type,  
            'multi_item'         => $check_multiple,
            'print_support'      => $check_support_document,
            'commodity_id'      => $product_id,
            'name_oncertificate' => $name_oncertificate,
            'name_scientific'    => $scientific_name,
            'commodity_description'=> $commodity_description,

            'quantity_net'        => $nquantity,
            'quantity_gross'     => $gquantity,
            'unit_id'            => $unit,
            'marks_item'         => $marks,
            'place_origin'      => $place_origin,
            'conveyance_id'     => $conveyance,
            'conveyance_sign'   => $conveyance_sign,
            'address_exporter'  => $exporter_address,
            'address_importer'  => $importer_address,
            'purpose'           => $purpose,
            'place_quarantine'  => $place_quarantine,
            'place_treatment'   => $place_treatment,
            'date_certificate'  => $certificate_date,
            'place_quarantine_other' => $place_quarantine_other,
            'place_treatment_other'  => $place_treatment_other
        ];
        
        $result = ApplicationUpdate($app_id, $data, $con); // Update tbapplication with the form data
        if ($result) {
          //  echo "<script>alert('Application updated successfully!');</script>";
        } else {
          //  echo "<script>alert('Failed to update application. Please try again.');</script>";
        }
    } // End of if - Submission for updating application FIRST TIME (NO CHANGE IS MADE)

   // UPDATE/CHANGE on application - CLICK ON UPDATE BUTTON in transaction.php
   if(isset($_POST['btnsubApplication_save']) && $_POST['btnsubApplication_save'] === "update"){
     echo "<script>alert('Update- Application ID: $app_id');</script>";
   }

   // *************************** INSPECTION ***************************
    // SAVE inspection data - CLICK ON SAVE BUTTON in inspection form in transaction.php
    if (isset($_POST['btnSubmitInspection'])) {
      
        $app_id = isset($_POST['appid']) ? $_POST['appid'] : ''; // hidden input
        $inspection_date = isset($_POST['inspection_date']) ? $_POST['inspection_date'] : null;
        $sample_no = isset($_POST['sampleno']) ? $_POST['sampleno'] : '';
        $sample_quantity = isset($_POST['sample_volume']) ? $_POST['sample_volume'] : '';
        $unit_id = isset($_POST['unit']) ? $_POST['unit'] : null;
        $sample_collected_by = isset($_POST['sample_collectedby']) ? $_POST['sample_collectedby'] : '';
        $inspected_by = isset($_POST['sample_inspectedby']) ? $_POST['sample_inspectedby'] : '';
        $certificate_fee = isset($_POST['certificate_fee']) ? $_POST['certificate_fee'] : '';
        $receipt_no = isset($_POST['receipt_no']) ? $_POST['receipt_no'] : '';
        $lot_number = isset($_POST['lot_no']) ? $_POST['lot_no'] : '';
        $inspection_method = isset($_POST['inspection_method']) && $_POST['inspection_method'] !== '' ? (int)$_POST['inspection_method'] : null;
        $pest_detected = isset($_POST['detected_pest']) ? 1 : 0;
        $treat_ability = isset($_POST['treatment_ability']) ? 1 : 0;
        $lab_required = isset($_POST['lab_analysis']) ? 1 : 0;  
        $treatment_method = isset($_POST['treatment_method']) && $_POST['treatment_method'] !== '' ? (int)$_POST['treatment_method'] : null;
        $treatment_date = isset($_POST['treatment_date']) ? $_POST['treatment_date'] : null;
        $chemical_used = isset($_POST['chemical_used']) ? $_POST['chemical_used'] : '';
        $chemical_fortreat = isset($_POST['chemical_fortreat']) ? $_POST['chemical_fortreat'] : '';
        $duration_temp = isset($_POST['duration_temp']) ? $_POST['duration_temp'] : '';
        $concentration = isset($_POST['concentration']) ? $_POST['concentration'] : '';
        $sample_inspectedby = isset($_POST['sample_inspectedby']) ? $_POST['sample_inspectedby'] : '';
        $additional_info = isset($_POST['additional_info']) ? $_POST['additional_info'] : '';
        $treatment_reason = isset($_POST['reason']) ? $_POST['reason'] : '';
        $post_treatment_details = isset($_POST['post_details']) ? $_POST['post_details'] : '';

         // Put them into $inspection_data array (keys = DB column names)

        $inspection_data = [
            'application_id' => $app_id,
            'inspection_date' => $inspection_date,
            'sample_no' => $sample_no,
            'sample_quantity' => $sample_quantity,
            'unit_id' => $unit_id,
            'sample_collected_by' => $sample_collected_by,
            'inspected_by' => $inspected_by,
            'certificate_fee' => $certificate_fee,
            'receipt_no' => $receipt_no,
            'lot_number' => $lot_number,
            'inspection_method' => $inspection_method,
            'pest_detected' => $pest_detected,
            'treat_ability' => $treat_ability,
            'lab_required' => $lab_required,
            'treatment_method' => $treatment_method,
            'treatment_date' => $treatment_date,
            'chemical_used' => $chemical_used,
            'chemical_fortreat' => $chemical_fortreat,
            'duration_temp' => $duration_temp,
            'concentration' => $concentration,
            'sample_inspectedby' => $sample_inspectedby,
            'additional_info' => $additional_info,
            'treatment_reason' => $treatment_reason,
            'post_treatment_details' => $post_treatment_details,
            'enabled' => 'yes'

        ];
        //echo "<script>alert('Save inspection data -UPDATE: pk - Application ID: $app_id');</script>";
         // ADD NEW inspection data
         if($_POST['btnSubmitInspection'] === 'submit'){
            $result = InspectionAdd($inspection_data, $con);
            if ($result) {
                echo "<script>alert('Inspection data saved successfully!');</script>";
            } else {
                echo "<script>alert('Failed to save inspection data. Please try again.');</script>";
            }
          } elseif($_POST['btnSubmitInspection'] === 'update'){  // UPDATE inspection data
            $result = InspectionUpdate($app_id, $inspection_data, $con);
            if ($result) {
                echo "<script>alert('Inspection data updated successfully!');</script>";
            } else {
                echo "<script>alert('Failed to update inspection data. Please try again.');</script>";
            }
          }
      
    } // end of if - submission of inspection form
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo $translations['dashboard']; ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet"> 
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
 


  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 7 2025 with Bootstrap v5.3.5
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">ePhyto Certificate</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <!-- Language Switcher -->
        <li class="nav-item">
          <a href="?lang=la" class="nav-link nav-icon">
          <img src="assets/img/flags/lao.png" alt="Lao" style="width: 24px; height: 16px;">
          </a>
        </li>
        <li class="nav-item">
          <a href="?lang=en" class="nav-link nav-icon">
          <img src="assets/img/flags/english.png" alt="English" style="width: 24px; height: 16px;">
          </a>
        </li>
    <!-- End Language Switcher -->

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->
        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?php echo $_SESSION['image']; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION["username"]; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $_SESSION['username']; ?></h6>
              <span><?php echo $_SESSION["position"]; ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span>Need Help?</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="index.php?logout=true">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link " href="index.php">
          <i class="bi bi-grid"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
          <span><?php echo $translations['Dashboard']; ?></span>
        </a>
      </li><!-- End Dashboard Nav -->
    
      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=export" >
          <i class="bi bi-box-arrow-up-right"></i>
          <span>Export entity</span>
        </a>
      </li><!-- End Export Entity Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=import" >
          <i class="bi bi-box-arrow-in-down" style="font-size: 1.2rem;"></i>
          <span>Import entity</span>
        </a>
      </li><!-- End Import Entity/Company form Nav -->

    <!-- Module Nav -->
     <!--
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span><?php echo $translations['modules']; ?></span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="modules.php?part=entity">
              <i class="bi bi-circle"></i><span>Entity/Company</span> 
            </a>
          </li>
          <li>
            <a href="modules.php?part=inspection">
              <i class="bi bi-circle"></i><span>Inspection</span>
            </a>
          </li>
          <li>
            <a href="modules.php?part=sample">
              <i class="bi bi-circle"></i><span>Sample</span>
            </a>
          </li>
          <li>
            <a href="modules.php?part=certificate">
              <i class="bi bi-circle"></i><span>Certificate</span>
            </a>
          </li>
          <li>
            <a href="modules.php?part=printing">
              <i class="bi bi-circle"></i><span>Printing</span>
            </a>
          </li>
        </ul>
      </li>
    -->
      <!-- End Forms Nav -->

      <?php if($_SESSION["groupname"] == "admin"){ ?><!-- Admin group check -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Master data</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
         
          <li>
            <a href="masterdata.php?part=product">
              <i class="bi bi-circle"></i><span>Product</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=conveyance">
              <i class="bi bi-circle"></i><span>Conveyance</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=countries">
              <i class="bi bi-circle"></i><span>Countries</span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span>Districts</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=entitytype">
              <i class="bi bi-circle"></i><span>Entity_type</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=inspectionmethod">
              <i class="bi bi-circle"></i><span>Inspection Method</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=locations">
              <i class="bi bi-circle"></i><span>Locations</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=modules">
              <i class="bi bi-circle"></i><span>Module List</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=provinces">
              <i class="bi bi-circle"></i><span>Provinces</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=treatmentmethod">
              <i class="bi bi-circle"></i><span>Treatment Method</span>
            </a>
          </li>
        </ul>
      </li><!-- End Master Data Nav -->

      <?php } // End of Admin group check ?>

      <li class="nav-heading">Users' Management</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=ugroup">
          <i class="bi bi-people"></i>
          <span>Users group</span>
        </a>
      </li><!-- End Users group -->

       <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=upermits">
          <i class="bi bi-shield-lock"></i>
          <span>Group permits</span>
        </a>
      </li><!-- End Permission: User Group and Module -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=userslist">
          <i class="bi bi-person-plus"></i><span>Users</span>
        </a>
      </li>  
      <!-- pk**: End of User Admin-->
    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-8" style="width: 100%;">
          <div class="row">           
                 
            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <h5 class="card-title">Phytosanitary Certificates <span>| Today</span></h5>

                  <table class="table datatable" style="font-size: 10pt;">
                    <thead>
                      <tr>
                        <th scope="col">Application No</th>
                        <th scope="col">Exporters</th>
                        <th scope="col">Submission date</th>
                        <th scope="col">Application</th>
                        <th scope="col">Inspection</th>
                        <th scope="col">Certificate</th>
                        <th scope="col">Certificate status</th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php ApplicationList($guid, $con); ?>
                    </tbody>
                  </table>

                </div>
          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns *****************PK************************ -->
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <!-- PK: no need for footer for this page
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>DOA</span></strong>. All Rights Reserved
    </div>
  </footer>
  -->
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>