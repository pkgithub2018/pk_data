<?php
// AJAX endpoint for deleting pest detection data - MUST be at the top
if (isset($_POST['action']) && $_POST['action'] == 'delete_pest_detected') {
    // Include database connection and functions
    require("php-bin/connection.php");
    require("php-bin/supports.php");
    
    // Clean any output buffers to prevent issues
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    // Debug: Log the incoming request
    error_log("Delete pest detected request received. POST data: " . print_r($_POST, true));
    
    $appid = isset($_POST['appid']) ? (int)$_POST['appid'] : 0;
    
    if ($appid > 0) {
        try {
            // Escape the application ID for security
            $appid_escaped = pg_escape_string($con, (string)$appid);
            
            // Delete from tbpest_detected table
            $delete_sql = "DELETE FROM tbpest_detected WHERE application_id = '$appid_escaped'";          
            $delete_result = pg_query($con, $delete_sql);
            
            if ($delete_result) {
                $affected_rows = pg_affected_rows($delete_result);
                // Update on tbinspection to set pest_detected = '0'- to be consistent
                $sql_update_inspection = "UPDATE tbinspection SET pest_detected = '0' WHERE application_id = '$appid_escaped'";
                $update_result = pg_query($con, $sql_update_inspection);
                
                if ($update_result) {
                    $inspection_affected = pg_affected_rows($update_result);
                    error_log("Delete successful. Pest table rows: $affected_rows, Inspection table rows: $inspection_affected");
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Pest detection data deleted successfully', 
                        'affected_rows' => $affected_rows,
                        'inspection_updated' => true,
                        'refresh_checkbox' => true // Signal frontend to refresh checkbox
                    ]);
                } else {
                    error_log("Inspection table update failed: " . pg_last_error($con));
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Pest data deleted but inspection update failed', 
                        'affected_rows' => $affected_rows,
                        'warning' => 'Inspection table not updated'
                    ]);
                }
            } else {
                $error = pg_last_error($con);
                error_log("Delete failed. Database error: " . $error);
                echo json_encode(['success' => false, 'error' => 'Database error: ' . $error]);
            }
        } catch (Exception $e) {
            error_log("Delete exception: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        error_log("Delete failed: Invalid application ID: " . $appid);
        echo json_encode(['success' => false, 'error' => 'Invalid application ID']);
    }
    
    exit;
}

// AJAX endpoint for toggling pest detection status

if (isset($_POST['action']) && $_POST['action'] == 'pest_detected_inspectionSave') {
  
    // Include database connection and functions
    require("php-bin/connection.php");
    require("php-bin/supports.php");
        
   // header('Content-Type: application/json');
   // header('Cache-Control: no-cache, must-revalidate');
   // header('Pragma: no-cache');
       
    $appid = isset($_POST['appid']) ? (int)$_POST['appid'] : 0;
    $inspectiondate = isset($_POST['inspection_date']) ? pg_escape_string($con, $_POST['inspection_date']) : '';
    $sampleno = isset($_POST['sampleno']) ? pg_escape_string($con, $_POST['sampleno']) : '';
    $samplequantity = isset($_POST['samplequantity']) ? pg_escape_string($con, $_POST['samplequantity']) : '';
    $unitid = isset($_POST['unitid']) ? pg_escape_string($con, $_POST['unitid']) : '';
    $samplecollectedby = isset($_POST['samplecollectedby']) ? pg_escape_string($con, $_POST['samplecollectedby']) : '';
    $inspectedby = isset($_POST['inspectedby']) ? pg_escape_string($con, $_POST['inspectedby']) : '';
    $certificatefee = isset($_POST['certificatefee']) ? pg_escape_string($con, $_POST['certificatefee']) : '';
    $receiptno = isset($_POST['receiptno']) ? pg_escape_string($con, $_POST['receiptno']) : '';
    $lotno = isset($_POST['lotno']) ? pg_escape_string($con, $_POST['lotno']) : '';
    $inspectionmethod = isset($_POST['inspectionmethod']) ? pg_escape_string($con, $_POST['inspectionmethod']) : '';

    $treatmentdate = date('Y-m-d'); // current date -temperary

    $dataInspection = [
        'application_id' => $appid,
        'inspection_date' => $inspectiondate,
        'sample_no' => $sampleno,
        'sample_quantity' => $samplequantity,
        'unit_id' => $unitid,
        'sample_collected_by' => $samplecollectedby,
        'inspected_by' => $inspectedby,
        'certificate_fee' => $certificatefee,
        'receipt_no' => $receiptno,
        'lot_number' => $lotno,
        'inspection_method' => $inspectionmethod,
        'pest_detected' => 1,
        'treat_ability' => '0',
        'lab_required' => '0',
        'treatment_method' => 0,
        'treatment_date' => $treatmentdate,
        'chemical_used' => 'na',
        'chemical_fortreat' => 'na',
        'duration_temp' => '0',
        'concentration' => '0',
        'sample_inspectedby' => $inspectedby,
        'additional_info' => 'na',
        'treatment_reason' => 'na',
        'post_treatment_details' => 'na',
        'enabled' => 'yes'
    ];
   // Check if application id exists
   $appid_exist = 0;
   $Inspectdata = InspectionInfo($appid, $con);
   $appid_exist = $Inspectdata ? $Inspectdata['application_id'] : 0;

    if ($appid_exist === 0) {
        // echo "<script>console.log('PK-Debug application id>0: Received appid = $appid, inspection_date = $inspection_date, sampleno = $sampleno');</script>";
        $result = InspectionAdd($dataInspection, $con);
        if(result){
          echo "<script>console.log('Inspection data added successfully for application ID: $appid');</script>";
        } else {
         echo "<script>console.log('Data aleady exists for appid = $appid, inspection_date = $inspection_date, sampleno = $sampleno');</script>";
        } 
        
    } else {     
      //  error_log("Toggle pest failed: Invalid application ID: " . $appid);   
    }   
    exit;
} // close if condition for pest_detected_inspectionSave

// Save application data before going to multiple commodities - AJAX endpoint
if (isset($_POST['action']) && $_POST['action'] == 'save_application_before_multiple') {
    // Include database connection and functions
    require("php-bin/connection.php");
    require("php-bin/supports.php");
    
    // Clean any output buffers to prevent issues
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    $appid = $_POST['appid'] ?? '';
    $app_date = $_POST['app_date'] ?? '';
    $applicant_name = $_POST['applicant_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $reg_no = $_POST['reg_no'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $entry_point = $_POST['entry_point'] ?? '';
    $import_country = $_POST['import_country'] ?? '';
    $import_point = $_POST['import_point'] ?? '';
    $export_certificate = $_POST['export_certificate'] ?? '0';
    $transit_certificate = $_POST['transit_certificate'] ?? '0';
    $certificate_type ="";
    if($export_certificate == '1'){
        $certificate_type ="export";
    } else if($transit_certificate == '1'){
        $certificate_type ="transit";
    } 
    
    // Validate appid
    if (empty($appid) || !is_numeric($appid)) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid application ID'
        ]);
        exit;
    }
    
    try {
        // Update tbapplication with the form data
        $sql = "UPDATE tbapplication SET             
                    contact_person = '". $applicant_name . "',
                    address_person = '". $address . "',
                    reg_no = '". $reg_no . "',
                    phone = '". $phone . "',
                    export_point = $entry_point,
                    country_import = $import_country,
                    import_point = '". $import_point . "',
                    certificate_type = '". $certificate_type . "'     
                WHERE id = $appid";
        
        $result = pg_query($con, $sql);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Application data saved successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to update application: ' . pg_last_error($con)
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Error updating application: ' . $e->getMessage()
        ]);
    }
    
    exit;
}

