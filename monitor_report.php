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
 $monitorMenu = isset($_GET['mn']) ? trim($_GET['mn']) : '';

 // Certificate search by certificate_no and guid
 $cert_show_modal = false;
 $cert_found = false;
 $cert_search_data = null;
 $cert_search_exporter = null;
 $cert_search_importer = null;
 $cert_search_import_country = null;
 $cert_search_origin_country = null;
 $cert_search_product = null;
 $cert_search_approver = null;

 if (isset($_GET['certificate_no']) && trim($_GET['certificate_no']) !== '') {
     $cert_show_modal = true;
     $search_cert_no = trim($_GET['certificate_no']);

     $cert_sql = "SELECT c.*, a.application_no, a.company_id, a.importerid,
                  a.country_import, a.certificate_type, a.commodity_id,
                  a.place_origin, a.conveyance_id, a.conveyance_sign,
                  a.quantity_gross, a.unit_id, a.name_scientific, a.marks_item,
                  a.import_point
                  FROM tbcertificate c
                  INNER JOIN tbapplication a ON c.application_id = a.id
                  WHERE c.certificate_no = \$1";

     $cert_result = pg_query_params($con, $cert_sql, [$search_cert_no]);

     if ($cert_result && pg_num_rows($cert_result) > 0) {
         $cert_found = true;
         $cert_search_data = pg_fetch_assoc($cert_result);

         $cert_search_exporter       = EntityExportInfo($cert_search_data['company_id'], $con);
         $cert_search_importer       = EntityImportInfo($cert_search_data['importerid'], $con);
         $cert_search_import_country = CountryInfo($cert_search_data['country_import'], $con);
         $cert_search_origin_country = CountryInfo($cert_search_data['place_origin'], $con);
         $cert_search_product        = ProductInfo($cert_search_data['commodity_id'], $con);
         $cert_search_approver       = ApproverInfo($cert_search_data['approved_by'], $con);
     }
 }

   $last_six_month_labels = [];
   for ($i = 5; $i >= 0; $i--) {
     $last_six_month_labels[] = date('M', strtotime("-{$i} months"));
   }
?>
<!DOCTYPE html>
<html lang="<?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == 'la') ? 'lo' : 'en'; ?>">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo isset($translations['Monitor and report']) ? $translations['Monitor and report'] : 'Monitor and report'; ?></title>
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
        <span class="d-none d-lg-block">e-Phytosanitary</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
    <!--
    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div>
