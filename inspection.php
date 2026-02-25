<?php
session_start();
require("php-bin/connection.php");
require("php-bin/supports.php");
$_SESSION['lang'] = 'en'; // NOT WORKING -FIX- Default to English
if (isset($_GET['lang'])) {
  $selectedLang = $_GET['lang'];
  $_SESSION['lang'] = $selectedLang; // Store the selected language in the session
} else {
  // Default to English if no language is selected
  if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
  }
}
//if (ob_get_level()) ob_end_clean();
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

// echo "<script>alert('Login-Updated with User id: " . (isset($_GET["uid"]) ? $_GET["uid"] : 'not set') . "');</script>"; 
// Dynamic Authentication System - same as entity.php
$userid = '';
// Try multiple sources for userid (priority order)
if (isset($_GET["uid"]) && !empty($_GET["uid"])) {
    $userid = $_GET["uid"];
} elseif (isset($_POST["uid"]) && !empty($_POST["uid"])) {
    $userid = $_POST["uid"];
} elseif (isset($_POST["huid"]) && !empty($_POST["huid"])) {
    $userid = $_POST["huid"];
} elseif (isset($_COOKIE["ephyto_uid"]) && !empty($_COOKIE["ephyto_uid"])) {
    $userid = $_COOKIE["ephyto_uid"];
} elseif (isset($_SERVER["HTTP_REFERER"]) && !empty($_SERVER["HTTP_REFERER"])) {
    $referer = $_SERVER["HTTP_REFERER"];
    if (preg_match('/[?&]uid=([^&]+)/', $referer, $matches)) {
        $userid = urldecode($matches[1]);
    }
}

