<?php
      // Pk: 2025-04-30
  session_start();
  /* NOT WORKING *************
  ini_set('session.cookie_secure', 1);
  ini_set('session.cookie_httponly', 1);
  ini_set('session.use_strict_mode', 1);
  ini_set('session.cookie_samesite', 'Lax');

  session_start();
*/
  require("php-bin/connection.php"); // replace include with require
  require("php-bin/supports.php"); // replace include with require

  if (isset($_GET['lang'])) {
    $selectedLang = $_GET['lang'];
    $_SESSION['lang'] = $selectedLang;
  } else {
    if (!isset($_SESSION['lang'])) {
      $_SESSION['lang'] = 'en';
    }
  }

  // Fixed language loading for cloud server compatibility
  $lang = 'en';
  if (isset($_SESSION['lang']) && !empty($_SESSION['lang'])) {
      $lang = $_SESSION['lang'];
  } elseif (isset($_GET['lang']) && !empty($_GET['lang'])) {
      $lang = $_GET['lang'];
  }

  $langFile = "php-bin/lang_" . $lang . ".php";
  if (file_exists($langFile)) {
      $translations = include($langFile);
  } else {
      $translations = array(
          'dashboard' => 'Dashboard',
          'Dashboard' => 'Dashboard'
      );
  }
    $translation = $translations;

  // Authentication check with dynamic detection
 
 // USER DATA
  $userid = '';
  $userid = Userconnect(
        isset($_GET['uid']) ? $_GET['uid'] : '',
        isset($_POST['uid']) ? $_POST['uid'] : '',
        isset($_POST['huid']) ? $_POST['huid'] : '',
        isset($_COOKIE['ephyto_uid']) ? $_COOKIE['ephyto_uid'] : '',
        isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
        $con
    );
  $userinfo = Userdata($userid, $con);
  $loginuser = isset($userinfo['name']) ? $userinfo['name'] : ''; // Name of user
  $uname = isset($userinfo['email']) ? $userinfo['email'] : ''; // Use email as login name

  $usname = isset($userinfo['surname']) ? $userinfo['surname'] : ''; // Surname
  $ufullname = $loginuser."  ".$usname;  // Full name
  $position = isset($userinfo['position']) ? $userinfo['position'] : '';
  $groupid = isset($userinfo['group_id']) && !empty($userinfo['group_id']) ? $userinfo['group_id'] : '1';
  $groupname = GroupName($groupid, $con);
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
?>