// Delete multiple transactions - AJAX endpoint
if(isset($_POST['action']) && $_POST['action'] == 'delete_multiple_commodities') {
    // Include database connection and functions
    require("php-bin/connection.php");
    require("php-bin/supports.php");
    
    // Clean any output buffers to prevent issues
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    $appid = isset($_POST['appid']) ? (int)$_POST['appid'] : 0;
    
    if (!empty($appid)) {
        // Update the application to set multiple_products = 'no'
        $appid_escaped = pg_escape_string($con, (string)$appid);

        // Delete data from tbmultiple_product based on application id
        $appid_escaped = pg_escape_string($con, (string)$appid);
        $deleteSql = "DELETE FROM tbmultiple_product WHERE application_id = '$appid_escaped'";
        
        try {
            $deleteResult = pg_query($con, $deleteSql);
            if ($deleteResult) {
                $affectedRows = pg_affected_rows($deleteResult);
                $updateSql = "UPDATE tbapplication SET multi_item = '0' WHERE id = '$appid_escaped'";
                $updateResult = pg_query($con, $updateSql);
                if ($updateResult) {
                    $updateAffected = pg_affected_rows($updateResult);
                } else {
                    $updateAffected = 0;
                }
                echo json_encode(['success' => true, 'message' => 'Transactions deleted successfully', 'affected_rows' => $affectedRows]);
            } else {
                $error = pg_last_error($con);
                echo json_encode(['success' => false, 'error' => 'Database error: ' . $error]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No transaction IDs provided']);
    }
    
    exit;
} // End of delete_multiple_transactions

// Include database connection and functions
require("php-bin/connection.php");
require("php-bin/supports.php");
/* NOT WORKING FOR NOW - FOR CLOUD SERVER
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
*/
// Dynamic Authentication System - same as entity.php and main.php
$userid = '';
// Try multiple sources for userid (Dynamic Authentication System)
// First, try to get from GET parameter (most reliable for sessionless)
if (isset($_GET["uid"]) && !empty($_GET["uid"])) {
  $userid = $_GET["uid"]; // GET from URL in EntityExportList function in supports.php
}
// Try to get from POST parameter (form submissions)
elseif (isset($_POST["uid"]) && !empty($_POST["uid"])) {
  $userid = $_POST["uid"];
}
elseif (isset($_POST["huid"]) && !empty($_POST["huid"])) {
  $userid = $_POST["huid"];
}
// Try to get from cookies if set
elseif (isset($_COOKIE["ephyto_uid"]) && !empty($_COOKIE["ephyto_uid"])) {
  $userid = $_COOKIE["ephyto_uid"];
}
// Last resort: try to get from HTTP_REFERER if coming from other pages
elseif (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
  $referer = $_SERVER['HTTP_REFERER'];
  if (preg_match('/[?&]uid=([^&]+)/', $referer, $matches)) {
    $userid = urldecode($matches[1]);
    
  }
}
// Authentication check
if(empty($userid)){
    // If user ID is not set, redirect to login page
    echo "<script>alert('Dynamic Authentication Required. Please log in to access this page.');</script>"; 
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}
// Language handling for UI (mirror main.php)
if (session_status() !== PHP_SESSION_ACTIVE) {
    @session_start();
}
$lang = 'en';
if (isset($_SESSION['lang']) && !empty($_SESSION['lang'])) {
  $lang = $_SESSION['lang'];
} elseif (isset($_GET['lang']) && !empty($_GET['lang'])) {
  $lang = $_GET['lang'];
} elseif (isset($_POST['hlang']) && !empty($_POST['hlang'])) {
  $lang = $_POST['hlang'];
}

// Persist selected language in session
if (session_status() !== PHP_SESSION_ACTIVE) {
    @session_start();
}
$_SESSION['lang'] = $lang;

// Include the appropriate language file

$langFile = "php-bin/lang_" . $lang . ".php";
if (file_exists($langFile)) {
    $translations = include($langFile);
} else {
    // Fallback minimal translations to avoid notices
    $translations = array(
        'dashboard' => 'Dashboard',
        'Dashboard' => 'Dashboard',
        'Application' => 'Application',
        'Export entity' => 'Export entity',
        'Import entity' => 'Import entity',
        'Inspection' => 'Inspection',
        'Certificate' => 'Certificate',
        'Master data' => 'Master data'
    );
}

// Build language switch URLs preserving current query parameters
$__lang_params = $_GET;
if (!isset($__lang_params['uid']) || empty($__lang_params['uid'])) {
    $__lang_params['uid'] = isset($userid) ? $userid : '';
}
$__lang_params_la = $__lang_params; $__lang_params_la['lang'] = 'la';
$__lang_params_en = $__lang_params; $__lang_params_en['lang'] = 'en';
$langHrefLa = '?' . http_build_query($__lang_params_la);
$langHrefEn = '?' . http_build_query($__lang_params_en);
// Link back to main.php preserving uid and current lang
$mainParams = ['uid' => isset($userid) ? $userid : '', 'lang' => $lang];
$mainHref = 'main.php?' . http_build_query($mainParams);
// Hidden field for forms to preserve language
$hiddenLangField = '<input type="hidden" name="hlang" id="hlang" value="' . htmlspecialchars($lang) . '">';

// Validate userid is numeric before using it
if (!is_numeric($userid)) {
    echo "<script>alert('Invalid user ID format: " . htmlspecialchars($userid) . ". Please log in again.');</script>"; 
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}

// User data
$userdata = Userdata($userid, $con);
if (!$userdata) {
    echo "<script>alert('User data not found. Please log in again.');</script>"; 
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}
$loginuser = $userdata['name']; // User name
$guid = $userdata['group_id'];
$position = $userdata['position'];       
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
// AJAX endpoint for importer name search
if (isset($_POST['action']) && $_POST['action'] == 'search_importer') {
    // Debug: Log the request
    error_log("Search request received for term: " . $_POST['term']);   
    $searchTerm = pg_escape_string($con, $_POST['term']);
    $sql = "SELECT title, address FROM tbentity_import 
            WHERE title ILIKE '%$searchTerm%' 
            ORDER BY title ASC 
            LIMIT 10";    
    // Debug: Log the SQL query
    error_log("SQL Query: " . $sql); 
    $result = pg_query($con, $sql);
    $importers = array();  
    if ($result && pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $importers[] = array(
                'title' => $row['title'],
                'address' => $row['address'],
                'full_text' => $row['title'] . ($row['address'] ? ', ' . $row['address'] : '')
            );
        }
    } 
    // Debug: Log the response
    error_log("Search results count: " . count($importers));
    
    header('Content-Type: application/json');
    echo json_encode($importers);
    exit;
}

?>
<!DOCTYPE html>
<html lang="<?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == 'la') ? 'lo' : 'en'; ?>">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Transaction</title>
  <meta content="" name="description">
  <meta content="" name="keywords"> 
  <!-- Ajax PK -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="jspk/users-validate.js"></script>  
  <script src="jspk/transaction-process.js"></script>
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
  <!--  CSS File- PK -->
  <link href="stylecss/scss.css" rel="stylesheet">
  <link href="stylecss/dformelement.css" rel="stylesheet">
  <link href="stylecss/lang.css" rel="stylesheet">
  <!-- Autocomplete CSS -->
  <style>
     .autocomplete-suggestions {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border: 1px solid #ddd;
      border-top: none;
      border-radius: 0 0 4px 4px;
      max-height: 200px;
      overflow-y: auto;
      z-index: 1000;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      display: none;
    }
    .autocomplete-suggestion {
      padding: 8px 12px;
      cursor: pointer;
      border-bottom: 1px solid #f0f0f0;
      font-size: 14px;
    } 
    .autocomplete-suggestion:hover,
    .autocomplete-suggestion.active {
      background-color: #f8f9fa;
    } 
    .autocomplete-suggestion:last-child {
      border-bottom: none;
    }
    .suggestion-title {
      font-weight: 500;
      color: #333;
    }
    .suggestion-address {
      font-size: 12px;
      color: #666;
      margin-top: 2px;
    }
  </style>
  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 7 2025 with Bootstrap v5.3.5
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>
<body class="<?php echo ($_SESSION['lang'] == 'la') ? 'lang-lao' : 'lang-en'; ?>">
  <!-- LANG DEBUG: lang=<?php echo htmlspecialchars($lang); ?> session=<?php echo isset($_SESSION['lang'])?htmlspecialchars($_SESSION['lang']):'none'; ?> hrefLa=<?php echo htmlspecialchars($langHrefLa); ?> hrefEn=<?php echo htmlspecialchars($langHrefEn); ?> -->
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block"><?php echo isset($translations['e-Phytosanitary']) ? $translations['e-Phytosanitary'] : 'e-Phytosanitary'; ?></span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
    <!--
    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div> End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <!-- Language Switcher -->
        <!--
        <li class="nav-item dropdown">
          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <img src="assets/img/flags/<?php echo ($lang === 'en') ? 'english' : 'lao'; ?>.png" alt="<?php echo ($lang === 'en') ? 'English' : 'ລາວ'; ?>" style="width: 24px; height: 16px;">
            <span style="font-size: 14px;"><?php echo ($lang === 'en') ? 'English' : 'ລາວ'; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo htmlspecialchars($langHrefLa); ?>">
                <img src="assets/img/flags/lao.png" alt="Lao" style="width: 24px; height: 16px; margin-right: 10px;">
                <span>ລາວ</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo htmlspecialchars($langHrefEn); ?>">
                <img src="assets/img/flags/english.png" alt="English" style="width: 24px; height: 16px; margin-right: 10px;">
                <span>English</span>
              </a>
            </li>
          </ul>
        </li>
    -->
    <!-- End Language Switcher -->

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?php echo $uimage; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $loginuser; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $loginuser; ?></h6>
              <span><?php echo $position; ?></span>
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
        <a class="nav-link collapsed" href="<?php echo htmlspecialchars($mainHref); ?>">
          <i class="bi bi-grid"></i>
          <span><?php echo isset($translations['Dashboard']) ? $translations['Dashboard'] : 'Dashboard'; ?></span>
        </a>
      </li><!-- End Dashboard Nav -->

       <li class="nav-item">
        <a class="nav-link collapsed" href="application.php?part=dashboard&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-file-earmark-text"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
          <span><?php echo isset($translations['Application']) ? $translations['Application'] : 'Application'; ?></span>
        </a>
      </li><!-- End Application Nav --> 
       <li class="nav-item">
        <a class="nav-link collapsed" href="inspection.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-journal-check"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
          <span><?php echo isset($translations['Inspection']) ? $translations['Inspection'] : 'Inspection'; ?></span>
        </a>
      </li><!-- End Inspection Nav --> 
      
       <li class="nav-item">
        <a class="nav-link collapsed" href="certificate.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-journal-album"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
          <span><?php echo isset($translations['Certificate']) ? $translations['Certificate'] : 'Certificate'; ?></span>
        </a>
      </li><!-- End Certificate Nav --> 

      <li class="nav-item">
        <a class="nav-link" href="entity.php?entity=export&uid=<?php echo $userid; ?>" >
          <i class="bi bi-box-arrow-up-right"></i>
          <span><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Export entity'; ?></span>
        </a>
      </li><!-- End Export Entity Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=import&uid=<?php echo $userid; ?>" >
          <i class="bi bi-box-arrow-in-down" style="font-size: 1.2rem;"></i>
          <span><?php echo isset($translations['Import entity']) ? $translations['Import entity'] : 'Import entity'; ?></span>
        </a>
      </li><!-- End Import Entity Nav -->

       <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span><?php echo isset($translations['Master data']) ? $translations['Master data'] : 'Master data'; ?></span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
           <li>
            <a href="masterdata.php?part=approvers&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Approvers']) ? $translations['Approvers'] : 'Approvers'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=conveyance&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Conveyance']) ? $translations['Conveyance'] : 'Conveyance'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=countries&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Countries']) ? $translations['Countries'] : 'Countries'; ?></span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Districts']) ? $translations['Districts'] : 'Districts'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=entitytype&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Entity Type']) ? $translations['Entity Type'] : 'Entity Type'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=inspectionmethod&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Inspection method']) ? $translations['Inspection method'] : 'Inspection method'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=locations&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Locations']) ? $translations['Locations'] : 'Locations'; ?></span>
            </a>
          </li>
           <li>
            <a href="masterdata.php?part=product&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Product']) ? $translations['Product'] : 'Product'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=productgroup&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Product Group']) ? $translations['Product Group'] : 'Product Group'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=productunit&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Product Unit']) ? $translations['Product Unit'] : 'Product Unit'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=provinces&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Provinces']) ? $translations['Provinces'] : 'Provinces'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=treatmentmethod&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Treatment Method']) ? $translations['Treatment Method'] : 'Treatment Method'; ?></span>
            </a>
          </li>
        </ul>
      </li><!-- End Master Data Nav -->
      
      <!-- Monitoring and Reporting -->
       <li class="nav-heading"><?php echo isset($translations['MONITORING AND REPORTING']) ? $translations['MONITORING AND REPORTING'] : 'MONITORING AND REPORTING'; ?></li>
        <li class="nav-item">
        <a class="nav-link collapsed" href="monitor_report.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bx bxs-file-find" style="font-size: 20px;"></i>
          <span><?php echo isset($translations['Certificate tracking']) ? $translations['Certificate tracking'] : 'Certificate tracking'; ?></span>
        </a>
      </li><!-- End Monitoring and Reporting Nav -->

      <li class="nav-heading"><?php echo isset($translations["USERS MANAGEMENT"]) ? $translations["USERS MANAGEMENT"] : "Users Management"; ?></li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-person"></i>
          <span><?php echo isset($translations['Profile']) ? $translations['Profile'] : 'Profile'; ?></span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=ugroup&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-people"></i>
          <span><?php echo isset($translations['Users group']) ? $translations['Users group'] : 'Users group'; ?></span>
        </a>
      </li><!-- End Users group -->

       <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=upermits&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-shield-lock"></i>
          <span><?php echo isset($translations['Group permits']) ? $translations['Group permits'] : 'Group permits'; ?></span>
        </a>
      </li><!-- End Permission: User Group and Module -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=userslist&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-person-plus"></i>
          <span><?php echo isset($translations['Users']) ? $translations['Users'] : 'Users'; ?></span>
        </a>
      </li>  
      <!-- pk**: End of User Admin-->
    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">
   <?php 
     // Export entity list  *******************
    if(isset($_GET['part']) && $_GET['part'] === 'exportentity_list') {
      echo "<script>document.title = 'Application';</script>";
    ?>
     <section class="section">
      <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Export entity'; ?></h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($mainHref); ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
            <li class="breadcrumb-item"><?php echo isset($translations['Tables']) ? $translations['Tables'] : 'Tables'; ?></li>
            <li class="breadcrumb-item"><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Export entity'; ?></li>
          </ol>
          </nav>
        </div>
        <div>
          <a href="entity.php?frm=newEntity_export" class="btn btn-success btn-sm" role="button">
            <i class="bi bi-plus-circle"></i> Add New Export Entity
          </a>
        </div>
      </div><!-- End Page Title - Users list -->
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Export entity'; ?></h5>
              <p>ePhytosanitary by Department of Agriculture, MAF - Export entity</p>

              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts" >
                <thead>
                  <tr>
                   <th>
                      <b>D</b>ate
                    </th>
                    <th>
                      <b>A</b>pplication No
                    </th>
                    <th>Exporter</th>
                    <th style="white-space: nowrap;">Contact person</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Importer</th>
                    <th>Edit</th>
                    <th>Overall status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php EntityExportList($con, $guid, $userid); ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>
        </div>
      </div>
    </section>
    <?php
    }   // End of Export Entity/
  ?> 
   <!-- Application form  -->
   <?php
   // EXPORT ENTITY/COMPANY-FORM  *******************
     if (isset($_GET['part']) && $_GET['part'] === 'application') {
          // Initialize variables with default values
          $reg_no = '';
          $phone = '';
          $contact_person = '';
          $address = '';
          
          if( isset($_GET['id']) && !empty($_GET['id'])) { // ExporterID -link <a> from Export entity form
            //$_GET['id'] is exporter ID
            $uid = $userid;  // from $_SESSION
           // $guidLogin = $guid; // from $_SESSION
          
            // Application Number will generated in FUNCTION: ApplicationNo
            // Application ID (id - auto_increment ($app_id)) along with USER'S ID, are INSERTED INTO tbapplication
            // $app_no - FULL APPLICATION NUMBER
          list($app_id, $app_no) = ApplicationNo($_GET['id'], $uid, $con);
        // }

          // Application information
          // $_GET['id'] is exporter ID from URL in function: EntityExportList() in supports.php
          //if(isset($_GET['id']) && !empty($_GET['id'])) {
            $exporter_id = $_GET['id'];
            $appdate = date('Y-m-d');  // initial application date
            
            // Ensure guid is properly formatted as integer
            $guid_value = is_numeric($guid) ? $guid : 0;
            
            // UPDATE tbapplication with exporter ID
            $sqlupdate = "UPDATE tbapplication SET company_id = '$exporter_id', application_no = '$app_no', application_date = '$appdate', guid = '$guid_value' WHERE id = '$app_id'";
            pg_query($con, $sqlupdate) or die(pg_last_error($con));

            $app_info = ApplicantInfo_Export($exporter_id, $con);
            if($app_info) {  
              $address = isset($app_info['address']) ? $app_info['address'] : '';
              $contact_person = isset($app_info['contact_name']) ? $app_info['contact_name'] : '';
              $phone = isset($app_info['phone']) ? $app_info['phone'] : '';
            }
          }

          // Product/Commodity ID - this is the product ID from Modal form- the URL: in ApplicationProductList($con)
          if(isset($_GET['comd_id']) && !empty($_GET['comd_id'])) {
            $pid = $_GET['comd_id'];
            $proData = ProductInfo($pid, $con);
            if($proData) {
              $proName = isset($proData['name']) ? $proData['name'] : '';
              $product_type = isset($proData['product_type']) ? $proData['product_type'] : '';
            }
          }

      // Set Application date ****
      if (!isset($date) || empty($date)) {
          $date = date('Y-m-d'); // Store as Y-m-d: for display only
      }

      // EDIT/UPDATE Application *********
        if(isset($_GET['appid_edit'])) {  // from ApplicationList function in supports.php
         // echo "Editing Application ID: " . htmlspecialchars($_GET['appid_edit']);
          $btnSubmit = "update";
          $appEdit_id = is_numeric($_GET['appid_edit']) && $_GET['appid_edit'] > 0 ? (int)$_GET['appid_edit'] : null;  // Integer - From function: ApplicationList

          $app_rows = null;
          if($appEdit_id !== null) {
              $app_rows = ApplicationInfo($appEdit_id, $con);
              if ($app_rows) {
                  // Populate form fields with existing application data
                  $app_no = isset($app_rows['application_no']) ? $app_rows['application_no'] : '';  // application No, not ID
                  $date = isset($app_rows['application_date']) ? $app_rows['application_date'] : '';
                  $reg_no = isset($app_rows['reg_no']) ? $app_rows['reg_no'] : '';
                  $contact_person = isset($app_rows['contact_person']) ? $app_rows['contact_person'] : '';
                  $address = isset($app_rows['address_person']) ? $app_rows['address_person'] : '';
                  $phone = isset($app_rows['phone']) ? $app_rows['phone'] : '';
                  $locid = isset($app_rows['export_point']) ? $app_rows['export_point'] : '';
                  $countryid = isset($app_rows['country_import']) ? $app_rows['country_import'] : '';
                  $import_point = isset($app_rows['import_point']) ? $app_rows['import_point'] : '';
                  $certificate_type = isset($app_rows['certificate_type']) ? $app_rows['certificate_type'] : '';
                  if($certificate_type == 'export') {
                      $export_certificate = true;
                      $transit_certificate = false;
                  } else if($certificate_type == 'transit') {
                      $export_certificate = false;
                      $transit_certificate = true;
                  }
                  $multiple_commodities = isset($app_rows['multi_item']) ? $app_rows['multi_item'] : 0;
                  $support_document = isset($app_rows['print_support']) ? $app_rows['print_support'] : 0;
                  $product_id = isset($app_rows['commodity_id']) ? $app_rows['commodity_id'] : '';
                  $prorows = ProductInfo($product_id, $con);
                  $proName = isset($prorows['name']) ? $prorows['name'] : '';
                  $scientific_name = isset($app_rows['name_scientific']) ? $app_rows['name_scientific'] : '';
                  $number_description = isset($app_rows['commodity_description']) ? $app_rows['commodity_description'] : '';
                  $nquantity = isset($app_rows['quantity_net']) ? $app_rows['quantity_net'] : '';
                  $gquantity = isset($app_rows['quantity_gross']) ? $app_rows['quantity_gross'] : '';
                  $unitid = isset($app_rows['unit_id']) ? $app_rows['unit_id'] : '';
                  $distinguishing_marks = isset($app_rows['marks_item']) ? $app_rows['marks_item'] : '';
                  $countryid_origin = isset($app_rows['place_origin']) ? $app_rows['place_origin'] : '';
                  $conveyanceid = isset($app_rows['conveyance_id']) ? $app_rows['conveyance_id'] : '';
                  $conveyance_sign = isset($app_rows['conveyance_sign']) ? $app_rows['conveyance_sign'] : '';
                  $exporter_address = isset($app_rows['address_exporter']) ? $app_rows['address_exporter'] : '';
                  $importer_address = isset($app_rows['address_importer']) ? $app_rows['address_importer'] : '';
                  $purposeid = isset($app_rows['purpose']) ? $app_rows['purpose'] : '';
                  $provinceid_quarantine = isset($app_rows['place_quarantine']) ? $app_rows['place_quarantine'] : '';
                  $provinceid_treatment = isset($app_rows['place_treatment']) ? $app_rows['place_treatment'] : '';
                  $place_of_quarantine_other = isset($app_rows['place_quarantine_other']) ? $app_rows['place_quarantine_other'] : '';
                  $place_of_treatment_other = isset($app_rows['place_treatment_other']) ? $app_rows['place_treatment_other'] : '';
                  $certificate_date = isset($app_rows['date_certificate']) ? $app_rows['date_certificate'] : '';

              }
          } // End of if - check Null
        }   // END of EDIT/UPDATE Application- isset($_GET['appid_edit'])   
      
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1><?php echo isset($translations['Application']) ? $translations['Application'] : 'Application'; ?></h1>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($mainHref); ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars('transaction.php?part=exportentity_list&uid='.$userid.'&lang='.$lang); ?>"><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Export entity'; ?></a></li>
          <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars('application.php?uid='.$userid.'&lang='.$lang); ?>"><?php echo isset($translations['Application list']) ? $translations['Application list'] : 'Application list'; ?></a></li>
        </ol>
        </div>
         <a href="main.php?btn=cancelApp&appid=<?php echo isset($app_id) ? $app_id : ''; ?>&uid=<?php echo $userid; ?>" class="btn btn-secondary btn-sm ms-3<?php echo (isset($btnSubmit) && $btnSubmit === 'update') ? ' disabled' : ''; ?>"
   <?php if (isset($btnSubmit) && $btnSubmit === 'update') echo 'tabindex="-1" aria-disabled="true" onclick="return false;"'; ?>>Cancel</a>
      </nav>
    </div><!-- End Application -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo isset($translations['Application Form']) ? $translations['Application Form'] : 'Application Form'; ?></h5>
               <!-- FORM: Entity/Company Form -->
              <form action="<?php echo htmlspecialchars($mainHref); ?>" method="POST">
                <!-- Hidden input to store application ID -->
                <input type="hidden" name="app_id" id="appid" value="<?php             
                                          if (!empty($app_id)) {
                                              echo $app_id;
                                          } elseif (isset($_GET['appid_edit']) && is_numeric($_GET['appid_edit']) && $_GET['appid_edit'] > 0) {
                                              echo (int)$_GET['appid_edit'];
                                          } else {
                                              echo '';
                                          } 
                                          ?>">
                <!-- Hidden input to preserve userid for dynamic authentication. $userid=$_GET['uid'] from EntityExportList function in supports.php -->
                <input type="hidden" name="huid" value="<?php echo $userid; ?>">
                <div class="row mb-3 align-items-center">
                  <!-- Application No -->
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Application No']) ? $translations['Application No'] : 'Application No'; ?></label>
                  <div class="col-sm-3">
                    <input type="text" name="application_no" id="application_no" class="form-control" value="<?php echo isset($app_no) ? $app_no : ''; ?>" readonly>
                  </div>
                  <!-- Application Date -->
                  <label class="col-sm-1 col-form-label"><?php echo isset($translations['Application date']) ? $translations['Application date'] : 'Date'; ?></label>
                  <div class="col-sm-2">
                    <input type="text" name="app_date" id="app_date" class="form-control" value="<?php echo date('d/m/Y', strtotime($date)); ?>" readonly>
                  </div>
                  <!-- Reg No -->
                  <label class="col-sm-1 col-form-label"><?php echo isset($translations['Reg No']) ? $translations['Reg No'] : 'Reg No'; ?></label>
                  <div class="col-sm-3">
                    <input type="text" name="reg_no" id="reg_no" class="form-control" value="<?php echo isset($reg_no) ? $reg_no : ''; ?>">
                  </div>
                </div>
                
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translations["Applicant Name"]) ? $translations["Applicant Name"] : "Applicant's Name"; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="applicant_name" id="applicant_name" class="form-control" value="<?php echo isset($contact_person) ? $contact_person : ''; ?>">
                  </div>
                </div>
                
                <div class="row mb-3">
                  <label for="inputPassword" class="col-sm-2 col-form-label"><?php echo isset($translations["Address"]) ? $translations["Address"] : "Address"; ?></label>
                  <div class="col-sm-10">
                    <textarea class="form-control" name="address" id="address" style="height: 100px"><?php echo isset($address) ? $address : ''; ?></textarea>
                  </div>
                </div>
                 <!-- Phone -->
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Phone"]) ? $translations["Phone"] : "Phone"; ?></label>
                  <div class="col-sm-4">
                    <input type="text" name="phone" id="phone" class="form-control"  value="<?php echo isset($phone) ? $phone : ''; ?>">
                  </div>
                </div>

                <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Export entry point"]) ? $translations["Export entry point"] : "Export entry point"; ?></label>
                  <div class="col-sm-10">
                    <select class="form-select" name="entry_point" id="entry_point" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectLocation($locid, $con); ?>
                    </select>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Import country"]) ? $translations["Import country"] : "Import country"; ?></label>
                  <div class="col-sm-4">
                    <select class="form-select" name="import_country" id="import_country" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectCountry($countryid, $con); ?>
                    </select>
                  </div>
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Import entry point"]) ? $translations["Import entry point"] : "Import entry point"; ?></label>
                  <div class="col-sm-4">
  <textarea class="form-control" name="import_point" id="import_point" rows="2" placeholder="Enter import entry point"><?php echo isset($import_point) ? $import_point : ''; ?></textarea>
</div>
                </div>

                <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Export certificate"]) ? $translations["Export certificate"] : "Export certificate"; ?></label>
                  <div class="col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="export_certificate" id="export_certificate" value="1" <?php echo (isset($export_certificate) && $export_certificate) ? 'checked' : ''; ?>>
                    <label for="export_certificate" class="ms-2 mb-0"><?php echo isset($translations["Yes"]) ? $translations["Yes"] : "Yes"; ?></label>
                  </div>
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Transit certificate"]) ? $translations["Transit certificate"] : "Transit certificate"; ?></label>
                  <div class="col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="transit_certificate" id="transit_certificate" value="1" <?php echo (isset($transit_certificate) && $transit_certificate) ? 'checked' : ''; ?>>
                    <label for="transit_certificate" class="ms-2 mb-0"><?php echo isset($translations["Yes"]) ? $translations["Yes"] : "Yes"; ?></label>
                  </div>
                </div>

 
                <div class="row mb-3">
                   <label class="col-sm-2 col-form-label"><?php echo isset($translations["Multiple commodities"]) ? $translations["Multiple commodities"] : "Multiple commodities"; ?></label>
                  <div class="col-sm-6 d-flex align-items-center">
                    <input type="checkbox" name="multiple_commodities" id="multiple_commodities" value="1" <?php echo (isset($multiple_commodities) && $multiple_commodities) ? 'checked' : ''; ?>>
                    <label for="multiple_commodities" class="ms-2 mb-0"><?php echo isset($translations["Yes"]) ? $translations["Yes"] : "Yes"; ?></label><span id="span_multiple" class="ms-3 text-muted">, <?php echo isset($translations["Please go to"]) ? $translations["Please go to"] : "Please go to"; ?> <a href="application.php?part=multiple_products&appid=<?php echo isset($_GET['appid_edit']) ? $_GET['appid_edit'] : $app_id; ?>&uid=<?php echo isset($userid) ? $userid : ''; ?>&lang=<?php echo isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en'; ?>" id="link_multiple_details">details</a></span>
                  </div>
                </div>
                
          <div class="row mb-3 align-items-center">
            <label class="col-sm-2 col-form-label"><?php echo isset($translations["Print supporting document"]) ? $translations["Print supporting document"] : "Print supporting document"; ?></label>
            <div class="col-sm-2 d-flex align-items-center">
              <input type="checkbox" name="support_document" id="support_document" value="1" <?php echo (isset($support_document) && $support_document) ? 'checked' : ''; ?>>
              <!--
              <a href="#" data-bs-toggle="modal" data-bs-target="#spdocModal">
                  <label for="support_document" class="ms-2 mb-0" style="cursor:pointer;">
                    <i class="bi bi-printer"></i>
                  </label>
              </a>
               -->
            </div>
          </div>
          <div class="row mb-3 align-items-center">
            <label class="col-sm-2 col-form-label"><?php echo isset($translations["Commodities"]) ? $translations["Commodities"] : "Commodities"; ?></label>
            <div class="col-sm-10 d-flex align-items-center">
             <a href="#" data-bs-toggle="modal" data-bs-target="#commodityModal">
                  <i class="bi bi-search ms-2" style="font-size: 1.2rem;"></i>
                </a>&nbsp;<input type="text" name="proname" id="proname" class="form-control" value="<?php echo isset($proName) ? $proName : ''; ?>">
                          <input type="hidden" name="proid" id="proid" value="<?php echo isset($pid) ? $pid : ''; ?>">
                          <button type="button" class="btn btn-primary btn-sm ms-1" style="height: 38px;" data-bs-toggle="modal" data-bs-target="#addcommodityModal">
              <i class="bi bi-plus-circle" style="font-size: 0.9rem;"></i>
            </button>
            </div>
          </div>
              
          <div class="row mb-3 align-items-center">
            <label for="name_oncertificate" class="col-sm-2 col-form-label"><?php echo isset($translations["Name on certificate"]) ? $translations["Name on certificate"] : "Name on certificate"; ?></label>
            <div class="col-sm-4">
              <input type="text" name="name_oncertificate" id="name_oncertificate" class="form-control" value="<?php echo isset($proName) ? $proName : ''; ?>">
            </div>
            <label for="scientific_name" class="col-sm-2 col-form-label"><?php echo isset($translations["Scientific Name"]) ? $translations["Scientific Name"] : "Scientific Name"; ?></label>
            <div class="col-sm-4">
              <input type="text" name="scientific_name" id="scientific_name" class="form-control" value="<?php echo isset($scientific_name) ? $scientific_name : ''; ?>">
            </div>
          </div>

          <div class="row mb-3">
            <label for="number_description" class="col-sm-2 col-form-label"><?php echo isset($translations["Number and description"]) ? $translations["Number and description"] : "Number and description"; ?></label>
            <div class="col-sm-10">
              <textarea name="number_description" id="number_description" class="form-control" rows="3"><?php echo isset($number_description) ? $number_description : ''; ?></textarea>
            </div>
          </div>

          <div class="row mb-3 align-items-center">
              <label for="nquantity" class="col-sm-2 col-form-label"><?php echo isset($translations["Net Quantity"]) ? $translations["Net Quantity"] : "Net Quantity"; ?></label>
            <div class="col-sm-2">
              <input type="number" step="0.01" min="0" name="nquantity" id="nquantity" class="form-control" value="<?php echo isset($nquantity) ? $nquantity : ''; ?>">
            </div>
            <label for="gquantity" class="col-sm-2 col-form-label"><?php echo isset($translations["Gross Quantity"]) ? $translations["Gross Quantity"] : "Gross Quantity"; ?></label>
            <div class="col-sm-2">
              <input type="number" step="0.01" min="0" name="gquantity" id="gquantity" class="form-control" value="<?php echo isset($gquantity) ? $gquantity : ''; ?>">
            </div>
              <label for="unit" class="col-sm-1 col-form-label"><?php echo isset($translations["Unit"]) ? $translations["Unit"] : "Unit"; ?></label>
            <div class="col-sm-3">
              <select name="unit" id="unit" class="form-select">
                <option value="">Select</option>
                <?php SelectUnit($unitid, $con); ?>
                <!-- Add more units as needed -->
              </select>
            </div>
            </div>
            <div class="row mb-3">
              <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translations["Distinguishing Marks"]) ? $translations["Distinguishing Marks"] : "Distinguishing Marks"; ?></label>
               <div class="col-sm-10">
                 <input type="text" name="marks" id="marks" class="form-control" value="<?php echo isset($distinguishing_marks) ? $distinguishing_marks : ''; ?>">
               </div>
             </div>
             <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Place of origin"]) ? $translations["Place of origin"] : "Place of origin"; ?></label>
                  <div class="col-sm-10">
                    <select class="form-select" name="place_origin" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectCountry($countryid_origin, $con); ?>
                    </select>
                  </div>
                </div>  
            <div class="row mb-3 align-items-center">
                <label class="col-sm-2 col-form-label"><?php echo isset($translations["Conveyance"]) ? $translations["Conveyance"] : "Conveyance"; ?></label>
                <div class="col-sm-4">
                  <select class="form-select" name="conveyance" id="conveyance" aria-label="Select packaging type">
                    <option value="">Select</option>
                    <?php SelectConveyance($conveyanceid, $con); ?> 
                  </select>
                </div>
                <label class="col-sm-2 col-form-label"><?php echo isset($translations["Conveyance Sign"]) ? $translations["Conveyance Sign"] : "Conveyance Sign"; ?></label>
                <div class="col-sm-4">
                  <input type="text" name="conveyance_sign" id="conveyance_sign" class="form-control" value="<?php echo isset($conveyance_sign) ? $conveyance_sign : ''; ?>">
                </div>
            </div>

            <div class="row mb-3 align-items-start">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Exporter address"]) ? $translations["Exporter address"] : "Exporter's address"; ?></label> <!-- Application -->
                  <div class="col-sm-4">
                      <textarea name="exporter" id="exporter" class="form-control" rows="3"><?php echo isset($exporter_address) ? $exporter_address : ''; ?></textarea>
                  </div>
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Importer address"]) ? $translations["Importer address"] : "Importer's address"; ?></label> <!-- Application -->
                  <div class="col-sm-4 position-relative">
                    <div class="d-flex align-items-start">
                      <a href="#" data-bs-toggle="modal" data-bs-target="#importerModal" class="me-2 mt-2">
                        <i class="bi bi-search" style="font-size: 1.2rem;"></i>
                      </a>
                      <textarea name="importer" id="importer" class="form-control" rows="3"><?php echo isset($importer_address) ? $importer_address : ''; ?></textarea>
                    </div>
                    <input type="hidden" name="importer_id" id="importer_id" value="<?php echo isset($importer_id) ? $importer_id : ''; ?>">
                    <div id="importer_suggestions" class="autocomplete-suggestions"></div>
                  </div>
            </div>
            

            <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Purpose"]) ? $translations["Purpose"] : "Purpose"; ?></label>
                  <div class="col-sm-4">
                    <select class="form-select" name="purpose" id="purpose" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectPurpose($purposeid, $con); ?>
                    </select>
                  </div>
                </div>

              <div class="row mb-3 align-items-center">
                <label class="col-sm-2 col-form-label"><?php echo isset($translations["Place of Quarantine"]) ? $translations["Place of Quarantine"] : "Place of Quarantine"; ?></label>
                <div class="col-sm-4">
                  <select class="form-select" name="place_quarantine" id="place_quarantine" aria-label="Select packaging type">
                    <option value="">Select</option>
                    <?php SelectProvince($provinceid_quarantine, $con); ?> 
                  </select>
                </div>
                <label class="col-sm-1 col-form-label"><?php echo isset($translations["Specify"]) ? $translations["Specify"] : "Specify"; ?></label>
                <div class="col-sm-5">
                  <input type="text" name="place_quarantine_other" id="place_quarantine_other" class="form-control" value="<?php echo isset($place_of_quarantine_other) ? $place_of_quarantine_other : ''; ?>">
                </div>
            </div>
           
             <div class="row mb-3 align-items-center">
                <label class="col-sm-2 col-form-label"><?php echo isset($translations["Place of treatment"]) ? $translations["Place of treatment"] : "Place of treatment"; ?></label>
                <div class="col-sm-4">
                  <select class="form-select" name="place_treatment" id="place_treatment" aria-label="Select packaging type">
                    <option value="">Select</option>
                    <?php SelectProvince($provinceid_treatment, $con); ?> 
                  </select>
                </div>
                <label class="col-sm-1 col-form-label"><?php echo isset($translations["Specify"]) ? $translations["Specify"] : "Specify"; ?></label>
                <div class="col-sm-5">
                  <input type="text" name="place_treatment_other" id="place_treatment_other" class="form-control" value="<?php echo isset($place_of_treatment_other) ? $place_of_treatment_other : ''; ?>">
                </div>
            </div>

                <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Certificate date"]) ? $translations["Certificate date"] : "Certificate date"; ?></label>
                    <div class="col-sm-4">
                      <input type="date" name="certificate_date" class="form-control" value="<?php echo isset($certificate_date) ? $certificate_date : ''; ?>">
                    </div>
                </div>
              
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">&nbsp;</label> 
                  <div class="col-sm-10 d-flex gap-2">
                    <button type="submit" name="btnsubApplication_save" class="btn btn-primary" value="<?php echo isset($btnSubmit) ? 'update' : 'submit'; ?>">
                      <?php echo isset($btnSubmit) ? 'Update' : 'Submit'; ?>
                    </button>
                    <!--
                    <button type="submit" name="btnsubApplication_save_continue" class="btn btn-secondary" value="save_continue" <?php echo (isset($btnSubmit) && $btnSubmit === 'update') ? 'disabled' : ''; ?>>
                      Save & continue
                    </button>
                    -->
                   </div>
                </div>
                    
                      <!-- Modal form for Importer ************** -->
                        <div class="modal fade" id="importerModal" tabindex="-1" aria-labelledby="importerModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="importerModalLabel">Search Importer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <!-- Search box above the table -->
                                <div class="mb-3">
                                  <input type="text" id="importerSearch" class="form-control" placeholder="Search importers...">
                                </div>
                                <!-- Data table for importer list -->
                                <div class="table-responsive">
                                  <table class="table table-bordered table-striped" id="importerTable">
                                    <thead>
                                      <tr>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Zip code</th>
                                        <th>Country</th>
                                        <th>Action</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php CertificateImporterList($con); ?>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                  <!-- End Modal form for Importer -->

              </form><!-- End Form for commodity -->
            </div>
          </div>

        </div>
      </div>
    </section>

      <!-- Modal form for Commodity ************** -->
      <div class="modal fade" id="commodityModal" tabindex="-1" aria-labelledby="commodityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="commodityModalLabel">Search Commodity</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <!-- Search box above the table -->
              <div class="mb-3">
                <input type="text" id="commoditySearch" class="form-control" placeholder="Search commodities...">
              </div>
              <!-- Data table for commodity list -->
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="commodityTable">
                  <thead>
                    <tr>
                      <th>Commodity Name</th>
                      <th>Scientific Name</th>
                      <th>Description</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php ApplicationProductList($con); ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
<!-- End Modal form for Commodity -->

<!-- Modal for Add New Product/commodity -->
<div class="modal fade" id="addcommodityModal" tabindex="-1" aria-labelledby="addcommodityModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="" method="POST" id="addCommodityForm">
        <div class="modal-header">
          <h5 class="modal-title" id="addcommodityModalLabel">Add New Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="product_code" class="form-label">Product Code</label>
            <input type="text" class="form-control" id="product_code" name="product_code" required>
          </div>
          <div class="mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="product_name" name="product_name" required>
          </div>
          <div class="mb-3">
            <label for="scientific_name" class="form-label">Scientific Name</label>
            <input type="text" class="form-control" id="scientific_name" name="scientific_name">
          </div>
          <div class="mb-3">
            <label for="hs_code" class="form-label">HS Code</label>
            <input type="text" class="form-control" id="hs_code" name="hs_code" required>
          </div>
          <div class="mb-3">
            <label for="product_group" class="form-label">Product Group</label>
            <select class="form-select" id="product_group" name="product_group" required>
              <option value="">*** Please select one ***</option>
              <?php SelectProductgroup($pgid, $con); ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="product_desc" class="form-label">Description</label>
            <textarea class="form-control" id="product_desc" name="product_desc" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success" id="saveProductBtn">Save</button> 
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="exporterModal" tabindex="-1" aria-labelledby="exporterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exporterModalLabel">Search Exporter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="exporterSearch" class="form-control mb-3" placeholder="Search exporter...">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="exporterTable">
            <thead>
              <tr>
                <th>Title</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Province</th>
                <th>District</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Example: Output all exporters (adjust query as needed)
              $res = pg_query($con, "SELECT * FROM tbentity_export WHERE created_guid='$guid' ORDER BY title ASC");
              while ($row = pg_fetch_assoc($res)) {
                $pid = $row['province'];
                $proname = Provincename($pid, $con);
                $did = $row['district'];
                $distname = Districtname($did, $con);

                $info = $row['title'] . "\n" . $row['address'] . "\n" . $row['phone'] . "\n" . $proname . ", " . $distname . ", Laos";
                $info_escaped = str_replace(array("\n", "\r", '"', "'"), array("\\n", "", "&quot;", "&#039;"), $info);
                echo "<tr>
                  <td>" . htmlspecialchars($row['title']) . "</td>
                  <td>" . htmlspecialchars($row['address']) . "</td>
                  <td>" . htmlspecialchars($row['phone']) . "</td>
                  <td>" . htmlspecialchars($proname) . "</td>
                  <td>" . htmlspecialchars($distname) . "</td>
                  <td>
                    <button type='button' class='btn btn-success btn-sm' onclick='selectExporter(\"" . $info_escaped . "\")'>Add</button>
                  </td>
                </tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Send data(exporter's address) to text area in main form
function selectExporter(info) {
  document.getElementById('exporter_address').value = info;
  // Close the modal
  var modal = bootstrap.Modal.getInstance(document.getElementById('exporterModal'));
  if (modal) modal.hide();
}

    window.addEventListener('DOMContentLoaded', function() {
      var applicationNo = document.querySelector('input[name="application_no"]');
          if (applicationNo) {
            applicationNo.focus();
          }

          // Modal for commodity - search and add
          const searchInput = document.getElementById('commoditySearch');
          const table = document.getElementById('commodityTable');
          if (searchInput && table) {
            searchInput.addEventListener('keyup', function() {
              const filter = searchInput.value.toLowerCase();
              const rows = table.querySelectorAll('tbody tr');
              rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
              });
            });
          }

      // ADD NEW COMMODITY/Product **************
      const addCommodityForm = document.getElementById('addCommodityForm');
      const saveProductBtn = document.getElementById('saveProductBtn');

        if (addCommodityForm && saveProductBtn) {
          saveProductBtn.addEventListener('click', function(e) {
            var namep = document.getElementById('product_name').value;
          // alert('Product Name: ' + namep);
          
            e.preventDefault();
            let form = document.getElementById('addCommodityForm');
            let formData = new FormData(form);

            fetch('transaction-dataprocess.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
              // console.log(data.message, 'Name:', data.name, 'Code:', data.code, 'Scientific:', data.scientific, 'Description:', data.desc, 'HS Code:', data.hs, 'Group:', data.group);
              var modalEl = document.getElementById('addcommodityModal');
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        // Optionally clear the form
                        addCommodityForm.reset();
              // alert(data.message + "\nName: " + data.name + "\nCode: " + data.code + "\nScientific: " + data.scientific + "\nDescription: " + data.desc + "\nHS Code: " + data.hs + "\nGroup: " + data.group);
              // Reload the commodity table body via AJAX
              $('#commodityTable tbody').load('transaction-productreload.php');
            })
            .catch(err => console.error('Error:', err));
          
          });
        }
      // Search/Filter exporter **************

      const exporterSearch = document.getElementById('exporterSearch');
      const exporterTable = document.getElementById('exporterTable');
      if (exporterSearch && exporterTable) {
        exporterSearch.addEventListener('keyup', function() {
          const filter = exporterSearch.value.toLowerCase();
          const rows = exporterTable.querySelectorAll('tbody tr');
          rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
          });
        });
      }

      // Search/Filter importer **************
      const importerSearch = document.getElementById('importerSearch');
      const importerTable = document.getElementById('importerTable');
      if (importerSearch && importerTable) {
        importerSearch.addEventListener('keyup', function() {
          const filter = importerSearch.value.toLowerCase();
          const rows = importerTable.querySelectorAll('tbody tr');
          rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
          });
        });
      }

  });