// Build language switch URLs and main link AFTER userid is set
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
if (!empty($userid)) {
    // Get user data from database
    $userdata = Userdata($userid, $con);  
    if ($userdata) {
        $username = $userdata['name'];
        $email = $userdata['email'];
        $position = $userdata['position'];
        $groupid = isset($userdata['group_id']) && !empty($userdata['group_id']) ? $userdata['group_id'] : '1'; // Default to group 1 if not set
        $groupname = GroupName($userdata['group_id'], $con);      
        // Get and store user profile image
        $uprofile = Profiledata($userid, $con);
        if (!$uprofile) {
            // Initialize profile if it doesn't exist
            InitializeProfile($userid, $con);
            $uprofile = Profiledata($userid, $con);
        }
        // Debug alert to show actual profile data - FIXED SYNTAX
        if ($uprofile && is_array($uprofile)) {
            $imgpath_value = isset($uprofile['imgfilepath']) ? $uprofile['imgfilepath'] : 'NOT SET';      
        } else {
            // echo "<script>alert('User profile: NO PROFILE DATA FOUND');</script>";
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
?>
<!DOCTYPE html>
<html lang="<?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == 'la') ? 'lo' : 'en'; ?>">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo isset($translations['inspection']) ? $translations['inspection'] : 'Inspection'; ?></title>
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
  <style>
    /* Custom styling for checkboxes when checked */
    #treatment:checked, #return_original:checked, #phytosanitary_requirements:checked, #other_conclusion:checked {
      background-color: #007bff !important; /* Blue background when checked */
      border-color: black !important;
    }
  </style>
</head>
<body class="<?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == 'la') ? 'lang-lao' : 'lang-en'; ?>">
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block"><?php echo isset($translations['e-Phytosanitary']) ? $translations['e-Phytosanitary'] : 'e-Phytosanitary'; ?></span>
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
        <!--
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
                <span><?php echo isset($translations['My Profile']) ? $translations['My Profile'] : 'My Profile'; ?></span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php?uid=<?php echo $userid; ?>">
                <i class="bi bi-gear"></i>
                <span><?php echo isset($translations['Account Settings']) ? $translations['Account Settings'] : 'Account Settings'; ?></span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span><?php echo isset($translations['Need Help?']) ? $translations['Need Help?'] : 'Need Help?'; ?></span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="index.php?logout=true">
                <i class="bi bi-box-arrow-right"></i>
                <span><?php echo isset($translations['Sign Out']) ? $translations['Sign Out'] : 'Sign Out'; ?></span>
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
        <a class="nav-link collapsed" href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-grid"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
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
        <a class="nav-link" href="inspection.php?uid=<?php echo $userid; ?>">
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
        <a class="nav-link collapsed" href="entity.php?entity=export&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" >
          <i class="bi bi-box-arrow-up-right"></i>
          <span><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Export entity'; ?></span>
        </a>
      </li><!-- End Export Entity Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=import&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" >
          <i class="bi bi-box-arrow-in-down" style="font-size: 1.2rem;"></i>
          <span><?php echo isset($translations['Import entity']) ? $translations['Import entity'] : 'Import entity'; ?></span>
        </a>
      </li><!-- End Import Entity/Company form Nav -->

      <?php if($groupname == "admin"){ ?><!-- Admin group check -->
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
            <a href="masterdata.php?part=pest&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Pest']) ? $translations['Pest'] : 'Pest'; ?></span>
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
      <?php } // End of Admin group check ?>

      <!-- Monitoring and Reporting -->
       <li class="nav-heading"><?php echo isset($translations['MONITORING AND REPORTING']) ? $translations['MONITORING AND REPORTING'] : 'MONITORING AND REPORTING'; ?></li>
        <li class="nav-item">
        <a class="nav-link collapsed" href="monitor_report.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bx bxs-file-find" style="font-size: 20px;"></i>
          <span><?php echo isset($translations['Certificate tracking']) ? $translations['Certificate tracking'] : 'Certificate tracking'; ?></span>
        </a>
      </li><!-- End Monitoring and Reporting Nav -->

      <li class="nav-heading"><?php echo isset($translations['USERS MANAGEMENT']) ? $translations['USERS MANAGEMENT'] : 'Users Management'; ?></li>
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
          <i class="bi bi-person-plus"></i><span><?php echo isset($translations['Users']) ? $translations['Users'] : 'Users'; ?></span>
        </a>
      </li>  
      <!-- pk**: End of User Admin-->
    </ul>
  </aside><!-- End Sidebar-->
  <main id="main" class="main">
    <?php 
       // if(isset($_GET['part']) && $_GET['part'] == 'dashboard_inspection'){ 
     ?>
    <div class="pagetitle">
      <h1><?php echo isset($translations['Inspection']) ? $translations['Inspection'] : 'Inspection'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($mainHref); ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item active"><a href="application.php?part=dashboard&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Application']) ? $translations['Application'] : 'Application'; ?></a></li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <!-- Charts Section -->
    
    <!-- End Charts Section -->

    <?php
    // Handle Pest Detected Form
    if (isset($_GET['part']) && $_GET['part'] === 'pest_detected') {
        $appid = isset($_GET['appid']) ? (int)$_GET['appid'] : 0;
        
        // Get application info
        $app_info = ApplicationInfo($appid, $con);
        $appno = $app_info ? $app_info['application_no'] : '';
        
        // Get pest detected info if exists using PestDetectedInfo function
        $pest_detected_info = null;
        $btnSubmitPest = 'submit';
        $record_id = '';
        $pestid = '';
        $pest_name = '';
        $infestation_level = '';
        $alive_status = '';
        $risk_category = '';
        $result_measure = '';
        
        if ($appid > 0) {
            // Check if pest detection record exists
            $pest_detected_info = PestDetectedInfo($appid, $con);
            if ($pest_detected_info) {
                $btnSubmitPest = 'update';
                $record_id = $pest_detected_info['id'];
                $pestid = $pest_detected_info['pestid'] ?? '';
                $infestation_level = $pest_detected_info['infestation_level'] ?? '';
                $alive_status = $pest_detected_info['alive_status'] ?? '';
                $risk_category = $pest_detected_info['risk_category'] ?? '';
                $result_measure = $pest_detected_info['result_measure'] ?? '';
                
                // Get pest name if pestid exists
                if ($pestid) {
                    $pest_info = PestInfo($pestid, $con);
                    if ($pest_info) {
                        $pest_name = $pest_info['pestname'];
                    }
                }
            }
        }
        
        // Handle form submission using PestDetectedSave and PestDetectedUpdate functions
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSubmitPestDetected'])) {
            $appid = isset($_POST['appid']) ? (int)$_POST['appid'] : 0;
            $pestid = isset($_POST['pestid']) ? (int)$_POST['pestid'] : 0;
            $infestation_level = $_POST['infestation_level'] ?? '';
            $alive_status = $_POST['alive_status'] ?? '';
            $risk_category = $_POST['risk_category'] ?? '';
            $result_measure = $_POST['result_measure'] ?? '';
            
            $result = false;
            if ($_POST['btnSubmitPestDetected'] === 'update') {
                // Update existing record using PestDetectedUpdate function
                $record_id = isset($_POST['record_id']) ? (int)$_POST['record_id'] : 0;
                $result = PestDetectedUpdate($record_id, $pestid, $infestation_level, 
                                            $alive_status, $risk_category, $result_measure, $con);
            } else {
                // Insert new record using PestDetectedSave function
                $result = PestDetectedSave($appid, $pestid, $infestation_level, 
                                          $alive_status, $risk_category, $result_measure, $con);
            }
            
            if ($result) {
                // Update inspection table to mark pest as detected
                PestDetectedInspectionUpdate($appid, $con);
                
                echo "<script>
                    alert('Pest detection information saved successfully!');
                    window.location.href = 'transaction.php?part=inspection&inspect=View/Edit&appid=" . $appid . "&uid=" . $userid . "';
                </script>";
            } else {
                echo "<script>alert('Error saving pest detection information.');</script>";
            }
        }
    ?>
    
    <div class="pagetitle">
        <h1><?php echo isset($translations['Data form']) ? $translations['Data form'] : 'Data Form'; ?></h1>
       <!--
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($mainHref); ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
                <li class="breadcrumb-item"><a href="transaction.php?part=inspection&inspect=View/Edit&appid=<?php echo $appid; ?>&uid=<?php echo $userid; ?>"><?php echo isset($translations['Inspection']) ? $translations['Inspection'] : 'Inspection'; ?></a></li>
                <li class="breadcrumb-item active"><?php echo isset($translations['Pest Detected']) ? $translations['Pest Detected'] : 'Pest Detected'; ?></li>
            </ol>
        </nav>
        -->
    </div>
    
    <style>
        /* Custom radio button styling to show checkmark when selected */
        .result-measure-radio {
            width: 1.3em !important;
            height: 1.3em !important;
            border-radius: 0.25em !important;
            border: 2px solid #000 !important;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #fff;
            cursor: pointer;
            position: relative;
        }
        
        .result-measure-radio:checked {
            background-color: #0d6efd;
            border-color: #0d6efd !important;
        }
        
        .result-measure-radio:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 1em;
            font-weight: bold;
        }
    </style>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo isset($translations['Pest Detected Information']) ? $translations['Pest Detected Information'] : 'Pest Detected Information'; ?></h5>
                        
                        <form id="pestDetectedForm" action="inspection.php?part=pest_detected&appid=<?php echo $appid; ?>&uid=<?php echo $userid; ?>" method="POST">
                            <input type="hidden" name="appid" value="<?php echo $appid; ?>">
                            <input type="hidden" name="record_id" value="<?php echo $record_id; ?>">
                            <input type="hidden" name="pestid" id="pestid" value="<?php echo $pestid; ?>">
                            <input type="hidden" name="uid" value="<?php echo $userid; ?>">
                            <input type="hidden" name="hlang" value="<?php echo $lang; ?>">
                            
                            <!-- Pest Information Section -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><?php echo isset($translations['Pest information']) ? $translations['Pest information'] : 'Pest information'; ?></strong>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label"><?php echo isset($translations['Pest name']) ? $translations['Pest name'] : "Pest's name"; ?> <span class="text-danger">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" id="pest_name_display" 
                                                   value="<?php echo htmlspecialchars($pest_name); ?>" 
                                                   placeholder="Click search to select pest" readonly required>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pestSearchModal">
                                                <i class="bi bi-search"></i> <?php echo isset($translations['Search']) ? $translations['Search'] : 'Search'; ?>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label"><?php echo isset($translations['Infestation Level']) ? $translations['Infestation Level'] : 'Infestation Level'; ?></label>
                                        <div class="col-sm-9">
                                            <select class="form-select" name="infestation_level">
                                                <option value=""><?php echo isset($translations['Select level']) ? $translations['Select level'] : 'Select level'; ?></option>
                                                <option value="trace" <?php if($infestation_level === 'trace') echo 'selected'; ?>><?php echo isset($translations['Trace']) ? $translations['Trace'] : 'Trace'; ?></option>
                                                <option value="low" <?php if($infestation_level === 'low') echo 'selected'; ?>><?php echo isset($translations['Low']) ? $translations['Low'] : 'Low'; ?></option>
                                                <option value="medium" <?php if($infestation_level === 'medium') echo 'selected'; ?>><?php echo isset($translations['Medium']) ? $translations['Medium'] : 'Medium'; ?></option>
                                                <option value="high" <?php if($infestation_level === 'high') echo 'selected'; ?>><?php echo isset($translations['High']) ? $translations['High'] : 'High'; ?></option>
                                                <option value="severe" <?php if($infestation_level === 'severe') echo 'selected'; ?>><?php echo isset($translations['Severe']) ? $translations['Severe'] : 'Severe'; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label"><?php echo isset($translations['Alive Status']) ? $translations['Alive Status'] : 'Alive Status'; ?></label>
                                        <div class="col-sm-9">
                                            <select class="form-select" name="alive_status">
                                                <option value="">Select status</option>
                                                <option value="alive" <?php if($alive_status === 'alive') echo 'selected'; ?>>Alive</option>
                                                <option value="dead" <?php if($alive_status === 'dead') echo 'selected'; ?>>Dead</option>
                                                <option value="mixed" <?php if($alive_status === 'mixed') echo 'selected'; ?>>Mixed (Alive and Dead)</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label"><?php echo isset($translations['Risk Category']) ? $translations['Risk Category'] : 'Risk Category'; ?></label>
                                        <div class="col-sm-9">
                                            <select class="form-select" name="risk_category">
                                                <option value=""><?php echo isset($translations['Select category']) ? $translations['Select category'] : 'Select category'; ?></option>
                                                <option value="low" <?php if($risk_category === 'low') echo 'selected'; ?>><?php echo isset($translations['Low']) ? $translations['Low'] : 'Low'; ?></option>
                                                <option value="medium" <?php if($risk_category === 'medium') echo 'selected'; ?>><?php echo isset($translations['Medium']) ? $translations['Medium'] : 'Medium'; ?></option>
                                                <option value="high" <?php if($risk_category === 'high') echo 'selected'; ?>><?php echo isset($translations['High']) ? $translations['High'] : 'High'; ?></option>
                                                <option value="critical" <?php if($risk_category === 'critical') echo 'selected'; ?>><?php echo isset($translations['Critical']) ? $translations['Critical'] : 'Critical'; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Inspection Results and Measures Section -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong><?php echo isset($translations['Inspection Results and Measures']) ? $translations['Inspection Results and Measures'] : 'Inspection Results and Measures'; ?></strong>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label"><?php echo isset($translations['Measure of result']) ? $translations['Measure of result'] : 'Measure of Result'; ?></label>
                                        <div class="col-sm-9" style="margin-top: 10px;">
                                            <div class="form-check mb-2">
                                                <input class="result-measure-radio" type="radio" name="result_measure" 
                                                       id="result_immediate_treatment" value="immediate_treatment"
                                                       <?php if($result_measure === 'immediate_treatment') echo 'checked'; ?>>
                                                <label class="form-check-label" for="result_immediate_treatment" style="padding-left: 0.3em; vertical-align: middle;">
                                                    <?php echo isset($translations['Immediately implement the treatment']) ? $translations['Immediately implement the treatment'] : 'Immediately implement the treatment'; ?>
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="result-measure-radio" type="radio" name="result_measure" 
                                                       id="result_not_accordance" value="not_accordance"
                                                       <?php if($result_measure === 'not_accordance') echo 'checked'; ?>>
                                                <label class="form-check-label" for="result_not_accordance" style="padding-left: 0.3em; vertical-align: middle;">
                                                    <?php echo isset($translations['Regulated article was not accordance']) ? $translations['Regulated article was not accordance'] : 'Regulated article was not accordance'; ?>
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="result-measure-radio" type="radio" name="result_measure" 
                                                       id="result_phytosanitary_requirements" value="phytosanitary_requirements"
                                                       <?php if($result_measure === 'phytosanitary_requirements') echo 'checked'; ?>>
                                                <label class="form-check-label" for="result_phytosanitary_requirements" style="padding-left: 0.3em; vertical-align: middle;">
                                                    <?php echo isset($translations['The regulated article was in accordance with Lao Phytosanitary requirements']) ? $translations['The regulated article was in accordance with Lao Phytosanitary requirements'] : 'The regulated article was in accordance with Lao Phytosanitary requirements'; ?>
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="result-measure-radio" type="radio" name="result_measure" 
                                                       id="result_other_conclusion" value="other_conclusion"
                                                       <?php if($result_measure === 'other_conclusion') echo 'checked'; ?>>
                                                <label class="form-check-label" for="result_other_conclusion" style="padding-left: 0.3em; vertical-align: middle;">
                                                   <?php echo isset($translations['Other conclusion']) ? $translations['Other conclusion'] : 'Other conclusion'; ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="row mb-3">
                                <div class="col-sm-12 d-flex gap-2">
                                    <button type="submit" name="btnSubmitPestDetected" 
                                            value="<?php echo $btnSubmitPest; ?>" 
                                            class="btn btn-success">
                                        <i class="bi bi-save"></i> <?php echo $btnSubmitPest === 'update' ? 'Update' : 'Submit'; ?>
                                    </button>
                                    <a href="transaction.php?part=inspection&inspect=View/Edit&appid=<?php echo $appid; ?>&uid=<?php echo $userid; ?>" 
                                       class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Back to Inspection
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pest Search Modal -->
    <div class="modal fade" id="pestSearchModal" tabindex="-1" aria-labelledby="pestSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pestSearchModalLabel">Search and Select Pest</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="pestSearchInput" placeholder="Search by pest name, scientific name, or category...">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="pestSearchTable">
                            <thead>
                                <tr>
                                    <th><?php echo isset($translations['Scientific Name']) ? $translations['Scientific Name'] : 'Scientific Name'; ?></th>
                                    <th><?php echo isset($translations['Pest Name']) ? $translations['Pest Name'] : 'Pest Name'; ?></th>
                                    <th><?php echo isset($translations['Category']) ? $translations['Category'] : 'Category'; ?></th>
                                    <th><?php echo isset($translations['Select']) ? $translations['Select'] : 'Select'; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get all pests from database
                                $pest_sql = "SELECT id, pestname, scientificname, category FROM tbpest ORDER BY pestname ASC";
                                $pest_result = pg_query($con, $pest_sql);
                                if ($pest_result && pg_num_rows($pest_result) > 0) {
                                    while ($pest_row = pg_fetch_assoc($pest_result)) {
                                        $pid = $pest_row['id'];
                                        $pname = htmlspecialchars($pest_row['pestname']);
                                        $scientific = htmlspecialchars($pest_row['scientificname'] ?? '');
                                        $category = htmlspecialchars($pest_row['category'] ?? '');
                                        
                                        echo "<tr>";
                                        echo "<td><em>$scientific</em></td>";
                                        echo "<td>$pname</td>";
                                        echo "<td>$category</td>";
                                        echo "<td><button type='button' class='btn btn-sm btn-danger' onclick='selectPestDetected($pid, \"$pname\")'>Select</button></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No pests found</td></tr>";
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
        // Pest selection function
        function selectPestDetected(pestId, pestName) {
            document.getElementById('pestid').value = pestId;
            document.getElementById('pest_name_display').value = pestName;
            
            // Close the modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('pestSearchModal'));
            modal.hide();
        }
        
        // Pest search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const pestSearchInput = document.getElementById('pestSearchInput');
            const pestSearchTable = document.getElementById('pestSearchTable');
            
            if (pestSearchInput && pestSearchTable) {
                pestSearchInput.addEventListener('keyup', function() {
                    const searchTerm = pestSearchInput.value.toLowerCase();
                    const rows = pestSearchTable.querySelectorAll('tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
    
    <?php
    } else {
        // Show normal inspection list
    ?>

    <section class="section dashboard">
      <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-8" style="width: 100%;">
          <div class="row">           
            <!-- Inspection list -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <h5 class="card-title"><?php echo isset($translations['Inspection List']) ? $translations['Inspection List'] : 'Inspection List'; ?><span>| <?php echo isset($translations['Recent']) ? $translations['Recent'] : 'Recent'; ?></span></h5>
                  <table class="table datatable" style="font-size: 10pt;">
                    <thead>
                      <tr>
                        <th scope="col"><?php echo isset($translations['Application date']) ? $translations['Application date'] : 'Application date'; ?></th>
                        <th scope="col"><?php echo isset($translations['Exporter']) ? $translations['Exporter'] : 'Exporter'; ?></th>
                        <th scope="col"><?php echo isset($translations['Inspection date']) ? $translations['Inspection date'] : 'Inspection date'; ?></th>
                        <th scope="col"><?php echo isset($translations['Sample collected by']) ? $translations['Sample collected by'] : 'Sample collected by'; ?></th>
                        <th scope="col"><?php echo isset($translations['Inspected by']) ? $translations['Inspected by'] : 'Inspected by'; ?></th>
                        <th scope="col"><?php echo isset($translations['Lot No']) ? $translations['Lot No'] : 'Lot No'; ?></th>
                        <th scope="col"><?php echo isset($translations['Action']) ? $translations['Action'] : 'Action'; ?></th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php InspectionList_items($guid, $con, $userid); ?>
                    </tbody>
                  </table>
                </div>
                <!-- treatement data table -->
                 <div class="card-body">
                  <h5 class="card-title"><?php echo isset($translations['Inspection - Treatment List']) ? $translations['Inspection - Treatment List'] : 'Inspection - Treatment List'; ?><span>| <?php echo isset($translations['Recent']) ? $translations['Recent'] : 'Recent'; ?></span></h5>
                  <table class="table datatable" style="font-size: 10pt;">
                    <thead>
                      <tr>
                        <th scope="col"><?php echo isset($translations['Application date']) ? $translations['Application date'] : 'Application date'; ?></th>
                        <th scope="col"><?php echo isset($translations['Exporter']) ? $translations['Exporter'] : 'Exporter'; ?></th>
                        <th scope="col"><?php echo isset($translations['Inspection date']) ? $translations['Inspection date'] : 'Inspection date'; ?></th>
                        <th scope="col"><?php echo isset($translations['Inspection method']) ? $translations['Inspection method'] : 'Inspection method'; ?></th>
                                                <th scope="col"><?php echo isset($translations['Pest detected']) ? $translations['Pest detected'] : 'Pest detected'; ?></th>
                        <th scope="col"><?php echo isset($translations['Treatment date']) ? $translations['Treatment date'] : 'Treatment date'; ?></th>
                        <th scope="col"><?php echo isset($translations['Treatment method']) ? $translations['Treatment method'] : 'Treatment method'; ?></th>
                        <th scope="col"><?php echo isset($translations['Action']) ? $translations['Action'] : 'Action'; ?></th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php Inspection_TreatmentList($guid, $con, $userid); ?>
                    </tbody>
                  </table>

                <!-- Inspection results data table -->
              <!--
                 <div class="card-body">
                  <h5 class="card-title"><?php echo isset($translations['Inspection Results List']) ? $translations['Inspection Results List'] : 'Inspection Results List'; ?><span>| <?php echo isset($translations['Recent']) ? $translations['Recent'] : 'Recent'; ?></span></h5>
                  <table class="table datatable" style="font-size: 10pt;">
                    <thead>
                      <tr>
                        <th scope="col"><?php echo isset($translations['Application date']) ? $translations['Application date'] : 'Application date'; ?></th>
                        <th scope="col"><?php echo isset($translations['Exporter']) ? $translations['Exporter'] : 'Exporter'; ?></th> 
                        <th scope="col"><?php echo isset($translations['Inspection date']) ? $translations['Inspection date'] : 'Inspection date'; ?></th>
                        <th scope="col"><?php echo isset($translations['Inspected by']) ? $translations['Inspected by'] : 'Inspected by'; ?></th>
                        <th scope="col"><?php echo isset($translations['Lot No']) ? $translations['Lot No'] : 'Lot No'; ?></th>
                        <th scope="col"><?php echo isset($translations['Pest Detected']) ? $translations['Pest Detected'] : 'Pest Detected'; ?></th>
                        <th scope="col"><?php echo isset($translations['Action']) ? $translations['Action'] : 'Action'; ?></th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php //PestDetectedInspectionList_items($guid, $con, $userid); ?>
                    </tbody>
                  </table>  

                </div>
                --> <!-- End treatement data table -->
              </div>
            </div><!-- End Inspection list -->
          </div>
        </div><!-- End Left side columns -->
        <!-- Right side columns *****************PK************************ -->
      </div>
    </section>
    
    <?php
    } // End of else block - normal inspection list
    ?>
 
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
  
  <!-- Custom JavaScript for Pest Selection -->
  <script>
    // Very simple pest selection function
    function selectPest(pestid, pestName, commonName, scientificName, pestType) {
      console.log('selectPest called with:', pestid, pestName, commonName, scientificName, pestType);
      
      try {
        // First, just test if we can find the fields
        var hpestidField = document.getElementById('hpestid');
        var pestNameField = document.getElementById('pest_name');
        var commonNameField = document.getElementById('common_name');
        var scientificNameField = document.getElementById('scientific_name');
        
        if (!hpestidField) {
          alert('hpestid field not found');
          return;
        }
        if (!pestNameField) {
          alert('pest_name field not found');
          return;
        }
        if (!commonNameField) {
          alert('common_name field not found');
          return;
        }
        if (!scientificNameField) {
          alert('scientific_name field not found');
          return;
        }
        
        // Populate the form fields
        hpestidField.value = pestid;
        pestNameField.value = pestName;
        commonNameField.value = commonName;
        scientificNameField.value = scientificName;
        
        // Show success
       // alert('Success! Pest selected: ' + pestName);
        
        // Try to close modal
        var modal = document.getElementById('pestlistModal');
        if (modal) {
          var bootstrapModal = bootstrap.Modal.getInstance(modal);
          if (bootstrapModal) {
            bootstrapModal.hide();
          }
        }
        
      } catch (error) {
        console.error('Error in selectPest:', error);
        alert('Error: ' + error.message);
      }
    }

    // Mutually exclusive checkboxes for inspection conclusions
    document.addEventListener('DOMContentLoaded', function() {
      const conclusionCheckboxes = ['treatment', 'return_original', 'phytosanitary_requirements', 'other_conclusion'];
      
      conclusionCheckboxes.forEach(checkboxId => {
        const checkbox = document.getElementById(checkboxId);
        if (checkbox) {
          checkbox.addEventListener('change', function() {
            if (this.checked) {
              // Uncheck all other checkboxes when this one is checked
              conclusionCheckboxes.forEach(otherId => {
                if (otherId !== checkboxId) {
                  const otherCheckbox = document.getElementById(otherId);
                  if (otherCheckbox) {
                    otherCheckbox.checked = false;
                  }
                }
              });
            }
          });
        }
      });
    });

    // Search functionality for pest list
    document.addEventListener('DOMContentLoaded', function() {
      const pestListSearch = document.getElementById('pestListSearch');
      const pestTypeFilter = document.getElementById('pestTypeFilter');
      const pestListTable = document.getElementById('pestListTable');
      
      function filterPestList() {
        const searchTerm = pestListSearch ? pestListSearch.value.toLowerCase() : '';
        const selectedType = pestTypeFilter ? pestTypeFilter.value.toLowerCase() : '';
        const rows = pestListTable ? pestListTable.querySelectorAll('tbody tr') : [];
        
        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          const pestType = row.getAttribute('data-pest-type') || '';
          
          const matchesSearch = !searchTerm || text.includes(searchTerm);
          const matchesType = !selectedType || pestType === selectedType;
          
          if (matchesSearch && matchesType) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      }
      
      if (pestListSearch && pestListTable) {
        pestListSearch.addEventListener('keyup', filterPestList);
      }
      
      if (pestTypeFilter && pestListTable) {
        pestTypeFilter.addEventListener('change', filterPestList);
      }
    });
  </script>

</body>
</html>