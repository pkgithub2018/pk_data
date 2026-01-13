<?php 
// session_start(); // Commented out for cloud server compatibility

// Session configuration for cloud environment
// NOT WORKING FOR CLOUD server
/*
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');

// Security headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
*/
 
// Check if a language is selected via the query parameter
// $_SESSION['lang'] = 'en'; // NOT WORKING -FIX- Default to English
/*
NOT WORKING FOR NOW - FOR CLOUD SERVER
if (isset($_GET['lang'])) {
  $selectedLang = $_GET['lang'];
  $_SESSION['lang'] = $selectedLang; // Store the selected language in the session
} else {
  // Default to English if no language is selected
  if (!isset($_SESSION['lang'])) {
      $_SESSION['lang'] = 'en';
  }
}
*/

// Fixed language loading for cloud server compatibility
$lang = 'en'; // Default to English
if (isset($_SESSION['lang']) && !empty($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} elseif (isset($_GET['lang']) && !empty($_GET['lang'])) {
    $lang = $_GET['lang'];
}

// Include the appropriate language file
$langFile = "php-bin/lang_" . $lang . ".php";
if (file_exists($langFile)) {
    $translations = include($langFile);
} else {
    // Fallback translations if file doesn't exist
    $translations = array(
        'dashboard' => 'Dashboard',
        'Dashboard' => 'Dashboard'
    );
}

// connection to database
 require("php-bin/connection.php");
 require("php-bin/supports.php");

 // echo "<script>alert('Login-Updated with User id: " . (isset($_GET["uid"]) ? $_GET["uid"] : 'not set') . "');</script>"; 

 // Dynamic Authentication System - same as entity.php
 $userid = '';

 // Try multiple sources for userid (Dynamic Authentication System)
 // First, try to get from GET parameter (most reliable for sessionless)
 if (isset($_GET["uid"]) && !empty($_GET["uid"])) { // GET from 
   $userid = $_GET["uid"];
   echo "<!-- Debug: Got userid from GET: " . $userid . " -->";
   // Set cookie for future visits
   setcookie("ephyto_uid", $userid, time() + (86400 * 30), "/"); // 30 days
 }
 // Try to get from POST parameter (form submissions)
 elseif (isset($_POST["uid"]) && !empty($_POST["uid"])) {
   $userid = $_POST["uid"];
   echo "<!-- Debug: Got userid from POST uid: " . $userid . " -->";
 }
 elseif (isset($_POST["huid"]) && !empty($_POST["huid"])) {  // hidden input from forms_application in transaction.php
   $userid = $_POST["huid"];
   echo "<!-- Debug: Got userid from POST huid: " . $userid . " -->";
 }
 // Try to get from cookies if set
 elseif (isset($_COOKIE["ephyto_uid"]) && !empty($_COOKIE["ephyto_uid"])) {
   $userid = $_COOKIE["ephyto_uid"];
   echo "<!-- Debug: Got userid from COOKIE: " . $userid . " -->";
 }
 // Last resort: try to get from HTTP_REFERER if coming from other pages
 elseif (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
   $referer = $_SERVER['HTTP_REFERER'];
   if (preg_match('/[?&]uid=([^&]+)/', $referer, $matches)) {
     $userid = urldecode($matches[1]);
     echo "<!-- Debug: Got userid from REFERER: " . $userid . " -->";
   }
 }

 
 if (!empty($userid)) {
    // Get user data from database
    $userdata = Userdata($userid, $con);
    echo "<!-- Debug: Full userdata: " . print_r($userdata, true) . " -->";
    if ($userdata) {
        $username = $userdata['name'];
        $email = $userdata['email'];
        $position = $userdata['position'];
        $groupid = isset($userdata['group_id']) && !empty($userdata['group_id']) ? $userdata['group_id'] : '1'; // Default to group 1 if not set
        echo "<!-- Debug: Retrieved group_id from userdata: '" . (isset($userdata['group_id']) ? $userdata['group_id'] : 'NOT SET') . "' -->";
        echo "<!-- Debug: groupid variable set to: '" . $groupid . "' -->";
        $groupname = GroupName($userdata['group_id'], $con);
        
        // Get and store user profile image
        $uprofile = Profiledata($userid, $con);
        if (!$uprofile) {
            // Initialize profile if it doesn't exist
            InitializeProfile($userid, $con);
            $uprofile = Profiledata($userid, $con);
        }

        // Debug alert to show actual profile data
        if ($uprofile && is_array($uprofile)) {
            $imgpath_value = isset($uprofile['imgfilepath']) ? $uprofile['imgfilepath'] : 'NOT SET';
           // echo "<script>alert('User profile data: " . addslashes($imgpath_value) . "');</script>";
            
            // Show all profile data for debugging - properly escaped for JavaScript
           // $profile_debug = "Profile keys: " . implode(", ", array_keys($uprofile));
           // echo "<script>console.log(" . json_encode($profile_debug) . ");</script>";
        } else {
            echo "<script>alert('User profile: NO PROFILE DATA FOUND');</script>";
        }
        
        if ($uprofile && isset($uprofile['imgfilepath']) && !empty($uprofile['imgfilepath']) && $uprofile['imgfilepath'] !== 'default_imgfilepath') {
            $uimage = $uprofile['imgfilepath'];
        } else {
            $uimage = 'assets/img/profile-img.jpg'; // default image if no profile or image
        }
    } else {
        $username = '';
        $position = '';
        $groupid = '';
        $groupname = '';
        $uimage = 'assets/img/profile-img.jpg';
    }
 } else {
    // No user ID provided, set defaults
    $username = '';
    $position = '';
    $groupid = '';
    $groupname = '';
    $uimage = 'assets/img/profile-img.jpg';
 }

 // Authentication check
 if(empty($userid)){
    echo "<script>alert('You are not logged in. Please log in to access this page.');</script>"; 
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
 }

 // Use group ID from user data
 $guid = $groupid;
 echo "<!-- Debug: groupid value: '" . $groupid . "' -->";
 echo "<!-- Debug: guid value: '" . $guid . "' -->";
 echo "<!-- Debug: is_numeric check: " . (is_numeric($guid) ? 'true' : 'false') . " -->";
 if(empty($guid) || !is_numeric($guid)){
    echo "<!-- Debug: Group ID invalid, setting to default value 1 -->";
    $guid = '1'; // Default to group 1 instead of 0
 }

 // echo "<script>alert('User group id: " . $guid . ". User image: " . $uimage . ". Profile data: " . ($uprofile ? 'found' : 'not found') . "');</script>";
 
 // CANCEL/DELETE Application
 if (isset($_GET['btn']) && $_GET['btn'] === 'cancelApp') {
   // Handle the cancellation logic here
    $appid = $_GET['appid']; // Get the application ID from the query parameter
   echo "<script>alert('Application cancelled successfully. ID: " . $appid . "');</script>";
    $del = DeleteApplication($appid, $con);
 }

 // SUBMIT/SAVE application by UPDATING tbapplication with the form data - CLICK ON SUBMIT BUTTON
    if (isset($_POST['btnsubApplication_save']) && ($_POST['btnsubApplication_save'] === "submit" || $_POST['btnsubApplication_save'] === "update")) {  // Submit from application form in transaction.php
        
       echo "<script>alert('Hello, submit application');</script>";

        $app_id = isset($_POST['app_id']) ? $_POST['app_id'] : ''; // hidden input
        echo "<script>alert('Application ID (hidden input): " . $app_id . "');</script>";
        
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

        $exporter_address = isset($_POST['exporter']) ? $_POST['exporter'] : '';  // exporter address
        $importerid = isset($_POST['importer_id']) ? $_POST['importer_id'] : ''; // hidden input for importer entity id
        
        // Ensure importerid is properly handled for database operations
        if ($importerid === '' || !is_numeric($importerid)) {
            $importerid = null;
        } else {
            $importerid = (int)$importerid;
        }
        
        // Handle importer info safely
        $importer_info = EntityImportInfo($importerid, $con);
        $importer_name = $importer_info ? $importer_info['title'] : '';
        $importer_address = $importer_info ? $importer_info['address'] : '';
       // $importer_address = isset($_POST['importer']) ? $_POST['importer'] : ''; // importer address

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
            'place_treatment_other'  => $place_treatment_other,
            'importerid'       => $importerid
        ];
        
        $result = ApplicationUpdate($app_id, $data, $con); // Update tbapplication with the form data
        if ($result) {
            echo "<script>alert('Application updated successfully!');</script>";
        } else {
            echo "<script>alert('Failed to update application. Please check the console for details.');</script>";
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

    // *************************** CERTIFICATE ***************************
    // SAVE certificate data - CLICK ON SAVE BUTTON in certificate form in transaction.php
    if (isset($_POST['btnSubmitCertificate'])) {
       
        $certificate_id = isset($_POST['certificate_id']) ? $_POST['certificate_id'] : ''; // hidden input
        $app_id = isset($_POST['appid_certificate']) ? $_POST['appid_certificate'] : ''; // hidden input from Certificate form
        $certificate_no = isset($_POST['certificate_no']) ? $_POST['certificate_no'] : '';
        $app_no = isset($_POST['application_no']) ? $_POST['application_no'] : '';
        $carbonpaper_id = isset($_POST['carbonpaper_id']) ? $_POST['carbonpaper_id'] : '';
        $approved_by = isset($_POST['approved_by']) ? $_POST['approved_by'] : '';
        $approver_position = isset($_POST['approver_position']) ? $_POST['approver_position'] : '';
        $place_issue = isset($_POST['place_issue']) ? $_POST['place_issue'] : '';
        $consignment_value = isset($_POST['consignment_value']) ? $_POST['consignment_value'] : '';
        $value_currency = isset($_POST['value_currency']) ? $_POST['value_currency'] : '';
        $additional_scientificname = isset($_POST['additional_scientificname']) ? $_POST['additional_scientificname'] : '';
        $additional_declaration = isset($_POST['additional_declaration']) ? $_POST['additional_declaration'] : '';
        $datetime_created = CertificateInfo($app_id, $con)['datetime_created']; // keep the original created date
        $datetime_updated = CertificateInfo($app_id, $con)['datetime_updated']; // keep the original updated date
        $created_uid = CertificateInfo($app_id, $con)['created_uid']; // keep the original created uid
        $updated_uid = CertificateInfo($app_id, $con)['updated_uid']; // update uid
        $date_issue = isset($_POST['date_issue']) ? $_POST['date_issue'] : null;
        $cert_status = CertificateInfo($app_id, $con)['certificate_status']; // keep the original certificate status
        $enabled = CertificateInfo($app_id, $con)['enabled']; // keep the original enabled status

        // These inputs are used for presentation on the form only - NOT saved in tbcertificate
        $import_country = isset($_POST['import_country']) ? $_POST['import_country'] : '';
        $import_entrypoint = isset($_POST['import_entrypoint']) ? $_POST['import_entrypoint'] : '';
        $export_entrypoint = isset($_POST['export_entrypoint']) ? $_POST['export_entrypoint'] : '';
        $exporter_name = isset($_POST['exporter_name']) ? $_POST['exporter_name'] : '';
        $importer_name = isset($_POST['importer_name']) ? $_POST['importer_name'] : '';
        $exporter_address = isset($_POST['exporter_address']) ? $_POST['exporter_address'] : '';
        $importer_address = isset($_POST['importer_address']) ? $_POST['importer_address'] : '';
       
         // Put them into $certificate_data array (keys = DB column names)

        $certificate_data = [
            'application_id' => $app_id,
            'certificate_no' => $certificate_no,
            'carbonpaper_id' => $carbonpaper_id,
            'approved_by' => $approved_by,
            'position_approved' => $approver_position,
            'place_issued' => $place_issue,
            'consignment_value' => $consignment_value,
            'value_currency' => $value_currency,
            'additional_scientificname' => $additional_scientificname,
            'additional_declaration' => $additional_declaration,
            'datetime_created' => $datetime_created,
            'datetime_updated' => $datetime_updated,
            'created_uid' => $userid,
            'updated_uid' => $userid,
            'gid' => $guid,
            'date_issued' => $date_issue,
            'certificate_status' => $cert_status,
            'enabled' => $enabled
        ];
       
         // SUBMIT AND UPDATE ARE THE SAME- certificate data because certificate no is generated when application is submitted
         if($_POST['btnSubmitCertificate'] === 'submit' || $_POST['btnSubmitCertificate'] === 'update'){
           $result = CertificateUpdate($certificate_id, $certificate_data, $con);
            if ($result) {
                $appid_for_cert = isset($_POST['appid_certificate']) ? $_POST['appid_certificate'] : 0;
                echo "<script>
                    if(confirm('Certificate data submit/updated successfully! Would you like to view and print the certificate?')) {
                        window.open('certificate_view.php?appid=" . intval($appid_for_cert) . "', '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes');
                    }
                </script>";
            } else {
                echo "<script>alert('Failed to update certificate data. Please try again.');</script>";
            }    
          }      
    } // end of if - submission of certificate form
?>
<!DOCTYPE html>
<html lang="<?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == 'la') ? 'lo' : 'en'; ?>">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo isset($translations['dashboard']) ? $translations['dashboard'] : 'Dashboard'; ?></title>
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
  <link href="stylecss/lang.css" rel="stylesheet">
 


  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 7 2025 with Bootstrap v5.3.5
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="<?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == 'la') ? 'lang-lao' : 'lang-en'; ?>">

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
            <img src="<?php echo $uimage; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $username; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $username; ?></h6>
              <span><?php echo $position; ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php?uid=<?php echo $userid; ?>">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php?uid=<?php echo $userid; ?>">
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
          <span><?php echo isset($translations['Dashboard']) ? $translations['Dashboard'] : 'Dashboard'; ?></span>
        </a>
      </li><!-- End Dashboard Nav -->
    
      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=export&uid=<?php echo $userid; ?>" >
          <i class="bi bi-box-arrow-up-right"></i>
          <span>Export entity</span>
        </a>
      </li><!-- End Export Entity Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=import&uid=<?php echo $userid; ?>" >
          <i class="bi bi-box-arrow-in-down" style="font-size: 1.2rem;"></i>
          <span>Import entity</span>
        </a>
      </li><!-- End Import Entity/Company form Nav -->

      <?php if($groupname == "admin"){ ?><!-- Admin group check -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Master data</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
         
          <li>
            <a href="masterdata.php?part=product&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span>Product</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=conveyance&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span>Conveyance</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=countries&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span>Countries</span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span>Districts</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=entitytype&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span>Entity_type</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=inspectionmethod&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span>Inspection Method</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=locations&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span>Locations</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=modules&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span>Module List</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=provinces&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span>Provinces</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=treatmentmethod&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span>Treatment Method</span>
            </a>
          </li>
        </ul>
      </li><!-- End Master Data Nav -->

      <?php } // End of Admin group check ?>

      <li class="nav-heading">Users' Management</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php?uid=<?php echo $userid; ?>">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=ugroup&uid=<?php echo $userid; ?>">
          <i class="bi bi-people"></i>
          <span>Users group</span>
        </a>
      </li><!-- End Users group -->

       <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=upermits&uid=<?php echo $userid; ?>">
          <i class="bi bi-shield-lock"></i>
          <span>Group permits</span>
        </a>
      </li><!-- End Permission: User Group and Module -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=userslist&uid=<?php echo $userid; ?>">
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

    <!-- Charts Section -->
    <section class="section">
      <div class="row">
        
        <!-- Chart 1: Application Status Chart -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Application Status <span>| This Month</span></h5>
              
              <!-- Donut Chart -->
              <div id="donutChart" style="min-height: 250px;" class="echart"></div>

              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  echarts.init(document.querySelector("#donutChart")).setOption({
                    tooltip: {
                      trigger: 'item'
                    },
                    legend: {
                      top: '5%',
                      left: 'center'
                    },
                    series: [{
                      name: 'Applications',
                      type: 'pie',
                      radius: ['40%', '70%'],
                      avoidLabelOverlap: false,
                      label: {
                        show: false,
                        position: 'center'
                      },
                      emphasis: {
                        label: {
                          show: true,
                          fontSize: '18',
                          fontWeight: 'bold'
                        }
                      },
                      labelLine: {
                        show: false
                      },
                      data: [
                        { value: 1048, name: 'Submitted' },
                        { value: 735, name: 'Under Review' },
                        { value: 580, name: 'Approved' },
                        { value: 484, name: 'Rejected' },
                        { value: 300, name: 'Pending' }
                      ]
                    }]
                  });
                });
              </script>
              <!-- End Donut Chart -->

            </div>
          </div>
        </div>

        <!-- Chart 2: Monthly Applications Trend -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Monthly Trends <span>| Last 6 Months</span></h5>

              <!-- Line Chart -->
              <div id="reportsChart" style="min-height: 250px;" class="echart"></div>

              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  echarts.init(document.querySelector("#reportsChart")).setOption({
                    tooltip: {
                      trigger: 'item'
                    },
                    legend: {
                      data: ['Applications', 'Certificates', 'Inspections']
                    },
                    toolbox: {
                      show: true,
                      feature: {
                        dataView: { show: true, readOnly: false },
                        magicType: { show: true, type: ['line', 'bar'] },
                        restore: { show: true },
                        saveAsImage: { show: true }
                      }
                    },
                    xAxis: {
                      type: 'category',
                      boundaryGap: false,
                      data: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep']
                    },
                    yAxis: {
                      type: 'value'
                    },
                    series: [{
                      name: 'Applications',
                      type: 'line',
                      data: [120, 132, 101, 134, 90, 230],
                      smooth: true
                    }, {
                      name: 'Certificates',
                      type: 'line',
                      data: [220, 182, 191, 234, 290, 330],
                      smooth: true
                    }, {
                      name: 'Inspections',
                      type: 'line',
                      data: [150, 232, 201, 154, 190, 330],
                      smooth: true
                    }]
                  });
                });
              </script>
              <!-- End Line Chart -->

            </div>
          </div>
        </div>

        <!-- Chart 3: Export Entities Performance -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Export Entities <span>| Performance</span></h5>

              <!-- Column Chart -->
              <div id="columnChart" style="min-height: 250px;" class="echart"></div>

              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  echarts.init(document.querySelector("#columnChart")).setOption({
                    tooltip: {
                      trigger: 'axis',
                      axisPointer: {
                        type: 'shadow'
                      }
                    },
                    legend: {},
                    grid: {
                      left: '3%',
                      right: '4%',
                      bottom: '3%',
                      containLabel: true
                    },
                    xAxis: [{
                      type: 'category',
                      data: ['Entity A', 'Entity B', 'Entity C', 'Entity D', 'Entity E', 'Entity F']
                    }],
                    yAxis: [{
                      type: 'value'
                    }],
                    series: [{
                      name: 'Applications',
                      type: 'bar',
                      emphasis: {
                        focus: 'series'
                      },
                      data: [320, 302, 301, 334, 390, 330]
                    }, {
                      name: 'Certificates',
                      type: 'bar',
                      emphasis: {
                        focus: 'series'
                      },
                      data: [120, 132, 101, 134, 90, 230]
                    }]
                  });
                });
              </script>
              <!-- End Column Chart -->

            </div>
          </div>
        </div>

      </div>
    </section><!-- End Charts Section -->

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
                     <?php ApplicationList($guid, $con, $userid); ?>
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