</script>
  <?php
    }  // End of Export Entity Form - $_GET['part'] === 'application'
  ?>
    <?php
     // IMPORT ENTITY/COMPANY FORM  *******************
    if(isset($_GET['entity']) && $_GET['entity'] == 'import') {   
   ?>
    <div class="pagetitle">
      <h1>Import Entity/Company</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($mainHref); ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item">Data input form</li>
          <li class="breadcrumb-item active">Import Entity/Company</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Import Entity/Company Data</h5>
              <!-- Import Entity/Company Form -->
              <form action="" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                  <label for="inputFile" class="col-sm-2 col-form-label">Select File</label>
                  <div class="col-sm-10">
                    <input type="file" name="import_file" class="form-control" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">&nbsp;</label> 
                  <div class="col-sm-10">
                    <button type="submit" name="btnimport" class="btn btn-primary">Import</button>
                  </div>
                </div>
              </form><!-- End Import Form -->
            </div>
          </div>

        </div>
      </div>
    </section>
    <?php
      }  // End of Import Entity/Company
     ?>
     <!-- ***************INSPECTION *************** -->
     <?php
      if (isset($_GET['part']) && $_GET['part'] === 'inspection') { // Open form -Get link from main.php - dashboard
        // Code for inspection part
        $appid_inspection = 0;

        if(isset($_GET['appid']) && $_GET['appid'] != ''){
           $appid_inspection = (int)$_GET['appid'];
        } else if(isset($_POST['happid']) && $_POST['happid'] != ''){
            $appid_inspection = (int)$_POST['happid']; // Hidden input from form submission-pest inspection
            // Process and save pest data here (add your save logic)
              $appid_pest = (int)$_POST['happid'];
              $pestid = (int)$_POST['hpestid'];
              $pestdetected_id = isset($_POST['pestdetected_id']) ? (int)$_POST['pestdetected_id'] : 0;
              $infestation_level = pg_escape_string($con, $_POST['infestation_level']);
              $alive_status = pg_escape_string($con, $_POST['alive_status']);
              $risk_category = pg_escape_string($con, $_POST['risk_category']);
              $inspection_result = "";
              if(isset($_POST['treatment'])){
                $inspection_result = "treatment";
              } else if(isset($_POST['return_original'])){
                $inspection_result = "return_original";
              } else if(isset($_POST['phytosanitary_requirements'])){
                $inspection_result = "phytosanitary_requirements";
              } else if(isset($_POST['other_conclusion'])){
                $inspection_result = "other_conclusion";
              }
           // SAVE/ADD data on pest detected and continue
            if(isset($_POST['save_continue_pest']) && $_POST['save_continue_pest'] == 'Save & Continue'){
              $resultSave = PestDetectedSave($appid_pest, $pestid, $infestation_level, $alive_status, $risk_category, $inspection_result, $con);
              if($resultSave){
                echo "<script>alert('Pest data saved successfully!');</script>";
                PestDetectedInspectionUpdate($appid_pest, $con);
              }
              echo "<script>window.location.href = 'transaction.php?part=inspection&inspect=View/Edit&appid=" . $appid_inspection . "&uid=" . $userid . "';</script>";
              exit();
              // UPDATE data on pest detected and continue
            } elseif(isset($_POST['save_continue_pest']) && $_POST['save_continue_pest'] == 'Update & Continue') {
              
              $resultUpdate = PestDetectedUpdate($pestdetected_id, $pestid, $infestation_level, $alive_status, $risk_category, $inspection_result, $con);
              if($resultUpdate){
                echo "<script>alert('Pest data updated successfully!');</script>";
              }
              echo "<script>window.location.href = 'transaction.php?part=inspection&inspect=View/Edit&appid=" . $appid_inspection . "&uid=" . $userid . "';</script>";
              exit();
            } // End of save and continue and update pest

            // CANCEL and return to inspection form
             if(isset($_POST['cancel_continue_pest'])){
              //echo "<script>alert('Returning to inspection form.');</script>";
              echo "<script>window.location.href = 'transaction.php?part=inspection&inspect=View/Edit&appid=" . $appid_inspection . "&uid=" . $userid . "';</script>";
              // Do nothing, just return to inspection form
              exit();
            }     // End of cancel
        } 

        // Initialize pest detection status based on database check
       /*
          $pest_detected_check_sql = "SELECT COUNT(*) as pest_count FROM tbpest_detected WHERE application_id = '$appid_inspection'";
          $pest_detected_result = pg_query($con, $pest_detected_check_sql);
          if($pest_detected_result && pg_fetch_assoc($pest_detected_result)['pest_count'] > 0){
            $pest_detected_checked = true;
          } else {
            $pest_detected_checked = false;
          }
        */
        // End of processing pest data

        if($_GET['inspect'] == 'Add'){
             // echo "<script>alert('Inspection - Add.');</script>";
                // Button state
                $btnSubmit = 'submit';   
                $approws = ApplicationInfo($appid_inspection, $con);
                if ($approws) {
                  $appno_inspection = $approws['application_no']; // Application No, not ID
                  $entity_id = $approws['company_id'];
                  $entity_rows = EntityExportInfo($entity_id, $con);
                  $entityexport_name = $entity_rows['title'];
                }
                
                // Initialize all inspection form fields with default empty values for new inspection
                $inspection_date = '';
                $sampleno = '';
                $sample_volume = '';
                $unitid = '';
                $sample_collectedby = '';
                $sample_inspected = '';
                $certificate_fee = '';
                $receipt_no = '';
                $lot_no = '';
                $inspection_method = '';
                $detected_pest = 0;
                $treatment_ability = 0;
                $lab_analysis = 0;
                $treatment_method = '';
                $treatment_date = '';
                $chemical_used = '';
                $chemical_fortreat = '';
                $duration_temp = '';
                $concentration = '';
                $sample_inspectedby = '';
                $additional_info = '';
                $reason = '';
                $post_details = '';
            } elseif ($_GET['inspect'] == 'View/Edit') {
               // $appid_inspection = isset($_GET['appid']) ? (int)$_GET['appid'] : 0; // Used the same variable from above
                $insprows = InspectionInfo($appid_inspection, $con);
                if ($insprows) {
                  // Button state
                  $btnSubmit = 'update';
                  // Populate inspection fields
                  $appid = $insprows['application_id'];
                  $appno_inspection = ApplicationInfo($appid, $con)['application_no'];
                  $entity_id = ApplicationInfo($appid, $con)['company_id'];
                  $entityexport_name = EntityExportInfo($entity_id, $con)['title'];
                  $inspection_date = $insprows['inspection_date'];
                  $sampleno = $insprows['sample_no'];
                  $sample_volume = $insprows['sample_quantity'];
                  $unitid = $insprows['unit_id'];
                  $sample_collectedby = $insprows['sample_collected_by'];
                  $sample_inspected = $insprows['inspected_by'];
                  $certificate_fee = $insprows['certificate_fee'];
                  $receipt_no = $insprows['receipt_no'];
                  $lot_no = $insprows['lot_number'];
                  $inspection_method = $insprows['inspection_method'];
                  $detected_pest = $insprows['pest_detected'];
                  $treatment_ability = $insprows['treat_ability'];
                  $lab_analysis = $insprows['lab_required'];
                  $treatment_method = $insprows['treatment_method'];
                  $treatment_date = $insprows['treatment_date'];
                  $chemical_used = $insprows['chemical_used'];
                  $chemical_fortreat = $insprows['chemical_fortreat'];
                  $duration_temp = $insprows['duration_temp'];
                  $concentration = $insprows['concentration'];
                  $sample_inspectedby = $insprows['sample_inspectedby'];
                  $additional_info = $insprows['additional_info'];
                  $reason = $insprows['treatment_reason'];
                  $post_details = $insprows['post_treatment_details'];
                } else {
                  // No inspection data found, default to submit
                  $btnSubmit = 'submit';
                }
        } else {
            // Invalid action
            echo "<div class='alert alert-danger'>Invalid action specified.</div>";
            exit;
        }
      ?>
     <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1><?php echo isset($translations['Inspection']) ? $translations['Inspection'] : 'Inspection'; ?></h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($mainHref); ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
            <li class="breadcrumb-item"><a href="application.php?part=dashboard&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Application']) ? $translations['Application'] : 'Application'; ?></a></li>
            <li class="breadcrumb-item active"><?php echo isset($translations['Inspection']) ? $translations['Inspection'] : 'Inspection'; ?></li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

<!-- ********* Inspection form *********** -->
<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><?php echo isset($translations['Inspection Form']) ? $translations['Inspection Form'] : 'Inspection Form'; ?></h5>
          <!-- FORM: Inspection Form -->
            <form id="inspectionFormID" action="<?php echo htmlspecialchars($mainHref); ?>" method="POST">
            <!-- Hidden input to store application ID -->
            <input type="hidden" name="appid" id="appid" value="<?php echo $appid_inspection; ?>">
            <!-- Hidden input to preserve userid for dynamic authentication -->
            <input type="hidden" name="uid" id="uid" value="<?php echo $userid; ?>">
            <input type="hidden" name="hlang" id="hlang" value="<?php echo $lang; ?>">

             <div class="row mb-3 align-items-center">
                  <!-- Application No -->
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Application No']) ? $translations['Application No'] : 'Application No'; ?></label>
                  <div class="col-sm-2">
                    <input type="text" name="appno_insp" id="appno_insp" class="form-control" style="background-color: #2ec691ff;" value="<?php echo isset($appno_inspection) ? $appno_inspection : ''; ?>" readonly >
                  </div>
                  <!-- Entity's name -->
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Entity Name"]) ? $translations["Entity Name"] : "Entity's Name"; ?></label>
                  <div class="col-sm-6">
                    <input type="text" name="entity_name" class="form-control" style="background-color: #f0f0f0;" value="<?php echo isset($entityexport_name) ? $entityexport_name : ''; ?>" readonly >
                  </div>  
                </div>

            <div class="row mb-3">
              <label for="inspection_date" class="col-sm-2 col-form-label"><?php echo isset($translations['Inspection date']) ? $translations['Inspection date'] : 'Inspection date'; ?></label>
              <div class="col-sm-4">
                <input type="date" class="form-control" id="inspection_date" name="inspection_date" value="<?php echo isset($inspection_date) ? $inspection_date : ''; ?>">
              </div>
            </div>
           
            <div class="row mb-3 align-items-center">
                <label for="sampleno" class="col-sm-2 col-form-label"><?php echo isset($translations['Number of sample']) ? $translations['Number of sample'] : 'Number of sample'; ?></label>
                <div class="col-sm-2">
                  <input type="text" name="sampleno" id="sampleno" class="form-control" value="<?php echo isset($sampleno) ? $sampleno : ''; ?>">
                </div>
                <label for="sample_volume" class="col-sm-2 col-form-label"><?php echo isset($translations['Sample Volume']) ? $translations['Sample Volume'] : 'Sample Volume'; ?></label>
                <div class="col-sm-2">
                  <input type="number" step="0.01" min="0" name="sample_volume" id="sample_volume" class="form-control" value="<?php echo isset($sample_volume) ? $sample_volume : ''; ?>">
                </div>
                <label for="unit" class="col-sm-1 col-form-label"><?php echo isset($translations['Unit']) ? $translations['Unit'] : 'Unit'; ?></label>
                <div class="col-sm-3">
                  <select name="unit" id="unit" class="form-select">
                    <option value="">Select</option>
                    <?php SelectUnit($unitid, $con); ?>
                    <!-- Add more units as needed -->
                  </select>
                </div>
            </div>
            <div class="row mb-3">
              <label for="sample_collectedby" class="col-sm-2 col-form-label"><?php echo isset($translations['Sample collected by']) ? $translations['Sample collected by'] : 'Sample collected by'; ?></label>
               <div class="col-sm-10">
                 <input type="text" name="sample_collectedby" id="sample_collectedby" class="form-control" value="<?php echo isset($sample_collectedby) ? $sample_collectedby : ''; ?>">
               </div>
             </div>
             <div class="row mb-3">
              <label for="sample_inspected" class="col-sm-2 col-form-label"><?php echo isset($translations['Inspected by']) ? $translations['Inspected by'] : 'Inspected by'; ?></label>
               <div class="col-sm-10">
                 <input type="text" name="sample_inspectedby" id="sample_inspectedby" class="form-control" value="<?php echo isset($sample_inspected) ? $sample_inspected : ''; ?>">
               </div>
             </div>

             <div class="row mb-3 align-items-center">
                  <!-- Certificate fee -->
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Certificate fee']) ? $translations['Certificate fee'] : 'Certificate fee'; ?></label>
                  <div class="col-sm-4">
                    <input type="number" name="certificate_fee" id="certificate_fee" class="form-control" value="<?php echo isset($certificate_fee) ? $certificate_fee : ''; ?>" >
                  </div>
                  <!-- Receipt No -->
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Receipt No']) ? $translations['Receipt No'] : 'Receipt No'; ?></label>
                  <div class="col-sm-4">
                    <input type="text" name="receipt_no" id="receipt_no" class="form-control" value="<?php echo isset($receipt_no) ? $receipt_no : ''; ?>" >
                  </div>  
              </div>

              <div class="row mb-3 align-items-center">
                  <!-- Lot Number -->
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Lot No']) ? $translations['Lot No'] : 'Lot No'; ?></label>
                  <div class="col-sm-2">
                    <input type="text" name="lot_no" id="lot_no" class="form-control" value="<?php echo isset($lot_no) ? $lot_no : ''; ?>" >
                  </div>
              </div>

              <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Inspection method']) ? $translations['Inspection method'] : 'Inspection Method'; ?></label>
                  <div class="col-sm-10">
                    <select class="form-select" name="inspection_method" id="inspection_method" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectInspectionMethod($inspection_method, $con); ?>
                    </select>
                  </div>
              </div>  

              <div class="row mb-3 align-items-center">
                <label class="col-sm-2 col-form-label"><?php echo isset($translations['Inspection Findings']) ? $translations['Inspection Findings'] : 'Inspection Findings'; ?></label>
                <div class="col-sm-10">
                  <div class="form-check mb-2">
                    <input class="form-check-input border border-success" type="checkbox" name="detected_pest" id="detected_pest" style="width: 1.5em; height: 1.5em;" value="1" <?php if (isset($detected_pest) && $detected_pest) echo 'checked'; ?>> <!-- onclick="pest_inspected();" -->
                     <label class="form-check-label" for="detected_pest">&nbsp;<?php echo isset($translations['Detected pest']) ? $translations['Detected pest'] : 'Detected pest'; ?>,</label>&nbsp;<span id="span_pest"><?php echo isset($translations['Please go to']) ? $translations['Please go to'] : 'Please go to'; ?>&nbsp;</span>&nbsp;&nbsp;<a id="link_pest_details" href="inspection.php?part=pest_detected&appid=<?php echo isset($_GET['appid']) ? $_GET['appid'] : ''; ?>&uid=<?php echo isset($userid) ? $userid : ''; ?>&lang=<?php echo isset($lang) ? $lang : 'en'; ?>" class="text-decoration-none"><i class='bi bi-box-arrow-right'></i>&nbsp;Details</a>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input border border-warning" type="checkbox" name="treatment_ability" id="treatment_ability" style="width: 1.5em; height: 1.5em;" value="1" <?php if (isset($treatment_ability) && $treatment_ability) echo 'checked'; ?>>
                    <label class="form-check-label" for="treatment_ability">&nbsp;<?php echo isset($translations['Treatment ability']) ? $translations['Treatment ability'] : 'Treatment ability'; ?></label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input border-primary" type="checkbox" name="lab_analysis" id="lab_analysis" style="width: 1.5em; height: 1.5em;" value="1" <?php if (isset($lab_analysis) && $lab_analysis) echo 'checked'; ?>>
                    <label class="form-check-label" for="lab_analysis">&nbsp;<?php echo isset($translations['Lab analysis required']) ? $translations['Lab analysis required'] : 'Lab analysis required'; ?></label>
                  </div>
                </div>
              </div>

          <div class="card mb-4" id="details_treatment"> <!-- details of treatment -->
              <div class="card-header">
                <strong><?php echo isset($translations['Details of treatment']) ? $translations['Details of treatment'] : 'Details of treatment'; ?></strong>
              </div>

              <div class="card-body">
                <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Treatment Method']) ? $translations['Treatment Method'] : 'Treatment Method'; ?></label>
                  <div class="col-sm-10">
                    <select class="form-select" name="treatment_method" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectTreatmentMethod($treatment_method, $con); ?>
                    </select>
                  </div>
                </div> 
              
                <div class="row mb-3 align-items-center">
                 <label for="treatment_date" class="col-sm-2 col-form-label"><?php echo isset($translations['Treatment date']) ? $translations['Treatment date'] : 'Treatment Date'; ?></label>
                  <div class="col-sm-4">
                    <input type="date" class="form-control" name="treatment_date" id="treatment_date"
                      value="<?php echo isset($treatment_date) && $treatment_date ? date('Y-m-d', strtotime($treatment_date)) : ''; ?>">
                    <?php if (!empty($treatment_date)) { ?>
                      <small class="text-muted">Selected: <?php echo date('d/m/Y', strtotime($treatment_date)); ?></small>
                    <?php } ?>
                  </div>

                    <label class="col-sm-2 col-form-label"><?php echo isset($translations['Treated by']) ? $translations['Treated by'] : 'Treated by'; ?></label>
                  <div class="col-sm-4">
                    <input type="text" class="form-control" name="chemical_fortreat" id="chemical_fortreat" value="<?php echo isset($chemical_fortreat) ? $chemical_fortreat : ''; ?>">
                  </div>
                </div>

                <div class="row mb-3">
                   <label class="col-sm-2 col-form-label"><?php echo isset($translations['Chemical Used']) ? $translations['Chemical Used'] : 'Chemical Used'; ?></label>
                  <div class="col-sm-4">
                    <input type="text" class="form-control" name="chemical_used" id="chemical_used" value="<?php echo isset($chemical_used) ? $chemical_used : ''; ?>">
                  </div>
                </div>

            <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Duration - Temperature']) ? $translations['Duration - Temperature'] : 'Duration - Temperature'; ?></label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="duration_temp" id="duration_temp" placeholder="e.g., 30 minutes - 50°C" value="<?php echo isset($duration_temp) ? $duration_temp : ''; ?>">
              </div>
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Concentration']) ? $translations['Concentration'] : 'Concentration'; ?></label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="concentration" id="concentration" placeholder="e.g., 0.5%" value="<?php echo isset($concentration) ? $concentration : ''; ?>">
              </div>
            </div>
            <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Sample Inspected by']) ? $translations['Sample Inspected by'] : 'Sample Inspected by'; ?></label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="sample_inspectedby" id="sample_inspectedby" value="<?php echo isset($sample_inspectedby) ? $sample_inspectedby : ''; ?>">
              </div>
            </div>   
          </div> 
        </div> <!-- End of details of treatment -->

             <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Additional information']) ? $translations['Additional information'] : 'Additional information'; ?></label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="additional_info" id="additional_info" placeholder="Enter additional information" value="<?php echo isset($additional_info) ? $additional_info : ''; ?>">
              </div>
            </div>
            <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Reason']) ? $translations['Reason'] : 'Reason'; ?></label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="reason" id="reason" placeholder="Enter reason" value="<?php echo isset($reason) ? $reason : ''; ?>">
              </div>
            </div>
            <div class="row mb-3 align-items-center">
            <label class="col-sm-2 col-form-label"><?php echo isset($translations['Post Treatment Details']) ? $translations['Post Treatment Details'] : 'Post Treatment Details'; ?></label>
            <div class="col-sm-10">
              <textarea class="form-control" name="post_details" id="post_details" rows="3" placeholder="Enter post treatment details"><?php echo isset($post_details) ? htmlspecialchars($post_details) : ''; ?></textarea>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-sm-10 offset-sm-2 d-flex gap-2">
              <button type="submit" name="btnSubmitInspection" value="<?php echo $btnSubmit === 'update' ? 'update' : 'submit'; ?>" class="btn btn-success">
                <i class="bi bi-save"></i><?php echo $btnSubmit === 'update' ? ' Update' : ' Submit'; ?>
              </button>
              <a href="<?php echo htmlspecialchars($mainHref); ?>" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancel
              </a>
            </div>
          </div>
      </form> <!-- End Form for Inspection -->
     </div>
    </div>
    </div>
    </div>
  </section>
   <?php
    }  // End of if- Inspection
  ?>
 <!-- ***************CERTIFICATE *************** -->
 <?php
   if (isset($_GET['part']) && $_GET['part'] === 'certificate') { // Open form -Get link from main.php - dashboard
        
            $appid_certificate = isset($_GET['appid']) ? (int)$_GET['appid'] : 0; // Application ID
            
            // Get application info with validation
            $app_info = ApplicationInfo($appid_certificate, $con);
            if (!$app_info) {
                echo "<div class='alert alert-danger'>Application not found.</div>";
                exit;
            }
            
            $application_no = $app_info['application_no'] ?? '';
            $import_country_id = $app_info['country_import'] ?? '';
            $import_country = $import_country_id ? CountryInfo($import_country_id, $con)['title'] : '';
            $import_point = $app_info['import_point'] ?? '';
            $uid = $app_info['uid'] ?? '';
            $locationid = $uid ? Userdata($uid, $con)['location_id'] : '';
            $place_issue = $locationid ? Locationname($locationid, $con) : '';
            $export_pointid = $app_info['export_point'] ?? '';
            $export_point = $export_pointid ? Locationname($export_pointid, $con) : '';
            $exporterid = $app_info['company_id'] ?? '';
            
            // Get exporter details with validation
            $exporter_info = EntityExportInfo($exporterid, $con);
            $exporter_name = $exporter_info ? $exporter_info['title'] : '';
            $exporter_address = $exporter_info ? $exporter_info['address'] : '';
            $app_importerid = $app_info['importerid'] ?? '';
            
            // Handle importer info - check if importer ID exists
            $importer_info = EntityImportInfo($app_importerid, $con);
            $importer_name = $importer_info ? $importer_info['title'] : '';
            $importer_address = $importer_info ? $importer_info['address'] : '';

            $provinceid = $exporter_info ? $exporter_info['province'] : '';
            $districtid = $exporter_info ? $exporter_info['district'] : '';
            $phone = $exporter_info ? $exporter_info['phone'] : '';
            $email = $exporter_info ? $exporter_info['email'] : '';
            // Importer details
            $import_countryid = $app_info['country_import'] ?? '';
            $import_country = $import_countryid ? CountryInfo($import_countryid, $con)['title'] : '';

    // ADD NEW CERTIFICATE ************
         if($_GET['certify'] == 'Add'){   
            // create new certificate number 
            $btnSubmitCertificate = 'submit';
            list($certificate_id, $certificate_no) = CertificateNo($appid_certificate, $userid, $guid, $con);
            $current_date = date('Y-m-d');
            
            // Initialize form variables with default values
            $carbonpaper_id = '';
            $approved_by = '';
            $approver_position = '';
            $place_issued = '';
            $consignment_value = '';
            $value_currency = '';
            $additional_scientificname = '';
            $additional_declaration = '';
            $date_issued = '';
            // Button state     //
         } else if ($_GET['certify'] == 'View/Edit') {
            $btnSubmitCertificate = 'update';
            $certrows = CertificateInfo($appid_certificate, $con); // Application ID
            if ($certrows) {
              // Button state
             // $btnSubmit = 'update';
              // Populate certificate fields
              $certificate_id = $certrows['id'];
              $certificate_no = $certrows['certificate_no'];
              $carbonpaper_id = $certrows['carbonpaper_id'];
              $approved_by = $certrows['approved_by'];
              $approver_position = $certrows['position_approved'];
              $place_issued = $certrows['place_issued'];
              $consignment_value = $certrows['consignment_value'];
              $value_currency = $certrows['value_currency'];
              $additional_scientificname = $certrows['additional_scientificname'];
              $additional_declaration = $certrows['additional_declaration'];
              $date_issued = $certrows['date_issued'];
                if ($date_issued == '0000-00-00' || is_null($date_issued)) {
                    $date_issued = '';
                }
            } // End of populate certificate fields
         } else {
            // Invalid action
            echo "<div class='alert alert-danger'>Invalid action specified.</div>";
            exit;
         }
          
        
 ?>
  <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1><?php echo isset($translations['Certificate']) ? $translations['Certificate'] : 'Certificate'; ?></h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($mainHref); ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
            <li class="breadcrumb-item"><a href="application.php?part=dashboard&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Application']) ? $translations['Application'] : 'Application'; ?></a></li>
            <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars('transaction.php?part=exportentity_list&uid='.$userid.'&lang='.$lang); ?>"><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Export entity'; ?></a></li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->
    <section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><?php echo isset($translations['Certificate Form']) ? $translations['Certificate Form'] : 'Certificate Form'; ?></h5>
          <form id="certificateFormID" action="<?php echo htmlspecialchars($mainHref); ?>" method="POST">
            <!-- HIDDEN INPUTS -->
            <input type="hidden" name="appid_certificate" value="<?php echo $appid_certificate; ?>">
            <input type="hidden" name="certificate_id" value="<?php echo isset($certificate_id) ? $certificate_id : ''; ?>">
            <input type="hidden" name="importer_id" id="importer_id" value="<?php echo isset($app_importerid) ? $app_importerid : ''; ?>">
            <!-- Hidden input to preserve userid for dynamic authentication -->
            <input type="hidden" name="uid" value="<?php echo $userid; ?>">

            <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Certificate No']) ? $translations['Certificate No'] : 'Certificate No'; ?></label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="certificate_no" id="certificate_no" required value="<?php echo isset($certificate_no) ? $certificate_no : ''; ?>" readonly>
              </div>
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Application No']) ? $translations['Application No'] : 'Application No'; ?></label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="application_no" id="application_no" value="<?php echo isset($application_no) ? $application_no : ''; ?>" readonly>
              </div>
            </div>

             <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Import country']) ? $translations['Import country'] : 'Import country'; ?></label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="import_country" id="import_country" required value="<?php echo isset($import_country) ? $import_country : ''; ?>" readonly>
              </div>
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Import entry point']) ? $translations['Import entry point'] : 'Import entry point'; ?></label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="import_entrypoint" id="import_entrypoint" required value="<?php echo isset($import_point) ? $import_point : ''; ?>">
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Place of Issue']) ? $translations['Place of Issue'] : 'Place of Issue'; ?></label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="place_issue" id="place_issue" required value="<?php echo isset($place_issue) ? $place_issue : ''; ?>">
              </div>
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Export entry point']) ? $translations['Export entry point'] : 'Export entry point'; ?></label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="export_entrypoint" id="export_entrypoint" required value="<?php echo isset($export_point) ? $export_point : ''; ?>">
              </div>
            </div>

            
                <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Issued date']) ? $translations['Issued date'] : 'Issued date'; ?></label>
                    <div class="col-sm-4">
                      <input type="date" name="date_issue" class="form-control" value="<?php echo isset($date_issued) ? $date_issued : ''; ?>">
                    </div>
                </div>

            <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Exporter name and address"]) ? $translations["Exporter name and address"] : "Exporter's name and address"; ?></label> <!-- Certificate-->
                  <div class="col-sm-4 d-flex align-items-start">
                      <input type="text" class="form-control" name="exporter_name" id="exporter_name" class="form-control" value="<?php echo isset($exporter_name) ? $exporter_name : ''; ?>"></input>
                  </div>
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations["Importer name and address"]) ? $translations["Importer name and address"] : "Importer's name and address"; ?></label> <!-- Certificate-->
                  <div class="col-sm-4 d-flex align-items-center position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#importerModal" class="me-2">
                      <i class="bi bi-plus-circle" style="font-size: 1.2rem; color: #28a745;"></i>
                    </a>
                    <input type="text" class="form-control" name="importer_name" id="importer_name" required 
                             value="<?php echo isset($importer_name) ? $importer_name : ''; ?>">
                  </div>
            </div>

            <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">&nbsp;</label>
                  <div class="col-sm-4 d-flex align-items-start">
                      <textarea name="exporter_address" id="exporter_address" class="form-control" rows="3"><?php echo isset($exporter_address) ? $exporter_address : ''; ?></textarea>
                  </div>
                  <label class="col-sm-2 col-form-label">&nbsp;</label>
                  <div class="col-sm-4">
                    <textarea name="importer_address" id="importer_address" class="form-control" rows="3"><?php echo isset($importer_address) ? $importer_address : ''; ?></textarea>
                  </div>
            </div>
         
             <div class="row mb-3 align-items-center"> 
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Carbon paper No']) ? $translations['Carbon paper No'] : 'Carbon paper No'; ?></label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="carbonpaper_id" id="carbonpaper_id" required value="<?php echo isset($carbonpaper_id) ? $carbonpaper_id : ''; ?>">
              </div>
            </div>

            
            <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Approved by']) ? $translations['Approved by'] : 'Approved by'; ?></label>
                  <div class="col-sm-4">
                    <select class="form-select" name="approved_by" aria-label="Select approver">
                      <option value="">Select approver...</option>
                      <?php CertificateApprovedBy($con, $guid, isset($approved_by) ? $approved_by : null); ?>
                    </select>
                  </div>
              <label class="col-sm-2 col-form-label"><?php echo isset($translations["Approver position"]) ? $translations["Approver position"] : "Approver's position"; ?></label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="approver_position" id="approver_position" required value="<?php echo isset($approver_position) ? $approver_position : ''; ?>">
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Consignment Value']) ? $translations['Consignment Value'] : 'Consignment Value'; ?></label>
              <div class="col-sm-4">
                <input type="number" step="0.01" class="form-control" name="consignment_value" id="consignment_value" required value="<?php echo isset($consignment_value) ? $consignment_value : ''; ?>">
              </div>
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Currency']) ? $translations['Currency'] : 'Currency'; ?></label>
              <div class="col-sm-4">
                <select class="form-select" name="value_currency" aria-label="Select currency">
                  <option value="">Select currency...</option>
                  <?php SelectCurrency($value_currency, $con); ?>
                </select>
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label"><?php echo isset($translations['Another Scientific Name']) ? $translations['Another Scientific Name'] : 'Another Scientific Name'; ?></label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="additional_scientificname" id="another_scientificname" value="<?php echo isset($additional_scientificname) ? $additional_scientificname : ''; ?>">
              </div>
            </div>
             <div class="row mb-3 align-items-center">
            <label class="col-sm-2 col-form-label"><?php echo isset($translations['Additional Declaration']) ? $translations['Additional Declaration'] : 'Additional Declaration'; ?></label>
            <div class="col-sm-10">
              <textarea class="form-control" name="additional_declaration" id="additional_declaration" rows="3" placeholder="Enter additional declaration"><?php echo isset($additional_declaration) ? htmlspecialchars($additional_declaration) : ''; ?></textarea>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-10 offset-sm-2 d-flex gap-2">
                <button type="submit" name="btnSubmitCertificate" class="btn btn-primary" value="<?php echo $btnSubmitCertificate === 'update' ? 'update' : 'submit'; ?>">
                  <i class="bi bi-save"></i> <?php echo $btnSubmitCertificate === 'update' ? ' Update' : ' Submit'; ?>
                </button>
                <?php if ($btnSubmitCertificate === 'update'): ?>
                <button type="button" class="btn btn-success" onclick="viewCertificate(<?php echo $appid_certificate; ?>)" title="Open certificate in new window">
                  <i class="bi bi-file-earmark-text"></i> View Certificate
                </button>
                <button type="button" class="btn btn-info" onclick="viewCertificateNewFormat(<?php echo $appid_certificate; ?>)" title="View certificate in new format">
                  <i class="bi bi-file-earmark-ruled"></i> New Format
                </button>
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#spdocModal" title="View supporting document">
                  <i class="bi bi-file-earmark-pdf"></i> Supporting Document
                </button>
                <?php endif; ?>
                
                <a href="<?php echo htmlspecialchars($mainHref); ?>" class="btn btn-secondary">
                  <i class="bi bi-x-circle"></i> Cancel
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  </section>
  <?php
      // Certificate's information for supporting document
      //$appid_certificate
      $certinfo = CertificateInfo($appid_certificate, $con);
      $dateissue = $certinfo ? $certinfo['date_issued'] : '';
      $placeissue = $certinfo ? $certinfo['place_issued'] : '';
      $approverid = $certinfo ? $certinfo['approved_by'] : ''; 
      $Approver = ApproverInfo($approverid,$con);
      $authorized_name = $Approver ? $Approver['name'] : '';
      $authorized_surname = $Approver ? $Approver['surname'] : '';
      $authorized_officer = trim($authorized_name . ' ' . $authorized_surname);
      // Convert to uppercase (use mb_strtoupper if available for multibyte-safe conversion)
      if (function_exists('mb_strtoupper')) {
        $authorized_officer = mb_strtoupper($authorized_officer, 'UTF-8');
      } else {
        $authorized_officer = strtoupper($authorized_officer);
      }
      } // End of if- Certificate
  ?>

