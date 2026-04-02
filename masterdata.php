<?php
      // Pk: 2025-04-30
  //session_start(); - not working on cloud server
  
  // Prevent page caching
  header("Cache-Control: no-cache, no-store, must-revalidate");
  header("Pragma: no-cache");
  header("Expires: 0");

  require("php-bin/connection.php"); // replace include with require
  require("php-bin/supports.php"); // replace include with require
  /*
  // Authentication check
  $userid = isset($_SESSION["uid"]) ? $_SESSION["uid"] : '';
  if(empty($userid)){
    // If user ID is not set, redirect to login page
    echo "<script>alert('You are not logged in. Please log in to access this page.');</script>"; 
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
  }
  $loginuser = isset($_SESSION["username"]) ? $_SESSION["username"] : ''; // use email or username
  $uname = isset($_SESSION['uname']) ? $_SESSION['uname'] : ''; // Name of user
  //echo "<script>alert('uname: " . $uname . "');</script>"; // Debugging line
  */
  // User data
   $userid = '';
    $userid = Userconnect(
        isset($_GET['uid']) ? $_GET['uid'] : '',
        isset($_POST['uid']) ? $_POST['uid'] : '',
        isset($_POST['huid']) ? $_POST['huid'] : '',
        isset($_COOKIE['ephyto_uid']) ? $_COOKIE['ephyto_uid'] : '',
        isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
        $con
    );
    $loginuser = Userdata($userid, $con)['name']; // User name
    $guid = Userdata($userid, $con)['group_id'];
    $position = Userdata($userid, $con)['position']; 
    $groupname = Groupname($guid, $con); // User group name
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

    // Language selection
    $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en'; // default language

    // Include the appropriate language file
    $langFile = "php-bin/lang_" . $lang . ".php";
    if (file_exists($langFile)) {
        $translations = include($langFile);
    } else {
        // Fallback translations if file doesn't exist
        $translations = array(
            'Dashboard' => 'Dashboard',
            'Master data' => 'Master data'
        );
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
?>

<!DOCTYPE html>
<html lang="<?php echo $lang === 'la' ? 'lo' : htmlspecialchars($lang); ?>">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo isset($translations['Master data']) ? $translations['Master data'] : 'Master data'; ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <!-- Ajax PK -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="jspk/users-validate.js"></script>  
   <!-- 
    PK Script: Users and Usersgroup includes:
              1. handleCheckboxChange function
  -->
    
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

  <!-- Template Main CSS File - PK -->
  <link href="assets/css/style.css" rel="stylesheet">
  <!--  CSS File- PK -->
  <link href="stylecss/scss.css" rel="stylesheet">
  <link href="stylecss/lang.css" rel="stylesheet">
  <link href="stylecss/dformelement.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 7 2025 with Bootstrap v5.3.5
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="<?php echo $lang === 'la' ? 'lang-lao' : 'lang-en'; ?>">
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" class="logo d-flex align-items-center">
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
            <img src="<?php echo $uimage; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $loginuser; ?></span>
          </a><!-- End Profile Iamge Icon -->
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $loginuser; ?></h6>
              <span>National IT Consultant</span>
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
              <a class="dropdown-item d-flex align-items-center" href="index.php?lang=<?php echo $lang; ?>">
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
        <a class="nav-link collapsed" href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" >
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
        <a class="nav-link active" href="entity.php?entity=export&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" >
          <i class="bi bi-box-arrow-up-right"></i>
          <span><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Export entity'; ?></span>
        </a>
      </li><!-- End Export Entity Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=import&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" >
          <i class="bi bi-box-arrow-in-down" style="font-size: 1.2rem;"></i>
          <span><?php echo isset($translations['Import entity']) ? $translations['Import entity'] : 'Import entity'; ?></span>
        </a>
      </li><!-- End Import Entity Nav -->

       <?php
          $masterParts = ['countries', 'locations', 'provinces','product', 'productgroup', 'productunit', 'conveyance', 'inspectionmethod','treatmentmethod', 'entitytype', 'approvers']; // Add all relevant parts here
          $isMasterActive = (isset($_GET['part']) && in_array($_GET['part'], $masterParts));
      ?>
      <li class="nav-item">
        <a class="nav-link <?php echo $isMasterActive ? '' : 'collapsed'; ?>" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span><?php echo isset($translations['Master data']) ? $translations['Master data'] : 'Master data'; ?></span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse<?php echo $isMasterActive ? ' show' : ''; ?>" data-bs-parent="#sidebar-nav">
          <li>
            <a href="masterdata.php?part=approvers&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'approvers') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Approvers']) ? $translations['Approvers'] : 'Approvers'; ?></span>
            </a>
          </li>
         <?php if($groupname == "admin"){ ?><!-- Admin group check -->
          <li>
            <a href="masterdata.php?part=conveyance&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'conveyance') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Conveyance']) ? $translations['Conveyance'] : 'Conveyance'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=countries&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'countries') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Countries']) ? $translations['Countries'] : 'Countries'; ?></span>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Districts']) ? $translations['Districts'] : 'Districts'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=entitytype&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'entitytype') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Entity Type']) ? $translations['Entity Type'] : 'Entity Type'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=inspectionmethod&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'inspectionmethod') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Inspection method']) ? $translations['Inspection method'] : 'Inspection method'; ?></span>
            </a>
          </li>

          <li>
            <a href="masterdata.php?part=locations&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'locations') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Locations']) ? $translations['Locations'] : 'Locations'; ?></span>
            </a>
          </li>
           <li>
            <a href="masterdata.php?part=product&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" class="<?php echo (isset($_GET['part']) && ($_GET['part'] === 'product' || $_GET['part'] ==='productgroup' || $_GET['part'] ==='productunit')) ? 'active' : ''; ?>">
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
            <a href="#">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Provinces']) ? $translations['Provinces'] : 'Provinces'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=treatmentmethod&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'treatmentmethod') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Treatment method']) ? $translations['Treatment method'] : 'Treatment method'; ?></span>
            </a>
          </li>
         <?php } ?><!-- End of Admin group check -->
        </ul>
      </li><!-- End Tables Nav -->

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
        <a class="nav-link collapsed" href="users-profile.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-person"></i>
          <span><?php echo isset($translations['Profile']) ? $translations['Profile'] : 'Profile'; ?></span>
        </a>
      </li><!-- End Profile -->

      <?php if($groupname == "admin"){ ?><!-- Admin group check -->
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
      </li><!-- End User Group permit -->

      <li class="nav-item">
        <a class="nav-link active" href="users.php?part=userslist&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-person-plus"></i><span><?php echo isset($translations['Users']) ? $translations['Users'] : 'Users'; ?></span>
        </a>
      </li>  <!-- End Users Nav -->
    <?php } ?><!-- End of Admin group check -->
    </ul>

  </aside><!-- End Sidebar-->
   
  <main id="main" class="main">
    <!-- ======= *************** Locations ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='locations') {
      // PK: Locations part: loc=edit&id=$locid
      //echo "<script>alert('Locations part is not implemented yet.');</script>";
    ?>

    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1><?php echo isset($translations['Locations']) ? $translations['Locations'] : 'Locations'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item active"><a href="masterdata.php?part=locations&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Locations']) ? $translations['Locations'] : 'Locations'; ?></a></li>
        </ol>
      </nav>
      </div>
      <!--
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addGroupModal" data-gid="new">
          <i class="bi bi-plus-circle"></i> <?php echo isset($translations['Add New Location']) ? $translations['Add New Location'] : 'Add New Location'; ?>
        </button>
      </div>
     -->
      <div>
          <a href="masterdata.php?loc=new&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" class="btn btn-success btn-sm" role="button">
            <i class="bi bi-plus-circle"></i> <?php echo isset($translations['Add New Location']) ? $translations['Add New Location'] : 'Add New Location'; ?>
          </a>
      </div>
    </div><!-- End Page Title -->
    
    <section class="section"> <!-- DATA TABLE - Locations -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              
              <h5 class="card-title"><?php echo isset($translations['Locations']) ? $translations['Locations'] : 'Locations'; ?></h5>
              <p><?php echo isset($translations['ePhytosanitary by Department of Agriculture, MAF - Locations']) ? $translations['ePhytosanitary by Department of Agriculture, MAF - Locations'] : 'ePhytosanitary by Department of Agriculture, MAF - Locations'; ?></p>

              <!-- Table with stripped rows -->
               
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b>N</b>o</th>
                   <th><?php echo isset($translations['Name(English)']) ? $translations['Name(English)'] : 'Name(English)'; ?></th>
                   <th><?php echo isset($translations['Name(Lao)']) ? $translations['Name(Lao)'] : 'Name(Lao)'; ?></th>
                   <th><?php echo isset($translations['Type']) ? $translations['Type'] : 'Type'; ?></th>
                   <th><?php echo isset($translations['District']) ? $translations['District'] : 'District'; ?></th>
                   <th><?php echo isset($translations['Province']) ? $translations['Province'] : 'Province'; ?></th>
                   <th><?php echo isset($translations['Status']) ? $translations['Status'] : 'Status'; ?></th>
                   <th><?php echo isset($translations['Edit']) ? $translations['Edit'] : 'Edit'; ?></th>
                   <th><?php echo isset($translations['Delete']) ? $translations['Delete'] : 'Delete'; ?></th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    Locationlist($con); // List of Locations
                  ?>
                </tbody>
              </table>
              
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </div>
    </section> <!-- End Data Table Locations -->
      
   <?php  } ?> <!-- ********* End of if part=locations ********* -->

   <?php
   // Locations form processing/submission
   if(isset($_POST['btnsublocation'])) {
       // Process the form submission for adding/updating locations
          $id = $_POST['hlid']; // Hidden input for ID
          // location ID - NOT UPDATED YET
          $locid = $_POST['locationid'];
          $nameeng = $_POST['nameeng'];
          $namelao = $_POST['namelao'];
          $loctype = $_POST['locationtype'];
          $pid = $_POST['province'];
          $did = $_POST['district'];
       if($_POST['btnsublocation'] === 'update') {
           // Update existing location
          Locationupdate($id,$locid, $nameeng, $namelao, $loctype,$pid, $did, $con); // Function to update location
       } else if ($_POST['btnsublocation'] === 'submit') {
           // Add new location
           //echo "<script>alert('Add new location: " . $locid . "');</script>"; // Debugging line
          Addlocation($locid, $nameeng, $namelao, $loctype, $pid, $did, $con); // Function to add new location
       }
       
   } // End of if btnsublocation

     // EDIT FORM - Locations loc=edit&id=6
    if(isset($_GET['loc']) && ($_GET['loc'] === 'edit' || $_GET['loc'] === 'new')) {
         if(isset($_GET['id']) && !empty($_GET['id'])) {
            $sbupdate = ''; // Initialize submit button value
            $sbupdate = 'update'; // Set submit button value for update
            $locsqid = $_GET['id']; // Location ID to edit/delete
            $sql = "SELECT * FROM locations WHERE id = ?";
            $locv = Locationvariables($locsqid, $con);
            if ($locv) {  // for Editing location
                // Access columns like this:
                $id = $locv['id'];
                $locid = $locv['lid'];
                $nameeng = $locv['name_eng'];
                $namelao = $locv['name_lao'];
                $loctype = $locv['location_type'];
                $pid = $locv['pid'];
                $did = $locv['did'];
               
            } else { // For new location
               // Initialize variables for new location
               $id = '';
               $locid = ''; 
               $nameeng = '';
               $namelao = '';
               $loctype = '';
               $pid = '';
               $did = ''; 
            }
             // end of if $locv
         } // end of if isset id
         
        // echo "<script>alert('Edit Location ID: " . $ledit . "');</script>"; // Debugging line
    ?>
     <div class="pagetitle">
        <h1><?php echo isset($translations['Add/Update Location']) ? $translations['Add/Update Location'] : 'Add/Update Location'; ?></h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
            <li class="breadcrumb-item"><?php echo isset($translations['Forms']) ? $translations['Forms'] : 'Forms'; ?></li>
            <li class="breadcrumb-item active"><?php echo isset($translations['Locations']) ? $translations['Locations'] : 'Locations'; ?></li>
          </ol>
        </nav>
      </div>
      <section class="section"> <!-- DATA FORM - Locations -->
      <div class="row">
        <div class="col-lg-6" style="width: 80%;"> <!-- Pk-Update: style -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo (isset($_GET['loc']) && $_GET['loc'] === 'edit') ? 'Location Update' : 'New Location'; ?></h5>
              <!-- Users Form -->
              <form action="masterdata.php?part=locations&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" method="POST">
                <!-- Hidden input for id : location  ID -->
                <input type="hidden" id="hlid" name="hlid" value="<?php echo isset($id) ? $id : ''; ?>">
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Location ID</label>
                  <div class="col-sm-10">
                    <input type="text" name="locationid" id="locationid" class="form-control" value="<?php echo isset($locid) ? $locid : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translations['English Name']) ? $translations['English Name'] : 'English Name'; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="nameeng" id="nameeng" class="form-control" value="<?php echo isset($nameeng) ? $nameeng : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translations['Lao Name']) ? $translations['Lao Name'] : 'Lao Name'; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="namelao" id="namelao" class="form-control" value="<?php echo isset($namelao) ? $namelao : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Location Type']) ? $translations['Location Type'] : 'Location Type'; ?></label>
                  <div class="col-sm-5">
                    <select class="form-select" name="locationtype" aria-label="Default select example">
                      <option selected>*** Please select one ***</option>
                      <?php 
                        SelectLocationType($loctype, $con); // Function to select location type
                      ?>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Province']) ? $translations['Province'] : 'Province'; ?></label>
                  <div class="col-sm-5">
                    <select class="form-select" name="province" id="province" aria-label="Default select example" onchange="SelectProvinceOnChange(this)">
                     <option value="">*** Please select one ***</option>
                      <?php SelectProvinces($pid, $con); ?>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['District']) ? $translations['District'] : 'District'; ?></label>
                  <div class="col-sm-5">
                    <select class="form-select" name="district" id="district" aria-label="Default select example">
                      <option value="">*** Please select one ***</option>
                      <?php SelectDistricts($did, $pid, $con); ?>
                    </select>
                  </div>
                </div>
              
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">&nbsp;</label> 
                  <div class="col-sm-10">
                    <button type="submit" name="btnsublocation" class="btn btn-primary" value="<?php echo isset($sbupdate) ? 'update' : 'submit'; ?>"><?php echo isset($sbupdate) ? 'Update' : 'Submit'; ?></button>
                  </div>
                </div>
              </form><!-- End of Locaiton form -->
            </div>
          </div>
        </div>
      </div>
    </section> <!-- End of Data Form locations -->
  <?php
    } // End of if Edit form location *************

    // DELETE location
    if(isset($_GET['loc']) && ($_GET['loc'] === 'del')){
        if(isset($_GET['id']) && !empty($_GET['id'])) {
            $locsqid = $_GET['id']; // Location ID to delete
            // Call the function to delete location
            //DeleteLocation($locsqid, $con); - NOT IMPLEMENTED YET
            echo "<script>alert('Location with ID: " . $locsqid . " TO be deleted (NOT IMPLEMENTED YET).');</script>"; // Debugging line
            echo "<script>window.location.href='masterdata.php?part=locations&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>';</script>"; // Redirect to locations page
        } else {
            echo "<script>alert('No Location ID provided for deletion.');</script>"; // Debugging line
        }
    }
  ?>   
  <!-- ======= *************** Countries ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='countries') {
      // PK: Locations part: loc=edit&id=$locid
      //echo "<script>alert('Locations part is not implemented yet.');</script>";
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1><?php echo isset($translations['Countries']) ? $translations['Countries'] : 'Countries'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">Home</a></li>
          <li class="breadcrumb-item"><?php echo isset($translations['Tables']) ? $translations['Tables'] : 'Tables'; ?></li>
          <li class="breadcrumb-item active"><?php echo isset($translations['Countries']) ? $translations['Countries'] : 'Countries'; ?></li>
        </ol>
      </nav>
      </div>
      
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCountryModal" data-cid="new">
          <i class="bi bi-plus-circle"></i><?php echo isset($translations['Add New Country']) ? $translations['Add New Country'] : 'Add New Country'; ?>
        </button>
      </div> 
    </div><!-- End Page Title -->
    <!-- == Modal form - Countries == -->
      <div class="modal fade" id="addCountryModal" tabindex="-1" aria-labelledby="addCountryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addCountryModalLabel"><b><?php echo isset($translations['Add New Country']) ? $translations['Add New Country'] : 'Add New Country'; ?></b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for cid -->
                <input type="hidden" id="countryId" name="countryId">
                <div class="mb-3">
                  <label for="countryName" class="form-label"><?php echo isset($translations['Country Name']) ? $translations['Country Name'] : 'Country Name'; ?></label>
                  <input type="text" class="form-control" id="countryName" name="countrypName" required>
                </div>
                <div class="mb-3">
                  <label for="alphaCode" class="form-label"><?php echo isset($translations['Alpha code']) ? $translations['Alpha code'] : 'Alpha code'; ?></label>
                  <input type="text" class="form-control" id="alphaCode" name="alphaCode" required>
                </div>
                <div class="mb-3">
                  <label for="numCode" class="form-label"><?php echo isset($translations['Numeric code']) ? $translations['Numeric code'] : 'Numeric code'; ?></label>
                  <input type="text" class="form-control" id="numCode" name="numCode" required>
                </div>
                <div class="mb-3">
                  <label for="currency" class="form-label"><?php echo isset($translations['Currency']) ? $translations['Currency'] : 'Currency'; ?></label>
                  <input type="text" class="form-control" id="currency" name="currency">
                </div>
                <div class="mb-3">
                  <label for="countryDescription" class="form-label"><?php echo isset($translations['Description']) ? $translations['Description'] : 'Description'; ?></label>
                  <textarea class="form-control" id="countryDescription" name="countryDescription" rows="3"></textarea>
                </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" id="submitCountry" name="submitCountry" class="btn btn-success">Submit</button>
            </div>
            </form>
          </div>
        </div>
      </div>
    <!-- End of Modal -->
     <section class="section"> <!-- DATA TABLE - Locations -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              
              <h5 class="card-title"><?php echo isset($translations['Countries']) ? $translations['Countries'] : 'Countries'; ?></h5>
              <p><?php echo isset($translations['ePhytosanitary by Department of Agriculture, MAF - Countries']) ? $translations['ePhytosanitary by Department of Agriculture, MAF - Countries'] : 'ePhytosanitary by Department of Agriculture, MAF - Countries'; ?></p>

              <!-- Table with stripped rows -->
               
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b>N</b>o</th>
                   <th><?php echo isset($translations['Name']) ? $translations['Name'] : 'Name'; ?></th>
                   <th><?php echo isset($translations['Alpha code']) ? $translations['Alpha code'] : 'Alpha code'; ?></th>
                   <th><?php echo isset($translations['Numeric code']) ? $translations['Numeric code'] : 'Numeric code'; ?></th>
                   <th><?php echo isset($translations['Currency']) ? $translations['Currency'] : 'Currency'; ?></th>
                   <th><?php echo isset($translations['Status']) ? $translations['Status'] : 'Status'; ?></th>
                   <th><?php echo isset($translations['Edit']) ? $translations['Edit'] : 'Edit'; ?></th>
                   <th><?php echo isset($translations['Delete']) ? $translations['Delete'] : 'Delete'; ?></th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    Countrylist($con); // List of Countries
                  ?>
                </tbody>
              </table>
              
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </div>
    </section> <!-- End Data Table countries -->
      
   <?php  } ?> 
   <!-- ********* End of if part=countries ********* -->
    <?php
    // Countries form processing/submission - MODAL form
    if(isset($_POST['submitCountry'])) {
        // Process the form submission for adding/updating countries
        $cid = $_POST['countryId']; // Hidden input for ID
        $alcode = $_POST['alphaCode'];
        $numcode = $_POST['numCode'];
        $cname = $_POST['countrypName'];
        $description = $_POST['countryDescription'];
        $currency = $_POST['currency'];
        
        if($cid === 'new') {
            // Add new country
            AddCountry($alcode, $numcode,$cname, $description,$currency, $con); // Function to add new country
            echo "<script>alert('New country added-Done');</script>"; // Debugging line
        } else {
            // Update existing country
            echo "<script>alert('Country with ID: " . $cid . " updated.');</script>"; // Debugging line
            UpdateCountry($cid, $alcode, $numcode, $cname, $description, $currency, $con); // Function to update country
           
        }
    } // End of if submitCountry

    if((isset($_GET['part']) && $_GET['part']==='countries') && (isset($_GET['del']) && $_GET['del'] === 'yes')) {
        if(isset($_GET['cid']) && !empty($_GET['cid'])) {
            $countryId = $_GET['cid']; // Country ID to delete
            // Call the function to delete country
            DeleteCountry($countryId, $con); // Function to delete country 
            echo "<script>alert('Country with ID: " . $countryId . " deleted.');</script>"; // Debugging line
            echo "<script>window.location.href='masterdata.php?part=countries&uid=" . $userid . "&lang=" . $lang . "';</script>"; // Redirect to countries page
    }
  }
  ?>
  <!-- ======= *************** Pest ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='pest') {
      
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1><?php echo isset($translations['Pest']) ? $translations['Pest'] : 'Pest'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item"><?php echo isset($translations['Tables']) ? $translations['Tables'] : 'Tables'; ?></li>
          <li class="breadcrumb-item active"><?php echo isset($translations['Pest']) ? $translations['Pest'] : 'Pest'; ?></li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPestModal" data-pestid="new">
          <i class="bi bi-plus-circle"></i><?php echo isset($translations['Add New Pest']) ? $translations['Add New Pest'] : 'Add New Pest'; ?>
        </button>
      </div> 
    </div><!-- End Page Title -->
     <!-- == Modal form - Pest == -->
      <div class="modal fade" id="addPestModal" tabindex="-1" aria-labelledby="addPestModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addPestModalLabel"><b><?php echo isset($translations['Add New Pest']) ? $translations['Add New Pest'] : 'Add New Pest'; ?></b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for pid -->
                <input type="hidden" id="pestId" name="pestId">
                <div class="mb-3">
                  <label for="pestName" class="form-label"><?php echo isset($translations['Pest Name']) ? $translations['Pest Name'] : 'Pest Name'; ?></label>
                  <input type="text" class="form-control" id="pestName" name="pestName" required>
                </div>
                <div class="mb-3">
                  <label for="pestScientificName" class="form-label"><?php echo isset($translations['Scientific Name']) ? $translations['Scientific Name'] : 'Scientific Name'; ?></label>
                  <input type="text" class="form-control" id="pestScientificName" name="pestScientificName" required>
                </div>
                <div class="mb-3">
                  <label for="pestCategory" class="form-label"><?php echo isset($translations['Category']) ? $translations['Category'] : 'Category'; ?></label>
                  <input type="text" class="form-control" id="pestCategory" name="pestCategory" required>
                </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" id="submitPest" name="submitPest" class="btn btn-success">Submit</button>
            </div>
            </form>
          </div>
        </div>
      </div>
    <!-- End of Modal -->
      <section class="section"> <!-- DATA TABLE - Pest -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              
              <h5 class="card-title"><?php echo isset($translations['Pest']) ? $translations['Pest'] : 'Pest'; ?></h5>
              <p><?php echo isset($translations['ePhytosanitary by Department of Agriculture, MAF - Pest']) ? $translations['ePhytosanitary by Department of Agriculture, MAF - Pest'] : 'ePhytosanitary by Department of Agriculture, MAF - Pest'; ?></p>
              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b><?php echo isset($translations['No']) ? $translations['No'] : 'No'; ?></b></th>
                   <th><?php echo isset($translations['Name']) ? $translations['Name'] : 'Name'; ?></th>
                   <th><?php echo isset($translations['Scientific Name']) ? $translations['Scientific Name'] : 'Scientific Name'; ?></th>
                   <th><?php echo isset($translations['Category']) ? $translations['Category'] : 'Category'; ?></th>
                   <th><?php echo isset($translations['Edit']) ? $translations['Edit'] : 'Edit'; ?></th>
                   <th><?php echo isset($translations['Delete']) ? $translations['Delete'] : 'Delete'; ?></th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    PestList($con); // List of Pest
                  ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </div>
    </section> <!-- End Data Table Pest -->
    <?php
     } 
     ?>
     <!-- Submit Pest form processing/submission - MODAL form -->
    <?php
    if(isset($_POST['submitPest'])) {
        // Process the form submission for adding/updating pest
        $pestid = $_POST['pestId']; // Hidden input for ID
        $pname = $_POST['pestName'];
        $pscientificname = $_POST['pestScientificName'];
        $pcategory = $_POST['pestCategory'];

        if($pestid === 'new') {
            // Add new pest
            AddPest($pname, $pscientificname, $pcategory, $userid, $con); // Function to add new pest
            echo "<script>alert('New pest added-Done');</script>"; // Debugging line
        } else {
            // Update existing pest
            echo "<script>alert('Pest with ID: " . $pestid . " updated.');</script>"; // Debugging line
            UpdatePest($pestid, $pname, $pscientificname, $pcategory, $userid, $con); // Function to update pest

        }
    } // End of if submitPest
    ?>
    
   <!-- ======= *************** Product ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='product') {
      // PK: Product part: product=edit&id=$productid
      //echo "<script>alert('Product part is not implemented yet.');</script>";
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1><?php echo isset($translations['Product']) ? $translations['Product'] : 'Product'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item"><a href="application.php?part=dashboard&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Application']) ? $translations['Application'] : 'Application'; ?></a></li>
          <li class="breadcrumb-item"><a href="masterdata.php?part=productgroup&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Product Group']) ? $translations['Product Group'] : 'Product Group'; ?></a></li>
          <li class="breadcrumb-item"><a href="masterdata.php?part=productunit&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Product Unit']) ? $translations['Product Unit'] : 'Product Unit'; ?></a></li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal" data-pid="new">
          <i class="bi bi-plus-circle"></i><?php echo isset($translations['Add New Product']) ? $translations['Add New Product'] : 'Add New Product'; ?>
        </button>
      </div> 
    </div><!-- End Page Title -->
     <!-- == Modal form - Product == -->
      <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel"><b><?php echo isset($translations['Add New Product']) ? $translations['Add New Product'] : 'Add New Product'; ?></b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for cid -->
                <input type="hidden" id="productId" name="productId">
                <input type="hidden" id="<?php echo $userid; ?>" name="userid" value="<?php echo $userid; ?>">
                <input type="hidden" id="<?php echo $lang; ?>" name="lang" value="<?php echo $lang; ?>">

                <div class="mb-3">
                  <label for="productCode" class="form-label"><?php echo isset($translations['Code']) ? $translations['Code'] : 'Code'; ?></label>
                  <input type="text" class="form-control" id="productCode" name="productCode" required>
                </div>
                <div class="mb-3">
                  <label for="productName" class="form-label"><?php echo isset($translations['Name']) ? $translations['Name'] : 'Name'; ?></label>
                  <input type="text" class="form-control" id="productName" name="productName" required>
                </div>
                <div class="mb-3">
                  <label for="scientName" class="form-label"><?php echo isset($translations['Scientific Name']) ? $translations['Scientific Name'] : 'Scientific Name'; ?></label>
                  <input type="text" class="form-control" id="scientName" name="scientName" required>
                </div>
                <div class="mb-3">
                  <label for="hsCode" class="form-label"><?php echo isset($translations['HS Code']) ? $translations['HS Code'] : 'HS Code'; ?></label>
                  <input type="text" class="form-control" id="hsCode" name="hsCode">
                </div>
                <div class="row mb-3">
                  <label class="col-sm-8 col-form-label"><?php echo isset($translations['Product Group']) ? $translations['Product Group'] : 'Product Group'; ?></label>
                  <div class="col-sm-15">
                    <select class="form-select" name="productGroup" id="productGroup" aria-label="Default select example" onchange="SelectProvinceOnChange(this)">
                     <option value="">*** Please select one ***</option>
                      <?php SelectProductgroup($pgid, $con); ?>
                    </select>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="description" class="form-label"><?php echo isset($translations['Description']) ? $translations['Description'] : 'Description'; ?></label>
                  <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" id="submitProduct" name="submitProduct" class="btn btn-success">Submit</button>
            </div>
            </form>
          </div>
        </div>
      </div>
    <!-- End of Modal -->
      <section class="section"> <!-- DATA TABLE - Product -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              
              <h5 class="card-title"><?php echo isset($translations['Product']) ? $translations['Product'] : 'Product'; ?></h5>
              <p><?php echo isset($translations['ePhytosanitary - Department of Agriculture, MAF']) ? $translations['ePhytosanitary - Department of Agriculture, MAF'] : 'ePhytosanitary - Department of Agriculture, MAF'; ?></p>

              <!-- Table with stripped rows -->
               
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b><?php echo isset($translations['No']) ? $translations['No'] : 'No'; ?></b></th>
                   <th><?php echo isset($translations['Code']) ? $translations['Code'] : 'Code'; ?></th>
                   <th><?php echo isset($translations['Name']) ? $translations['Name'] : 'Name'; ?></th>
                   <th><?php echo isset($translations['Scientific Name']) ? $translations['Scientific Name'] : 'Scientific Name'; ?></th>
                   <th><?php echo isset($translations['HS Code']) ? $translations['HS Code'] : 'HS Code'; ?></th>
                   <th><?php echo isset($translations['Product Group']) ? $translations['Product Group'] : 'Product Group'; ?></th>
                   <th><?php echo isset($translations['Status']) ? $translations['Status'] : 'Status'; ?></th>
                   <th><?php echo isset($translations['Edit']) ? $translations['Edit'] : 'Edit'; ?></th>
                   <th><?php echo isset($translations['Delete']) ? $translations['Delete'] : 'Delete'; ?></th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    ProductList($userid, $con); // List of Product
                  ?>
                </tbody>
              </table>
              
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </div>
    </section> <!-- End Data Table Product -->
    <?php
     }
    ?>
    <!-- Product form processing/submission - MODAL form -->
    <?php 
    if(isset($_POST['submitProduct'])) {  // ADD/UPDATE Product
        // Process the form submission for adding/updating product
        $pid = $_POST['productId']; // Hidden input for ID
        $pcode = $_POST['productCode'];
        $pname = $_POST['productName'];
        $scientname = $_POST['scientName'];
        $hsCode = $_POST['hsCode'];
        $productgroup = $_POST['productGroup'];
        $description = $_POST['description'];
        
        if($pid === 'new') {
            // Add new product
           // AddProduct($pcode, $pname, $scientName, $hsCode, $pgid, $description, $con); // Function to add new product
           AddProduct($pcode, $pname, $scientname, $description, $hsCode, $productgroup, $con);
        //   echo "<script>alert('New product added-Done');</script>"; // Debugging line
          
        } else {
            // Update existing product
          //  echo "<script>alert('Product with ID: " . $pid . " updated.');</script>"; // Debugging line
           UpdateProduct($pid, $pcode, $pname, $scientname, $description, $hsCode, $productgroup, $con); // Function to update product
           
        }
    } // End of if submitProduct
    // DELETE product
    if(isset($_GET['part']) && $_GET['part']==='product' && isset($_GET['del']) && $_GET['del'] === 'yes') {
        if(isset($_GET['pid']) && !empty($_GET['pid'])) {
            $productId = $_GET['pid']; // Product ID to delete
            // Call the function to delete product
            DeleteProduct($productId, $con); // Function to delete product    
        }
    }
    ?>
    <!-- ======= *************** Product Group ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='productgroup') {
      // PK: Product part: product=edit&id=$productid
      //echo "<script>alert('Product part is not implemented yet.');</script>";
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1><?php echo isset($translations['Product Group']) ? $translations['Product Group'] : 'Product Group'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item"><a href="masterdata.php?part=product&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Product']) ? $translations['Product'] : 'Product'; ?></a></li> 
          <li class="breadcrumb-item active"><?php echo isset($translations['Product Group']) ? $translations['Product Group'] : 'Product Group'; ?></li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProductGroupModal" data-pgroupid="new">
          <i class="bi bi-plus-circle"></i><?php echo isset($translations['Add New Product Group']) ? $translations['Add New Product Group'] : 'Add New Product Group'; ?>
        </button>
      </div>
    </div><!-- End Page Title -->
    <!-- == Modal form - Product Group == -->
      <div class="modal fade" id="addProductGroupModal" tabindex="-1" aria-labelledby="addProductGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addProductGroupModalLabel"><b><?php echo isset($translations['Add New Product Group']) ? $translations['Add New Product Group'] : 'Add New Product Group'; ?></b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for cid -->
                <input type="hidden" id="productGroupId" name="productGroupId">
                <div class="mb-3">
                  <label for="productGroupName" class="form-label"><?php echo isset($translations['Name']) ? $translations['Name'] : 'Name'; ?></label>
                  <input type="text" class="form-control" id="productGroupName" name="productGroupName" required>
                </div>
                <div class="mb-3">
                  <label for="productGroupDescription" class="form-label"><?php echo isset($translations['Description']) ? $translations['Description'] : 'Description'; ?></label>
                  <textarea class="form-control" id="productGroupDescription" name="productGroupDescription" rows="3"></textarea>
                </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" id="submitProductGroup" name="submitProductGroup" class="btn btn-success">Submit</button>
            </div>
            </form>
          </div>
        </div>
      </div>
    <!-- End of Modal -->
      <section class="section"> <!-- DATA TABLE - Product Group -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              
              <h5 class="card-title"><?php echo isset($translations['Product Group']) ? $translations['Product Group'] : 'Product Group'; ?></h5>
              <p><?php echo isset($translations['ePhytosanitary - Department of Agriculture, MAF']) ? $translations['ePhytosanitary - Department of Agriculture, MAF'] : 'ePhytosanitary - Department of Agriculture, MAF'; ?></p> 
              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b><?php echo isset($translations['No']) ? $translations['No'] : 'No'; ?></b></th>
                   <th><?php echo isset($translations['Name']) ? $translations['Name'] : 'Name'; ?></th>
                   <th><?php echo isset($translations['Description']) ? $translations['Description'] : 'Description'; ?></th>
                   <th><?php echo isset($translations['Status']) ? $translations['Status'] : 'Status'; ?></th>
                   <th><?php echo isset($translations['Edit']) ? $translations['Edit'] : 'Edit'; ?></th>
                   <th><?php echo isset($translations['Delete']) ? $translations['Delete'] : 'Delete'; ?></th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    ProductgroupList($con); // List of Product Group
                  ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </div>
    </section> <!-- End Data Table Product Group -->
    <?php
     }
    ?>
    <!-- Product Group form processing/submission - MODAL form -->
    <?php
    if(isset($_POST['submitProductGroup'])) {  // ADD/UPDATE Product Group
        // Process the form submission for adding/updating product group
        $pgid = $_POST['productGroupId']; // Hidden input for ID
        $pgname = $_POST['productGroupName'];
        $pgdescription = $_POST['productGroupDescription'];
        
        if($pgid === 'new') {
            // Add new product group
           // echo "<script>alert('New product group added-Done');</script>"; // Debugging line
            AddProductgroup($pgname, $pgdescription, $con); // Function to add new product group
            
        } else {
            // Update existing product group
            echo "<script>alert('Product group with ID: " . $pgid . " updated.');</script>"; // Debugging line
            UpdateProductgroup($pgid, $pgname, $pgdescription, $con); // Function to update product group
           
        }
    } // End of if submitProductGroup
    // DELETE product group
    if(isset($_GET['part']) && $_GET['part']==='productgroup' && isset($_GET['del']) && $_GET['del'] === 'yes') {
        if(isset($_GET['gid']) && !empty($_GET['gid'])) {
            $productGroupId = $_GET['gid']; // Product Group ID to delete
            // Call the function to delete product group
            DeleteProductgroup($productGroupId, $con); // Function to delete product group    
        }
    }
    ?>
    <!-- ======= *************** Product Unit ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='productunit') {
      // PK: Product part: product=edit&id=$productid
      //echo "<script>alert('Product part is not implemented yet.');</script>";
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1><?php echo isset($translations['Product Unit']) ? $translations['Product Unit'] : 'Product Unit'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item"><a href="masterdata.php?part=product&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Product']) ? $translations['Product'] : 'Product'; ?></a></li>
          <li class="breadcrumb-item active"><?php echo isset($translations['Product Unit']) ? $translations['Product Unit'] : 'Product Unit'; ?></li>
        </ol>
      </nav>
      </div>  
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProductUnitModal" data-punitid="new">
          <i class="bi bi-plus-circle"></i><?php echo isset($translations['Add New Product Unit']) ? $translations['Add New Product Unit'] : 'Add New Product Unit'; ?>
        </button>
      </div>
    </div><!-- End Page Title -->
    <!-- == Modal form - Product Unit == -->
      <div class="modal fade" id="addProductUnitModal" tabindex="-1" aria-labelledby="addProductUnitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addProductUnitModalLabel"><b><?php echo isset($translations['Add New Product Unit']) ? $translations['Add New Product Unit'] : 'Add New Product Unit'; ?></b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for cid -->
                <input type="hidden" id="productUnitId" name="productUnitId">
                <div class="mb-3">
                  <label for="productUnitCode" class="form-label"><?php echo isset($translations['Code']) ? $translations['Code'] : 'Code'; ?></label>
                  <input type="text" class="form-control" id="productUnitCode" name="productUnitCode" required>
                </div>
                <div class="mb-3">
                  <label for="productUnitName" class="form-label"><?php echo isset($translations['Symbol']) ? $translations['Symbol'] : 'Symbol'; ?></label>
                  <input type="text" class="form-control" id="productUnitSymbol" name="productUnitSymbol" required>
                </div>
                <div class="mb-3">
                  <label for="productUnitName" class="form-label"><?php echo isset($translations['Title']) ? $translations['Title'] : 'Title'; ?></label>
                  <input type="text" class="form-control" id="productUnitTitle" name="productUnitTitle" required>
                </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" id="submitProductUnit" name="submitProductUnit" class="btn btn-success">Submit</button>
            </div>
            </form>
          </div>
        </div>
      </div>
    <!-- End of Modal -->
      <section class="section"> <!-- DATA TABLE - Product Unit -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              
              <h5 class="card-title"><?php echo isset($translations['Product Unit']) ? $translations['Product Unit'] : 'Product Unit'; ?></h5>
       
              <p><?php echo isset($translations['ePhytosanitary - Department of Agriculture, MAF']) ? $translations['ePhytosanitary - Department of Agriculture, MAF'] : 'ePhytosanitary - Department of Agriculture, MAF'; ?></p>
              <!-- Table with stripped rows --> 
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b><?php echo isset($translations['No']) ? $translations['No'] : 'No'; ?></b></th>
                   <th><?php echo isset($translations['Code']) ? $translations['Code'] : 'Code'; ?></th>
                   <th><?php echo isset($translations['Symbol']) ? $translations['Symbol'] : 'Symbol'; ?></th>
                   <th><?php echo isset($translations['Title']) ? $translations['Title'] : 'Title'; ?></th>
                   <th><?php echo isset($translations['Status']) ? $translations['Status'] : 'Status'; ?></th>
                   <th><?php echo isset($translations['Edit']) ? $translations['Edit'] : 'Edit'; ?></th>
                   <th><?php echo isset($translations['Delete']) ? $translations['Delete'] : 'Delete'; ?></th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    ProductunitList($con); // List of Product Unit
                  ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </div>
    </section> <!-- End Data Table Product Unit -->
    <?php
     }
    ?>
    <?php 
    // Product Unit form processing/submission - MODAL form 
    if(isset($_POST['submitProductUnit'])) {  // ADD/UPDATE Product Unit
        // Process the form submission for adding/updating product unit
        $punitid = $_POST['productUnitId']; // Hidden input for ID 
        $code = $_POST['productUnitCode'];
        $symb = $_POST['productUnitSymbol'];
        $title = $_POST['productUnitTitle'];
        
        if($punitid === 'new') {
            // Add new product unit
           // echo "<script>alert('New product unit added-Done');</script>"; // Debugging line
            AddProductunit($code, $symb, $title, $con); // Function to add new product unit 
        } else {
            // Update existing product unit
            //echo "<script>alert('Product unit with ID: " . $punitid . " updated.');</script>"; // Debugging line
            UpdateProductunit($punitid, $code, $symb, $title, $con); // Function to update product unit
           
        }
    } // End of if submitProductUnit

    // DELETE product unit
    if(isset($_GET['part']) && $_GET['part']==='productunit' && isset($_GET['del']) && $_GET['del'] === 'yes') {
        if(isset($_GET['uid']) && !empty($_GET['uid'])) {
            $productUnitId = $_GET['uid']; // Product Unit ID to delete
            // Call the function to delete product unit
            DeleteProductunit($productUnitId, $con); // Function to delete product unit    
        }
    }
  ?>
  <!-- ======= *************** Approvers ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='approvers') {
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1><?php echo isset($translations['Approvers']) ? $translations['Approvers'] : 'Approvers'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item"><a href="application.php?part=dashboard&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Application']) ? $translations['Application'] : 'Application'; ?></a></li>
          <li class="breadcrumb-item active"><a href="inspection.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Inspection']) ? $translations['Inspection'] : 'Inspection'; ?></a></li>
        </ol>
      </nav>
    </div>
    <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addApproverModal" data-id="new">
          <i class="bi bi-plus-circle"></i><?php echo isset($translations['Add New']) ? $translations['Add New'] : 'Add New'; ?>
        </button>
      </div>
  </div>
  <!-- End Page Title -->
  <!-- == Modal form - Approver == -->
      <div class="modal fade" id="addApproverModal" tabindex="-1" aria-labelledby="addApproverModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addApproverModalLabel"><b><?php echo isset($translations['Add New Approver']) ? $translations['Add New Approver'] : 'Add New Approver'; ?></b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for cid -->
                <input type="hidden" id="huid" name="huid" value="<?php echo $userid; ?>">
                <input type="hidden" id="approverId" name="approverId">
                <input type="hidden" id="hlang" name="hlang" value="<?php echo $lang; ?>">

                <div class="mb-3">
                  <label for="approverName" class="form-label"><?php echo isset($translations['Name']) ? $translations['Name'] : 'Name'; ?></label>
                  <input type="text" class="form-control" id="approverName" name="approverName" value="" required>
                </div>
                <div class="mb-3">
                  <label for="approverSurname" class="form-label"><?php echo isset($translations['Surname']) ? $translations['Surname'] : 'Surname'; ?></label>
                  <input type="text" class="form-control" id="approverSurname" name="approverSurname" required>
                </div>
                <div class="mb-3">
                  <label for="approverRole" class="form-label"><?php echo isset($translations['Roles']) ? $translations['Roles'] : 'Roles'; ?></label>
                  <input type="text" class="form-control" id="approverRole" name="approverRole" required>
                </div>
                <div class="mb-3">
                  <label for="approverPosition" class="form-label"><?php echo isset($translations['Position']) ? $translations['Position'] : 'Position'; ?></label>
                  <input type="text" class="form-control" id="approverPosition" name="approverPosition" required>
                </div>
                <div class="mb-3">
                  <label for="approverWorkplace" class="form-label"><?php echo isset($translations['Workplace']) ? $translations['Workplace'] : 'Workplace'; ?></label>
                  <!--
                  <select class="form-select" name="approverWorkplace" id="approverWorkplace" aria-label="Default select example">
                     <option value="">*** Please select one ***</option>
                      <?php //SelectLocations($locid, $con); ?>
                    </select>
                  -->
                  <input type="text" class="form-control" id="approverWorkplace" name="approverWorkplace" required>
                </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo isset($translations['Close']) ? $translations['Close'] : 'Close'; ?></button>
              <button type="submit" id="submitApprover" name="submitApprover" class="btn btn-success"><?php echo isset($translations['Submit']) ? $translations['Submit'] : 'Submit'; ?></button>
            </div>
            </form>
          </div>
        </div>
      </div>
  <!-- End of Modal -->
    <section class="section"> <!-- DATA TABLE - Approvers -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              
              <h5 class="card-title"><?php echo isset($translations['Approvers']) ? $translations['Approvers'] : 'Approvers'; ?></h5>
              <p><?php echo isset($translations['ePhytosanitary - Department of Agriculture, MAF']) ? $translations['ePhytosanitary - Department of Agriculture, MAF'] : 'ePhytosanitary - Department of Agriculture, MAF'; ?></p>
              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b><?php echo isset($translations['No']) ? $translations['No'] : 'No'; ?></b></th>
                   <th><?php echo isset($translations['Name and surname']) ? $translations['Name and surname'] : 'Name and surname'; ?></th>
                   <th><?php echo isset($translations['Roles']) ? $translations['Roles'] : 'Roles'; ?></th>
                   <th><?php echo isset($translations['Position']) ? $translations['Position'] : 'Position'; ?></th>
                   <th><?php echo isset($translations['Workplace']) ? $translations['Workplace'] : 'Workplace'; ?></th>
                   <th><?php echo isset($translations['Edit']) ? $translations['Edit'] : 'Edit'; ?></th>
                   <th><?php echo isset($translations['Delete']) ? $translations['Delete'] : 'Delete'; ?></th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    Approverslist($guid, $con); // List of Approvers
                  ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </section> <!-- End Data Table Approvers -->
    <?php
     }
    ?>
    <!-- Approvers form processing/submission - MODAL form -->
    <?php
      if(isset($_POST['submitApprover'])) {  // ADD/UPDATE Approver
        // Process the form submission for adding/updating approver
        
        // Debug: Check database connection
        if (!$con) {
            echo "<script>alert('Error: Database connection is not available.');</script>";
            exit;
        }
        
        $aid = $_POST['approverId']; // Hidden input for ID
        $aname = $_POST['approverName'];
        $asurname = $_POST['approverSurname'];
        $arole = $_POST['approverRole'];
        $aposition = $_POST['approverPosition'];
        $aworkplace = $_POST['approverWorkplace'];
        
        if($aid === 'new' || empty($aid)) {
            // Add new approver
            $result = AddApprover($aname, $asurname, $arole, $aposition, $aworkplace, $userid, $guid, $con); // Function to add new approver

            if ($result) {
               // echo "<script>alert('New approver added successfully!');</script>"; // Success message
                echo "<script>window.location.href='masterdata.php?part=approvers&uid=" . $userid . "';</script>"; // Redirect to refresh the page
            } 
        } else {
            // Update existing approver
            UpdateApprover($aid, $aname, $asurname, $arole, $aposition, $aworkplace, $con); // Function to update approver
           // echo "<script>alert('Approver with ID: " . $aid . " updated.');</script>"; // Debugging line
            echo "<script>window.location.href='masterdata.php?part=approvers&uid=" . $userid . "';</script>"; // Redirect to refresh the page
        }
    } // End of if submitApprover
    // DELETE approver
    if((isset($_GET['part']) && $_GET['part']==='approvers') && (isset($_GET['del']) && $_GET['del'] === 'yes')) {
        if(isset($_GET['aid']) && !empty($_GET['aid'])) {
            $approverId = $_GET['aid']; // Approver ID to delete
            // Call the function to delete approver
          //  DeleteApprover($approverId, $con); // Function to delete approver 
            echo "<script>alert('Approver with ID: " . $approverId . " deleted.');</script>"; // Debugging line
            echo "<script>window.location.href='masterdata.php?part=approvers&uid=" . $userid . "&lang=" . $lang . "';</script>"; // Redirect to approvers page
    }
  }
  ?>
  <!-- ======= *************** Conveyance ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='conveyance') {
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1><?php echo isset($translations['Conveyance']) ? $translations['Conveyance'] : 'Conveyance'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item"><?php echo isset($translations['Tables']) ? $translations['Tables'] : 'Tables'; ?></li>
          <li class="breadcrumb-item active"><?php echo isset($translations['Conveyance']) ? $translations['Conveyance'] : 'Conveyance'; ?></li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addConveyenceModal" data-cid="new">
          <i class="bi bi-plus-circle"></i><?php echo isset($translations['Add New Conveyance']) ? $translations['Add New Conveyance'] : 'Add New Conveyance'; ?>
        </button>
      </div>
    </div><!-- End Page Title -->
    <!-- == Modal form - Conveyance == -->
      <div class="modal fade" id="addConveyenceModal" tabindex="-1" aria-labelledby="addConveyenceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addConveyenceModalLabel"><b><?php echo isset($translations['Add New Conveyance']) ? $translations['Add New Conveyance'] : 'Add New Conveyance'; ?></b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for cid -->
                <input type="hidden" id="conveyanceId" name="conveyanceId">
                <input type="hidden" id="huid" name="huid" value="<?php echo $userid; ?>">
                <input type="hidden" id="hlang" name="hlang" value="<?php echo $lang; ?>">
                <div class="mb-3">
                  <label for="conveyanceCode" class="form-label"><?php echo isset($translations['Code']) ? $translations['Code'] : 'Code'; ?></label>
                  <input type="text" class="form-control" id="conveyanceCode" name="conveyanceCode" required>
                </div>
                <div class="mb-3">
                  <label for="conveyanceType" class="form-label"><?php echo isset($translations['Conveyance Type']) ? $translations['Conveyance Type'] : 'Conveyance Type'; ?></label>
                  <input type="text" class="form-control" id="conveyanceType" name="conveyanceType" required>
                </div>
                <div class="mb-3">
                  <label for="conveyanceDescription" class="form-label"><?php echo isset($translations['Description']) ? $translations['Description'] : 'Description'; ?></label>
                  <textarea class="form-control" id="conveyanceDescription" name="conveyanceDescription" rows="3"></textarea>
                </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" id="submitConveyance" name="submitConveyance" class="btn btn-success">Submit</button>
            </div>
            </form>
          </div>
        </div>
      </div>
    <!-- End of Modal -->
    <section class="section"> <!-- DATA TABLE - Conveyance -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              
              <h5 class="card-title"><?php echo isset($translations['Conveyance']) ? $translations['Conveyance'] : 'Conveyance'; ?></h5>
              <p><?php echo isset($translations['ePhytosanitary by Department of Agriculture, MAF - Conveyance']) ? $translations['ePhytosanitary by Department of Agriculture, MAF - Conveyance'] : 'ePhytosanitary by Department of Agriculture, MAF - Conveyance'; ?></p>

              <!-- Table with stripped rows -->
               
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b><?php echo isset($translations['No']) ? $translations['No'] : 'No'; ?></b></th>
                   <th><?php echo isset($translations['Code']) ? $translations['Code'] : 'Code'; ?></th>
                   <th><?php echo isset($translations['Conveyance Type']) ? $translations['Conveyance Type'] : 'Conveyance Type'; ?></th>
                   <th><?php echo isset($translations['Description']) ? $translations['Description'] : 'Description'; ?></th>
                   <th><?php echo isset($translations['Status']) ? $translations['Status'] : 'Status'; ?></th>
                   <th><?php echo isset($translations['Edit']) ? $translations['Edit'] : 'Edit'; ?></th>
                   <th><?php echo isset($translations['Delete']) ? $translations['Delete'] : 'Delete'; ?></th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    Conveyancelist($userid, $con); // List of Conveyance
                  ?>
                </tbody>
              </table>
              
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </div>
    </section> <!-- End Data Table Conveyance -->
    <?php
     }
    ?>
    <!-- Conveyance form processing/submission - MODAL form -->
    <?php
      if(isset($_POST['submitConveyance'])) {  // ADD/UPDATE Conveyance
        // Process the form submission for adding/updating conveyance
        $cid = $_POST['conveyanceId']; // Hidden input for ID
        $cCode = $_POST['conveyanceCode'];
        $cType = $_POST['conveyanceType'];
        $cDescription = $_POST['conveyanceDescription'];
        $cuid = isset($_POST['huid']) ? $_POST['huid'] : '';
        $clang = isset($_POST['hlang']) ? $_POST['hlang'] : '';
        
        if($cid === 'new') {
            // Add new conveyance
            AddConveyance($cuid, $cCode, $cType, $cDescription, $con); // Function to add new conveyance
            // Redirect to conveyance list with uid and lang parameters preserved
            echo "<script>alert('New conveyance added-Done');</script>";
            echo "<script>setTimeout(function() { window.location.href = 'masterdata.php?part=conveyance&uid=" . urlencode($cuid) . "&lang=" . urlencode($clang) . "'; }, 500);</script>";
        } else {
            // Update existing conveyance
            UpdateConveyance($cid, $cCode, $cType, $cDescription, $con); // Function to update conveyance
            echo "<script>alert('Conveyance with ID: " . $cid . " updated.');</script>";
            echo "<script>setTimeout(function() { window.location.href = 'masterdata.php?part=conveyance&uid=" . urlencode($cuid) . "&lang=" . urlencode($clang) . "'; }, 500);</script>";
        }
      } // End of if submitConveyance

      // DELETE conveyance
      if(isset($_GET['part']) && $_GET['part']==='conveyance' && isset($_GET['del']) && $_GET['del'] === 'yes') {
          if(isset($_GET['cid']) && !empty($_GET['cid'])) {
              $conveyanceId = $_GET['cid']; // Conveyance ID to delete
              // Call the function to delete conveyance
              DeleteConveyance($conveyanceId, $con); // Function to delete conveyance    
          }
      }
    ?>
    <!--============= Inspection methods =============-->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='inspectionmethod') {
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1><?php echo isset($translations['Inspection method']) ? $translations['Inspection method'] : 'Inspection Method'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item"><?php echo isset($translations['Tables']) ? $translations['Tables'] : 'Tables'; ?></li>
          <li class="breadcrumb-item active"><?php echo isset($translations['Inspection method']) ? $translations['Inspection method'] : 'Inspection Method'; ?></li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addInspectionMethodModal" data-imid="new">
          <i class="bi bi-plus-circle"></i>Add New Inspection Method
        </button>
      </div>
    </div><!-- End Page Title -->
    <!-- == Modal form - Inspection Method == -->
      <div class="modal fade" id="addInspectionMethodModal" tabindex="-1" aria-labelledby="addInspectionMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <input type="hidden" name="uid" value="<?php echo $userid; ?>">
              <div class="modal-header">
                <h5 class="modal-title" id="addInspectionMethodModalLabel"><b>Add New Inspection Method</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for imid -->
                <input type="hidden" id="inspectionMethodId" name="inspectionMethodId">
                <div class="mb-3">
                  <label for="inspectionMethodCode" class="form-label">Code</label>
                  <input type="text" class="form-control" id="inspectionMethodCode" name="inspectionMethodCode" required>
                </div>
                <div class="mb-3">
                  <label for="inspectionMethodName" class="form-label">Name</label>
                  <input type="text" class="form-control" id="inspectionMethodName" name="inspectionMethodName" required>
                </div>
                <div class="mb-3">
                  <label for="inspectionMethodDescription" class="form-label">Description</label>
                  <textarea class="form-control" id="inspectionMethodDescription" name="inspectionMethodDescription" rows="3"></textarea> 
                </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" id="submitInspectionMethod" name="submitInspectionMethod" class="btn btn-success">Submit</button> 
            </div>
            </form>
          </div>
        </div>
      </div>
    <!-- End of Modal -->
    <section class="section"> <!-- DATA TABLE - Inspection Method -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              
              <h5 class="card-title">Inspection Method</h5>
              <p>ePhytosanitary by Department of Agriculture, MAF - Inspection Method</p>

              <!-- Table with stripped rows -->
               
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b>N</b>o</th>
                   <th>Code</th>
                   <th>Name</th>
                   <th>Description</th>
                   <th>Status</th>
                   <th>Edit</th>
                   <th>Delete</th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    InspectionMethodList($userid, $con); // List of Inspection Method 
                  ?>
                </tbody>
              </table>
              
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </div>
    </section> <!-- End Data Table Inspection Method -->
    <?php
     }
    ?>
    <!-- Inspection Method form processing/submission - MODAL form -->
    <?php
      if(isset($_POST['submitInspectionMethod'])) {  // ADD/UPDATE Inspection Method
        // Process the form submission for adding/updating inspection method
        $imid = $_POST['inspectionMethodId']; // Hidden input for ID
        $imCode = $_POST['inspectionMethodCode'];
        $imName = $_POST['inspectionMethodName'];
        $imDescription = $_POST['inspectionMethodDescription'];
        $userid = isset($_POST['uid']) ? $_POST['uid'] : ''; // Get userid from form
        
        if($imid === 'new') {
            // Add new inspection method
            AddInspectionMethod($imCode, $imName, $imDescription, $userid, $con); // Function to add new inspection method
            echo "<script>alert('New inspection method added-Done');</script>"; // Debugging line
    
        } else {
            // Update existing inspection method
            echo "<script>alert('Inspection method with ID: " . $imid . " updated.');</script>"; // Debugging line
            UpdateInspectionMethod($imid, $imCode, $imName, $imDescription, $userid, $con); // Function to update inspection method
          
        }
     } // End of if submitInspectionMethod

     // DELETE inspection method
     if(isset($_GET['part']) && $_GET['part']==='inspectionmethod' && isset($_GET['del']) && $_GET['del'] === 'yes') {
         if(isset($_GET['mid']) && !empty($_GET['mid'])) {
             $inspectionMethodId = $_GET['mid']; // Inspection Method ID to delete
             // Call the function to delete inspection method
             DeleteInspectionMethod($inspectionMethodId, $userid, $con); // Function to delete inspection method    
         }
     } // End of DELETE inspection method
