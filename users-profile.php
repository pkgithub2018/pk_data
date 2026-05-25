<?php
      // Pk: 2025-07-03
      // $_SESSION is not working, so we will use a workaround by passing user ID in the URL
  /*
  ini_set('session.cookie_secure', 1);
  ini_set('session.cookie_httponly', 1);
  ini_set('session.use_strict_mode', 1);
  ini_set('session.cookie_samesite', 'Lax');

  session_start();
  */

  require("php-bin/connection.php"); // replace include with require
  require("php-bin/supports.php"); // replace include with require

 // $userid = isset($_SESSION["uid"]) ? $_SESSION["uid"] : ''; // User ID
  // USER DATA - Dynamic Authentication System
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
 
 // Permission check for entity menu
 $entityPermit = UserPermitCheck($userid, 'FRM - ENTITY', $con);

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
        'Profile' => 'Profile',
        'Users Profile' => 'Users Profile'
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

  $userinfo = Userdata($userid, $con);
  $loginuser = isset($userinfo['name']) ? $userinfo['name'] : ''; // Name of user
  $uname = isset($userinfo['email']) ? $userinfo['email'] : ''; // Use email as login name

  $usname = isset($userinfo['surname']) ? $userinfo['surname'] : ''; // Surname
  $ufullname = $loginuser."  ".$usname;  // Full name
  $position = isset($userinfo['position']) ? $userinfo['position'] : '';
  $unit = isset($userinfo['unit']) ? $userinfo['unit'] : '';
  $phone = isset($userinfo['phone']) ? $userinfo['phone'] : '';
  $email = isset($userinfo['email']) ? $userinfo['email'] : '';
 // PROFILE DATA
  $profileinfo = Profiledata($userid, $con);
  $pdescription = isset($profileinfo['description']) ? $profileinfo['description'] : '';
  $paddress = isset($profileinfo['address']) ? $profileinfo['address'] : '';
  $ptwitter = isset($profileinfo['twitter']) ? $profileinfo['twitter'] : '';
  $pfacebook = isset($profileinfo['facebook']) ? $profileinfo['facebook'] : '';
  $plinkedin = isset($profileinfo['linkin']) ? $profileinfo['linkin'] : '';
  $pinstagram = isset($profileinfo['instagram']) ? $profileinfo['instagram'] : '';
  $pimagfilepath = isset($profileinfo['imgfilepath']) ? $profileinfo['imgfilepath'] : ''; // Default image path

 // echo "<script>alert('P linkedin: " . $plinkedin . "');</script>"; // Debugging line

  // GET IMAGE
  
  // Permission checks for menu items
  $masterDataPermit = UserPermitCheck($userid, 'FRM-MASTER DATA', $con);
  $userGroupPermit = UserPermitCheck($userid, 'FRM-USERGROUP', $con);
  $groupPermitsPermit = UserPermitCheck($userid, 'FRM-USERS_PERMIT', $con);
  $usersPermit = UserPermitCheck($userid, 'FRM-USERS', $con);
  $modulesPermit = UserPermitCheck($userid, 'FRM-MODULES', $con);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Users Profile</title>
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

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">ePhyto Certificate</span>
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

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?php echo $pimagfilepath; ?>" alt="Profile" class="rounded-circle">
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
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
                <i class="bi bi-person"></i>
                <span><?php echo isset($translations['My Profile']) ? $translations['My Profile'] : 'My Profile'; ?></span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
                <i class="bi bi-gear"></i>
                <span><?php echo isset($translations['Account Settings']) ? $translations['Account Settings'] : 'Account Settings'; ?></span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="#">
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
                <span><?php echo isset($translations['Logout']) ? $translations['Logout'] : 'Logout'; ?></span>
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
          <i class="bi bi-grid"></i>
          <span><?php echo isset($translations['Dashboard']) ? $translations['Dashboard'] : 'Dashboard'; ?></span>
        </a>
      </li><!-- End Dashboard Nav -->

      <?php if ($entityPermit['pread']): ?>
        <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo htmlspecialchars('entity.php?entity=export&uid='.$userid.'&lang='.$lang); ?>" >
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
      <?php endif; ?>

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
      
      <?php if ($masterDataPermit['pread']) { ?>
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
        <?php // if($groupname == "admin"){ ?><!-- Admin group check -->
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
            <a href="masterdata.php?part=pest&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Pest']) ? $translations['Pest'] : 'Pest'; ?></span>
            </a>
          
          <li>
            <a href="masterdata.php?part=provinces&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Provinces']) ? $translations['Provinces'] : 'Provinces'; ?></span>
            </a>
          </li>
         <?php // } // End of Admin group check ?> 
        </ul>
      </li><!-- End Master data -->
      <?php } // End masterDataPermit check ?>
       <!-- Monitoring and Reporting -->
       <li class="nav-heading"><?php echo isset($translations['MONITORING AND REPORTING']) ? $translations['MONITORING AND REPORTING'] : 'MONITORING AND REPORTING'; ?></li>
        <li class="nav-item">
        <a class="nav-link collapsed" href="monitor_report.php?mn=certtrack&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bx bxs-file-find" style="font-size: 20px;"></i>
          <span><?php echo isset($translations['Certificate verification']) ? $translations['Certificate verification'] : 'Certificate verification'; ?></span>
        </a>
        <a class="nav-link collapsed" href="monitor_report.php?mn=datareport&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bx bx-bar-chart-alt-2" style="font-size: 20px;"></i>
          <span><?php echo isset($translations['Data reporting']) ? $translations['Data reporting'] : 'Data reporting'; ?></span>
        </a>
      </li><!-- End Monitoring and Reporting Nav -->

      <li class="nav-heading"><?php echo isset($translations['Users Management']) ? $translations['Users Management'] : 'Users Management'; ?></li>

      <li class="nav-item">
        <a class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'users-profile.php') echo 'active'; ?>" href="users-profile.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-person"></i>
          <span><?php echo isset($translations['Profile']) ? $translations['Profile'] : 'Profile'; ?></span>
        </a>
      </li><!-- End Profile Page Nav -->
     
      <?php if ($userGroupPermit['pread']): ?>
      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=ugroup&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-people"></i>
          <span><?php echo isset($translations['Users group']) ? $translations['Users group'] : 'Users group'; ?></span>
        </a>
      </li><!-- End Users group -->
      <?php endif; ?>

      
      <?php if ($groupPermitsPermit['pread']): ?>
      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=upermits&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-shield-lock"></i>
          <span><?php echo isset($translations['Group permits']) ? $translations['Group permits'] : 'Group permits'; ?></span>
        </a>
      </li><!-- End Permission: User Group and Module -->
      <?php endif; ?>

      
      <?php if ($usersPermit['pread']): ?>
      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=userslist&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-person-plus"></i><span><?php echo isset($translations['Users']) ? $translations['Users'] : 'Users'; ?></span>
        </a>
      </li><!-- End Users Page Nav -->
      <?php endif; ?>
      
      <?php if ($modulesPermit['pread']): ?>
      <li class="nav-item"> <!--*********** Module *****************-->
        <a class="nav-link collapsed" href="users.php?part=modulelist&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-grid-3x3-gap"></i><span><?php echo isset($translations['Modules']) ? $translations['Modules'] : 'Modules'; ?></span>
        </a>
      </li>
      <?php endif; ?>

      <!-- Logout -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span><?php echo isset($translations['Logout']) ? $translations['Logout'] : 'Logout'; ?></span>
        </a>
      </li><!-- End Logout -->

    </ul>
  

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1><?php echo isset($translations['Profile']) ? $translations['Profile'] : 'Profile'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item"><?php echo isset($translations['Users']) ? $translations['Users'] : 'Users'; ?></li>
          <li class="breadcrumb-item active"><?php echo isset($translations['Profile']) ? $translations['Profile'] : 'Profile'; ?></li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row">
        <div class="col-xl-4">

          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

              <img src="<?php echo $pimagfilepath; ?>" alt="Profile" class="rounded-circle">
              <h2><?php echo $ufullname; ?></h2>
              <h3><?php echo $position; ?></h3>
              <div class="social-links mt-2">
                <a href="#" class="twitter"><i class="bi bi-whatsapp"></i></a>
                <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
               <!--
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
               -->
              </div>
            </div>
          </div>

        </div>

        <div class="col-xl-8">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview"><?php echo isset($translations['overview']) ? $translations['overview'] : 'Overview'; ?></button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit"><?php echo isset($translations['Edit Profile']) ? $translations['Edit Profile'] : 'Edit Profile'; ?></button>
                </li>
              <!--
                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Settings</button>
                </li>
              -->
                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password"><?php echo isset($translations['Change Password']) ? $translations['Change Password'] : 'Change Password'; ?></button>
                </li>

              </ul>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                  <h5 class="card-title"><?php echo isset($translations['Address']) ? $translations['Address'] : 'Address'; ?></h5>
                  <p class="small fst-italic"><?php echo $pdescription; ?></p>

                  <h5 class="card-title"><?php echo isset($translations['Profile Details']) ? $translations['Profile Details'] : 'Profile Details'; ?></h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label "><?php echo isset($translations['Full Name']) ? $translations['Full Name'] : 'Full Name'; ?></div>
                    <div class="col-lg-9 col-md-8"><?php echo $ufullname; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label"><?php echo isset($translations['Work Unit']) ? $translations['Work Unit'] : 'Work Unit'; ?></div>
                    <div class="col-lg-9 col-md-8"><?php echo $unit; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label"><?php echo isset($translations['Position']) ? $translations['Position'] : 'Position'; ?></div>
                    <div class="col-lg-9 col-md-8"><?php echo $position; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label"><?php echo isset($translations['User group']) ? $translations['User group'] : 'User group'; ?></div>
                    <div class="col-lg-9 col-md-8"><?php echo $groupname; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label"><?php echo isset($translations['Country']) ? $translations['Country'] : 'Country'; ?></div>
                    <div class="col-lg-9 col-md-8">Lao PDR</div>
                  </div>
                <!--
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label"><?php echo isset($translations['Address']) ? $translations['Address'] : 'Address'; ?></div>
                    <div class="col-lg-9 col-md-8"><?php echo $paddress; ?></div>
                  </div>
                -->
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label"><?php echo isset($translations['Phone']) ? $translations['Phone'] : 'Phone'; ?></div>
                    <div class="col-lg-9 col-md-8"><?php echo $phone; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label"><?php echo isset($translations['Email']) ? $translations['Email'] : 'Email'; ?></div>
                    <div class="col-lg-9 col-md-8"><?php echo $email; ?></div>
                  </div>

                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form method="POST" action="users-profile.php?uid=<?php echo $userid; ?>&lang=<?php echo htmlspecialchars($lang); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="huid" value="<?php echo $userid; ?>">
                    <input type="hidden" name="hlang" value="<?php echo htmlspecialchars($lang); ?>">
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Profile Image']) ? $translations['Profile Image'] : 'Profile Image'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <img src="<?php echo $pimagfilepath; ?>" alt="Profile">
                        <div class="pt-2">
                          <!-- <a href="#" class="btn btn-primary btn-sm" title="Upload new profile image"><i class="bi bi-upload"></i></a> -->
                          <input type="file" name="profile_image" id="profile_image" accept="image/*" style="display:none;" onchange="this.form.submit()">
                          <label for="profile_image" class="btn btn-primary btn-sm" title="Upload new profile image">
                              <i class="bi bi-upload"></i>
                           </label>
                          <a href="#" class="btn btn-danger btn-sm" title="Remove my profile image"><i class="bi bi-trash"></i></a>
                        </div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Full Name']) ? $translations['Full Name'] : 'Full Name'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="fullName" type="text" class="form-control" id="fullName" value="<?php echo $ufullname; ?>">
                        <input name="userid_profile" type="hidden" value="<?php echo $userid; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="about" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Address']) ? $translations['Address'] : 'Address'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <textarea name="about" class="form-control" id="about" style="height: 100px"><?php echo $pdescription; ?></textarea>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="company" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Work Unit']) ? $translations['Work Unit'] : 'Work Unit'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="workunit" type="text" class="form-control" id="workunit" value="<?php echo $unit; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Job" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Position']) ? $translations['Position'] : 'Position'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="position" type="text" class="form-control" id="position" value="<?php echo $position; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Country" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Country']) ? $translations['Country'] : 'Country'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="country" type="text" class="form-control" id="Country" value="Lao PDR">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Address" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Address']) ? $translations['Address'] : 'Address'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="address" type="text" class="form-control" id="address" value="<?php echo $paddress; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Phone" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Phone']) ? $translations['Phone'] : 'Phone'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="phone" type="text" class="form-control" id="phone" value="<?php echo $phone; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Email" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Email']) ? $translations['Email'] : 'Email'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="email" type="email" class="form-control" id="email" value="<?php echo $email; ?>">
                      </div>
                    </div>
                  <!--
                    <div class="row mb-3">
                      <label for="Twitter" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Twitter Profile']) ? $translations['Twitter Profile'] : 'Twitter Profile'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="twitter" type="text" class="form-control" id="twitter" value="<?php echo $ptwitter; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Facebook" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Facebook Profile']) ? $translations['Facebook Profile'] : 'Facebook Profile'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="facebook" type="text" class="form-control" id="facebook" value="<?php echo $pfacebook; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Instagram" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Instagram Profile']) ? $translations['Instagram Profile'] : 'Instagram Profile'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="instagram" type="text" class="form-control" id="instagram" value="<?php echo $pinstagram; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Linkedin" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Linkedin Profile']) ? $translations['Linkedin Profile'] : 'Linkedin Profile'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="linkedin" type="text" class="form-control" id="linkedin" value="<?php echo $plinkedin; ?>">
                      </div>
                    </div>
                -->
                    <div class="text-center">
                      <button type="submit" class="btn btn-primary" name="submitEditProfile"><?php echo isset($translations['Save Changes']) ? $translations['Save Changes'] : 'Save Changes'; ?></button>
                    </div>
                  </form><!-- End Profile Edit Form -->

                </div>

                <div class="tab-pane fade pt-3" id="profile-settings">

                  <!-- Settings Form -->
                  <form>
                     <input type="hidden" name="huid" value="<?php echo $userid; ?>">
                    <input type="hidden" name="hlang" value="<?php echo htmlspecialchars($lang); ?>">

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Email Notifications</label>
                      <div class="col-md-8 col-lg-9">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="changesMade" checked>
                          <label class="form-check-label" for="changesMade">
                            Changes made to your account
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="newProducts" checked>
                          <label class="form-check-label" for="newProducts">
                            Information on new products and services
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="proOffers">
                          <label class="form-check-label" for="proOffers">
                            Marketing and promo offers
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="securityNotify" checked disabled>
                          <label class="form-check-label" for="securityNotify">
                            Security alerts
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                  </form><!-- End settings Form -->

                </div>

                <div class="tab-pane fade pt-3" id="profile-change-password">

                  <!-- Change Password Form -->
                  <form method="POST" action="users-profile.php?uid=<?php echo $userid; ?>">

                    <div class="row mb-3">
                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Current Password']) ? $translations['Current Password'] : 'Current Password'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="password" type="password" class="form-control" id="currentPassword">
                        <input name="huserid_profile" type="hidden" value="<?php echo $userid; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['New Password']) ? $translations['New Password'] : 'New Password'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="newpassword" type="password" class="form-control" id="newPassword">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label"><?php echo isset($translations['Re-enter New Password']) ? $translations['Re-enter New Password'] : 'Re-enter New Password'; ?></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="renewpassword" type="password" class="form-control" id="renewPassword">
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" name="SubmitChangePassword" id="SubmitChangePassword" class="btn btn-primary">Change Password</button>
                    </div>
                  </form><!-- End Change Password Form -->

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->
    <?php
    // UPDATE Profile Form Submission *****************

     if (isset($_POST['submitEditProfile'])) {
      
       // Process form data
        $ufname = $_POST['fullName'];
        $userid = $_POST['userid_profile']; // Get the user ID from the hidden input
        $about = $_POST['about'];
        $workunit = $_POST['workunit'];
        $position = $_POST['position'];
        //$country = $_POST['country'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];

       $email = $_POST['email'];
       $twitter = $_POST['twitter'];
       $facebook = $_POST['facebook'];
       $instagram = $_POST['instagram'];
       $linkedin = $_POST['linkedin'];
       //echo "<script>alert('Profile updated: " . $ufname . "');</script>"; // Debugging line

        if(!empty($ufname) && !empty($userid)){
          
          if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
              echo "<script>
                alert('Invalid email format');
                window.location.href = 'users-profile.php?uid=" . $userid . "';
              </script>";
              exit();
          }

          if (UpdateProfile($userid, $about, $address, $twitter, $facebook, $instagram, $linkedin, $position, $workunit, $phone, $email, $con)) {
              echo "<script>
                alert('Profile updated successfully!');
                window.location.href = 'users-profile.php?uid=" . $userid . "';
              </script>";
              exit();
          } else {
              echo "<script>
                alert('Error updating profile. Please try again.');
                window.location.href = 'users-profile.php?uid=" . $userid . "';
              </script>";
              exit();
          }
          // Update tbusers table with position, unit, phone, and email
       } // End of if empty check - user ID and full name
     } // End of form submission check

     // UPLOAD Profile Image *******************************
        
      // Check if a file was uploaded
       if (!empty($_FILES['profile_image']['name'])) {
                   
         $userid_img = $_POST['userid_profile']; // Get the user ID from the hidden input
       //  echo "<script>alert('User ID for image upload: " . $userid_img . "');</script>"; // Debugging line

         // Get file details
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
         $fileName = basename($_FILES['profile_image']['name']);
         $fileSize = $_FILES['profile_image']['size'];
         $fileType = $_FILES['profile_image']['type'];

         // Validate and move the uploaded file
         $uploadFileDir = 'assets/img/';
         $filepath = $uploadFileDir . $fileName;

         if (move_uploaded_file($fileTmpPath, $filepath)) {
              // Update the profile image in the database
              ProfileImageUpload($userid_img, $fileName, $filepath, $con); 
             echo "<script>window.location.href='users-profile.php?uid=" . $userid . "';</script>";
         } else {
             echo "<script>alert('Error uploading profile image. Please try again.');</script>";
         }
      }  // End of file upload check- empty check

    // CHANGE PASSWORD Form Submission *****************
     if (isset($_POST['SubmitChangePassword'])) {
         $userid = $_POST['huserid_profile']; // Get the user ID from the hidden input
          $currentpassword = $_POST['password'];

          // Validate current password
          $currentpassword = trim($currentpassword);
          if (empty($currentpassword)) {
              echo "<script>alert('Current password is required.');</script>";
              exit;
          } else {
           // echo "<script>alert('Current password: " . $currentpassword . "');</script>"; // Debugging line
            $cpsw = currentPasswordCheck($userid, $currentpassword, $con);
           // echo "<script>alert('Current password check: " . ($cpsw ? "true" : "false") . "');</script>"; // Debugging line
            if (!$cpsw) {
                echo "<script>
                        alert('Current password is incorrect.');
                        window.location.href='users-profile.php?uid=" . $userid . "';
                      </script>";
            }
          }

          // Validate new password
          $newpassword = $_POST['newpassword'];
          $newpassword = trim($newpassword);
          $renewpassword = $_POST['renewpassword'];
          $renewpassword = trim($renewpassword);

         if (empty($_POST['newpassword']) || empty($_POST['renewpassword'])) {
            echo "<script>
                alert('New password is required.');
                window.location.href = 'users-profile.php?uid=" . $userid . "';
            </script>";
            exit();
         } 
        // $showChangePasswordTab = false; // Show change password tab if form is submitted
         if ($_POST['newpassword'] !== $_POST['renewpassword']) {
             echo "<script>
                      alert('New passwords do not match.');
                      window.location.href = 'users-profile.php?uid=" . $userid . "';
                   </script>";
             exit();
         } else {
             //echo "<script>alert('New password: " . $newpassword . "');</script>"; // Debugging line
              if (UpdateProfileChangePassword($userid, $newpassword, $con)) {
                  echo "<script>
                    alert('Password changed successfully!');
                    window.location.href = 'users-profile.php?uid=" . $userid . "';
                  </script>";
                  exit();
              } else {
                    echo "<script>
                      alert('Error changing password. Please try again.');
                      window.location.href = 'users-profile.php?uid=" . $userid . "';
                    </script>";
                    exit();
              }
         } // End of new password validation check
         
      } // End of change password form submission check
    ?>
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>DOA</span></strong>. All Rights Reserved
    </div>

  </footer><!-- End Footer -->

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
  
  <!-- NOT WORKING YET -->
  <?php if(isset($_POST['SubmitChangePassword'])){ ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        alert("Changing password...");
        /*
        var tab = document.querySelector('button[data-bs-target="#profile-change-password"]');
        if (tab) {
          bootstrap.Tab.getOrCreateInstance(tab).show();
        }
        */
      });
    </script>
  <?php } ?>

</body>

</html>