<!-- Modal form for Supporting Document -->
<!-- Hide specific UI elements when printing -->
<style>
  @media print {
    .no-print { display: none !important; }
  }
</style>
<div class="modal fade" id="spdocModal" tabindex="-1" aria-labelledby="spdocModalLabel" aria-hidden="true" style="font-family: Arial, sans-serif;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="spdocModalLabel">Supporting Document Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div id="spdocModalBody" class="modal-body">
        <!-- Present data in a word-like document style -->
        <div class="border p-3 bg-white" style="min-height:300px;">
          <a href="#" onclick="printSupportDoc('spdocModalBody'); return false;" class="position-absolute no-print" style="top: 10px; right: 15px; color: #333;" title="Print">
            <i class="bi bi-printer no-print" style="font-size: 1.5rem;"></i>
          </a>
           <div class="text-center mb-3">
            <img src="assets/img/national_logo.jpg" alt="National Logo" style="max-height:80px;">
          </div>
          <h6 class="text-center" style="white-space: pre-line;"><b>LAO PEOPLE'S DEMOCRATIC REPUBLIC</b>
            PEACE INDEPENDENCE DEMOCRACY UNITY PROSPERITY
            MINISTRY OF AGRICULTURE AND ENVIRONMENT
            DEPARTMENT OF AGRICULTURE<br>
            <strong>LIST OF CONSIGNMENTS</strong>
            FOR PHYTOSANITARY CERTIFICATE No: <?php echo isset($certificate_no) ? $certificate_no : ''; ?>
          </h6>
          <table class="table" style="border-top: 2px solid #000; border-bottom: 2px solid #000; border-left: none; border-right: none;">
            <thead>
              <tr>
                <th style="border: none; vertical-align: middle;"><strong>COMMON NAME/VARIETY</strong></th>
                <th style="border: none; vertical-align: middle;"><strong>BOTANICAL NAME</strong></th>
                <th style="border: none; vertical-align: middle;"><strong>QUANTITY(WT/No)</strong></th>
                <th style="border: none; vertical-align: middle;"><strong>NUMBER-PACKAGES</strong></th>
              </tr>
            </thead>
            <tbody>
              <?php 
              if (isset($appid_certificate) && $appid_certificate > 0) {
                  CertificateSupportingDocumentList($appid_certificate, $con); 
              } else {
                  echo '<tr><td colspan="4" class="text-center">No data available</td></tr>';
              }
              ?>
            </tbody>
          </table>
          
          <table style="width:100%; border:1px solid #000; border-collapse:collapse;">
            <tr>
              <td style="width:55%; padding:12px; vertical-align:top;">
                <div>PLACE OF ISSUE</div>
                <div style="margin-top:8px; font-weight:700;"><?php echo isset($place_of_issue) ? htmlspecialchars($place_of_issue) : 'DOA'; ?></div>
              </td>
              <td rowspan="2" style="width:45%; padding:12px; vertical-align:top; border-left:1px solid #000;">
                <div>NAME AND SIGNATURE OF AUTHORIZED OFFICER</div>
                <div style="height:48px;"></div>
                <div style="text-align:center; font-weight:700;"><?php echo isset($authorized_officer) ? htmlspecialchars($authorized_officer) : 'Na'; ?></div>
              </td>
            </tr>
            <tr>
              <td style="padding:12px; vertical-align:top; border-top:1px solid #000;">
                <div>DATE ISSUE</div>
                <div style="margin-top:8px; font-weight:700;">
                  <?php echo isset($dateissue) ? htmlspecialchars($dateissue) : date('d-M-Y'); ?>
                </div>
              </td>
            </tr>
          </table>
          <p align="center" style="font-size:11px;">Department of Agriculture, P.O. Box:11, Vientiane, Lao PDR, Tel: (856)21 412350, Fax: (856)21 412349, <br>E-mail: doag@hotmail.com</p>
          
          <!-- Add more fields as needed -->
        </div>
      </div>
    </div>
  </div>