?>
   <!--============= Treatment methods =============-->    
    <?php
     if(isset($_GET['part']) && $_GET['part']==='treatmentmethod') {
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1>Treatment Method</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>">Home</a></li>
          <li class="breadcrumb-item">Tables</li> 
          <li class="breadcrumb-item active">Treatment Method</li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addTreatmentMethodModal" data-tmid="new">
          <i class="bi bi-plus-circle"></i>Add New Treatment Method
        </button>
      </div>
    </div><!-- End Page Title -->
    <!-- == Modal form - Treatment Method == -->
      <div class="modal fade" id="addTreatmentMethodModal" tabindex="-1" aria-labelledby="addTreatmentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addTreatmentMethodModalLabel"><b>Add New Treatment Method</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for tmid -->
                <input type="hidden" id="treatmentMethodId" name="treatmentMethodId">
                <input type="hidden" id="huid" name="huid" value="<?php echo $userid; ?>">
                <div class="mb-3">
                  <label for="treatmentMethodCode" class="form-label">Code</label>
                  <input type="text" class="form-control" id="treatmentMethodCode" name="treatmentMethodCode" required> 
                </div>
                <div class="mb-3">
                  <label for="treatmentMethodName" class="form-label">Name</label>
                  <input type="text" class="form-control" id="treatmentMethodName" name="treatmentMethodName" required> 
                </div>
                <div class="mb-3">
                  <label for="treatmentMethodDescription" class="form-label">Description</label>
                  <textarea class="form-control" id="treatmentMethodDescription" name="treatmentMethodDescription" rows="3"></textarea> 
                </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" id="submitTreatmentMethod" name="submitTreatmentMethod" class="btn btn-success">Submit</button>
            </div>
            </form>
          </div>
        </div>
      </div>
    <!-- End of Modal -->
    <section class="section"> <!-- DATA TABLE - Treatment Method -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              
              <h5 class="card-title">Treatment Method</h5>
              <p>ePhytosanitary by Department of Agriculture, MAF - Treatment Method</p>
              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b>N</b>o</th>
                   <th>Code</th>
                   <th>Name</th>
                   <th>Description</th>
                   <th>Status</th>
                   <th>Edit</th>
                   <th>Delete</th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    TreatmentMethodList($con); // List of Treatment Method
                  ?>
                </tbody>
              </table>
            </div>
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </div>
    </section> <!-- End Data Table Treatment Method -->
    <?php
     }
    ?>   
    <!-- Treatment Method form processing/submission - MODAL form -->
    <?php
      if(isset($_POST['submitTreatmentMethod'])) {  // ADD/UPDATE Treatment Method
        // Process the form submission for adding/updating treatment method
       
        $tmid = $_POST['treatmentMethodId']; // Hidden input for ID
        $huid = $_POST['huid']; // Hidden input for user ID
        $tmCode = $_POST['treatmentMethodCode'];
        $tmName = $_POST['treatmentMethodName'];
        $tmDescription = $_POST['treatmentMethodDescription'];
        
        if($tmid === 'new') {
            // Add new treatment method
            AddTreatmentMethod($huid, $tmCode, $tmName, $tmDescription, $con); // Function to add new treatment method
            
        } else {
            // Update existing treatment method
           // echo "<script>alert('Treatment method with ID: " . $tmid . " updated.');</script>"; // Debugging line
            UpdateTreatmentMethod($huid, $tmid, $tmCode, $tmName, $tmDescription, $con); // Function to update treatment method
           
        }
      } // End of if submitTreatmentMethod

    // DELETE treatment method
    if(isset($_GET['part']) && $_GET['part']==='treatmentmethod' && isset($_GET['del']) && $_GET['del'] === 'yes') {
        if(isset($_GET['tmid']) && !empty($_GET['tmid'])) {
            $treatmentMethodId = $_GET['tmid']; // Treatment Method ID to delete
            // Call the function to delete treatment method
            DeleteTreatmentMethod($treatmentMethodId, $con); // Function to delete treatment method    
        }
    }
    ?>
    <!-- ======= *************** Entity Type ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='entitytype') {
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1>Entity Type</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>">Home</a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item active">Entity Type</li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addEntityTypeModal" data-etid="new">
          <i class="bi bi-plus-circle"></i>Add New Entity Type
        </button>
      </div>
    </div><!-- End Page Title -->
    <!-- == Modal form - Entity Type == -->
      <div class="modal fade" id="addEntityTypeModal" tabindex="-1" aria-labelledby="addEntityTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addEntityTypeModalLabel"><b>Add New Entity Type</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for etid -->
                <input type="hidden" id="entityTypeId" name="entityTypeId">
                <input type="hidden" name="userid" value="<?php echo $userid; ?>">
                <div class="mb-3">
                  <label for="entityTypeCode" class="form-label">Code</label>
                  <input type="text" class="form-control" id="entityTypeCode" name="entityTypeCode" required>
                </div>
                <div class="mb-3">
                  <label for="entityTypeName" class="form-label">Name</label>
                  <input type="text" class="form-control" id="entityTypeName" name="entityTypeName" required>
                </div>
                <div class="mb-3">
                  <label for="entityTypeDescription" class="form-label">Description</label>
                  <textarea class="form-control" id="entityTypeDescription" name="entityTypeDescription" rows="3"></textarea>
                </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" id="submitEntityType" name="submitEntityType" class="btn btn-success">Submit</button>
            </div>
            </form>
          </div>
        </div>
      </div>
    <!-- End of Modal -->
    <section class="section"> <!-- DATA TABLE - Entity Type -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              
              <h5 class="card-title">Entity Type</h5>
              <p>ePhytosanitary by Department of Agriculture, MAF - Entity Type</p>
              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b>N</b>o</th>
                   <th>Code</th>
                   <th>Name</th>
                   <th>Description</th>
                   <th>Status</th>
                   <th>Edit</th>
                   <th>Delete</th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    EntityTypeList($con); // List of Entity Type
                  ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
     </div>
    </section> <!-- End Data Table Entity Type -->
    <?php
     }
    ?>
    <!-- Entity Type form processing/submission - MODAL form -->
    <?php
      if(isset($_POST['submitEntityType'])) {  // ADD/UPDATE Entity Type
        // Process the form submission for adding/updating entity type
        $etid = $_POST['entityTypeId']; // Hidden input for ID
        $etCode = $_POST['entityTypeCode'];
        $etName = $_POST['entityTypeName'];
        $etDescription = $_POST['entityTypeDescription'];
        
        if($etid === 'new') {
            // Add new entity type
            AddEntityType($etCode, $etName, $etDescription, $userid, $con); // Function to add new entity type
            
        } else {
            // Update existing entity type
            echo "<script>alert('Entity type with ID: " . $etid . " updated.');</script>"; // Debugging line
            UpdateEntityType($etid, $etCode, $etName, $etDescription, $userid, $con); // Function to update entity type
           
        }
      } // End of if submitEntityType
    // DELETE entity type
    if(isset($_GET['part']) && $_GET['part']==='entitytype' && isset($_GET['del']) && $_GET['del'] === 'yes') {
        if(isset($_GET['etid']) && !empty($_GET['etid'])) {
            $entityTypeId = $_GET['etid']; // Entity Type ID to delete
            // Call the function to delete entity type
            DeleteEntityType($entityTypeId, $con); // Function to delete entity type    
        }
    }
    ?>
   <!-- ======= *************** Modules ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='modules') {
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1>Modules</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>">Home</a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item active">Modules</li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModuleModal" data-mid="new">
          <i class="bi bi-plus-circle"></i>Add New Module
        </button> 
      </div>
    </div><!-- End Page Title -->
    <!-- == Modal form - Module == -->
      <div class="modal fade" id="addModuleModal" tabindex="-1" aria-labelledby="addModuleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addModuleModalLabel"><b>Add New Module</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for mid -->
                <input type="hidden" id="moduleId" name="moduleId">
                <div class="mb-3">
                  <label for="moduleCode" class="form-label">Code</label>
                  <input type="text" class="form-control" id="moduleCode" name="moduleCode" required>
                </div>
                <div class="mb-3">
                  <label for="moduleName" class="form-label">Name</label>
                  <input type="text" class="form-control" id="moduleName" name="moduleName" required>
                </div>
                <div class="mb-3">
                  <label for="moduleDescription" class="form-label">Description</label>
                  <textarea class="form-control" id="moduleDescription" name="moduleDescription" rows="3"></textarea>
                </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" id="submitModule" name="submitModule" class="btn btn-success">Submit</button>
            </div>
            </form>
          </div>
        </div>
      </div>
    <!-- End of Modal -->
    <section class="section"> <!-- DATA TABLE - Modules -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              
              <h5 class="card-title">Modules</h5>
              <p>ePhytosanitary by Department of Agriculture, MAF - Modules</p>
              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b>N</b>o</th>
                   <th>Code</th>
                   <th>Name</th>
                   <th>Description</th>
                   <th>Status</th>
                   <th>Edit</th>
                   <th>Delete</th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    ModuleList($con); // List of Modules
                  ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </div>
    </section> <!-- End Data Table Modules -->
    <?php
     }
    ?>
    <!-- Module form processing/submission - MODAL form -->
    <?php
      if(isset($_POST['submitModule'])) {  // ADD/UPDATE Module
        // Process the form submission for adding/updating module
        $mid = $_POST['moduleId']; // Hidden input for ID
        $mCode = $_POST['moduleCode'];
        $mName = $_POST['moduleName'];
        $mDescription = $_POST['moduleDescription'];
        
        if($mid === 'new') {
            // Add new module
            AddModule($mCode, $mName, $mDescription, $con); // Function to add new module
            
        } else {
            // Update existing module
            echo "<script>alert('Module with ID: " . $mid . " updated.');</script>"; // Debugging line
            UpdateModule($mid, $mCode, $mName, $mDescription, $con); // Function to update module
           
        }
      } // End of if submitModule
    // DELETE module
    if(isset($_GET['part']) && $_GET['part']==='modules' && isset($_GET['del']) && $_GET['del'] === 'yes') {
        if(isset($_GET['mid']) && !empty($_GET['mid'])) {
            $moduleId = $_GET['mid']; // Module ID to delete
            // Call the function to delete module
            DeleteModule($moduleId, $con); // Function to delete module    
        }
    }
  ?>

 </main> <!-- End #main -->
 
 <!-- End User -->
 
  <!-- ======= Footer ======= -->
  <!--  PK: No need for footer in this page
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>DOA</span></strong>. All Rights Reserved
    </div>

  </footer>
    -->
  <!-- End Footer -->
  <div id="usdiv"></div> <!-- Make ajax happy in users-validate.js -->
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
  
    // 2) Check if new or edit country modal is opened
    $('#addCountryModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var cid = button.data('cid'); // Extract info from data-* attributes
      var modal = $(this);
      var countryNameInput = modal.find('#countryName');
      var alphaCodeInput = modal.find('#alphaCode');
      var numCodeInput = modal.find('#numCode');
      var currencyInput = modal.find('#currency');
      var countryDescriptionInput = modal.find('#countryDescription');
      var submitButton = modal.find('#submitCountry');
      

      modal.find('#countryId').val(cid); // Set the hidden input value
      if (cid === 'new') {
        countryNameInput.val(''); //  clear inputs
        alphaCodeInput.val('');
        numCodeInput.val('');
        currencyInput.val('');
        countryDescriptionInput.val('');
        modal.find('.modal-title').text('Add New Country');
        submitButton.text('Submit');
      } else {
        countryNameInput.val(button.data('cname')); // Set the country name
        alphaCodeInput.val(button.data('alcode')); // Set the alpha code from data-alcode attribute
        numCodeInput.val(button.data('numcode')); // Set the numeric code from data-numcode attribute
        currencyInput.val(button.data('currency')); // Set the currency from data-currency attribute
        modal.find('.modal-title').text('Edit Country');
        submitButton.text('Update');
      }
    });
    // 3) process the form submission for adding/updating products
    $('#addProductModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var pid = button.data('pid'); // Extract info from data-* attributes
      var modal = $(this);
      var productCodeInput = modal.find('#productCode');
      var productNameInput = modal.find('#productName');
      var scientNameInput = modal.find('#scientName');
      var hsCodeInput = modal.find('#hsCode');
      var productGroupSelect = modal.find('#productGroup');
      var descriptionInput = modal.find('#description');
      var submitButton = modal.find('#submitProduct');

      modal.find('#productId').val(pid); // Set the hidden input value
      if (pid === 'new') {
        productCodeInput.val(''); // Clear inputs
        productNameInput.val('');
        scientNameInput.val('');
        hsCodeInput.val('');
        descriptionInput.val('');
        productGroupSelect.val(''); // Reset product group selection
        modal.find('.modal-title').text('Add New Product');
        submitButton.text('Submit');
      } else {
        productCodeInput.val(button.data('code')); // Set the product code
        productNameInput.val(button.data('pname')); // Set the product name
        scientNameInput.val(button.data('scientname')); // Set the scientific name
        hsCodeInput.val(button.data('hscode')); // Set the HS code
        descriptionInput.val(button.data('desc')); // Set the description
        productGroupSelect.val(button.data('productgroup')); // Set the product group ID
        modal.find('.modal-title').text('Edit Product');
        submitButton.text('Update');
      }
    });
   // 4) process the form submission for adding/updating product groups
    $('#addProductGroupModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var pgroupid = button.data('pgroupid'); // Extract info from data-* attributes
      var modal = $(this);
      var productGroupNameInput = modal.find('#productGroupName');
      var productGroupDescriptionInput = modal.find('#productGroupDescription');
      var submitButton = modal.find('#submitProductGroup');

      modal.find('#productGroupId').val(pgroupid); // Set the hidden input value
      if (pgroupid === 'new') {
        productGroupNameInput.val(''); // Clear inputs
        productGroupDescriptionInput.val('');
        modal.find('.modal-title').text('Add New Product Group');
        submitButton.text('Submit');
      } else {
        productGroupNameInput.val(button.data('gname')); // Set the product group name
        productGroupDescriptionInput.val(button.data('gdesc')); // Set the product group description
        modal.find('.modal-title').text('Edit Product Group');
        submitButton.text('Update');
      }
    });

    // 5) process the form submission for adding/updating product units
    $('#addProductUnitModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var punitid = button.data('punitid'); // Extract info from data-* attributes
      var modal = $(this);
      var productUnitCodeInput = modal.find('#productUnitCode');
      var productUnitSymbolInput = modal.find('#productUnitSymbol');
      var productUnitTitleInput = modal.find('#productUnitTitle');
      var submitButton = modal.find('#submitProductUnit');

      modal.find('#productUnitId').val(punitid); // Set the hidden input value
      if (punitid === 'new') {
        productUnitCodeInput.val(''); // Clear inputs
        productUnitSymbolInput.val('');
        productUnitTitleInput.val('');
        modal.find('.modal-title').text('Add New Product Unit');
        submitButton.text('Submit');
      } else {
        productUnitCodeInput.val(button.data('code')); // Set the product unit name
        productUnitSymbolInput.val(button.data('symb')); // Set the product unit symbol
        productUnitTitleInput.val(button.data('title')); // Set the product unit title
        modal.find('.modal-title').text('Edit Product Unit');
        submitButton.text('Update');
      }
    });

    // Pest Modals and form processing done in pests.php
    $('#addPestModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var pestid = button.data('pestid'); // Extract info from data-* attributes
      var modal = $(this);
      var pestNameInput = modal.find('#pestName');
      var pestScientificNameInput = modal.find('#pestScientificName');
      var pestCategoryInput = modal.find('#pestCategory');
      var submitButton = modal.find('#submitPest');

      modal.find('#pestId').val(pestid); // Set the hidden input value
      if (pestid === 'new') {
        pestNameInput.val(''); // Clear inputs
        pestScientificNameInput.val('');
        pestCategoryInput.val('');
        modal.find('.modal-title').text('Add New Pest');
        submitButton.text('Submit');
      } else {
        pestNameInput.val(button.data('pname')); // Set the pest name (corrected from 'name' to 'pname')
        pestScientificNameInput.val(button.data('scientificname')); // Set the pest scientific name
        pestCategoryInput.val(button.data('category')); // Set the category
        modal.find('.modal-title').text('Edit Pest');
        submitButton.text('Update');
      }
    });

    // 6) process the form submission for adding/updating conveyance
    $('#addConveyenceModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var cid = button.data('cid'); // Extract info from data-* attributes
      var modal = $(this);
      var conveyanceCodeInput = modal.find('#conveyanceCode');
      var conveyanceTypeInput = modal.find('#conveyanceType');
      var conveyanceDescriptionInput = modal.find('#conveyanceDescription');
      var submitButton = modal.find('#submitConveyance');

      modal.find('#conveyanceId').val(cid); // Set the hidden input value
      if (cid === 'new') {
        conveyanceCodeInput.val(''); // Clear inputs
        conveyanceTypeInput.val('');
        conveyanceDescriptionInput.val('');
        modal.find('.modal-title').text('Add New Conveyance');
        submitButton.text('Submit');
      } else {
        conveyanceCodeInput.val(button.data('code')); // Set the conveyance code
        conveyanceTypeInput.val(button.data('cvtype')); // Set the conveyance type
        conveyanceDescriptionInput.val(button.data('desc')); // Set the description
        modal.find('.modal-title').text('Edit Conveyance');
        submitButton.text('Update');
      }
    });

    // 7) process the form submission for adding/updating inspection method
    $('#addInspectionMethodModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var imid = button.data('imid'); // Extract info from data-* attributes
      var modal = $(this);
      var inspectionMethodCodeInput = modal.find('#inspectionMethodCode');
      var inspectionMethodNameInput = modal.find('#inspectionMethodName');
      var inspectionMethodDescriptionInput = modal.find('#inspectionMethodDescription');
      var submitButton = modal.find('#submitInspectionMethod');

      modal.find('#inspectionMethodId').val(imid); // Set the hidden input value
      if (imid === 'new') {
        inspectionMethodCodeInput.val(''); // Clear inputs
        inspectionMethodNameInput.val('');
        inspectionMethodDescriptionInput.val('');
        modal.find('.modal-title').text('Add New Inspection Method');
        submitButton.text('Submit');
      } else {
        inspectionMethodCodeInput.val(button.data('code')); // Set the inspection method code
        inspectionMethodNameInput.val(button.data('name')); // Set the inspection method name
        inspectionMethodDescriptionInput.val(button.data('desc')); // Set the description
        modal.find('.modal-title').text('Edit Inspection Method');
        submitButton.text('Update');
      }
    });

    // 8) process the form submission for adding/updating treatment method
    $('#addTreatmentMethodModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var tmid = button.data('tmid'); // Extract info from data-* attributes
      var modal = $(this);
      var treatmentMethodCodeInput = modal.find('#treatmentMethodCode');
      var treatmentMethodNameInput = modal.find('#treatmentMethodName');
      var treatmentMethodDescriptionInput = modal.find('#treatmentMethodDescription');
      var submitButton = modal.find('#submitTreatmentMethod');

      modal.find('#treatmentMethodId').val(tmid); // Set the hidden input value
      if (tmid === 'new') {
        treatmentMethodCodeInput.val(''); // Clear inputs
        treatmentMethodNameInput.val('');
        treatmentMethodDescriptionInput.val('');
        modal.find('.modal-title').text('Add New Treatment Method');
        submitButton.text('Submit');
      } else {
        treatmentMethodCodeInput.val(button.data('code')); // Set the treatment method code
        treatmentMethodNameInput.val(button.data('name')); // Set the treatment method name
        treatmentMethodDescriptionInput.val(button.data('desc')); // Set the description
        modal.find('.modal-title').text('Edit Treatment Method');
        submitButton.text('Update');
      }
    });
