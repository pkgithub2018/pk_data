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

// Build language switch URLs preserving current query parameters
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
  <title><?php echo isset($translations['application']) ? $translations['application'] : 'Application'; ?></title>
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
  <script src="jspk/users-validate.js"></script>  
  <script src="jspk/transaction-process.js"></script>
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
        <a class="nav-link" href="transaction.php?part=application&uid=<?php echo $userid; ?>">
          <i class="bi bi-file-earmark-text"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
          <span><?php echo isset($translations['Application']) ? $translations['Application'] : 'Application'; ?></span>
        </a>
      </li><!-- End Application Nav --> 
       <li class="nav-item">
        <a class="nav-link collapsed" href="inspection.php?uid=<?php echo $userid; ?>">
          <i class="bi bi-journal-check"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
          <span><?php echo isset($translations['Inspection']) ? $translations['Inspection'] : 'Inspection'; ?></span>
        </a>
      </li><!-- End Inspection Nav --> 
       <li class="nav-item">
        <a class="nav-link collapsed" href="transaction.php?part=certificate&uid=<?php echo $userid; ?>">
          <i class="bi bi-journal-album"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
          <span><?php echo isset($translations['Certificate']) ? $translations['Certificate'] : 'Certificate'; ?></span>
        </a>
      </li><!-- End Certificate Nav --> 

      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=export&uid=<?php echo $userid; ?>" >
          <i class="bi bi-box-arrow-up-right"></i>
          <span><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Export entity'; ?></span>
        </a>
      </li><!-- End Export Entity Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=import&uid=<?php echo $userid; ?>" >
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
            <a href="masterdata.php?part=approvers&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Approvers']) ? $translations['Approvers'] : 'Approvers'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=conveyance&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Conveyance']) ? $translations['Conveyance'] : 'Conveyance'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=countries&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Countries']) ? $translations['Countries'] : 'Countries'; ?></span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Districts']) ? $translations['Districts'] : 'Districts'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=entitytype&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Entity_type']) ? $translations['Entity_type'] : 'Entity_type'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=inspectionmethod&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Inspection Method']) ? $translations['Inspection Method'] : 'Inspection Method'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=locations&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Locations']) ? $translations['Locations'] : 'Locations'; ?></span>
            </a>
          </li>
           <li>
            <a href="masterdata.php?part=pest&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Pest']) ? $translations['Pest'] : 'Pest'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=product&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Product']) ? $translations['Product'] : 'Product'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=provinces&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Provinces']) ? $translations['Provinces'] : 'Provinces'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=treatmentmethod&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Treatment Method']) ? $translations['Treatment Method'] : 'Treatment Method'; ?></span>
            </a>
          </li>
        </ul>
      </li><!-- End Master Data Nav -->
      <?php } // End of Admin group check ?>
      <li class="nav-heading"><?php echo isset($translations["USERS MANAGEMENT"]) ? $translations["USERS MANAGEMENT"] : "USERS' MANAGEMENT"; ?></li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php?uid=<?php echo $userid; ?>">
          <i class="bi bi-person"></i>
          <span><?php echo isset($translations['Profile']) ? $translations['Profile'] : 'Profile'; ?></span>
        </a>
      </li><!-- End Profile Page Nav -->
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
      <!-- pk**: End of User Admin-->
    </ul>
  </aside><!-- End Sidebar-->
  <main id="main" class="main">
    <?php 
      if($_GET['part'] == 'dashboard'){   // Dashboard part
    ?>
    <div class="pagetitle">
      <h1><?php echo $translations['Application']; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo $mainHref; ?>"><?php echo $translations['Home']; ?></a></li>
          <li class="breadcrumb-item active"><?php echo $translations['Dashboard']; ?></li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <!-- Charts Section -->
 <!--
    <section class="section">
      <div class="row">   
        <div class="col-lg-4"> 
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Application Status <span>| This Month</span></h5>
      <div id="kpiCard" style="min-height: 250px; display: flex; align-items: center; justify-content: center;">
        <div class="kpi-container" style="text-align: center; width: 100%;">
          <div style="margin-bottom: 20px; color: #6c757d; font-size: 14px; letter-spacing: 0.5px;">
            APPLICATIONS THIS MONTH
          </div>
          
          <div style="font-size: 64px; font-weight: 700; color: #2c3e50; margin-bottom: 15px; line-height: 1;">
            23
          </div>
          
          <div style="font-size: 16px; color: #495057; margin-bottom: 25px;">
            Total Applications
          </div>
          
          <div style="display: flex; justify-content: center; gap: 30px; margin-top: 30px;">
            <div style="text-align: center;">
              <div style="font-size: 24px; font-weight: 600; color: #27ae60;">15</div>
              <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">Submitted</div>
            </div>
            <div style="text-align: center;">
              <div style="font-size: 24px; font-weight: 600; color: #3498db;">5</div>
              <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">In Review</div>
            </div>
            <div style="text-align: center;">
              <div style="font-size: 24px; font-weight: 600; color: #9b59b6;">3</div>
              <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">Approved</div>
            </div>
          </div>
          
          <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e9ecef;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px; color: #27ae60; font-weight: 500;">
              <span style="font-size: 18px;">↑</span>
              <span>12% increase from last month</span>
            </div>
          </div>
        </div>
      </div>   
    </div>
  </div>
</div>
       
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Monthly Trends <span>| Last 6 Months</span></h5>
             
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
             
            </div>
          </div>
        </div>
       
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Export Entities <span>| Performance</span></h5>
             
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
             
            </div>
          </div>
        </div>
      </div>
    </section>
  -->
    <!-- End Charts Section -->
    <section class="section dashboard">
      <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-8" style="width: 100%;">
          <div class="row">           
            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <h5 class="card-title"><?php echo isset($translations['Application list']) ? $translations['Application list'] : 'Application list'; ?> <span>| <?php echo isset($translations['Recent']) ? $translations['Recent'] : 'Recent'; ?></span></h5>
                  <table class="table datatable" style="font-size: 10pt;">
                    <thead>
                      <tr>
                        <th scope="col"><?php echo isset($translations['Date']) ? $translations['Date'] : 'Date'; ?></th>
                        <th scope="col"><?php echo isset($translations['Exporter']) ? $translations['Exporter'] : 'Exporter'; ?></th>
                        <th scope="col"><?php echo isset($translations['Importer']) ? $translations['Importer'] : 'Importer'; ?></th>
                        <th scope="col"><?php echo isset($translations['Import country']) ? $translations['Import country'] : 'Import country'; ?></th>
                        <th scope="col"><?php echo isset($translations['Product']) ? $translations['Product'] : 'Product'; ?></th>
                        <th scope="col"><?php echo isset($translations['Action']) ? $translations['Action'] : 'Action'; ?></th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php ApplicationList_items($guid, $con, $userid); ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- End Recent Sales -->
          </div>
        </div><!-- End Left side columns -->
        <!-- Right side columns *****************PK************************ -->
      </div>
    </section>
  <?php
      }  // End of Dashboard part
  ?>
    <!-- Multiple PRODUCTS FORM ******* -->
     <?php
        if(isset($_GET['part']) && $_GET['part'] == 'multiple_products'){     
        // Accept either 'appid' or 'appid_edit' GET parameter (some links use appid_edit)
        $appid = '';
        if (isset($_GET['appid']) && $_GET['appid'] !== '') {
          $appid = $_GET['appid'];
        } elseif (isset($_GET['appid_edit']) && $_GET['appid_edit'] !== '') {
          $appid = $_GET['appid_edit'];
        }
     ?>
        <section class="section">
        <div class="row">
            <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                <h5 class="card-title">Multiple Products</h5>
                
                <!-- Form Section -->
                <form id="multipleProductsForm" method="POST">
                  <input type="hidden" id="appid" name="appid" value="<?php echo htmlspecialchars($appid); ?>">
                  <div class="row mb-3 align-items-center">
                    <label for="product_name" class="col-sm-2 col-form-label">Product Name</label>
                    <div class="col-sm-10">
                      <div class="input-group">
                        <input type="text" class="form-control" id="product_name" name="product_name" readonly style="background-color: #e7f3ff; border-color: #4a9eff;">
                        <button type="button" class="btn btn-outline-secondary" id="searchProductBtn">
                          <i class="bi bi-search"></i>
                        </button>
                      </div>
                      <input type="hidden" id="product_id" name="product_id" value="">
                    </div>
                  </div>

                  <div class="row mb-3 align-items-center">
                    <label for="scientific_name" class="col-sm-2 col-form-label">Scientific Name</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="scientific_name" name="scientific_name" readonly style="background-color: #e7f3ff; border-color: #4a9eff;">
                    </div>
                  </div>

                  <div class="row mb-3 align-items-center">
                    <label for="number_description" class="col-sm-2 col-form-label">Number and Description</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="number_description" name="number_description" style="background-color: #e7f3ff; border-color: #4a9eff;">
                    </div>
                  </div>

                  <div class="row mb-3 align-items-center">
                    <label for="net_quantity" class="col-sm-2 col-form-label">Net Quantity</label>
                    <div class="col-sm-4">
                      <input type="number" step="0.01" min="0" class="form-control" id="net_quantity" name="net_quantity" style="background-color: #e7f3ff; border-color: #4a9eff;">
                    </div>
                  </div>

                  <div class="row mb-3 align-items-center">
                    <label for="gross_quantity" class="col-sm-2 col-form-label">Gross Quantity</label>
                    <div class="col-sm-4">
                      <input type="number" step="0.01" min="0" class="form-control" id="gross_quantity" name="gross_quantity" style="background-color: #e7f3ff; border-color: #4a9eff;">
                    </div>
                  </div>

                  <div class="row mb-3 align-items-center">
                    <label for="unit" class="col-sm-2 col-form-label">Unit</label>
                    <div class="col-sm-4">
                      <select class="form-select" id="unit" name="unit" style="background-color: #e7f3ff; border-color: #4a9eff;">
                        <option value="">&nbsp;</option>
                        <?php SelectUnit($unitid, $con); ?>
                      </select>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <div class="col-sm-10 offset-sm-2">
                      <button type="button" class="btn btn-primary" id="addProductBtn">
                        <i class="bi bi-plus-circle"></i> Add
                      </button>
                    </div>
                  </div>
                </form>

                <hr>

                <!-- Data Table Section -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h5 class="mb-0">Products List</h5>
                  <button type="button" class="btn btn-outline-primary btn-sm" id="printTableBtn" title="Print Products List">
                    <i class="bi bi-printer"></i> Print
                  </button>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered table-striped" id="productsTable">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Product Name</th>
                        <th>Scientific Name</th>
                        <th>Number and Description</th>
                        <th>Net Quantity</th>
                        <th>Gross Quantity</th>
                        <th>Unit</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody id="productsTableBody">
                      <!-- Dynamic rows will be added here -->
                       <?php MultipleProductList($appid, $con); ?>
                    </tbody>
                  </table>
                </div>

                <!-- Form Actions -->
                <div class="row mt-4">
                  <div class="col-md-12 text-end">
                    <button type="button" class="btn btn-success" id="submitAllBtn">
                      <i class="bi bi-check-circle"></i> Submit
                    </button>
                    <button type="button" class="btn btn-secondary" id="cancelBtn">
                      <i class="bi bi-x-circle"></i> Cancel
                    </button>
                  </div>
                </div>

                </div>
            </div>

            <!-- Product Search Modal -->
            <div class="modal fade" id="productSearchModal" tabindex="-1" aria-labelledby="productSearchModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="productSearchModalLabel">Search Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <input type="text" id="productSearchInput" class="form-control" placeholder="Search products...">
                    </div>
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped" id="productSearchTable">
                        <thead>
                          <tr>
                            <th>Product Name</th>
                            <th>Scientific Name</th>
                            <th>Description</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Product list - SEARCH FOR PRODUCT -->
                          <?php ApplicationMultipleProductList($con); ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <script>
              console.log('=== SCRIPT LOADED ===');
              
              // Global counter for row numbering - initialize with existing rows count
              let productCounter = document.querySelectorAll('#productsTableBody tr').length;
              const productsArray = [];

              // Open product search modal - with debugging
              document.addEventListener('DOMContentLoaded', function() {
                console.log('=== DOMContentLoaded fired ===');
                const searchBtn = document.getElementById('searchProductBtn');
                const modal = document.getElementById('productSearchModal');
                
                console.log('Search button found:', searchBtn);
                console.log('Modal found:', modal);
                
                if (searchBtn && modal) {
                  console.log('Both elements found, attaching click listener');
                  searchBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('=== BUTTON CLICKED ===');
                  //  alert('Opening modal...');
                    try {
                      const bsModal = new bootstrap.Modal(modal);
                      bsModal.show();
                      console.log('Modal show() called');
                    } catch (error) {
                      console.error('Error opening modal:', error);
                      alert('Error: ' + error.message);
                    }
                  });
                  console.log('Event listener attached successfully');
                } else {
                  console.error('ERROR: Search button or modal not found!');
                  console.error('searchBtn:', searchBtn);
                  console.error('modal:', modal);
                  alert('Error: Search button or modal not found. Check console.');
                }
              });

                // Ensure hidden appid has a value — fall back to URL params (appid_edit or appid)
                (function() {
                  try {
                    const appidInput = document.getElementById('appid');
                    if (appidInput && (!appidInput.value || appidInput.value === '')) {
                      const params = new URLSearchParams(window.location.search);
                      const fallback = params.get('appid_edit') || params.get('appid') || '';
                      if (fallback) {
                        appidInput.value = fallback;
                        console.log('appid input populated from URL param:', fallback);
                      } else {
                        console.warn('No application id available in DOM or URL');
                      }
                    }
                  } catch (e) {
                    console.error('Error populating appid fallback:', e);
                  }
                })();

              // Function to select product from modal
              function selectProduct(id, name, scientificName) {
                document.getElementById('product_id').value = id;
                document.getElementById('product_name').value = name;
                document.getElementById('scientific_name').value = scientificName;
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('productSearchModal'));
                if (modal) {
                  modal.hide();
                }
              }

              // Add product to table
              document.getElementById('addProductBtn').addEventListener('click', function() {
                const productId = document.getElementById('product_id').value;
                const productName = document.getElementById('product_name').value;
                const scientificName = document.getElementById('scientific_name').value;
                const numberDescription = document.getElementById('number_description').value;
                const netQuantity = document.getElementById('net_quantity').value;
                const grossQuantity = document.getElementById('gross_quantity').value;
                const unitSelect = document.getElementById('unit');
                const unitId = unitSelect.value;
                const unitSymbol = unitSelect.options[unitSelect.selectedIndex].text;
                

                // Validation
                if (!productName || !netQuantity || !grossQuantity || !unitId) {
                  alert('Please fill in all required fields: Product Name, Net Quantity, Gross Quantity, and Unit');
                  return;
                }

                productCounter++;
                const product = {
                  id: productCounter,
                  productId: productId,
                  productName: productName,
                  scientificName: scientificName,
                  numberDescription: numberDescription,
                  netQuantity: netQuantity,
                  grossQuantity: grossQuantity,
                  unitId: unitId,
                  unitSymbol: unitSymbol
                };

                productsArray.push(product);
                addProductRow(product);

                // Clear form
                document.getElementById('multipleProductsForm').reset();
                document.getElementById('product_id').value = '';
              });

              // Function to add row to table
              function addProductRow(product) {
                const tableBody = document.getElementById('productsTableBody');
                const row = document.createElement('tr');
                row.setAttribute('data-id', product.id);
                
                // Get the current total number of rows (including database rows)
                const currentRowCount = tableBody.querySelectorAll('tr').length + 1;

                row.innerHTML = `
                  <td>${currentRowCount}</td>
                  <td>${product.productName}</td>
                  <td>${product.scientificName || '-'}</td>
                  <td>${product.numberDescription || '-'}</td>
                  <td>${product.netQuantity}</td>
                  <td>${product.grossQuantity}</td>
                  <td>${product.unitSymbol}</td>
                  <td>
                    <button type="button" class="btn btn-sm btn-warning" onclick="editProduct(${product.id})">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                `;

                tableBody.appendChild(row);
              }

              // Function to edit product
              function editProduct(id) {
                const product = productsArray.find(p => p.id === id);
                if (product) {
                  document.getElementById('product_id').value = product.productId;
                  document.getElementById('product_name').value = product.productName;
                  document.getElementById('scientific_name').value = product.scientificName;
                  document.getElementById('number_description').value = product.numberDescription;
                  document.getElementById('net_quantity').value = product.netQuantity;
                  document.getElementById('gross_quantity').value = product.grossQuantity;
                  document.getElementById('unit').value = product.unitId;

                  // Remove from array and table
                  deleteProduct(id);
                }
              }

              // Function to delete product
              function deleteProduct(id) {
                if (confirm('Are you sure you want to delete this product?')) {
                  const index = productsArray.findIndex(p => p.id === id);
                  if (index > -1) {
                    productsArray.splice(index, 1);
                  }

                  const row = document.querySelector(`tr[data-id="${id}"]`);
                  if (row) {
                    row.remove();
                    // Reorder all row numbers
                    reorderTableRows();
                  }
                }
              }

              // Function to edit product from database
              function editProductFromDb(button) {
                const row = button.closest('tr');
                const dbId = row.getAttribute('data-db-id');
                const productId = row.getAttribute('data-product-id');
                const unitId = row.getAttribute('data-unit-id');
                const cells = row.cells;
                
                // Populate form with data from row
                document.getElementById('product_id').value = productId;
                document.getElementById('product_name').value = cells[1].textContent;
                document.getElementById('scientific_name').value = cells[2].textContent;
                document.getElementById('number_description').value = cells[3].textContent;
                document.getElementById('net_quantity').value = cells[4].textContent;
                document.getElementById('gross_quantity').value = cells[5].textContent;
                document.getElementById('unit').value = unitId;
                
                // Delete the row from database and table
                deleteProductFromDb(button, false);
              }

              // Function to delete product from database
              function deleteProductFromDb(button, confirmDelete = true) {
                if (confirmDelete && !confirm('Are you sure you want to delete this product?')) {
                  return;
                }
                
                const row = button.closest('tr');
                const dbId = row.getAttribute('data-db-id');
                const appid = document.getElementById('appid').value;
                
                // Remove from database via AJAX
                fetch('transaction-dataprocess.php?action=delete_multiple_product', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                  },
                  body: JSON.stringify({ id: dbId, appid: appid })
                })
                .then(response => response.json())
                .then(data => {
                  if (data.success) {
                    row.remove();
                    // Reorder the row numbers
                    reorderTableRows();
                  } else {
                    alert('Error deleting product: ' + (data.message || 'Unknown error'));
                  }
                })
                .catch(error => {
                  console.error('Error:', error);
                  alert('Error deleting product. Please try again.');
                });
              }

              // Function to reorder table row numbers
              function reorderTableRows() {
                const rows = document.querySelectorAll('#productsTableBody tr');
                rows.forEach((row, index) => {
                  row.cells[0].textContent = index + 1;
                });
              }

              // Submit all products
              document.getElementById('submitAllBtn').addEventListener('click', function() {
                const tableBody = document.getElementById('productsTableBody');
                const rows = tableBody.querySelectorAll('tr');
                
                if (rows.length === 0) {
                  alert('Please add at least one product before submitting.');
                  return;
                }

                const appid = document.getElementById('appid').value;
                const allProducts = [];
                
                // Collect all products from table (both existing and new)
                rows.forEach(row => {
                  const cells = row.cells;
                  const productId = row.getAttribute('data-product-id');
                  const unitId = row.getAttribute('data-unit-id');
                  const dataId = row.getAttribute('data-id');
                  
                  if (dataId) {
                    // This is a newly added product - get from productsArray
                    const product = productsArray.find(p => p.id == dataId);
                    if (product) {
                      allProducts.push({
                        productId: product.productId,
                        numberDescription: product.numberDescription || '',
                        netQuantity: product.netQuantity,
                        grossQuantity: product.grossQuantity,
                        unitId: product.unitId
                      });
                    }
                  } else {
                    // This is an existing product from database
                    allProducts.push({
                      productId: productId,
                      numberDescription: cells[3].textContent.trim() === '-' ? '' : cells[3].textContent.trim(),
                      netQuantity: parseFloat(cells[4].textContent),
                      grossQuantity: parseFloat(cells[5].textContent),
                      unitId: unitId
                    });
                  }
                });
                
                // Prepare data for submission
                const submitData = {
                  appid: appid,
                  products: allProducts
                };

                console.log('Submitting all products:', submitData);

                // Ensure appid is present and numeric before submitting
                if (!appid || isNaN(appid)) {
                  alert('Invalid application id. Unable to submit products.');
                  return;
                }

                // AJAX call to save data to server (robust JSON parsing)
                fetch('transaction-dataprocess.php?action=save_multiple_products', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                  },
                  body: JSON.stringify(submitData)
                })
                .then(response => response.text())
                .then(text => {
                  let data;
                  try {
                    data = text ? JSON.parse(text) : {};
                  } catch (err) {
                    console.error('Invalid JSON response from server:', text);
                    alert('Server returned an invalid response. Check console for details.');
                    return;
                  }

                  if (data && data.success) {
                    // Use the number of submitted products from the payload for the message
                    const count = Array.isArray(submitData.products) ? submitData.products.length : 0;
                    alert('Successfully submitted ' + count + ' products!');
                    // Clear the table and array
                    productsArray.length = 0;
                    productCounter = 0;
                    document.getElementById('productsTableBody').innerHTML = '';
                    // Redirect back to the same page
                    window.location.href = 'transaction.php?part=application&appid_edit=' + encodeURIComponent(appid) + '&uid=<?php echo $userid; ?>';
                  } else {
                    alert('Error saving products: ' + (data && data.message ? data.message : 'Unknown error'));
                  }
                })
                .catch(error => {
                  console.error('Fetch error:', error);
                  alert('Network error while submitting products. Please try again.');
                });
              });

              // Cancel button
              document.getElementById('cancelBtn').addEventListener('click', function() {
                if (confirm('Are you sure you want to cancel? All unsaved data will be lost.')) {
                  window.location.href = 'transaction.php?part=application&appid_edit=<?php echo $appid; ?>&uid=<?php echo $userid; ?>';
                }
              });

              // Print table button
              document.getElementById('printTableBtn').addEventListener('click', function() {
                const tableBody = document.getElementById('productsTableBody');
                const rows = tableBody.querySelectorAll('tr');
                
                if (rows.length === 0) {
                  alert('No products to print.');
                  return;
                }

                // Create print window content
                let printContent = '<!DOCTYPE html>' +
                  '<html>' +
                  '<head>' +
                  '<title>Products List - Application #<?php echo $appid; ?></title>' +
                  '<style>' +
                  'body { font-family: Arial, sans-serif; padding: 20px; }' +
                  'h2 { text-align: center; margin-bottom: 20px; }' +
                  'table { width: 100%; border-collapse: collapse; margin-top: 20px; }' +
                  'th, td { border: 1px solid #000; padding: 8px; text-align: left; }' +
                  'th { background-color: #f2f2f2; font-weight: bold; }' +
                  '.text-center { text-align: center; }' +
                  '@media print { button { display: none; } }' +
                  '</style>' +
                  '</head>' +
                  '<body>' +
                  '<h2>Products List - Application #<?php echo $appid; ?></h2>' +
                  '<table>' +
                  '<thead>' +
                  '<tr>' +
                  '<th class="text-center">No</th>' +
                  '<th>Product Name</th>' +
                  '<th>Scientific Name</th>' +
                  '<th>Number and Description</th>' +
                  '<th class="text-center">Net Quantity</th>' +
                  '<th class="text-center">Gross Quantity</th>' +
                  '<th class="text-center">Unit</th>' +
                  '</tr>' +
                  '</thead>' +
                  '<tbody>';

                // Add all rows (excluding Action column)
                rows.forEach((row, index) => {
                  const cells = row.cells;
                  printContent += '<tr>' +
                    '<td class="text-center">' + (index + 1) + '</td>' +
                    '<td>' + cells[1].textContent + '</td>' +
                    '<td>' + cells[2].textContent + '</td>' +
                    '<td>' + cells[3].textContent + '</td>' +
                    '<td class="text-center">' + cells[4].textContent + '</td>' +
                    '<td class="text-center">' + cells[5].textContent + '</td>' +
                    '<td class="text-center">' + cells[6].textContent + '</td>' +
                    '</tr>';
                });

                printContent += '</tbody>' +
                  '</table>' +
                  '<script>' +
                  'window.onload = function() { window.print(); }' +
                  '<\/script>' +
                  '</body>' +
                  '</html>';

                // Open print window
                const printWindow = window.open('', '_blank', 'width=800,height=600');
                printWindow.document.write(printContent);
                printWindow.document.close();
              });

              // Product search filter
              document.getElementById('productSearchInput').addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('#productSearchTable tbody tr');
                rows.forEach(row => {
                  const text = row.textContent.toLowerCase();
                  row.style.display = text.includes(filter) ? '' : 'none';
                });
              });
            </script>
            </div>
        </div>
        </section>

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