<!DOCTYPE html>
<html lang="<?php echo ($lang === 'la') ? 'lo' : 'en'; ?>">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Users Group</title>
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
  <link href="stylecss/dformelement.css" rel="stylesheet">
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
      <a href="main.php" class="logo d-flex align-items-center">
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
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
                <i class="bi bi-person"></i>
                <span><?php echo isset($translation['My Profile']) ? $translation['My Profile'] : 'My Profile'; ?></span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
                <i class="bi bi-gear"></i>
                <span><?php echo isset($translation['Account Settings']) ? $translation['Account Settings'] : 'Account Settings'; ?></span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <i class="bi bi-question-circle"></i>
                <span><?php echo isset($translation['Need Help?']) ? $translation['Need Help?'] : 'Need Help?'; ?></span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="index.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
                <i class="bi bi-box-arrow-right"></i>
                <span><?php echo isset($translation['Sign Out']) ? $translation['Sign Out'] : 'Sign Out'; ?></span>
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
          <span><?php echo isset($translation['Dashboard']) ? $translation['Dashboard'] : 'Dashboard'; ?></span>
        </a>
      </li><!-- End Dashboard Nav -->
      <li class="nav-item">
        <a class="nav-link" href="transaction.php?part=application&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
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
        <a class="nav-link collapsed" href="entity.php?entity=export&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" >
          <i class="bi bi-box-arrow-up-right"></i>
          <span><?php echo isset($translation['Export entity']) ? $translation['Export entity'] : 'Export entity'; ?></span>
        </a>
      </li><!-- End Export Entity Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=import&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" >
          <i class="bi bi-box-arrow-in-down" style="font-size: 1.2rem;"></i>
          <span><?php echo isset($translation['Import entity']) ? $translation['Import entity'] : 'Import entity'; ?></span>
        </a>
      </li><!-- End Import Entity Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#M-masterdata-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span><?php echo isset($translation['Master data']) ? $translation['Master data'] : 'Master data'; ?></span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="M-masterdata-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="masterdata.php?part=approvers&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translation['Approvers']) ? $translation['Approvers'] : 'Approvers'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=conveyance&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translation['Conveyance']) ? $translation['Conveyance'] : 'Conveyance'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=countries&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translation['Countries']) ? $translation['Countries'] : 'Countries'; ?></span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span><?php echo isset($translation['Districts']) ? $translation['Districts'] : 'Districts'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=entitytype&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translation['Entity Type']) ? $translation['Entity Type'] : 'Entity Type'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=inspectionmethod&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translation['Inspection method']) ? $translation['Inspection method'] : 'Inspection method'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=locations&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translation['Locations']) ? $translation['Locations'] : 'Locations'; ?></span>
            </a>
          </li>
          <!--
          <li>
            <a href="masterdata.php?part=modules&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translation['Module List']) ? $translation['Module List'] : 'Module List'; ?></span>
            </a>
          </li>
  -->
          <li>
            <a href="masterdata.php?part=product&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translation['Product']) ? $translation['Product'] : 'Product'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=provinces&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translation['Provinces']) ? $translation['Provinces'] : 'Provinces'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=treatmentmethod&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translation['Treatment Method']) ? $translation['Treatment Method'] : 'Treatment Method'; ?></span>
            </a>
          </li>
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

      <li class="nav-heading"><?php echo isset($translation["USERS MANAGEMENT"]) ? $translation["USERS MANAGEMENT"] : "Users' Management"; ?></li>
        
      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-person"></i>
          <span><?php echo isset($translation['Profile']) ? $translation['Profile'] : 'Profile'; ?></span>
        </a>
      </li><!-- End Profile Page Nav -->
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=ugroup&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-people"></i>
          <span><?php echo isset($translation['Users group']) ? $translation['Users group'] : 'Users group'; ?></span>
        </a>
      </li> <!-- End Users group -->
      
       <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=upermits&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-shield-lock"></i>
          <span><?php echo isset($translation['Group permits']) ? $translation['Group permits'] : 'Group permits'; ?></span>
        </a>
      </li> <!-- End User Group permit -->
      
      <li class="nav-item">
        <a class="nav-link<?php echo (isset($_GET['part']) && $_GET['part'] === 'userslist') ? ' active' : ''; ?>" href="users.php?part=userslist&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-person-plus"></i><span><?php echo isset($translation['Users']) ? $translation['Users'] : 'Users'; ?></span>
        </a>
      </li>  <!-- End Users Nav -->
     
    </ul>
  </aside><!-- End Sidebar-->
   
  <main id="main" class="main">
   
    <!-- ======= *************** User Groups ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='ugroup'){
    ?>
 <section class="section">
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1><?php echo isset($translation["Users group"]) ? $translation["Users group"] : "Users Group"; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translation["Home"]) ? $translation["Home"] : "Home"; ?></a></li>
          <li class="breadcrumb-item"><?php echo isset($translation["Users group"]) ? $translation["Users group"] : "Users Group"; ?></li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addGroupModal" data-gid="new">
          <i class="bi bi-plus-circle"></i> <?php echo isset($translation["Add New Group"]) ? $translation["Add New Group"] : "Add New Group"; ?>
        </button>
      </div>
    </div><!-- End Page Title - Users Group -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo isset($translation["Users group"]) ? $translation["Users group"] : "Users Group"; ?></h5>
              <p><?php echo isset($translation["ePhytosanitary - Department of Agriculture, MAF"]) ? $translation["ePhytosanitary - Department of Agriculture, MAF"] : "ePhytosanitary by Department of Agriculture, MAF - List of Users Group"; ?></p>

              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts" >
                <thead>
                  <tr>
                   <th><?php echo isset($translation["No"]) ? $translation["No"] : "<b>N</b>o"; ?></th>
                   <th><?php echo isset($translation["Group Name"]) ? $translation["Group Name"] : "Group Name"; ?></th>
                   <th><?php echo isset($translation["Description"]) ? $translation["Description"] : "Description"; ?></th>
                   <th><?php echo isset($translation["Status"]) ? $translation["Status"] : "Status"; ?></th>
                   <th><?php echo isset($translation["Edit"]) ? $translation["Edit"] : "Edit"; ?></th>
                   <th><?php echo isset($translation["Delete"]) ? $translation["Delete"] : "Delete"; ?></th>
                 </tr>
                </thead>
                <tbody>
                  <?php Grouplist($con); ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>
        </div>
      </div>
  </section>

    <!-- == Modal form == -->
      <div class="modal fade" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addGroupModalLabel"><?php echo isset($translation["Add New Group"]) ? $translation["Add New Group"] : "Add New Group"; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for gid -->
                <input type="hidden" id="groupId" name="groupId">
                <div class="mb-3">
                  <label for="groupName" class="form-label"><?php echo isset($translation["Group Name"]) ? $translation["Group Name"] : "Group Name"; ?></label>
                  <input type="text" class="form-control" id="groupName" name="groupName" required>
                </div>
                <div class="mb-3">
                  <label for="groupDescription" class="form-label"><?php echo isset($translation["Description"]) ? $translation["Description"] : "Description"; ?></label>
                  <textarea class="form-control" id="groupDescription" name="groupDescription" rows="3" required></textarea>
                </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo isset($translation["Close"]) ? $translation["Close"] : "Close"; ?></button>
              <button type="submit" id="submitGroup" name="submitGroup" class="btn btn-success"><?php echo isset($translation["Submit"]) ? $translation["Submit"] : "Submit"; ?></button>
            </div>
            </form>
          </div>
        </div>
      </div>
    <!-- End of Modal -->
    
     <?php
      //1.) Add new group: Modal form submit
      if (isset($_POST['submitGroup']) && $_POST['submitGroup'] === 'Submit') {
        $groupName = pg_escape_string($con, $_POST['groupName']);
        $groupDescription = pg_escape_string($con, $_POST['groupDescription']);
        $groupInsertResult = Groupnew($groupName,$groupDescription,$con); // List of Groups
        if ($groupInsertResult !== "yes") {
          $redirectUrl = 'users.php?part=ugroup&uid=' . urlencode($userid) . '&lang=' . urlencode($lang);
          echo "<script>window.location.href = '" . $redirectUrl . "';</script>";
          exit();
        }
      }
      //2.) Delete group:  link in Grouplist function in supports.php
      if (isset($_GET['ug']) && isset($_GET['ugid'])) { 
        $groupid = $_GET['ugid'];
        Groupdelete($groupid,$con); // Delete group and back to list
      }

      //3.) Updat/Edit group:  link in Grouplist function in supports.php
      if (isset($_GET['ug']) && isset($_GET['ugid'])) { 
        $groupid = $_GET['ugid'];
        Groupedit($groupid,$con); // Edit group and back to list
      }
      //4.) Update group: Modal form submit
      if(isset($_POST['submitGroup']) && $_POST['submitGroup'] === 'Update') {
        $groupId = pg_escape_string($con,$_POST['groupId']);
        $groupName = pg_escape_string($con, $_POST['groupName']);
        $groupDescription = pg_escape_string($con, $_POST['groupDescription']);

        echo "<script>alert('Group ID for update: " . $groupId . "');</script>"; // Debugging line
        
        Groupupdate($groupId,$groupName,$groupDescription,$con); // Update group and back to list
      }
    ?>
   
   <?php } ?> <!-- ********* End of if part=ugroup ********* -->
    <!-- ======= Users List ======= -->
     <?php
     if(isset($_GET['part']) && $_GET['part']==='userslist'){
    ?>
   <section class="section">
      <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1><?php echo isset($translation["Users"]) ? $translation["Users"] : "Users"; ?></h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translation["Home"]) ? $translation["Home"] : "Home"; ?></a></li>
             <li class="breadcrumb-item"><?php echo isset($translation["Users List"]) ? $translation["Users List"] : "Users List"; ?></li>
          </ol>
          </nav>
        </div>
        <div>
          <a href="users.php?frm=newuser&lang=<?php echo $lang; ?>" class="btn btn-success btn-sm" role="button">
            <i class="bi bi-plus-circle"></i> <?php echo isset($translation["Add New User"]) ? $translation["Add New User"] : "Add New User"; ?>
          </a>
        </div>
      </div><!-- End Page Title - Users list -->
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo isset($translation["Users List"]) ? $translation["Users List"] : "Users List"; ?></h5>
              <p><?php echo isset($translation["ePhytosanitary - Department of Agriculture, MAF"]) ? $translation["ePhytosanitary - Department of Agriculture, MAF"] : "ePhytosanitary - Department of Agriculture, MAF"; ?></p>

              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts" >
                <thead>
                  <tr>
                   <th>
                      <?php echo isset($translation["No"]) ? $translation["No"] : "No"; ?>
                    </th>
                    <th>
                      <?php echo isset($translation["Name"]) ? $translation["Name"] : "Name"; ?>
                    </th>
                    <th><?php echo isset($translation["Phone"]) ? $translation["Phone"] : "Phone"; ?></th>
                    <th><?php echo isset($translation["Email"]) ? $translation["Email"] : "Email"; ?></th>
                    <th><?php echo isset($translation["Last login"]) ? $translation["Last login"] : "Last login"; ?></th>
                    <th><?php echo isset($translation["Group"]) ? $translation["Group"] : "Group"; ?></th>
                    <th><?php echo isset($translation["Location"]) ? $translation["Location"] : "Location"; ?></th>
                    <th><?php echo isset($translation["Status"]) ? $translation["Status"] : "Status"; ?></th>
                    <th><?php echo isset($translation["Edit"]) ? $translation["Edit"] : "Edit"; ?></th>
                    <th><?php echo isset($translation["Delete"]) ? $translation["Delete"] : "Delete"; ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php Userlist($con, $userid, $lang); ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>
        </div>
      </div>
    </section>
    <?php } ?> <!-- ********* End of if part=userslist ********* -->
   
    <!-- =======**************** Users Add/Updates - Form ************* ======= -->
    <?php
     // USERS DELETE ********
      
      if (isset($_GET['frm']) && $_GET['frm'] === 'userdelete') {
        if (isset($_GET['uidup']) && !empty($_GET['uidup']) && is_numeric($_GET['uidup'])) {
            $duid = htmlspecialchars($_GET['uidup']); // Sanitize the input
            //Deleteuser($duid, $con); // Delete user and back to list
            // TESTING
            echo "<script>alert('User ID to delete: " . $duid . "');</script>"; // Debugging line
            Deleteuser($duid,$con,$userid);
            //echo "<script>window.location.href = 'users.php?part=userslist';</script>"; // Redirect to users list
        }
      }
     // USERS UPDATE AND ADD new user form - SUBMIT ******
     if ($_SERVER["REQUEST_METHOD"] == "POST" && $_GET["part"]==='userslist') {  // Form submission for BOTH NEW and UPDATE
          $huid = isset($_POST['huid']) ? htmlspecialchars($_POST['huid']) : ''; // Sanitize the input
          $name = htmlspecialchars($_POST['name']); // Sanitize the input
          $surname = htmlspecialchars($_POST['surname']);
          $sex = htmlspecialchars($_POST['gender']);
          $psw = htmlspecialchars($_POST['password']);
          $position = htmlspecialchars($_POST['position']);
          $unit = htmlspecialchars($_POST['workunit']);
          $phone = htmlspecialchars($_POST['phone']);
          $email = htmlspecialchars($_POST['email']);
          $groupid = htmlspecialchars($_POST['usergroup']);
          $admingroup = isset($_POST['admingroup']) ? htmlspecialchars($_POST['admingroup']) : 'no'; // Admin group
          $location = htmlspecialchars($_POST['location']);
          
        if(isset($_POST['btnsubuser']) && $_POST['btnsubuser'] === 'submit') { // New user submission
            $sbupdate = 'submit'; // Set submit button value
            Addusers($name, $surname, $sex, $psw, $position, $unit, $phone, $email, $groupid,$admingroup, $location, $con, $userid);
           // echo "<script>alert('Submit button clicked- New User');</script>"; // Debugging line

        } elseif (isset($_POST['btnsubuser']) && $_POST['btnsubuser'] === 'update') { // Update user submission
            if (!empty($huid) && is_numeric($huid)) {
                $sbupdate = 'update'; // Set update button value
                UpdateuserSubmit($huid, $name, $surname, $sex, $psw, $position, $unit, $phone, $email, $groupid,$admingroup, $location, $con, $userid);
            } else {
                echo "<script>alert('Invalid user ID for update.');</script>";
            }
        }    

     }
     // FIRST LINK - add or update user 
 if((isset($_GET['frm']) && ($_GET['frm']==='userupdate' || $_GET['frm']==='newuser'))){
       // Update user - Check multiple sources for uid (dynamic detection)
       $uid_update = '';
       
       // Try to get uid from GET parameter (direct link)
       if(isset($_GET['uidup']) && !empty($_GET['uidup']) && is_numeric($_GET['uidup'])){
            $uid_update = htmlspecialchars($_GET['uidup']); // Sanitize the input
       }
       // Try to get uid from POST parameter (form submission)  
       elseif(isset($_POST['huid']) && !empty($_POST['huid']) && is_numeric($_POST['huid'])){
            $uid_update = htmlspecialchars($_POST['huid']); // Sanitize the input
       }
       
       if(!empty($uid_update)){
            $sbupdate = 'update';
            $uid = $uid_update; // Set uid for form usage
            /*
            echo "<script>
                   var uid = '" . $uid . "';
                    document.getElementById('huid').value = uid; 
                  </script>"; 
            */
            list($name, $surname, $sex, $psw, $position, $unit, $phone, $email, $groupid, $admingroup, $location, $status) = Updateuser_values($uid_update,$con); // Get user data for update
          
            // Decrypt password - PK: 2024-06-10
            // $key = '1234567890abcdef'; // Make xampp happy.
           // $iv = 'abcdef1234567890';
           // $psw = openssl_decrypt($psw, 'AES-128-CTR', $key, 0, $iv); // Decrypt the password

        // New user
        } else { 
            $uid_update = '';
            // Initialize variables for new user
            $name = $surname = $sex = $psw = $position = $unit = $phone = $email = $groupid = $admingroup = $location = $status = '';
        } // end of if uid
      
    ?>
      <div class="pagetitle">
        <h1><?php echo isset($translation["ADD/UPDATE USERS"]) ? $translation["ADD/UPDATE USERS"] : "Add/Update Users"; ?></h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translation["Home"]) ? $translation["Home"] : "Home"; ?></a></li>
            <li class="breadcrumb-item"><?php echo isset($translation["Users"]) ? $translation["Users"] : "Users"; ?></li>
          </ol>
        </nav>
      </div>
      <section class="section">
      <div class="row">
        <div class="col-lg-6" style="width: 80%;"> <!-- Pk-Update: style -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo (isset($_GET['frm']) && $_GET['frm'] == 'userupdate') ? (isset($translation["USERS UPDATE"]) ? $translation["USERS UPDATE"] : "Users Update") : (isset($translation["NEW USERS"]) ? $translation["NEW USERS"] : "New Users"); ?></h5>
              <!-- Users Form -->
              <form action="users.php?part=userslist&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" method="POST">
                <!-- Hidden input for uid : User ID -->
                <input type="hidden" id="huid" name="huid" value="<?php echo isset($uid) ? $uid : ''; ?>">
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translation["Name"]) ? $translation["Name"] : "Name"; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translation["Surname"]) ? $translation["Surname"] : "Surname"; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="surname" class="form-control" value="<?php echo isset($surname) ? $surname : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translation["Gender"]) ? $translation["Gender"] : "Gender"; ?></label>
                  <div class="col-sm-5">
                    <select class="form-select" name="gender" aria-label="Default select example">
                      <option selected>*** Please select one ***</option>
                      <option value="m" <?php echo (isset($sex) && $sex == 'm') ? 'selected' : ''; ?>>ຊາຍ</option>
                      <option value="f" <?php echo (isset($sex) && $sex == 'f') ? 'selected' : ''; ?>>ຍິງ</option>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputPassword" class="col-sm-2 col-form-label"><?php echo isset($translation["Password"]) ? $translation["Password"] : "Password"; ?></label>
                  <div class="col-sm-5">
                    <input type="password" name="password" class="form-control" value="<?php echo isset($psw) ? htmlspecialchars($psw) : ''; ?>">
                  </div>
                </div>
                
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translation["Phone"]) ? $translation["Phone"] : "Phone"; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="phone" class="form-control" value="<?php echo isset($phone) ? $phone : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputEmail" class="col-sm-2 col-form-label"><?php echo isset($translation["Email"]) ? $translation["Email"] : "Email"; ?></label>
                  <div class="col-sm-10">
                    <input type="email" name="email" class="form-control" value="<?php echo isset($email) ? $email : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translation["Position"]) ? $translation["Position"] : "Position"; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="position" class="form-control me-2" value="<?php echo isset($position) ? $position : ''; ?>"><span id="usermiss"><?php if(!empty($cfuser)){ echo "Already exists";} ?></span>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputPassword" class="col-sm-2 col-form-label"><?php echo isset($translation["Work Unit"]) ? $translation["Work Unit"] : "Work Unit"; ?></label>
                  <div class="col-sm-10">
                    <textarea class="form-control" name="workunit" style="height: 100px"><?php echo isset($unit) ? $unit : ''; ?></textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translation["Location"]) ? $translation["Location"] : "Location"; ?></label>
                  <div class="col-sm-10">
                    <select class="form-select" name="location" aria-label="Default select example">
                     <option value="">*** Please select one ***</option>
                      <?php SelectLocation($location, $con); ?>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translation["Group"]) ? $translation["Group"] : "Group"; ?></label>
                  <div class="col-sm-5">
                    <select class="form-select" name="usergroup" aria-label="Default select example">
                      <option value="">*** Please select one ***</option>
                      <?php SelectUsergroup($groupid, $con); ?>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translation["Admin Group"]) ? $translation["Admin Group"] : "Admin Group"; ?></label>
                  <div class="col-sm-5">
                    <select class="form-select" name="admingroup" aria-label="Default select example">
                      <option selected>*** Please select one ***</option>
                      <option value="yes" <?php echo (isset($admingroup) && $admingroup == 'yes') ? 'selected' : ''; ?>>Yes</option>
                      <option value="no" <?php echo (isset($admingroup) && $admingroup == 'no') ? 'selected' : ''; ?>>No</option>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">&nbsp;</label> 
                  <div class="col-sm-10">
                    <button type="submit" name="btnsubuser" class="btn btn-primary" value="<?php echo isset($sbupdate) ? 'update' : 'submit'; ?>"><?php echo isset($sbupdate) ? 'Update' : 'Submit'; ?></button>
                  </div>
                </div>
              <!-- PHP testing - PK -->
                    <div class="col-12">
                      <?php if (!empty($message)): ?>
                      <div class="alert alert-info" role="alert">
                        <?php echo $message; ?>
                      </div>
                      <?php endif; ?>
                    </div>
              </form><!-- End General Form Elements -->

            </div>
          </div>

        </div>

      </div>
    </section>
      <!-- Set focus on the name input field when the page loads -->
       <script>
          document.addEventListener('DOMContentLoaded', function() {
          var nameInput = document.getElementById('name');
            if (nameInput) {
              nameInput.focus();
            }
          });
        </script>
     <?php } ?>
    <!-- End Users Updates - Form -->

     <!-- ======= *************** Group permission ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='upermits'){
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1><?php echo isset($translation["Group Permits"]) ? $translation["Group Permits"] : "Group Permits"; ?></h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translation["Home"]) ? $translation["Home"] : "Home"; ?></a></li>
            <li class="breadcrumb-item"><?php echo isset($translation["Group Permits"]) ? $translation["Group Permits"] : "Group Permits"; ?></li>
          </ol>
        </nav>
      </div>
      <div>
        <a href='users.php?part=upermits&apermit=new&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>' class='btn btn-primary btn-sm'><i class="bi bi-plus-circle"></i><?php echo isset($translation["Add New Permit"]) ? $translation["Add New Permit"] : "Add New Permit"; ?></a>
      </div>
    </div><!-- End Page Title -->

    <!-- == Modal form - Group permits == --> 
     <div class="modal fade" id="addPermitModal" tabindex="-1" aria-labelledby="addPermitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addPermitModalLabel"><b><?php echo isset($translation["Add New Group Permit"]) ? $translation["Add New Group Permit"] : "Add New Group Permit"; ?></b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
               
                <input type="hidden" id="permitid" name="permitid"> 
                <input type="hidden" id="permitstatus" name="permitstatus"> 
                <div class="mb-3">
                  <label class="col-sm-3 col-form-label"><?php echo isset($translation["Users group"]) ? $translation["Users group"] : "Users group"; ?></label>
                   <select class="form-select" id="grouppermitid" name="grouppermitid" aria-label="Default select example">
                     <option value=""><?php echo isset($translation["Please select one"]) ? $translation["Please select one"] : "*** Please select one ***"; ?></option>
                      <?php SelectUsergroup($groupPermitId, $con); ?>
                    </select> 
                </div>
                <div class="mb-3">
                  <label class="col-sm-3 col-form-label"><?php echo isset($translation["Module"]) ? $translation["Module"] : "Module"; ?></label>
                   <select class="form-select" id="moduleid" name="moduleid" aria-label="Default select example">
                     <option value=""><?php echo isset($translation["Please select one"]) ? $translation["Please select one"] : "*** Please select one ***"; ?></option>
                      <?php SelectModules($modulePermitId, $con); ?>
                    </select>  
                </div>
                <div class="mb-3">
                  <label class="form-label d-block"><?php echo isset($translation["Permissions"]) ? $translation["Permissions"] : "Permissions"; ?></label>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="permitRead" name="permitRead" value="1">
                    <label class="form-check-label" for="permitRead"><?php echo isset($translation["Read"]) ? $translation["Read"] : "Read"; ?></label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="permitAdd" name="permitAdd" value="1">
                    <label class="form-check-label" for="permitAdd"><?php echo isset($translation["Add"]) ? $translation["Add"] : "Add"; ?></label>
                  </div>
                  <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="permitUpdate" name="permitUpdate" value="1">
                      <label class="form-check-label" for="permitUpdate"><?php echo isset($translation["Update"]) ? $translation["Update"] : "Update"; ?></label>
                  </div>
                  <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="permitDelete" name="permitDelete" value="1">
                      <label class="form-check-label" for="permitDelete"><?php echo isset($translation["Delete"]) ? $translation["Delete"] : "Delete"; ?></label>
                  </div>
                </div>
              </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo isset($translation["Close"]) ? $translation["Close"] : "Close"; ?></button>
              <button type="submit" id="submitgroupPermit" name="submitgroupPermit" class="btn btn-success"><?php echo isset($translation["Submit"]) ? $translation["Submit"] : "Submit"; ?></button>
            </div>
            </form>
          </div>
        </div>
      </div>
    <!-- End of Modal -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo isset($translation["Group Permits"]) ? $translation["Group Permits"] : "Group Permits"; ?></h5>
              <p><?php echo isset($translation["ePhytosanitary - Department of Agriculture, MAF"]) ? $translation["ePhytosanitary - Department of Agriculture, MAF"] : "ePhytosanitary - Department of Agriculture, MAF"; ?></p>

              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts" >
                <thead>
                  <tr>
                   <th><?php echo isset($translation["No"]) ? $translation["No"] : "No"; ?></th>
                   <th><?php echo isset($translation["Group Name"]) ? $translation["Group Name"] : "Group Name"; ?></th>
                   <th><?php echo isset($translation["Module"]) ? $translation["Module"] : "Module"; ?></th>
                   <th><?php echo isset($translation["Read"]) ? $translation["Read"] : "Read"; ?></th>
                   <th><?php echo isset($translation["Write"]) ? $translation["Write"] : "Write"; ?></th>
                   <th><?php echo isset($translation["Edit"]) ? $translation["Edit"] : "Edit"; ?></th>
                   <th><?php echo isset($translation["Delete"]) ? $translation["Delete"] : "Delete"; ?></th>
                   <th><?php echo isset($translation["Update"]) ? $translation["Update"] : "Update"; ?></th>
                   <th><?php echo isset($translation["Remove"]) ? $translation["Remove"] : "Remove"; ?></th>
                 </tr>
                </thead>
                <tbody>
                  <?php GroupPermitList($con, $userid, $lang); ?> 
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>
        </div>
      </div>
     </section>
    <?php } ?> <!-- ********* End of if part=upermits ********* -->