</div>

  <!-- close Modal form for Supporting Document -->

  <!-- Modal form for Importer Selection (Certificate) -->
  <div class="modal fade" id="importerModal" tabindex="-1" aria-labelledby="importerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="importerModalLabel">Search Importer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="text" id="importerSearch" class="form-control mb-3" placeholder="Search importer...">
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="importerTable">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Country</th>
                  <th>Company Name</th>
                  <th>Address</th>
                  <th>Phone</th>
                  <th>Email</th>
                  <th>Contact Person</th>
                  <th>Select</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Get all import entities for selection
                $import_sql = "SELECT * FROM tbentity_import ORDER BY title ASC";
                $import_result = pg_query($con, $import_sql);
                $i = 0;
                if ($import_result && pg_num_rows($import_result) > 0) {
                  while ($import_row = pg_fetch_assoc($import_result)) {
                    $i++;
                    $import_id = $import_row['id'];
                    $import_title = $import_row['title'];
                    $import_address = $import_row['address'];
                    $import_phone = $import_row['phone'];
                    $import_email = $import_row['email'];
                    $import_contact = $import_row['contact_name'];
                    $import_country_name = CountryInfo($import_row['country_id'], $con)['title'];
                    
                    echo "<tr>";
                    echo "<td>$i</td>";
                    echo "<td>$import_country_name</td>";
                    echo "<td>$import_title</td>";
                    echo "<td>$import_address</td>";
                    echo "<td>$import_phone</td>";
                    echo "<td>$import_email</td>";
                    echo "<td>$import_contact</td>";
                    echo "<td><button type='button' class='btn btn-primary btn-sm' onclick='selectImporter(\"$import_id\", \"$import_title\", \"$import_address\")'>Select</button></td>";
                    echo "</tr>";
                  }
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Modal form for Importer Selection -->
   <script>
        // Print supporting document: DIV id = spdocModalBody
      function printSupportDoc(divId) {
        var printContents = document.getElementById(divId).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // Optional: reload to restore JS and events
      }
   </script>
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
   <!--
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>DOA</span></strong>. All Rights Reserved
    </div>

  </footer>
  End Footer -->

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

  <script>
      document.addEventListener('DOMContentLoaded', function() {
        const exportCert = document.getElementById('export_certificate');
        const transitCert = document.getElementById('transit_certificate');

        if (exportCert && transitCert) {
          exportCert.addEventListener('change', function() {
            if (this.checked) transitCert.checked = false;
          });
          transitCert.addEventListener('change', function() {
            if (this.checked) exportCert.checked = false;
          });
        }
      });

      // Function to refresh form data from server
      function refreshInspectionForm(appid) {
        console.log('Refreshing inspection form for appid:', appid);
        
        // You could implement an AJAX call to get fresh form data
        // fetch('transaction.php?action=get_inspection_data&appid=' + appid)
        //   .then(response => response.json())
        //   .then(data => {
        //     if (data.success) {
        //       // Update form fields with fresh data
        //       document.getElementById('detected_pest').checked = data.pest_detected;
        //       // Update other fields as needed
        //     }
        //   });
        
        // For now, just reload the page section or entire page
        // window.location.reload(); // Full page reload
        
        // Or reload just the form section if you prefer
        console.log('Form refresh completed');
      }

      // Function to view certificate in new window
      function viewCertificate(appid) {
        console.log('viewCertificate called with appid:', appid, 'type:', typeof appid); // Enhanced debug
        
        // Convert to string for validation
        const appidStr = String(appid);
        
        if (!appid || appidStr === '' || appidStr === '0' || appidStr === 'undefined' || appidStr === 'null') {
          console.error('Invalid appid received:', appid);
          alert('Error: Invalid application ID (' + appidStr + '). Please make sure the certificate is saved first.');
          return;
        }
        
        // Build the URL
        const certificateUrl = 'certificate_view.php?appid=' + encodeURIComponent(appid);
        console.log('Opening certificate URL:', certificateUrl);
        
        try {
          const certWindow = window.open(
            certificateUrl, 
            'certificateView', 
            'width=900,height=700,scrollbars=yes,resizable=yes,toolbar=no,menubar=no'
          );
          
          if (certWindow) {
            certWindow.focus();
            
            // Check if window was closed immediately (popup blocker)
            setTimeout(function() {
              if (certWindow.closed) {
                alert('Popup was blocked. Please allow popups for this site or use the "View PDF" button.');
              }
            }, 1000);
            
          } else {
            alert('Popup blocked! Please allow popups for this site to view the certificate, or use the "View PDF" button instead.');
          }
        } catch (error) {
          console.error('Error opening certificate window:', error);
          alert('Error opening certificate window: ' + error.message + '. Please try the "View PDF" button instead.');
        }
      }
      
      // Function to view certificate in new format
      function viewCertificateNewFormat(appid) {
        console.log('viewCertificateNewFormat called with appid:', appid);
        
        const appidStr = String(appid);
        
        if (!appid || appidStr === '' || appidStr === '0' || appidStr === 'undefined' || appidStr === 'null') {
          console.error('Invalid appid received:', appid);
          alert('Error: Invalid application ID. Please make sure the certificate is saved first.');
          return;
        }
        
        const certificateUrl = 'certificate_preview_new.php?appid=' + encodeURIComponent(appid);
        console.log('Opening certificate URL:', certificateUrl);
        
        try {
          const certWindow = window.open(
            certificateUrl, 
            'certificateNewFormat', 
            'width=1000,height=900,scrollbars=yes,resizable=yes,toolbar=no,menubar=no'
          );
          
          if (certWindow) {
            certWindow.focus();
          } else {
            alert('Popup blocked! Please allow popups for this site to view the certificate.');
          }
        } catch (error) {
          console.error('Error opening certificate window:', error);
          alert('Error opening certificate window: ' + error.message);
        }
      }
      
      // Function to view certificate in same window (fallback)
      function viewCertificateInSameWindow(appid) {
        console.log('viewCertificateInSameWindow called with appid:', appid, 'type:', typeof appid); // Enhanced debug
        
        // Convert to string for validation
        const appidStr = String(appid);
        
        if (!appid || appidStr === '' || appidStr === '0' || appidStr === 'undefined' || appidStr === 'null') {
          console.error('Invalid appid received:', appid);
          alert('Error: Invalid application ID (' + appidStr + '). Please make sure the certificate is saved first.');
          return;
        }
        
        // Build the URL and open in same window
        const certificateUrl = 'certificate_view.php?appid=' + encodeURIComponent(appid);
        console.log('Navigating to certificate URL:', certificateUrl);
        
        try {
          window.location.href = certificateUrl;
        } catch (error) {
          console.error('Error navigating to certificate:', error);
          alert('Error opening certificate: ' + error.message);
        }
      }

      // Function to select importer from modal and populate form fields
      function selectImporter(importerId, importerName, importerAddress) {
        // Populate the importer ID hidden field
        document.getElementById('importer_id').value = importerId;
        
        // Populate the importer name field
        document.getElementById('importer_name').value = importerName;
        
        // Populate the importer address field
        document.getElementById('importer_address').value = importerAddress;
        
        // Close the modal
        var modal = bootstrap.Modal.getInstance(document.getElementById('importerModal'));
        if (modal) {
          modal.hide();
        } else {
          // If modal instance doesn't exist, create and hide it
          var modalElement = document.getElementById('importerModal');
          var newModal = new bootstrap.Modal(modalElement);
          newModal.hide();
        }
      }

      // Search/Filter importer functionality for certificate modal
      document.addEventListener('DOMContentLoaded', function() {
        const importerSearch = document.getElementById('importerSearch');
        const importerTable = document.getElementById('importerTable');
        
        if (importerSearch && importerTable) {
          importerSearch.addEventListener('keyup', function() {
            const filter = importerSearch.value.toLowerCase();
            const rows = importerTable.querySelectorAll('tbody tr');
            rows.forEach(row => {
              const text = row.textContent.toLowerCase();
              row.style.display = text.includes(filter) ? '' : 'none';
            });
          });
        }

        // Control visibility of pest details span and link based on checkbox
        const detectedPestCheckbox = document.getElementById('detected_pest');
        const spanPest = document.getElementById('span_pest');
        const linkPestDetails = document.getElementById('link_pest_details');
        
        if (detectedPestCheckbox && spanPest && linkPestDetails) {
          // Function to toggle visibility
          function togglePestDetails() {
            if (detectedPestCheckbox.checked) {
              spanPest.style.display = 'inline';
              linkPestDetails.style.display = 'inline';
            } else {
              spanPest.style.display = 'none';
              linkPestDetails.style.display = 'none';
            }
          }
          
          // Set initial state based on current checkbox state
          togglePestDetails();
          
          // Add event listener for checkbox changes
          detectedPestCheckbox.addEventListener('change', function() {
            togglePestDetails();
            
            // Get application ID for both scenarios
            const appid = document.querySelector('input[name="appid"]');
            const inspectionMethod = document.getElementById('inspection_method');
            const appidValue = appid ? appid.value : '';
            const inspectionMethodValue = inspectionMethod ? inspectionMethod.value : '';

            if (appidValue && !this.checked) { // the BOX IS BEING UNCHECKED-some data exists
              // When checkbox is UNCHECKED - delete pest detection data
              if (confirm('Are you sure you want to delete the pest detection data?')) {
                // Delete pest detection data
                fetch('transaction.php', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                  },
                  body: 'action=delete_pest_detected&appid=' + encodeURIComponent(appidValue)
                })
                .then(response => response.json())
                .then(data => {
                  if (data.success) {
                    console.log('Pest detection data deleted successfully');
                    
                    // Handle refresh if requested by backend
                    if (data.refresh_checkbox) {
                      // Ensure checkbox is unchecked and UI is updated
                      detectedPestCheckbox.checked = false;
                      togglePestDetails();
                      console.log('Checkbox and form refreshed after deletion');
                    }
                    
                    // Show success message if inspection was updated
                    if (data.inspection_updated) {
                      // Optional: Show a brief success notification
                      const successMsg = document.createElement('div');
                      successMsg.className = 'alert alert-success alert-dismissible fade show';
                      successMsg.innerHTML = `
                        <strong>Success!</strong> Pest detection data removed and inspection record updated.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      `;
                      document.querySelector('.card-body').insertBefore(successMsg, document.querySelector('.card-body').firstChild);
                      
                      // Auto-dismiss after 3 seconds
                      setTimeout(() => {
                        if (successMsg.parentNode) {
                          successMsg.remove();
                        }
                      }, 3000);
                    }
                    
                    // Show warning if there was an issue
                    if (data.warning) {
                      console.warn('Warning:', data.warning);
                    }
                  } else {
                    console.error('Error deleting pest detection data:', data.error);
                    alert('Error deleting pest detection data: ' + (data.error || 'Unknown error'));
                    // Recheck the checkbox if deletion failed
                    detectedPestCheckbox.checked = true;
                    togglePestDetails();
                  }
                })
                .catch(error => {
                  console.error('Network error:', error);
                //  alert('Network error occurred while deleting pest detection data.');
                  // Recheck the checkbox if deletion failed
                  detectedPestCheckbox.checked = true;
                  togglePestDetails();
                });
              } else {
                // User cancelled, recheck the checkbox
                detectedPestCheckbox.checked = true;
                togglePestDetails();
              }
            } else {
              // the BOX IS BEING CHECKED - pest detected
            //  alert('Hello, Pest detected box is being checked.');
              const appidElementPest = document.getElementById('appid');
              const appidh = appidElementPest ? appidElementPest.value : ''; // hidden input
              
              // Check if inspection form elements exist before accessing them
              const inspectionDateEl = document.getElementById('inspection_date');
              const samplenoEl = document.getElementById('sampleno');
              const sampleVolumeEl = document.getElementById('sample_volume');
              const unitEl = document.getElementById('unit');
              const sampleCollectedByEl = document.getElementById('sample_collectedby');
              const inspectedByEl = document.getElementById('sample_inspectedby');
              const certificateFeeEl = document.getElementById('certificate_fee');
              const receiptNoEl = document.getElementById('receipt_no');
              const lotNoEl = document.getElementById('lot_no');
              const inspectionMethodEl = document.getElementById('inspection_method');
              
              // Only proceed if we have the necessary elements (inspection form)
              if (appidh && inspectionDateEl && samplenoEl) {
                const inspectiondate = inspectionDateEl.value;
                const sampleno = samplenoEl.value;
                const samplequantity = sampleVolumeEl ? sampleVolumeEl.value : '';
                const unitid = unitEl ? unitEl.value : '';
                const samplecollectedby = sampleCollectedByEl ? sampleCollectedByEl.value : '';
                const inspectedby = inspectedByEl ? inspectedByEl.value : '';
                const certificatefee = certificateFeeEl ? certificateFeeEl.value : '';
                const receiptno = receiptNoEl ? receiptNoEl.value : '';
                const lotno = lotNoEl ? lotNoEl.value : '';
                const inspectionmethod = inspectionMethodEl ? inspectionMethodEl.value : '';

              $.ajax({
                  type: 'POST',
                  url: 'transaction.php',
                  data: {
                      action: 'pest_detected_inspectionSave',
                      appid: appidh,
                      inspection_date: inspectiondate,
                      sampleno: sampleno,
                      samplequantity: samplequantity,
                      unitid: unitid,
                      samplecollectedby: samplecollectedby,
                      inspectedby: inspectedby,
                      certificatefee: certificatefee,
                      receiptno: receiptno,
                      lotno: lotno,
                      inspectionmethod: inspectionmethod,
                      detected: 1
                  },
                  //dataType: 'json',
                  success: function(response) {
                      console.log('AJAX Success Response:', response);
                     // alert("Pest detection inspection Save - response!");
                  },
                  error: function(xhr, status, error) {
                      console.error('AJAX Error:', error);
                      alert("Error occurred: " + error);
                  }
              }); // Close AJAX call
                } else {
                  console.log('Inspection form elements not found, skipping AJAX call');
                }
            } // Close the if condition for UNCHECKING
          }); // Close the change event listener
        } // Close the if condition for checkbox existence

        // Control visibility of treatment details based on checkbox
        const appidElement = document.getElementById('appid');
        const appidhtreat = appidElement ? appidElement.value : ''; // hidden input
        const treatmentCheckbox = document.getElementById('treatment_ability');
      
        if (treatmentCheckbox) {
          // Create a reusable function to toggle treatment details
          function toggleTreatmentDetails() {
            const divTreatment = document.getElementById('details_treatment');
            if (divTreatment) {
              if (treatmentCheckbox.checked) {
                divTreatment.style.display = 'inline';  // show the div/block-details_treatment
              } else {
                divTreatment.style.display = 'none';
              }
            }
          }
          
          // Set initial state on page load
          toggleTreatmentDetails();
          
          // Add change event listener
          treatmentCheckbox.addEventListener('change', toggleTreatmentDetails);
        }    

        // Show modal form for multiple commodities details
        const multipleCommoditiesCheckbox = document.getElementById('multiple_commodities'); // Checkbox
        const multiCommoditiesSpan = document.getElementById('span_multiple');

        console.log('Multiple commodities checkbox:', multipleCommoditiesCheckbox);
        console.log('Multiple commodities span:', multiCommoditiesSpan);

        if (multipleCommoditiesCheckbox && multiCommoditiesSpan) {
            console.log('Both elements found, setting up functionality');
            
            // Function to toggle visibility
            function toggleMultipleCommoditiesDetails() {
              console.log('Toggle function called, checkbox checked:', multipleCommoditiesCheckbox.checked);
              if (multipleCommoditiesCheckbox.checked) {
                multiCommoditiesSpan.style.display = 'inline';
                // Update tbapplication with these so that all the data can be back when submitting the form multiple commodities
                const appidElementMultival = document.getElementById('appid').value;
                const appdateval = document.getElementById('app_date').value;
                const applicantnameval = document.getElementById('applicant_name').value;
                const applicantaddressval = document.getElementById('address').value;
                const regnoval = document.getElementById('reg_no').value;
                const phoneval = document.getElementById('phone').value;
                const entrypointval = document.getElementById('entry_point').value;
                const importcountryval = document.getElementById('import_country').value;
                const importpointval = document.getElementById('import_point').value;
                const exportcertval = document.getElementById('export_certificate').checked ? 1 : 0;
                const transitcertval = document.getElementById('transit_certificate').checked ? 1 : 0;
                console.log('Showing span');
                
                // Save application data via AJAX
                if (appidElementMultival) {
                  fetch('transaction.php', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                      action: 'save_application_before_multiple',
                      appid: appidElementMultival,
                      app_date: appdateval,
                      applicant_name: applicantnameval,
                      address: applicantaddressval,
                      reg_no: regnoval,
                      phone: phoneval,
                      entry_point: entrypointval,
                      import_country: importcountryval,
                      import_point: importpointval,
                      export_certificate: exportcertval,
                      transit_certificate: transitcertval
                    })
                  })
                  .then(response => response.json())
                  .then(data => {
                    if (data.success) {
                      console.log('Application data saved successfully before multiple commodities');
                    } else {
                      console.error('Error saving application data:', data.error);
                    }
                  })
                  .catch(error => {
                    console.error('Network error while saving application data:', error);
                  });
                }
              } else {
                multiCommoditiesSpan.style.display = 'none';
                console.log('Hiding span');
                // delete existing multiple commodities data if any
                const appidElementMulti = document.getElementById('appid');
                const appidhmulti = appidElementMulti ? appidElementMulti.value : ''; // hidden input
                if (appidhmulti) {
                  fetch('transaction.php', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=delete_multiple_commodities&appid=' + encodeURIComponent(appidhmulti)
                  })
                  .then(response => response.json())
                  .then(data => {
                    if (data.success) {
                      console.log('Multiple commodities data deleted successfully');
                      // Optionally, refresh the commodities table or UI here
                      const tableBody = document.getElementById('multiCommodityTableBody');
                      if (tableBody) {
                        tableBody.innerHTML = ''; // Clear existing rows
                      }
                    } else {
                      console.error('Error deleting multiple commodities data:', data.error);
                    }
                  })
                  .catch(error => {
                    console.error('Network error while deleting multiple commodities data:', error);
                  });
                }
              }  // Close the else block-unchecked
            }
            
            // Set initial state based on current checkbox state on page load
            toggleMultipleCommoditiesDetails();
            
            // Add event listener for checkbox changes
            multipleCommoditiesCheckbox.addEventListener('change', function() {
              console.log('Checkbox change event fired');
              toggleMultipleCommoditiesDetails();
            });
        } else {
            console.log('Elements not found - checkbox:', !!multipleCommoditiesCheckbox, 'span:', !!multiCommoditiesSpan);
        }

        // Multiple Commodities Modal Functionality
        let commodityCounter = 0;
        const commodities = [];

        // Product search functionality
        const productSearchInput = document.getElementById('productSearchInput');
        const productSearchTable = document.getElementById('productSearchTable');
        
        if (productSearchInput && productSearchTable) {
          productSearchInput.addEventListener('keyup', function() {
            const filter = productSearchInput.value.toLowerCase();
            const rows = productSearchTable.querySelectorAll('tbody tr');
            rows.forEach(row => {
              const text = row.textContent.toLowerCase();
              row.style.display = text.includes(filter) ? '' : 'none';
            });
          });

          // Add select buttons to product search table
          setTimeout(function() {
            const rows = productSearchTable.querySelectorAll('tbody tr');
            rows.forEach(row => {
              const cells = row.querySelectorAll('td');
              if (cells.length >= 3) {
                const productName = cells[0].textContent.trim();
                const scientificName = cells[1].textContent.trim();
                const description = cells[2].textContent.trim();
                
                // Create select button
                const selectBtn = document.createElement('button');
                selectBtn.type = 'button';
                selectBtn.className = 'btn btn-primary btn-sm';
                selectBtn.innerHTML = '<i class="bi bi-check-circle"></i> Select';
                selectBtn.onclick = function() {
                  selectMultiProduct('', productName, scientificName, description);
                };
                
                // Add or replace the action cell
                if (cells.length === 3) {
                  const actionCell = document.createElement('td');
                  actionCell.appendChild(selectBtn);
                  row.appendChild(actionCell);
                } else if (cells.length >= 4) {
                  cells[3].innerHTML = '';
                  cells[3].appendChild(selectBtn);
                }
              }
            });
          }, 100);
        }

        // Function to select product from search modal
        window.selectMultiProduct = function(productId, productName, scientificName, description) {
          document.getElementById('multiProductId').value = productId;
          document.getElementById('multiProductName').value = productName;
          document.getElementById('multiScientificName').value = scientificName || '';
          
          // Close only the product search modal, keep main modal open
          const productSearchModal = bootstrap.Modal.getInstance(document.getElementById('productSearchModal'));
          if (productSearchModal) {
            productSearchModal.hide();
          }
          
          // Focus back to the main modal - specifically the description field
          setTimeout(() => {
            document.getElementById('multiDescription').focus();
          }, 300);
        };

        // Add commodity to table
        const submitMultiCommodityBtn = document.getElementById('submitMultiCommodity');
        if (submitMultiCommodityBtn) {
          submitMultiCommodityBtn.addEventListener('click', function() {
            const form = document.getElementById('multipleCommodityForm');
            const formData = new FormData(form);
            
            // Get form values
            const productId = formData.get('product_id');
            const productName = formData.get('product_name');
            const scientificName = formData.get('scientific_name');
            const description = formData.get('description');
            const netQuantity = formData.get('net_quantity');
            const grossQuantity = formData.get('gross_quantity');
            
            // Validate required fields
            if (!productName || !description || !netQuantity || !grossQuantity) {
              alert('Please fill in all required fields: Product Name, Description, Net Quantity, and Gross Quantity');
              return;
            }
            
            // Add to commodities array
            commodityCounter++;
            const commodity = {
              id: commodityCounter,
              productId: productId,
              productName: productName,
              scientificName: scientificName,
              description: description,
              netQuantity: parseFloat(netQuantity),
              grossQuantity: parseFloat(grossQuantity)
            };
            
            commodities.push(commodity);
            
            // Add row to table
            addCommodityRow(commodity);
            
            // Clear form
            form.reset();
            document.getElementById('multiProductId').value = '';
          });
        }

        // Function to add commodity row to table
        function addCommodityRow(commodity) {
          const tableBody = document.getElementById('multiCommodityTableBody');
          const row = document.createElement('tr');
          row.setAttribute('data-id', commodity.id);
          
          row.innerHTML = `
            <td>${commodity.id}</td>
            <td>${commodity.productName}</td>
            <td>${commodity.scientificName || '-'}</td>
            <td>${commodity.description}</td>
            <td>${commodity.netQuantity}</td>
            <td>${commodity.grossQuantity}</td>
            <td>
              <button type="button" class="btn btn-danger btn-sm" onclick="removeCommodity(${commodity.id})">
                <i class="bi bi-trash"></i> Remove
              </button>
            </td>
          `;
          
          tableBody.appendChild(row);
        }

        // Function to remove commodity
        window.removeCommodity = function(commodityId) {
          if (confirm('Are you sure you want to remove this commodity?')) {
            // Remove from array
            const index = commodities.findIndex(c => c.id === commodityId);
            if (index > -1) {
              commodities.splice(index, 1);
            }
            
            // Remove from table
            const row = document.querySelector(`tr[data-id="${commodityId}"]`);
            if (row) {
              row.remove();
            }
          }
        };

        // Save all commodities
        const saveAllCommoditiesBtn = document.getElementById('saveAllCommodities');
        if (saveAllCommoditiesBtn) {
          saveAllCommoditiesBtn.addEventListener('click', function() {
            if (commodities.length === 0) {
              alert('Please add at least one commodity before saving.');
              return;
            }
            
            // Here you can send the commodities data to the server
            console.log('Saving commodities:', commodities);
            
            // For now, show success message and close modal
            alert(`Successfully saved ${commodities.length} commodities!`);
            
            // Close the modal
            const multiModal = bootstrap.Modal.getInstance(document.getElementById('multipleCommoditiesModal'));
            if (multiModal) {
              multiModal.hide();
            }
            
            // Optional: Clear the data after successful save
            // commodities.length = 0;
            // document.getElementById('multiCommodityTableBody').innerHTML = '';
            // commodityCounter = 0;
          });
        }

        // Handle modal events
        const multipleCommoditiesModal = document.getElementById('multipleCommoditiesModal');
        if (multipleCommoditiesModal) {
          multipleCommoditiesModal.addEventListener('shown.bs.modal', function() {
            // Focus on the search button when modal opens
            const searchBtn = document.getElementById('searchProductBtn');
            if (searchBtn) {
              searchBtn.focus();
            }
          });
        }

        const productSearchModal = document.getElementById('productSearchModal');
        if (productSearchModal) {
          productSearchModal.addEventListener('shown.bs.modal', function() {
            // Focus on search input when product search modal opens
            const searchInput = document.getElementById('productSearchInput');
            if (searchInput) {
              searchInput.focus();
            }
          });
          
          // When product search modal is closed, ensure main modal stays open
          productSearchModal.addEventListener('hidden.bs.modal', function() {
            // Ensure the main modal backdrop and focus is maintained
            document.body.classList.add('modal-open');
            
            // Re-focus on the main modal
            setTimeout(() => {
              const mainModal = document.getElementById('multipleCommoditiesModal');
              if (mainModal && mainModal.classList.contains('show')) {
                const descField = document.getElementById('multiDescription');
                if (descField) {
                  descField.focus();
                }
              }
            }, 100);
          });
        }

      }); // Close the DOMContentLoaded event listener

</script>

</body>

</html>