// 9) process the form submission for adding/updating entity type
    $('#addEntityTypeModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var etid = button.data('etid'); // Extract info from data-* attributes
      var modal = $(this);
      var entityTypeCodeInput = modal.find('#entityTypeCode');
      var entityTypeNameInput = modal.find('#entityTypeName');
      var entityTypeDescriptionInput = modal.find('#entityTypeDescription');
      var submitButton = modal.find('#submitEntityType');

      modal.find('#entityTypeId').val(etid); // Set the hidden input value
      if (etid === 'new') {
        entityTypeCodeInput.val(''); // Clear inputs
        entityTypeNameInput.val('');
        entityTypeDescriptionInput.val('');
        modal.find('.modal-title').text('Add New Entity Type');
        submitButton.text('Submit');
      } else {
        entityTypeCodeInput.val(button.data('code')); // Set the entity type code
        entityTypeNameInput.val(button.data('name')); // Set the entity type name
        entityTypeDescriptionInput.val(button.data('desc')); // Set the description
        modal.find('.modal-title').text('Edit Entity Type');
        submitButton.text('Update');
      }
    });

    //10) process the form submission for adding/updating modules
    $('#addModuleModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var mid = button.data('mid'); // Extract info from data-* attributes
      var modal = $(this);
      var moduleCodeInput = modal.find('#moduleCode');
      var moduleNameInput = modal.find('#moduleName');
      var moduleDescriptionInput = modal.find('#moduleDescription');
      var submitButton = modal.find('#submitModule');

      modal.find('#moduleId').val(mid); // Set the hidden input value
      if (mid === 'new') {
        moduleCodeInput.val(''); // Clear inputs
        moduleNameInput.val('');
        moduleDescriptionInput.val('');
        modal.find('.modal-title').text('Add New Module');
        submitButton.text('Submit');
      } else {
        moduleCodeInput.val(button.data('code')); // Set the module code
        moduleNameInput.val(button.data('name')); // Set the module name
        moduleDescriptionInput.val(button.data('desc')); // Set the description
        modal.find('.modal-title').text('Edit Module');
        submitButton.text('Update');
      }
    });

    // SET FOCUS on input fields in data form when the page loads
    window.addEventListener('DOMContentLoaded', function() {
     // Countries form
      var addCountryModal = document.getElementById('addCountryModal');
      if (addCountryModal) {
          addCountryModal.addEventListener('shown.bs.modal', function () {
            var countryNameInput = document.getElementById('countryName');
            if (countryNameInput) {
                  countryNameInput.focus();
                  countryNameInput.select();
            }
          });
      } // End of if addCountryModal
      
      // Product Group form
      var productGroupNameInput = document.getElementById('productGroupName');
      if (productGroupNameInput) {
        productGroupNameInput.focus();
        productGroupNameInput.select();
      } // End of if productGroupName
      
     // Location form
      var nameInput = document.getElementById('locationid');
        if (nameInput) {
            nameInput.focus();
            nameInput.select();
      } // End of if locationid

     // Product form
      var addProductModal = document.getElementById('addProductModal');
      if (addProductModal) {
        addProductModal.addEventListener('shown.bs.modal', function () {
        var productCodeInput = document.getElementById('productCode');
          if (productCodeInput) {
            productCodeInput.focus();
            productCodeInput.select();
          }
        });
      }
      // Product Group form
      var addProductGroupModal = document.getElementById('addProductGroupModal');
          if (addProductGroupModal) {
              addProductGroupModal.addEventListener('shown.bs.modal', function () {
                var productGroupNameInput = document.getElementById('productGroupName');
                if (productGroupNameInput) {
                    productGroupNameInput.focus();
                    productGroupNameInput.select();
                }
              });
          } // End of product group -addProductGroupModal

      // Product Unit form
      var addProductUnitModal = document.getElementById('addProductUnitModal');
          if (addProductUnitModal) {
              addProductUnitModal.addEventListener('shown.bs.modal', function () {
                var productUnitCodeInput = document.getElementById('productUnitCode');
                if (productUnitCodeInput) {
                    productUnitCodeInput.focus();
                    productUnitCodeInput.select();
                }
              });
          } // End of product unit -addProductUnitModal
      // Conveyance form
      var addConveyenceModal = document.getElementById('addConveyenceModal');
          if (addConveyenceModal) {
              addConveyenceModal.addEventListener('shown.bs.modal', function () {
                var conveyanceCodeInput = document.getElementById('conveyanceCode');
                if (conveyanceCodeInput) {
                    conveyanceCodeInput.focus();
                    conveyanceCodeInput.select();
                }
              });
          } // End of conveyance -addConveyenceModal
      // Inspection Method form
      var addInspectionMethodModal = document.getElementById('addInspectionMethodModal');
          if (addInspectionMethodModal) {
              addInspectionMethodModal.addEventListener('shown.bs.modal', function () {
                var inspectionMethodCodeInput = document.getElementById('inspectionMethodCode');
                if (inspectionMethodCodeInput) {
                    inspectionMethodCodeInput.focus();
                    inspectionMethodCodeInput.select();
                }
              });
          } // End of inspection method -addInspectionMethodModal
      // Treatment Method form
      var addTreatmentMethodModal = document.getElementById('addTreatmentMethodModal');
          if (addTreatmentMethodModal) {
              addTreatmentMethodModal.addEventListener('shown.bs.modal', function () {
                var treatmentMethodCodeInput = document.getElementById('treatmentMethodCode');
                if (treatmentMethodCodeInput) {
                    treatmentMethodCodeInput.focus();
                    treatmentMethodCodeInput.select();
                }
              });
          } // End of treatment method -addTreatmentMethodModal

      // Module list form
      var addModuleModal = document.getElementById('addModuleModal');
          if (addModuleModal) {
              addModuleModal.addEventListener('shown.bs.modal', function () {
                var moduleCodeInput = document.getElementById('moduleCode');
                if (moduleCodeInput) {
                    moduleCodeInput.focus();
                    moduleCodeInput.select();
                }
              });
          } // End of module -addModuleModal
    }); // End of Window DOMContentLoaded

    // Search box for ALL THE DATA TABLES in this file - automatically submit the form on input
    window.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('search-query');
      if (searchInput) {
        searchInput.focus();
        searchInput.select();
        searchInput.addEventListener('input', function() {
        // Submit the form automatically on each input
        this.form.submit();
      });
      }
    });

    // Handle edit approver modal population
    $('#addApproverModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      
      // Extract data from data-* attributes
      var id = button.data('id');
      var name = button.data('name');
      var surname = button.data('surname');
      var role = button.data('role');
      var position = button.data('position');
      var workplace = button.data('workplace');
      
      // Debug: Show what data we extracted
     // console.log('Modal data extracted:', {id: id, name: name, surname: surname, role: role, position: position, workplace: workplace});
      
      // Get the modal
      var modal = $(this);
      
      // Check if we're editing an existing approver or adding a new one
      if (id && id !== 'new') {
        // Editing existing approver - populate form fields
        modal.find('.modal-title').text('Edit Approver');
        modal.find('#approverId').val(id);
        modal.find('#approverName').val(name);
        modal.find('#approverSurname').val(surname);
        modal.find('#approverRole').val(role);
        modal.find('#approverPosition').val(position);
        modal.find('#approverWorkplace').val(workplace);
        modal.find('#submitApprover').text('Update');
      } else {
        // Adding new approver - clear form fields
        modal.find('.modal-title').text('Add New Approver');
        modal.find('#approverId').val('');
        modal.find('#approverName').val('');
        modal.find('#approverSurname').val('');
        modal.find('#approverRole').val('');
        modal.find('#approverPosition').val('');
        modal.find('#approverWorkplace').val('');
        modal.find('#submitApprover').text('Submit');
      }
    });
  </script>
  
</body>

</html>