<!-- Processing Group permits form submission -->
    <?php
     // Add new Group permit ************* ModalPermitAdd()
     if(isset($_GET['part']) && $_GET['part'] === 'upermits' && isset($_GET['apermit']) && !empty($_GET['apermit'])){
          // OPEN MODAL FORM FOR ADDING NEW
          echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    ModalPermitAdd();  
                    });
               </script>";
     }
      // GROUP PERMIT FOR UPDATE: link in GroupPermitList function in supports.php
      if (isset($_GET['part']) && $_GET['part'] === 'upermits' && isset($_GET['epermit']) && !empty($_GET['epermit'])) {
          if(isset($_GET['id']) && !empty($_GET['id'])){
            $pmtid = htmlspecialchars($_GET['id']);
            $pstatus = htmlspecialchars($_GET['epermit']); // value: edit

            list($groupPermitId, $modulePermitId, $permitRead, $permitAdd, $permitUpdate, $permitDelete) = GrouppermitVariables($pmtid, $con); // Get group permit data for update
            // Add variables to Modal form when it pops up for update
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                      ModalPermitUpdate('$pmtid', '$pstatus' ,'$groupPermitId','$modulePermitId','$permitRead','$permitAdd','$permitUpdate','$permitDelete');
                    });
                  </script>";
          }   
      }

      // Modal form submit - GROUP PERMIT
      if(isset($_POST['submitgroupPermit'])) {  // ADD/UPDATE Module
        // Process the form submission for adding/updating module
        $gpermit = $_POST['permitid']; //  - Hidden input for ID  
        $ptstatus = $_POST['permitstatus']; // Hidden input for apermit and epermit
        $groupPermit = $_POST['grouppermitid'];
        $modulePermit = $_POST['moduleid'];
        $permitRead = isset($_POST['permitRead']) ? 'yes' : 'no'; // Checkbox for Read
        $permitAdd = isset($_POST['permitAdd']) ? 'yes' : 'no'; // Checkbox for Add
        $permitUpdate = isset($_POST['permitUpdate']) ? 'yes' : 'no'; // Checkbox for Update
        $permitDelete = isset($_POST['permitDelete']) ? 'yes' : 'no'; // Checkbox for Delete
        // Add new permit id=$id&editpermit=yes
          // Add new permit
        if(!empty($ptstatus) && $ptstatus === 'new') { // Check if it's a new module or empty    
            AddGroupPermit($groupPermit, $modulePermit, $permitRead,$permitAdd,$permitUpdate, $permitDelete, $con); // Function to add new module    
        } 
         // Edit permit
        if(!empty($ptstatus) && $ptstatus ==='edit'){
           UpdateGroupPermit($gpermit, $groupPermit, $modulePermit, $permitRead, $permitAdd, $permitUpdate, $permitDelete, $con);
        }

      } // End of if submitModule

      // DELET PERMIT ****************upermits&id=$id&dpermit=del
      if(isset($_GET['part']) && $_GET['part'] === 'upermits' && isset($_GET['dpermit']) && !empty($_GET['dpermit']))
        {
          $pmtid_del = $_GET['id'];
          if(!empty($pmtid_del)){
            DeleteGroupPermit($pmtid_del,$con);
          }
       }
    ?>

  </main><!-- End #main -->
 
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
   /* ----===== Users and Usersgroup =====----  */    
   // MODAL FORM - UPDATE ON USERGROUP:  link in Grouplist function in supports.php
    // Add an event listener for when the modal is shown
    var addGroupModal = document.getElementById('addGroupModal');
        addGroupModal.addEventListener('show.bs.modal', function (event) {
        // Get the button that triggered the modal
        var button = event.relatedTarget;

        // Extract the group ID from the data-gid attribute - MODAL FORM
        var gId = button.getAttribute('data-gid');
        var gName = button.getAttribute('data-gname');
        var gDesc = button.getAttribute('data-gdesc'); 
        // Populate the modal with the group ID (if needed)
        var modalTitle = addGroupModal.querySelector('#addGroupModalLabel');
        var groupId = addGroupModal.querySelector('#groupId');
        var groupNameInput = addGroupModal.querySelector('#groupName');
        var groupDesc = addGroupModal.querySelector('#groupDescription');
        var btnsubmit = addGroupModal.querySelector('#submitGroup');
        
        if(gId !== 'new') { // Update group
            groupId.value = gId; // Assign the group ID to the hidden input field
           // alert('Group ID: ' + gId); // Debugging line
            groupNameInput.value = gName || ''; // Assign the data values to input field
            groupDesc.value = gDesc || '';
            btnsubmit.textContent = 'Update'; // Change button value to Update for Updating Usergroup
            btnsubmit.value = 'Update';
            if(modalTitle) modalTitle.textContent = 'Update Users Group';
        } else {
            groupNameInput.value = '';
            groupDesc.value = ''; 
            btnsubmit.textContent = 'Submit'; // Change button value to Submit
            btnsubmit.value = 'Submit';
            if(modalTitle) modalTitle.textContent = 'Add New Group';
        }    
    });

    // Set focus after modal is fully shown - MODAL FORM
        addGroupModal.addEventListener('shown.bs.modal', function () {
          var groupNameInput = addGroupModal.querySelector('#groupName');
        if (groupNameInput) groupNameInput.focus();
        });
    /* ----===== End of Users and Usersgroup =====----  */ 
    // Set focus name input in FORM - Add New User
    var nameInput = document.getElementById('name');
    if (nameInput) {
      nameInput.addEventListener('focus', function() {
        this.select();
      });
    }

    // Permit - addPermitModal *******
  // 1.) Add new permit *************
  function ModalPermitAdd(){
    // Show modal
    var modalPmAdd = document.getElementById('addPermitModal');
    if (modalPmAdd) {
        var modal = new bootstrap.Modal(modalPmAdd);
        modal.show();

        var label = document.getElementById('addPermitModalLabel');
        if (label) label.textContent = 'Add New Permit';

        var pstatus = document.getElementById('permitstatus'); // for new 
        if (pstatus) pstatus.value = 'new';

        var btname = document.getElementById('submitgroupPermit');
        if (btname) btname.textContent = 'Submit';
    }
  }
  // 2.) Update Permit **************