-->
    <!-- End Search Bar -->
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <!-- Language Switcher -->
        <li class="nav-item dropdown">
          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <img src="assets/img/flags/<?php echo ($lang === 'en') ? 'english' : 'lao'; ?>.png" alt="<?php echo ($lang === 'en') ? 'English' : 'ລາວ'; ?>" style="width: 24px; height: 16px;">
            <span style="font-size: 14px;"><?php echo ($lang === 'en') ? 'English' : 'ລາວ'; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item d-flex align-items-center" href="?lang=la&uid=<?php echo $userid; ?>&part=<?php echo isset($_GET['part']) ? $_GET['part'] : 'dashboard_monitor'; ?>">
                <img src="assets/img/flags/lao.png" alt="Lao" style="width: 24px; height: 16px; margin-right: 10px;">
                <span>ລາວ</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="?lang=en&uid=<?php echo $userid; ?>&part=<?php echo isset($_GET['part']) ? $_GET['part'] : 'dashboard_monitor'; ?>">
                <img src="assets/img/flags/english.png" alt="English" style="width: 24px; height: 16px; margin-right: 10px;">
                <span>English</span>
              </a>
            </li>
          </ul>
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
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="#">
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
        <a class="nav-link collapsed" href="main.php?uid=<?php echo $userid; ?>"&lang=<?php echo $lang; ?>">
          <i class="bi bi-grid"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
          <span><?php echo isset($translations['Dashboard']) ? $translations['Dashboard'] : 'Dashboard'; ?></span>
        </a>
      </li><!-- End Dashboard Nav --> 

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

    <li class="nav-item">
        <a class="nav-link collapsed" href="application.php?part=dashboard&uid=<?php echo $userid; ?>"&lang=<?php echo $lang; ?>">
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
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span><?php echo isset($translations['Master data']) ? $translations['Master data'] : 'Master data'; ?></span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
         <li>
            <a href="masterdata.php?part=approvers&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Approvers']) ? $translations['Approvers'] : 'Approvers'; ?></span>
            </a>
          </li>
        <?php if($groupname == "admin"){ ?><!-- Admin group check -->
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
            <a href="masterdata.php?part=districts&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'districts') ? 'active' : ''; ?>">
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
         <?php } // End of Admin group check ?>
        </ul>
      </li><!-- End Master Data Nav -->

      <!-- Monitoring and Reporting -->
       <li class="nav-heading">Monitoring and Reporting</li>
        <li class="nav-item">
         
        <a class="nav-link collapsed" href="monitor_report.php?mn=certtrack&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bx bxs-file-find" style="font-size: 20px;"></i>
          <span><?php echo isset($translations['Certificate verification']) ? $translations['Certificate verification'] : 'Certificate verification'; ?></span>
        </a>
        <a class="nav-link" href="monitor_report.php?mn=datareport&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bx bxs-file-find" style="font-size: 20px;"></i>
          <span><?php echo isset($translations['Data reporting']) ? $translations['Data reporting'] : 'Data reporting'; ?></span>
        </a>
      </li><!-- End Monitoring and Reporting Nav -->
    


      <li class="nav-heading"><?php echo isset($translations["Users' Management"]) ? $translations["Users' Management"] : "Users' Management"; ?></li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php?uid=<?php echo $userid; ?>">
          <i class="bi bi-person"></i>
          <span><?php echo isset($translations['Profile']) ? $translations['Profile'] : 'Profile'; ?></span>
        </a>
      </li><!-- End Profile Page Nav -->
    <?php if($groupname == "admin"){ ?><!-- Admin group check -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=ugroup&uid=<?php echo $userid; ?>">
          <i class="bi bi-people"></i>
          <span><?php echo isset($translations['Users group']) ? $translations['Users group'] : 'Users group'; ?></span>
        </a>
      </li><!-- End Users group -->
       <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=upermits&uid=<?php echo $userid; ?>">
          <i class="bi bi-shield-lock"></i>
          <span><?php echo isset($translations['Group permits']) ? $translations['Group permits'] : 'Group permits'; ?></span>
        </a>
      </li><!-- End Permission: User Group and Module -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=userslist&uid=<?php echo $userid; ?>">
          <i class="bi bi-person-plus"></i><span><?php echo isset($translations['Users']) ? $translations['Users'] : 'Users'; ?></span>
        </a>
      </li>  
      <?php } // End of Admin group check ?>
      <!-- pk**: End of User Admin-->
    </ul>
  </aside><!-- End Sidebar-->
  <main id="main" class="main">
    <?php 
       // if(isset($_GET['part']) && $_GET['part'] == 'dashboard_monitor'){ 
     ?>
    <div class="pagetitle">
      <h1> Monitor and Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item active"><?php echo isset($translations['Monitor and Report']) ? $translations['Monitor and Report'] : 'Monitor and Report'; ?></li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    
    <?php if ($monitorMenu === 'certtrack') { ?>
    <!-- Certificate Search Form -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo isset($translations['Search Certificate']) ? $translations['Search Certificate'] : 'Search Certificate'; ?></h5>
              <form class="row g-3" method="GET" action="">
                <input type="hidden" name="part" value="dashboard_monitor">
                <input type="hidden" name="mn" value="certtrack">
                <input type="hidden" name="uid" value="<?php echo $userid; ?>">
                <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                <div class="col-md-8">
                  <label for="certificate_no" class="form-label">Certificate No.</label>
                  <input type="text" class="form-control" id="certificate_no" name="certificate_no" placeholder="Enter Certificate Number" value="<?php echo isset($_GET['certificate_no']) ? htmlspecialchars($_GET['certificate_no']) : ''; ?>">
                </div>
                <div class="col-md-4">
                  <label class="form-label">&nbsp;</label>
                  <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Search
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section><!-- End Certificate Search Form -->
    <?php } ?>
    
    <?php if ($monitorMenu === 'datareport') { ?>
    <section class="section dashboard">
      <div class="row">
        
        <!-- Left side columns - Main Content -->
        <div class="col-lg-9">
          <div class="row">           
            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Province Summary <span>| Last 6 Months</span></h5>
                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-outline-secondary btn-sm" title="Print Table" onclick="printProvinceSummary()">
                        <i class="bi bi-printer"></i>
                      </button>
                      <button type="button" class="btn btn-outline-success btn-sm" title="Export to Excel" onclick="exportProvinceSummaryToExcel()">
                        <i class="bi bi-file-earmark-excel"></i>
                      </button>
                    </div>
                  </div>
                  <table id="provinceSummaryTable" class="table table-bordered" style="font-size: 10pt;">
                    <thead>
                      <tr>
                        <th scope="col">No</th>
                        <th scope="col">Provinces</th>
                        <th scope="col"># of Export entities</th>
                        <th scope="col"># of Import entities</th>
                        <th scope="col">Values ($)</th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php ProvinceEntitySummaryLastSixMonths($con); ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End Recent Sales -->

            <!-- Province Monthly Matrix -->
            <div class="col-12 mt-3">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Province Monthly Values Matrix <span>| Last 6 Months</span></h5>
                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-outline-secondary btn-sm" title="Print Table" onclick="printProvinceMonthlyMatrix()">
                        <i class="bi bi-printer"></i>
                      </button>
                      <button type="button" class="btn btn-outline-success btn-sm" title="Export to Excel" onclick="exportProvinceMonthlyMatrixToExcel()">
                        <i class="bi bi-file-earmark-excel"></i>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Province Filter Box -->
                  <div class="row mt-3 mb-3">
                    <div class="col-md-6">
                      <small class="text-muted d-block mt-4">Showing <span id="provinceMonthlyRowCount">0</span> rows</small>
                    </div>
                    <div class="col-md-6">
                      <label for="provinceFilter" class="form-label">Filter by Province</label>
                      <div class="input-group">
                        <select class="form-control" id="provinceFilter" onchange="filterProvinceMonthlyTable()">
                          <option value="">-- All Provinces --</option>
                        </select>
                        <button class="btn btn-outline-secondary" type="button" onclick="clearProvinceMonthlyFilter()" title="Clear filter">
                          <i class="bi bi-x"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <!-- End Province Filter Box -->
                  
                  <table id="provinceMonthlyMatrixTable" class="table table-bordered" style="font-size: 10pt;">
                    <thead>
                      <tr>
                        <th scope="col">No</th>
                        <th scope="col">Province</th>
                        <?php foreach ($last_six_month_labels as $month_label): ?>
                          <th scope="col"><?php echo htmlspecialchars($month_label); ?></th>
                        <?php endforeach; ?>
                        <th scope="col">Values ($)</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php ProvinceMonthlyValueMatrixLastSixMonths($con); ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End Province Monthly Matrix -->

            <div class="col-12 mt-3">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Product Monthly Values Matrix <span>| Last 6 Months</span></h5>
                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-outline-secondary btn-sm" title="Print Table" onclick="printProductMonthlyMatrix()">
                        <i class="bi bi-printer"></i>
                      </button>
                      <button type="button" class="btn btn-outline-success btn-sm" title="Export to Excel" onclick="exportProductMonthlyMatrixToExcel()">
                        <i class="bi bi-file-earmark-excel"></i>
                      </button>
                    </div>
                  </div>
                  <table id="productMonthlyMatrixTable" class="table table-bordered mt-3" style="font-size: 10pt;">
                    <thead>
                      <tr>
                        <th scope="col">No</th>
                        <th scope="col">Product</th>
                        <?php foreach ($last_six_month_labels as $month_label): ?>
                          <th scope="col"><?php echo htmlspecialchars($month_label); ?></th>
                        <?php endforeach; ?>
                        <th scope="col">Values ($)</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php ProductMonthlyValueMatrixLastSixMonths($con); ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End Product Monthly Matrix -->
          </div>
        </div><!-- End Left side columns -->
        
        <!-- Right side columns - Charts Sidebar -->
        <div class="col-lg-3">
          
          <!-- Chart 1: Application Status Chart -->
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Province Values <span>| Last 6 Months</span></h5>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-outline-secondary btn-sm" title="Expand chart" onclick="expandChart('donutChart', 'Province Values - Last 6 Months')">
                    <i class="bi bi-arrows-fullscreen"></i>
                  </button>
                  <button type="button" class="btn btn-outline-success btn-sm" title="Download chart image" onclick="downloadChartImage('donutChart', 'province_values_last_6_months')">
                    <i class="bi bi-download"></i>
                  </button>
                </div>
              </div>
              <!-- Donut Chart -->
              <div id="donutChart" style="min-height: 250px;" class="echart"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  const tableRows = document.querySelectorAll('#provinceSummaryTable tbody tr');
                  const pieData = [];

                  tableRows.forEach((row) => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length < 5) {
                      return;
                    }

                    // Skip total row and any malformed rows.
                    if (cells[0].colSpan > 1 || cells[0].textContent.trim().toLowerCase() === 'total') {
                      return;
                    }

                    const province = cells[1].textContent.trim();
                    const rawValue = cells[4].textContent.trim();
                    const value = parseFloat(rawValue.replace(/[^0-9.-]/g, '')) || 0;

                    if (province !== '') {
                      pieData.push({ value: value, name: province });
                    }
                  });

                  if (pieData.length === 0) {
                    pieData.push({ value: 1, name: 'No Data' });
                  }

                  echarts.init(document.querySelector("#donutChart")).setOption({
                    tooltip: {
                      trigger: 'item'
                    },
                    legend: {
                      top: '2%',
                      left: 'center'
                    },
                    series: [{
                      name: 'Consignment Value',
                      type: 'pie',
                      radius: ['35%', '62%'],
                      center: ['50%', '68%'],
                      avoidLabelOverlap: false,
                      label: {
                        show: true,
                        position: 'inside',
                        formatter: '{c}',
                        fontSize: 10
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
                      data: pieData
                    }]
                  });
                });
              </script>
              <!-- End Donut Chart -->
            </div>
          </div>
          
          <!-- Chart 2: Monthly Applications Trend -->
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Monthly Trends <span>| Last 6 Months</span></h5>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-outline-secondary btn-sm" title="Expand chart" onclick="expandChart('reportsChart', 'Monthly Trends - Last 6 Months')">
                    <i class="bi bi-arrows-fullscreen"></i>
                  </button>
                  <button type="button" class="btn btn-outline-success btn-sm" title="Download chart image" onclick="downloadChartImage('reportsChart', 'monthly_trends_last_6_months')">
                    <i class="bi bi-download"></i>
                  </button>
                </div>
              </div>
              <!-- Line Chart -->
              <div id="reportsChart" style="min-height: 250px;" class="echart"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  const matrixTable = document.getElementById('provinceMonthlyMatrixTable');
                  const monthLabels = [];
                  const seriesData = [];

                  if (matrixTable) {
                    const headerCells = matrixTable.querySelectorAll('thead th');
                    // Columns: No, Province, [6 month columns], Values ($)
                    for (let i = 2; i < headerCells.length - 1; i++) {
                      monthLabels.push(headerCells[i].textContent.trim());
                    }

                    const bodyRows = matrixTable.querySelectorAll('tbody tr');
                    bodyRows.forEach((row) => {
                      const cells = row.querySelectorAll('td');
                      if (cells.length < 9) {
                        return;
                      }

                      const provinceName = cells[1].textContent.trim();
                      const values = [];
                      for (let i = 2; i < cells.length - 1; i++) {
                        const numericValue = parseFloat(cells[i].textContent.trim().replace(/[^0-9.-]/g, '')) || 0;
                        values.push(numericValue);
                      }

                      seriesData.push({
                        name: provinceName,
                        type: 'line',
                        data: values,
                        smooth: true
                      });
                    });
                  }

                  if (monthLabels.length === 0) {
                    monthLabels.push('No Data');
                  }

                  if (seriesData.length === 0) {
                    seriesData.push({
                      name: 'No Data',
                      type: 'line',
                      data: [0],
                      smooth: true
                    });
                  }

                  echarts.init(document.querySelector("#reportsChart")).setOption({
                    tooltip: {
                      trigger: 'axis'
                    },
                    legend: {
                      top: '2%',
                      data: seriesData.map((s) => s.name)
                    },
                    grid: {
                      left: '8%',
                      right: '5%',
                      top: '22%',
                      bottom: '10%',
                      containLabel: true
                    },
                    xAxis: {
                      type: 'category',
                      boundaryGap: false,
                      data: monthLabels
                    },
                    yAxis: {
                      type: 'value'
                    },
                    series: seriesData
                  });
                });
              </script>
              <!-- End Line Chart -->
            </div>
          </div>
          
          <!-- Chart 3: Export Entities Performance -->
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Top 3 Products <span>| Consignment Value (Last 6 Months)</span></h5>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-outline-secondary btn-sm" title="Expand chart" onclick="expandChart('columnChart', 'Top 3 Products - Last 6 Months')">
                    <i class="bi bi-arrows-fullscreen"></i>
                  </button>
                  <button type="button" class="btn btn-outline-success btn-sm" title="Download chart image" onclick="downloadChartImage('columnChart', 'top_3_products_last_6_months')">
                    <i class="bi bi-download"></i>
                  </button>
                </div>
              </div>
              <!-- Column Chart -->
              <div id="columnChart" style="min-height: 250px;" class="echart"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  const table = document.getElementById('productMonthlyMatrixTable');
                  const dataRows = [];

                  if (table) {
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach((row) => {
                      const cells = row.querySelectorAll('td');
                      if (cells.length < 3) {
                        return;
                      }

                      const firstCellText = cells[0].innerText.trim().toLowerCase();
                      const hasSummaryColspan = cells[0].colSpan && cells[0].colSpan > 1;
                      if (hasSummaryColspan || firstCellText === 'total') {
                        return;
                      }

                      const productName = cells[1].innerText.trim();
                      if (!productName) {
                        return;
                      }

                      const totalText = cells[cells.length - 1].innerText.replace(/,/g, '').trim();
                      const totalValue = parseFloat(totalText);
                      dataRows.push({
                        name: productName,
                        value: Number.isNaN(totalValue) ? 0 : totalValue
                      });
                    });
                  }

                  dataRows.sort((a, b) => b.value - a.value);
                  const topProducts = dataRows.slice(0, 3);
                  const xCategories = topProducts.map(item => item.name);
                  const yValues = topProducts.map(item => item.value);

                  echarts.init(document.querySelector("#columnChart")).setOption({
                    tooltip: {
                      trigger: 'axis',
                      axisPointer: {
                        type: 'shadow'
                      }
                    },
                    legend: {
                      top: '2%'
                    },
                    grid: {
                      left: '3%',
                      right: '4%',
                      bottom: '10%',
                      containLabel: true
                    },
                    xAxis: [{
                      type: 'category',
                      data: xCategories,
                      axisLabel: {
                        interval: 0,
                        rotate: 20
                      }
                    }],
                    yAxis: [{
                      type: 'value'
                    }],
                    series: [{
                      name: 'Consignment Value',
                      type: 'bar',
                      label: {
                        show: true,
                        position: 'top',
                        formatter: function (params) {
                          return Number(params.value).toLocaleString();
                        }
                      },
                      emphasis: {
                        focus: 'series'
                      },
                      data: yValues,
                      itemStyle: {
                        color: '#2E7D32'
                      }
                    }]
                  });
                });
              </script>
              <!-- End Column Chart -->
            </div>
          </div>
          
        </div><!-- End Right side columns - Charts Sidebar -->
        
      </div>
    </section>
    <?php } ?>
   <?php
   // }  // End of dashboard Monitor and report
   ?>
    <!-- PEST DETECTED FORM ******* -->
     <?php
        if(isset($_GET['part']) && $_GET['part'] == 'pest_detected'){
          $appid = isset($_GET['appid']) ? $_GET['appid'] : '';
          $pestDetectedInfo = PestDetectedInfo($appid, $con);

          $pestdet_id = isset($pestDetectedInfo['id']) ? (int)$pestDetectedInfo['id'] : 0;
          $pestid = isset($pestDetectedInfo['pestid']) ? (int)$pestDetectedInfo['pestid'] : 0;
          $infest_level = isset($pestDetectedInfo['infestation_level']) ? $pestDetectedInfo['infestation_level'] : '';
          $alive_status = isset($pestDetectedInfo['alive_status']) ? $pestDetectedInfo['alive_status'] : '';
          $risk_category = isset($pestDetectedInfo['risk_category']) ? $pestDetectedInfo['risk_category'] : '';
          $result_measure = isset($pestDetectedInfo['result_measure']) ? $pestDetectedInfo['result_measure'] : '';
          
          // Extract pest details if available
          $pestInfo = PestInfo($pestid, $con);
          $pest_name = isset($pestInfo['pestname']) ? $pestInfo['pestname'] : '';
          $common_name = isset($pestInfo['pestname']) ? $pestInfo['pestname'] : '';
          $scientific_name = isset($pestInfo['scientificname']) ? $pestInfo['scientificname'] : '';
          $hpestid = isset($pestInfo['id']) ? (int)$pestInfo['id'] : 0;
          
     ?>
        <section class="section">
        <div class="row">
            <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                <h5 class="card-title">Pest Detected Form</h5>
                <!-- Pest Detected Form - Submit to go back to Inspection -->
                <form method="post" action="transaction.php?part=inspection&uid=<?php echo $userid; ?>">
                  <input type="hidden" name="happid" value="<?php echo isset($_GET['appid']) ? $_GET['appid'] : ''; ?>">
                  <input type="hidden" name="hpestid" id="hpestid" value="<?php echo htmlspecialchars($hpestid); ?>"> <!-- To be filled when selecting pest from modal -->
                  <input type="hidden" name="pestdetected_id" value="<?php echo isset($pestDetectedInfo['id']) ? (int)$pestDetectedInfo['id'] : 0; ?>">
                  <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Pest Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="scientific_name" class="col-sm-2 col-form-label">Scientific Name</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="scientific_name" name="scientific_name" style="background-color: #e3f2fd; border: solid 1px black" value="<?php echo htmlspecialchars($scientific_name); ?>" required>
                                    <button type="button" class="btn btn-outline-secondary" style="background-color: #e3f2fd;" title="Search Pest Database" data-bs-toggle="modal" data-bs-target="#pestlistModal">
                                        <i class="bi bi-search" style="background-color: #e3f2fd;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="pest_name" class="col-sm-2 col-form-label">Pest Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pest_name" name="pest_name" style="background-color: #e3f2fd; border: solid 1px black" value="<?php echo htmlspecialchars($pest_name); ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="common_name" class="col-sm-2 col-form-label">Common Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="common_name" name="common_name" style="background-color: #e3f2fd; border: solid 1px black" value="<?php echo htmlspecialchars($common_name); ?>" required>
                            </div>
                        </div>
                        
                         <div class="row mb-3">
                            <label for="infestation_level" class="col-sm-2 col-form-label">Infestation Level</label>
                            <div class="col-sm-10">
                                <select class="form-select" id="infestation_level" name="infestation_level" style="background-color: #e3f2fd; border: solid 1px black" required>
                                    <option value="" <?php echo empty($infest_level) ? 'selected' : ''; ?>>&nbsp;</option>
                                    <option value="trace" <?php echo ($infest_level == 'trace') ? 'selected' : ''; ?>>Trace</option>
                                    <option value="low" <?php echo ($infest_level == 'low') ? 'selected' : ''; ?>>Low</option>
                                    <option value="medium" <?php echo ($infest_level == 'medium') ? 'selected' : ''; ?>>Medium</option>
                                    <option value="high" <?php echo ($infest_level == 'high') ? 'selected' : ''; ?>>High</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="remarks" class="col-sm-2 col-form-label">Alive Status</label>
                            <div class="col-sm-10">
                               <select class="form-select" id="alive_status" name="alive_status" style="background-color: #e3f2fd;border: solid 1px black" required>
                                    <option value="" <?php echo empty($alive_status) ? 'selected' : ''; ?>>&nbsp;</option>
                                    <option value="dead" <?php echo ($alive_status == 'dead') ? 'selected' : ''; ?>>Dead</option>
                                    <option value="alive" <?php echo ($alive_status == 'alive') ? 'selected' : ''; ?>>Alive</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="remarks" class="col-sm-2 col-form-label">Risk Category</label>
                            <div class="col-sm-10">
                                <select class="form-select" id="risk_category" name="risk_category" style="background-color: #e3f2fd; border: solid 1px black" required>
                                    <option value="" <?php echo empty($risk_category) ? 'selected' : ''; ?>>&nbsp;</option>
                                    <option value="low" <?php echo ($risk_category == 'low') ? 'selected' : ''; ?>>Quarantine pest</option>
                                    <option value="medium" <?php echo ($risk_category == 'medium') ? 'selected' : ''; ?>>Regulated None Quarantine pest</option>
                                    <option value="high" <?php echo ($risk_category == 'high') ? 'selected' : ''; ?>>None Quarantine pest</option>
                                    <option value="very_high" <?php echo ($risk_category == 'very_high') ? 'selected' : ''; ?>>Unknown pest</option>
                                </select>
                            </div>
                        </div>
                    </div> <!-- End of pest information -->
                  </div> <!-- End of Pest Information Card -->

                  <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Inspection Results and Measures</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="treatment" class="col-sm-6 col-form-label">Immediately implement the treatment as specified</label>
                            <div class="col-sm-4">
                                <input type="checkbox" class="form-check-input border border-dark" id="treatment" name="treatment" style="background-color: #e3f2fd; width: 1.5em; height: 1.5em;" value="1" <?php echo ($result_measure == 'treatment') ? 'checked' : ''; ?>>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="return_to_original_place" class="col-sm-6 col-form-label">Regulated article was not accordance. Return to the original place</label>
                            <div class="col-sm-4">
                                <input type="checkbox" class="form-check-input border border-dark" id="return_original" name="return_original" style="background-color: #e3f2fd;width: 1.5em; height: 1.5em;" value="1" <?php echo ($result_measure == 'return_original') ? 'checked' : ''; ?>>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="phytosanitary_requirements" class="col-sm-6 col-form-label">Phytosanitary requirements</label>
                            <div class="col-sm-4">
                                <input type="checkbox" class="form-check-input border border-dark" id="phytosanitary_requirements" name="phytosanitary_requirements" style="background-color: #e3f2fd; width: 1.5em; height: 1.5em;" value="1" <?php echo ($result_measure == 'phytosanitary_requirements') ? 'checked' : ''; ?>>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="other_conclusion" class="col-sm-6 col-form-label">Other conclusion</label>
                            <div class="col-sm-4">
                                <input type="checkbox" class="form-check-input border border-dark" id="other_conclusion" name="other_conclusion" style="background-color: #e3f2fd;width: 1.5em; height: 1.5em;" value="1" <?php echo ($result_measure == 'other_conclusion') ? 'checked' : ''; ?>>
                            </div>
                        </div>

                    </div> <!-- End of In -->
                  </div> <!-- End of Inspection results and measure -->
                  <div class="text-left">
                    <button type="submit" name="save_continue_pest" id="save_continue_pest" class="btn btn-primary" value="<?php echo ($pestDetectedInfo && !empty($pestDetectedInfo)) ? 'Update & Continue' : 'Save & Continue'; ?>"><?php echo ($pestDetectedInfo && !empty($pestDetectedInfo)) ? 'Update & Continue' : 'Save & Continue'; ?></button>&nbsp;
                    <button type="submit" name="cancel_continue_pest" id="cancel_continue_pest" class="btn btn-secondary">Cancel & Continue</button>
                  </div>
                </form><!-- End Pest Detected Form -->
                </div>
            </div>
            </div>
        </div>
        </section>

  <!-- Modal form for Pest List Selection -->
  <div class="modal fade" id="pestlistModal" tabindex="-1" aria-labelledby="pestlistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="pestlistModalLabel">Select Pest from Database</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Search Input -->
          <div class="row mb-3">
            <div class="col-md-6">
              <input type="text" id="pestListSearch" class="form-control" placeholder="Search by pest name, common name, or scientific name...">
            </div>
            <div class="col-md-6">
              <select id="pestTypeFilter" class="form-select">
                <option value="">All Pest Types</option>
                <option value="insect">Insect</option>
                <option value="disease">Disease/Pathogen</option>
                <option value="weed">Weed</option>
                <option value="nematode">Nematode</option>
                <option value="virus">Virus</option>
                <option value="bacteria">Bacteria</option>
                <option value="fungus">Fungus</option>
                <option value="mite">Mite</option>
                <option value="mollusk">Mollusk</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>
          
          <!-- Pest List Table -->
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="pestListTable">
              <thead>
                <tr>
                  <th>Scientific Name</th>
                  <th>Pest Name</th>
                  <th>Category</th>
                  <th>Select</th>
                </tr>
              </thead>
              <tbody>
               <?php
                $pest_counter = PestSelectionList($con);
               ?>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination Info -->
          <div class="row mt-3">
            <div class="col-md-6">
              <small class="text-muted" id="pestListInfo">
                Showing <span id="visibleRows">0</span> of <span id="totalRows"><?php echo $pest_counter; ?></span> pests
              </small>
            </div>
            <div class="col-md-6 text-end">
              <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshPestList()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
              </button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle"></i> Cancel
          </button>
          <button type="button" class="btn btn-info" onclick="clearPestFilters()">
            <i class="bi bi-funnel"></i> Clear Filters
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Modal form for Pest List Selection -->

     <?php
        }  // End of pest detected form
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
    function printProvinceSummary() {
      const table = document.getElementById('provinceSummaryTable');
      if (!table) {
        return;
      }

      const printWindow = window.open('', '_blank');
      if (!printWindow) {
        return;
      }

      const printableHtml = `
        <html>
          <head>
            <title>Province Summary - Last 6 Months</title>
            <style>
              body { font-family: Arial, sans-serif; padding: 20px; }
              h3 { margin-bottom: 12px; }
              table { width: 100%; border-collapse: collapse; font-size: 12px; }
              th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
              th { background: #f2f2f2; }
            </style>
          </head>
          <body>
            <h3>Province Summary | Last 6 Months</h3>
            ${table.outerHTML}
          </body>
        </html>`;

      printWindow.document.open();
      printWindow.document.write(printableHtml);
      printWindow.document.close();

      // Wait for the new document to fully render before triggering print.
      printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
      };

      // Close only after print dialog completes.
      printWindow.onafterprint = function() {
        printWindow.close();
      };
    }

    function exportProvinceSummaryToExcel() {
      const table = document.getElementById('provinceSummaryTable');
      if (!table) {
        return;
      }

      const html = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel"
              xmlns="http://www.w3.org/TR/REC-html40">
          <head>
            <meta charset="UTF-8">
            <style>
              table { border-collapse: collapse; }
              th, td { border: 1px solid #000; padding: 6px; }
            </style>
          </head>
          <body>
            <table>${table.innerHTML}</table>
          </body>
        </html>`;

      const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = 'province_summary_last_6_months.xls';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
    }

    function printProvinceMonthlyMatrix() {
      const table = document.getElementById('provinceMonthlyMatrixTable');
      if (!table) {
        return;
      }

      const printWindow = window.open('', '_blank');
      if (!printWindow) {
        return;
      }

      const printableHtml = `
        <html>
          <head>
            <title>Province Monthly Values Matrix - Last 6 Months</title>
            <style>
              body { font-family: Arial, sans-serif; padding: 20px; }
              h3 { margin-bottom: 12px; }
              table { width: 100%; border-collapse: collapse; font-size: 12px; }
              th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
              th { background: #f2f2f2; }
            </style>
          </head>
          <body>
            <h3>Province Monthly Values Matrix | Last 6 Months</h3>
            ${table.outerHTML}
          </body>
        </html>`;

      printWindow.document.open();
      printWindow.document.write(printableHtml);
      printWindow.document.close();

      printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
      };

      printWindow.onafterprint = function() {
        printWindow.close();
      };
    }

    function exportProvinceMonthlyMatrixToExcel() {
      const table = document.getElementById('provinceMonthlyMatrixTable');
      if (!table) {
        return;
      }

      const html = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel"
              xmlns="http://www.w3.org/TR/REC-html40">
          <head>
            <meta charset="UTF-8">
            <style>
              table { border-collapse: collapse; }
              th, td { border: 1px solid #000; padding: 6px; }
            </style>
          </head>
          <body>
            <table>${table.innerHTML}</table>
          </body>
        </html>`;

      const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = 'province_monthly_values_matrix_last_6_months.xls';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
    }

    // Province Monthly Table Filter Functions
    function populateProvinceFilter() {
      const table = document.getElementById('provinceMonthlyMatrixTable');
      const select = document.getElementById('provinceFilter');
      if (!table || !select) return;

      const rows = table.querySelectorAll('tbody tr');
      const provinces = new Set();

      rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const noCell = cells[0]; // First column (No)
        const provinceCell = cells[1]; // Province is in 2nd column
        
        if (noCell && provinceCell) {
          const noText = noCell.textContent.trim().toLowerCase();
          const provinceName = provinceCell.textContent.trim();
          
          // Skip total rows (where No column contains "total") and empty entries
          if (provinceName && noText !== 'total') {
            provinces.add(provinceName);
          }
        }
      });

      // Sort provinces alphabetically
      const sortedProvinces = Array.from(provinces).sort();

      // Clear existing options (keep the first "All Provinces" option)
      while (select.options.length > 1) {
        select.remove(1);
      }

      // Add province options
      sortedProvinces.forEach(province => {
        const option = document.createElement('option');
        option.value = province;
        option.textContent = province;
        select.appendChild(option);
      });
    }

    function filterProvinceMonthlyTable() {
      const filterValue = document.getElementById('provinceFilter').value.trim();
      const table = document.getElementById('provinceMonthlyMatrixTable');
      const rows = table.querySelectorAll('tbody tr');
      let visibleCount = 0;

      rows.forEach(row => {
        const provinceCell = row.querySelectorAll('td')[1]; // Province is in 2nd column
        const provinceName = provinceCell ? provinceCell.textContent.trim() : '';
        
        if (filterValue === '' || provinceName === filterValue) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      });

      // Update row count
      document.getElementById('provinceMonthlyRowCount').textContent = visibleCount;
    }

    function clearProvinceMonthlyFilter() {
      document.getElementById('provinceFilter').value = '';
      const table = document.getElementById('provinceMonthlyMatrixTable');
      const rows = table.querySelectorAll('tbody tr');
      
      rows.forEach(row => {
        row.style.display = '';
      });

      document.getElementById('provinceMonthlyRowCount').textContent = rows.length;
    }

    function printProductMonthlyMatrix() {
      const table = document.getElementById('productMonthlyMatrixTable');
      if (!table) {
        return;
      }

      const printWindow = window.open('', '_blank', 'width=900,height=600');
      printWindow.document.write(`
        <html>
          <head>
            <title>Product Monthly Values Matrix - Last 6 Months</title>
            <style>
              body { font-family: Arial, sans-serif; font-size: 10pt; }
              table { border-collapse: collapse; width: 100%; }
              th, td { border: 1px solid #000; padding: 6px; text-align: left; }
              th { background-color: #f0f0f0; }
            </style>
          </head>
          <body>
            <h3>Product Monthly Values Matrix | Last 6 Months</h3>
            <table>${table.innerHTML}</table>
          </body>
        </html>`);
      printWindow.document.close();
      printWindow.onload = function() {
        printWindow.print();
      };
      printWindow.onafterprint = function() {
        printWindow.close();
      };
    }

    function exportProductMonthlyMatrixToExcel() {
      const table = document.getElementById('productMonthlyMatrixTable');
      if (!table) {
        return;
      }

      const html = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel"
              xmlns="http://www.w3.org/TR/REC-html40">
          <head>
            <meta charset="UTF-8">
            <style>
              table { border-collapse: collapse; }
              th, td { border: 1px solid #000; padding: 6px; }
            </style>
          </head>
          <body>
            <table>${table.innerHTML}</table>
          </body>
        </html>`;

      const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = 'product_monthly_values_matrix_last_6_months.xls';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
    }

    function expandChart(chartId, chartTitle) {
      const chartElement = document.getElementById(chartId);
      if (!chartElement) {
        return;
      }

      const chartInstance = echarts.getInstanceByDom(chartElement);
      if (!chartInstance) {
        return;
      }

      const imageDataUrl = chartInstance.getDataURL({
        type: 'png',
        pixelRatio: 2,
        backgroundColor: '#ffffff'
      });

      const popup = window.open('', '_blank', 'width=1200,height=800');
      if (!popup) {
        return;
      }

      popup.document.write(`
        <html>
          <head>
            <title>${chartTitle}</title>
            <style>
              body { margin: 0; padding: 16px; font-family: Arial, sans-serif; background: #f5f5f5; }
              h3 { margin: 0 0 12px 0; }
              .chart-wrap { background: #fff; border: 1px solid #ddd; padding: 12px; }
              img { width: 100%; height: auto; display: block; }
            </style>
          </head>
          <body>
            <h3>${chartTitle}</h3>
            <div class="chart-wrap">
              <img src="${imageDataUrl}" alt="${chartTitle}">
            </div>
          </body>
        </html>
      `);
      popup.document.close();
    }

    function downloadChartImage(chartId, fileName) {
      const chartElement = document.getElementById(chartId);
      if (!chartElement) {
        return;
      }

      const chartInstance = echarts.getInstanceByDom(chartElement);
      if (!chartInstance) {
        return;
      }

      const imageDataUrl = chartInstance.getDataURL({
        type: 'png',
        pixelRatio: 2,
        backgroundColor: '#ffffff'
      });

      const link = document.createElement('a');
      link.href = imageDataUrl;
      link.download = `${fileName}.png`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

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

<!-- Certificate Search Result Modal -->
<div class="modal fade" id="certSearchModal" tabindex="-1" aria-labelledby="certSearchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%); color: white;">
        <h5 class="modal-title" id="certSearchModalLabel">
          <i class="bi bi-shield-check me-2"></i>Certificate Search Result
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if ($cert_show_modal): ?>
          <?php if ($cert_found && $cert_search_data): ?>

            <div class="text-center mb-3">
              <span class="badge bg-success fs-6 px-4 py-2">
                <i class="bi bi-check-circle-fill me-2"></i>Certificate Found
              </span>
            </div>

            <!-- Certificate Details -->
            <h6 class="text-success fw-bold border-bottom border-2 pb-2 mb-3">
              <i class="bi bi-file-earmark-text me-2"></i>Certificate Details
            </h6>
            <div class="row mb-2">
              <div class="col-md-6">
                <div class="py-2 border-bottom">
                  <div class="fw-semibold text-muted small">Certificate Number:</div>
                  <div class="fw-bold"><?php echo htmlspecialchars($cert_search_data['certificate_no']); ?></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="py-2 border-bottom">
                  <div class="fw-semibold text-muted small">Application Number:</div>
                  <div><?php echo htmlspecialchars($cert_search_data['application_no']); ?></div>
                </div>
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-md-6">
                <div class="py-2 border-bottom">
                  <div class="fw-semibold text-muted small">Date Issued:</div>
                  <div><?php echo !empty($cert_search_data['date_issued']) ? date('d-M-Y', strtotime($cert_search_data['date_issued'])) : 'N/A'; ?></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="py-2 border-bottom">
                  <div class="fw-semibold text-muted small">Place Issued:</div>
                  <div><?php echo htmlspecialchars($cert_search_data['place_issued'] ?? 'N/A'); ?></div>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="py-2 border-bottom">
                  <div class="fw-semibold text-muted small">Certificate Type:</div>
                  <div><span class="badge bg-success"><?php echo strtoupper(htmlspecialchars($cert_search_data['certificate_type'] ?? '')); ?></span></div>
                </div>
              </div>
            </div>

            <!-- Exporter Information -->
            <h6 class="text-success fw-bold border-bottom border-2 pb-2 mb-3">
              <i class="bi bi-building me-2"></i>Exporter Information
            </h6>
            <div class="py-2 border-bottom mb-2">
              <div class="fw-semibold text-muted small">Exporter Name:</div>
              <div><?php echo htmlspecialchars($cert_search_exporter['title'] ?? 'N/A'); ?></div>
            </div>
            <div class="py-2 border-bottom mb-3">
              <div class="fw-semibold text-muted small">Address:</div>
              <div><?php echo htmlspecialchars($cert_search_exporter['address'] ?? 'N/A'); ?></div>
            </div>

            <!-- Importer Information -->
            <h6 class="text-success fw-bold border-bottom border-2 pb-2 mb-3">
              <i class="bi bi-globe me-2"></i>Importer Information
            </h6>
            <div class="py-2 border-bottom mb-2">
              <div class="fw-semibold text-muted small">Importer Name:</div>
              <div><?php echo htmlspecialchars($cert_search_importer['title'] ?? 'N/A'); ?></div>
            </div>
            <div class="py-2 border-bottom mb-3">
              <div class="fw-semibold text-muted small">Destination Country:</div>
              <div><i class="bi bi-geo-alt-fill text-danger me-1"></i><?php echo htmlspecialchars($cert_search_import_country['title'] ?? 'N/A'); ?></div>
            </div>

            <!-- Product Information -->
            <h6 class="text-success fw-bold border-bottom border-2 pb-2 mb-3">
              <i class="bi bi-box-seam me-2"></i>Product Information
            </h6>
            <div class="py-2 border-bottom mb-2">
              <div class="fw-semibold text-muted small">Product / Commodity:</div>
              <div><?php echo htmlspecialchars($cert_search_product['name'] ?? 'N/A'); ?></div>
            </div>
            <div class="py-2 border-bottom mb-3">
              <div class="fw-semibold text-muted small">Place of Origin:</div>
              <div><?php echo htmlspecialchars($cert_search_origin_country['title'] ?? 'N/A'); ?></div>
            </div>

            <!-- Authorized Officer -->
            <h6 class="text-success fw-bold border-bottom border-2 pb-2 mb-3">
              <i class="bi bi-person-badge me-2"></i>Authorized By
            </h6>
            <div class="py-2 border-bottom mb-2">
              <div class="fw-semibold text-muted small">Authorized Officer:</div>
              <div>
                <?php
                  $cs_officer = trim(($cert_search_approver['name'] ?? '') . ' ' . ($cert_search_approver['surname'] ?? ''));
                  echo htmlspecialchars(strtoupper($cs_officer)) ?: 'N/A';
                ?>
              </div>
            </div>
            <div class="py-2 border-bottom mb-3">
              <div class="fw-semibold text-muted small">Position:</div>
              <div><?php echo htmlspecialchars($cert_search_data['position_approved'] ?? 'N/A'); ?></div>
            </div>

          <?php else: ?>

            <!-- Certificate not found -->
            <div class="text-center py-5">
              <i class="bi bi-x-circle-fill text-danger" style="font-size: 3rem;"></i>
              <h5 class="mt-3 text-danger">The certificate is not found</h5>
              <p class="text-muted">No certificate matching "<strong><?php echo htmlspecialchars($_GET['certificate_no'] ?? ''); ?></strong>" was found in the system.</p>
            </div>

          <?php endif; ?>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <a href="monitor_report.php?part=dashboard_monitor&uid=<?php echo urlencode($userid); ?>&lang=<?php echo urlencode($lang); ?>" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i>Back to Monitor Report
        </a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php if ($cert_show_modal): ?>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var certModal = new bootstrap.Modal(document.getElementById('certSearchModal'));
    certModal.show();
  });
</script>
<?php endif; ?>

<!-- Initialize Province Monthly Table Row Count and Populate Filter -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('provinceMonthlyMatrixTable');
    if (table) {
      const rows = table.querySelectorAll('tbody tr');
      document.getElementById('provinceMonthlyRowCount').textContent = rows.length;
      // Populate the province filter dropdown
      populateProvinceFilter();
    }
  });
</script>

</body>
</html>