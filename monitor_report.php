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
    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->
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
        <a class="nav-link collapsed" href="main.php?uid=<?php echo $userid; ?>">
          <i class="bi bi-grid"></i>  <!-- set color: style="color: #28a745; font-size: 1.5em;" -->
          <span><?php echo isset($translations['Dashboard']) ? $translations['Dashboard'] : 'Dashboard'; ?></span>
        </a>
      </li><!-- End Dashboard Nav --> 
    <li class="nav-item">
        <a class="nav-link collapsed" href="transaction.php?part=application&uid=<?php echo $userid; ?>">
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
            <a href="masterdata.php?part=approvers&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span>Approvers</span>
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
            <a href="masterdata.php?part=pest&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span>Pest</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=product&uid=<?php echo $userid; ?>">
              <i class="bi bi-circle"></i><span>Product</span>
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

      <!-- Monitoring and Reporting -->
       <li class="nav-heading">Monitoring and Reporting</li>
        <li class="nav-item">
        <a class="nav-link" href="monitor_report.php?part=dashboard_monitor&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bx bxs-file-find" style="font-size: 20px;"></i>
          <span>Certificate tracking</span>
        </a>
      </li><!-- End Monitoring and Reporting Nav -->
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
    <?php 
       // if(isset($_GET['part']) && $_GET['part'] == 'dashboard_monitor'){ 
     ?>
    <div class="pagetitle">
      <h1> Monitor and Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Monitor and Report</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    
    <!-- Certificate Search Form -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Search Certificate</h5>
              <form class="row g-3" method="GET" action="">
                <input type="hidden" name="part" value="dashboard_monitor">
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
    
    <section class="section dashboard">
      <div class="row">
        
        <!-- Left side columns - Main Content -->
        <div class="col-lg-9">
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
            </div><!-- End Recent Sales -->
          </div>
        </div><!-- End Left side columns -->
        
        <!-- Right side columns - Charts Sidebar -->
        <div class="col-lg-3">
          
          <!-- Chart 1: Application Status Chart -->
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
          
          <!-- Chart 2: Monthly Applications Trend -->
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
          
          <!-- Chart 3: Export Entities Performance -->
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
          
        </div><!-- End Right side columns - Charts Sidebar -->
        
      </div>
    </section>
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