function ModalPermitUpdate(pmit, pstatus, gpmit, mpmit, pread, padd, pupdate, pdelete) {
  // Set modal fields
  var permitIdInput = document.getElementById('permitid'); // hidden input in modal 
  if (permitIdInput) permitIdInput.value = pmit;

  var permitStatusInput = document.getElementById('permitstatus'); // hidden input in modal 
  if (permitStatusInput) permitStatusInput.value = pstatus;

  var groupPermitSelect = document.getElementById('grouppermitid');
  if (groupPermitSelect) groupPermitSelect.value = gpmit;

  var moduleSelect = document.getElementById('moduleid');
  if (moduleSelect) moduleSelect.value = mpmit;

  var permitReadCheckbox = document.getElementById('permitRead');
  if (permitReadCheckbox) permitReadCheckbox.checked = (pread === 'yes');

  var permitAddCheckbox = document.getElementById('permitAdd');
  if (permitAddCheckbox) permitAddCheckbox.checked = (padd === 'yes');

  var permitUpdateCheckbox = document.getElementById('permitUpdate');
  if (permitUpdateCheckbox) permitUpdateCheckbox.checked = (pupdate === 'yes');

  var permitDeleteCheckbox = document.getElementById('permitDelete');
  if (permitDeleteCheckbox) permitDeleteCheckbox.checked = (pdelete === 'yes');

  // Show modal
  var modalElement = document.getElementById('addPermitModal');
  if (modalElement) {
    var modal = new bootstrap.Modal(modalElement);
    modal.show();
    var label = document.getElementById('addPermitModalLabel');
    if (label) label.textContent = 'Update Group Permit';
    var btname = document.getElementById('submitgroupPermit');
    if (btname) btname.textContent = 'Update';
  }
}

</script>
  
</body>

</html>