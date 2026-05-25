<?php
// Language handling (session-first, falls back to GET, then 'en')
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$lang = 'en';
if (isset($_SESSION['lang']) && !empty($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} elseif (isset($_GET['lang']) && !empty($_GET['lang'])) {
    $lang = $_GET['lang'];
}
// Persist and expose for templates
$_SESSION['lang'] = $lang;
$selectedLang = $lang;

// Include translations (fallback to empty array)
$langFile = "php-bin/lang_" . $lang . ".php";
if (file_exists($langFile)) {
    $translations = include($langFile);
} else {
    $translations = array();
}
// connection to database
 require("php-bin/connection.php");
 require("php-bin/supports.php");

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
$groupid = isset($userinfo['group_id']) && !empty($userinfo['group_id']) ? $userinfo['group_id'] : '0';
$groupname = $groupid !== '0' ? GroupName($groupid, $con) : '';

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

// User group ID - get from user data instead of session
$guid = $groupid;

// Check permissions for Import and Export entity module using enhanced user-level permission check
$entityPermissions = UserPermitCheck($userid, 'FRM - ENTITY', $con);
$canReadEntity = $entityPermissions['pread'];
$canAddEntity = $entityPermissions['padd'];
$canUpdateEntity = $entityPermissions['pupdate'];
$canDeleteEntity = $entityPermissions['pdelete'];

// Permission checks for menu items
$masterDataPermit = UserPermitCheck($userid, 'FRM - MASTER DATA', $con);
$userGroupPermit = UserPermitCheck($userid, 'FRM - USERS_PERMIT', $con);
$groupPermitsPermit = UserPermitCheck($userid, 'FRM - USERS_PERMIT', $con);
$usersPermit = UserPermitCheck($userid, 'FRM - USERS_PERMIT', $con);
$modulesPermit = UserPermitCheck($userid, 'FRM - MODULE', $con);

// Set form control attributes based on permissions
$formDisabled = ($canAddEntity || $canUpdateEntity) ? '' : 'disabled';
$formReadonly = ($canAddEntity || $canUpdateEntity) ? '' : 'readonly';
$showSubmitButton = ($canAddEntity || $canUpdateEntity);

// Build main link preserving uid and lang
$mainParams = ['uid' => isset($userid) ? $userid : '', 'lang' => isset($lang) ? $lang : 'en'];
$mainHref = 'main.php?' . http_build_query($mainParams);

?>

<!DOCTYPE html>
<html lang="<?php echo ($lang == 'la') ? 'lo' : 'en'; ?>">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Entity'; ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  
  <!-- Ajax PK -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="jspk/users-validate.js"></script>  

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
  <!--  CSS File- PK -->
  <link href="stylecss/scss.css" rel="stylesheet">
  <link href="stylecss/dformelement.css" rel="stylesheet">

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
                <span>Logout</span>
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
        <a class="nav-link <?php echo (isset($_GET['entity']) && $_GET['entity'] == 'export') ? 'active' : 'collapsed'; ?>" href="entity.php?entity=export&uid=<?php echo $userid; ?>" >
          <i class="bi bi-box-arrow-up-right"></i>
          <span><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Export entity'; ?></span>
        </a>
      </li><!-- End Export Entity Nav -->

      <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['entity']) && $_GET['entity'] == 'import') ? 'active' : 'collapsed'; ?>" href="entity.php?entity=import&uid=<?php echo $userid; ?>" >
          <i class="bi bi-box-arrow-in-down" style="font-size: 1.2rem;"></i>
          <span><?php echo isset($translations['Import entity']) ? $translations['Import entity'] : 'Import entity'; ?></span>
        </a>
      </li><!-- End Import Entity Nav -->

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

       <?php if ($masterDataPermit['pread']): ?>
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
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Entity_type']) ? $translations['Entity_type'] : 'Entity_type'; ?></span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=inspectionmethod&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
              <i class="bi bi-circle"></i><span><?php echo isset($translations['Inspection Method']) ? $translations['Inspection Method'] : 'Inspection Method'; ?></span>
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
        <?php // } // End of Admin group check ?>
        </ul>
      </li>
      <?php endif; ?><!-- End Master Data Nav -->    
      
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

      <li class="nav-heading"><?php echo isset($translations['USERS MANAGEMENT']) ? $translations['USERS MANAGEMENT'] : "Users' Management"; ?></li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php?uid=<?php echo $userid; ?>">
          <i class="bi bi-person"></i>
          <span><?php echo isset($translations['Profile']) ? $translations['Profile'] : 'Profile'; ?></span>
        </a>
      </li><!-- End Profile Page Nav -->
      <?php if ($userGroupPermit['pread']): ?>
      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=ugroup&uid=<?php echo $userid; ?>">
          <i class="bi bi-people"></i>
          <span><?php echo isset($translations['Users group']) ? $translations['Users group'] : 'Users group'; ?></span>
        </a>
      </li>
      <?php endif; ?><!-- End Users group -->

      <?php if ($groupPermitsPermit['pread']): ?>
       <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=upermits&uid=<?php echo $userid; ?>">
          <i class="bi bi-shield-lock"></i>
          <span><?php echo isset($translations['Group permits']) ? $translations['Group permits'] : 'Group permits'; ?></span>
        </a>
      </li>
      <?php endif; ?><!-- End Permission: User Group and Module -->

      <?php if ($usersPermit['pread']): ?>
      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=userslist&uid=<?php echo $userid; ?>">
          <i class="bi bi-person-plus"></i>
          <span><?php echo isset($translations['Users']) ? $translations['Users'] : 'Users'; ?></span>
        </a>
      </li>
      <?php endif; ?>
      <?php if ($modulesPermit['pread']): ?>
      <li class="nav-item"> <!--*********** Module *****************-->
        <a class="nav-link collapsed" href="users.php?part=modulelist&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>">
          <i class="bi bi-grid-3x3-gap"></i><span><?php echo isset($translations['Modules']) ? $translations['Modules'] : 'Modules'; ?></span>
        </a>
      </li>
      <?php endif; ?>
      <!-- pk**: End of User Admin-->

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
   <?php 
     // EXPORT ENTITY/COMPANY  *******************
    if(isset($_GET['entity']) && $_GET['entity'] == 'export') {
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
          <a href="entity.php?frm=newEntity_export&uid=<?php echo $userid; ?>" class="btn btn-success btn-sm" role="button">
            <i class="bi bi-plus-circle"></i> <?php echo isset($translations['Add New']) ? $translations['Add New'] : 'Add New'; ?> 
          </a>
        </div>
      </div><!-- End Page Title - Users list -->
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo isset($translations['Export entity list']) ? $translations['Export entity list'] : 'Export entity list'; ?></h5>
              <p><?php echo isset($translations['ePhytosanitary - Department of Agriculture, MAF']) ? $translations['ePhytosanitary - Department of Agriculture, MAF'] : 'ePhytosanitary - Department of Agriculture, MAF'; ?></p>

              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts" >
                <thead>
                  <tr>
                   <th>
                      <?php echo isset($translations['No']) ? $translations['No'] : '<b>N</b>o'; ?>
                    </th>
                    <th>
                      <?php echo isset($translations['Name']) ? $translations['Name'] : '<b>N</b>ame'; ?>
                    </th>
                    <th><?php echo isset($translations['Address']) ? $translations['Address'] : 'Address'; ?></th>
                    <th style="white-space: nowrap;"><?php echo isset($translations['Contact person']) ? $translations['Contact person'] : 'Contact person'; ?></th>
                    <th><?php echo isset($translations['Phone']) ? $translations['Phone'] : 'Phone'; ?></th>
                    <th><?php echo isset($translations['Email']) ? $translations['Email'] : 'Email'; ?></th>
                    <th><?php echo isset($translations['Province']) ? $translations['Province'] : 'Province'; ?></th>
                    <th><?php echo isset($translations['Edit']) ? $translations['Edit'] : 'Edit'; ?></th>
                    <th><?php echo isset($translations['Application']) ? $translations['Application'] : 'Application'; ?></th>
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
  <!-- =======**************** Export entity Add/Updates - Form ************* ======= -->
   <?php
       // Handle form submission for Export Entity/Company
       if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['btnsubEntityExport'])) {
           echo "<!-- Debug: Form submitted with method POST -->";
           echo "<!-- Debug: btnsubEntityExport value: " . $_POST['btnsubEntityExport'] . " -->";
           
           $sbupdate = ''; // Default to submit action
           // $uid = $_POST['huid'];
           $bustype = $_POST['business_type'];
           $enttype = $_POST['entity_type'];
           $name = $_POST['name'];
            $address = $_POST['address'];
            $zip = $_POST['zipcode'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $pid = $_POST['province'];
            $did = $_POST['district'];
            $contact_person = $_POST['contact_person'];
            // Null for date= "1990-01-01"
            $datenull = "'1990-01-01'";
            $isregister = isset($_POST['isregister']) ? 1 : 0;
            $regdate1 = empty($_POST['register_date_from']) ? $datenull : "'" . $_POST['register_date_from'] . "'";
            $regdate2 = empty($_POST['register_date_to']) ? $datenull : "'" . $_POST['register_date_to'] . "'";

            $checkreg = isset($_POST['checklist_registered']) ? 1 : 0;
            $gap = isset($_POST['gap']) ? 1 : 0;
            $license_export = isset($_POST['license_export']) ? 1 : 0; 
            $created_date = date('Y-m-d H:i:s');    

       // Insert or update logic here
        if($_POST['btnsubEntityExport'] === 'update') {
          // Update existing entity
          echo "<!-- Debug: Entering update branch -->";
          $sbupdate = 'update';
          $entity_id = $_POST['entity_id']; // Get entity ID from hidden form field
          echo "<!-- Debug: Entity ID from POST: $entity_id -->";
          echo "<!-- Debug: UserID: $userid -->";
          UpdateEntityExport($entity_id, $bustype, $enttype, $name, $address, $zip, $pid, $did, $phone, $email, $contact_person, $isregister, $regdate1, $regdate2, $checkreg, $gap, $license_export, $userid, $con);
        } else if($_POST['btnsubEntityExport'] === 'submit') {
          // Add new entity
          echo "<!-- Debug: Entering submit branch -->";
          AddEntityExport($bustype, $enttype, $name, $address, $zip, $pid, $did, $phone, $email, $contact_person, $isregister, $regdate1, $regdate2, $checkreg, $gap, $license_export, $created_date, $guid, $con);
        }
     } 
   ?>
   <!-- Open export entry form  -->
   <?php
   // EXPORT ENTITY/COMPANY-FORM  *******************
     if (isset($_GET['frm']) && ($_GET['frm'] === 'newEntity_export' || $_GET['frm'] === 'editEntity_export')) {
         echo "<script>document.title = '" . (isset(
           $translations['Export entity']) ? $translations['Export entity'] . " / Company Form" : 'Export Entity/Company Form') . "';</script>";
       if(isset($_GET['id'])) { 
         $sbupdate = 'update';
         // Fetch the entity data for editing
         // Assuming you have a function GetEntityExport that retrieves the entity data by ID 
         $entity_id = $_GET['id'];
         $entityData = EntityExportInfo($entity_id, $con);
         if ($entityData) {
           // Extract data from the result
          
           $bustype = $entityData['business_type'];
           $enttype = $entityData['entity_type'];
           $name = $entityData['title'];
           $address = $entityData['address'];
           $zip = $entityData['zipcode'];
           $phone = $entityData['phone'];
           $email = $entityData['email'];
           $pid = $entityData['province'];
           $did = $entityData['district'];
           $contact_person = $entityData['contact_name'];
           $isregister = (bool)$entityData['registered'];
           $regdate1 = empty($entityData['registered_date_from']) ? '' : date('Y-m-d', strtotime($entityData['registered_date_from']));
           $regdate2 = empty($entityData['registered_date_to']) ? '' : date('Y-m-d', strtotime($entityData['registered_date_to']));

           $checklist_registered = (bool)$entityData['check_list_registered'];
           $gap = (bool)$entityData['gap'];
           $license_export = (bool)$entityData['license_export'];
         }
       }
    ?>
    <div class="pagetitle">
      <h1><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Export Entity'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item"><a href="entity.php?entity=export"><?php echo isset($translations['Export entity list']) ? $translations['Export entity list'] : 'Export entity-List'; ?></a></li>
          <li class="breadcrumb-item active"><?php echo isset($translations['Export entity']) ? $translations['Export entity'] : 'Export Entity'; ?></li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo isset($translations['Entity Data Form']) ? $translations['Entity Data Form'] : 'Entity Data Form'; ?></h5>
              
              <?php if (!$canAddEntity && !$canUpdateEntity && $canReadEntity): ?>
              <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-1"></i>
                You have <strong>read-only</strong> access to this module. You cannot add or edit entity information.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php endif; ?>
              
               <!-- Entity/Company Form -->
              <form action="" method="POST">
                <!-- Hidden inputs to preserve parameters -->
                <?php if (isset($_GET['id'])): ?>
                <input type="hidden" name="entity_id" value="<?php echo $_GET['id']; ?>">
                <?php endif; ?>
                <input type="hidden" name="uid" value="<?php echo $userid; ?>">
                <?php if (isset($_GET['frm'])): ?>
                <input type="hidden" name="frm" value="<?php echo $_GET['frm']; ?>">
                <?php endif; ?>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Business Type']) ? $translations['Business Type'] : 'Business Type'; ?></label>
                  <div class="col-sm-4">
                    <select class="form-select" name="business_type" aria-label="Default select example" <?php echo $formDisabled; ?>>
                      <option selected></option>
                      <option value="1" <?php echo (isset($bustype) && $bustype == '1') ? 'selected' : ''; ?>>Individual</option>
                      <option value="2" <?php echo (isset($bustype) && $bustype == '2') ? 'selected' : ''; ?>>Company</option>
                    </select>
                  </div>
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Entity Type']) ? $translations['Entity Type'] : 'Entity Type'; ?></label>
                  <div class="col-sm-4">
                    <select class="form-select" name="entity_type" aria-label="Default select example" <?php echo $formDisabled; ?>>
                      <option selected></option>
                      <?php SelectEntitytype($enttype, $con); ?>
                    </select>
                  </div> 
                </div>

                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translations['Entity Name']) ? $translations['Entity Name'] : 'Entity Name'; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>" <?php echo $formReadonly; ?>>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputPassword" class="col-sm-2 col-form-label"><?php echo isset($translations['Address']) ? $translations['Address'] : 'Address'; ?></label>
                  <div class="col-sm-10">
                    <textarea class="form-control" name="address" style="height: 100px" <?php echo $formReadonly; ?>><?php echo isset($address) ? $address : ''; ?></textarea>
                  </div>
                </div>

                <div class="row mb-3">
                  <!-- Zip Code -->
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Zip Code']) ? $translations['Zip Code'] : 'Zip Code'; ?></label>
                  <div class="col-sm-2">
                    <input type="text" name="zipcode" class="form-control" value="<?php echo isset($zip) ? $zip : ''; ?>" <?php echo $formReadonly; ?>>
                  </div>

                  <!-- Phone -->
                  <label class="col-sm-1 col-form-label"><?php echo isset($translations['Phone']) ? $translations['Phone'] : 'Phone'; ?></label>
                  <div class="col-sm-7">
                    <input type="text" name="phone" class="form-control"  value="<?php echo isset($phone) ? $phone : ''; ?>" <?php echo $formReadonly; ?>>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translations['Email']) ? $translations['Email'] : 'Email'; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="email" id="email" class="form-control" value="<?php echo isset($email) ? $email : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Province']) ? $translations['Province'] : 'Province'; ?></label>
                  <div class="col-sm-4">
                    <select class="form-select" name="province" id="province" aria-label="Default select example" onchange="SelectProvinceOnChange(this)"> <!-- this function is in users-validate.js -->
                      <option selected></option>
                      <?php SelectProvinces($pid, $con); ?>
                    </select>
                  </div>
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Districts']) ? $translations['Districts'] : 'District'; ?></label>
                  <div class="col-sm-4">
                    <select class="form-select" name="district" id="district" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectDistricts($did, $pid, $con); ?>
                    </select>
                  </div> 
                </div>

                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translations['Contact person']) ? $translations['Contact person'] : 'Contact Person'; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="contact_person" id="contact_person" class="form-control" value="<?php echo isset($contact_person) ? $contact_person : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-sm-2 col-form-label"><?php echo isset($translations['Is it registered?']) ? $translations['Is it registered?'] : 'Is it registered?'; ?></label>
                    <div class="col-sm-1 d-flex align-items-center">
                      <input type="checkbox" name="isregister" id="isregister" value="1" <?php echo (isset($isregister) && $isregister) ? 'checked' : ''; ?>>
                      <label for="isregister" class="ms-2 mb-0"><?php echo isset($translations['Yes']) ? $translations['Yes'] : 'Yes'; ?></label>
                    </div>
                    <label class="col-sm-2 col-form-label"><?php echo isset($translations['Register date from']) ? $translations['Register date from'] : 'Register date from'; ?></label>
                    <div class="col-sm-3">
                      <input type="date" name="register_date_from" class="form-control" value="<?php echo isset($regdate1) ? $regdate1 : ''; ?>">
                    </div>
                    <label class="col-sm-1 col-form-label"><?php echo isset($translations['to']) ? $translations['to'] : 'to'; ?></label>
                    <div class="col-sm-3">
                      <input type="date" name="register_date_to" class="form-control" value="<?php echo isset($regdate2) ? $regdate2 : ''; ?>">
                    </div>
                 </div>
                 <div class="row mb-3 align-items-center">
                    <label class="col-sm-2 col-form-label"><?php echo isset($translations['Checklist registered?']) ? $translations['Checklist registered?'] : 'Checklist registered?'; ?></label>
                    <div class="col-sm-2 d-flex align-items-center">
                      <input type="checkbox" name="checklist_registered" id="checklist_registered" value="1" <?php echo (isset($checklist_registered) && $checklist_registered) ? 'checked' : ''; ?>>
                      <label for="checklist_registered" class="ms-2 mb-0"><?php echo isset($translations['Yes']) ? $translations['Yes'] : 'Yes'; ?></label>
                    </div>
                  </div>
                  
                <div class="row mb-3">
                   <label class="col-sm-2 col-form-label"><?php echo isset($translations['License export?']) ? $translations['License export?'] : 'License export?'; ?></label>
                  <div class="col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="license_export" id="license_export" value="1" <?php echo (isset($license_export) && $license_export) ? 'checked' : ''; ?>>
                    <label for="license_export" class="ms-2 mb-0"><?php echo isset($translations['Yes']) ? $translations['Yes'] : 'Yes'; ?></label>
                  </div>
                </div>
                
                <div class="row mb-3 align-items-center">
                    <label class="col-sm-2 col-form-label"><?php echo isset($translations['Is GAP?']) ? $translations['Is GAP?'] : 'Is GAP?'; ?></label>
                    <div class="col-sm-2 d-flex align-items-center">
                      <input type="checkbox" name="gap" id="gap" value="1" <?php echo (isset($gap) && $gap) ? 'checked' : ''; ?>>
                      <label for="gap" class="ms-2 mb-0"><?php echo isset($translations['Yes']) ? $translations['Yes'] : 'Yes'; ?></label>
                    </div>
                  </div>

                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">&nbsp;</label> 
                  <div class="col-sm-10">
                    <?php if ($showSubmitButton): ?>
                    <button type="submit" name="btnsubEntityExport" class="btn btn-primary" value="<?php echo isset($sbupdate) ? 'update' : 'submit'; ?>"><?php echo isset($sbupdate) ? (isset($translations['Update']) ? $translations['Update'] : 'Update') : (isset($translations['Submit']) ? $translations['Submit'] : 'Submit'); ?></button>
                    <?php else: ?>
                    <p class="text-muted"><i class="bi bi-lock me-1"></i>You don't have permission to submit or update entity data.</p>
                    <?php endif; ?>
                  </div>
                </div>
              </form><!-- End Export entity Form -->
            </div>
          </div>

        </div>
      </div>
    </section>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
          var businessTypeSelect = document.querySelector('select[name="business_type"]');
          if (businessTypeSelect) {
            businessTypeSelect.focus();
          }
        });
    </script>
    <?php
      }  // End of Export Entity Form
     ?>
    <?php
     // IMPORT ENTITY/COMPANY FORM  *******************
    if(isset($_GET['entity']) && $_GET['entity'] == 'import') {   
   ?>
    <section class="section">
      <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1><?php echo isset($translations['Import entity']) ? $translations['Import entity'] : 'Import entity'; ?></h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
            <li class="breadcrumb-item"><a href="application.php?part=dashboard&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>"><?php echo isset($translations['Application list']) ? $translations['Application list'] : 'Application list'; ?></a></li>
          </ol>
          </nav>
        </div>
        <div>
          <a href="entity.php?frm=newEntity_import&uid=<?php echo $userid; ?>&lang=<?php echo $lang; ?>" class="btn btn-success btn-sm" role="button">
            <i class="bi bi-plus-circle"></i> <?php echo isset($translations['Add New']) ? $translations['Add New'] : 'Add New'; ?>
          </a>
        </div>
      </div><!-- End Page Title - Import entity list -->
       <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo isset($translations['Import entity list']) ? $translations['Import entity list'] : 'Import entity list'; ?></h5>
              <p><?php echo isset($translations['ePhytosanitary - Department of Agriculture, MAF']) ? $translations['ePhytosanitary - Department of Agriculture, MAF'] : 'ePhytosanitary - Department of Agriculture, MAF'; ?></p>

              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts" >
                <thead>
                  <tr>
                   <th>
                      <b><?php echo isset($translations['No']) ? $translations['No'] : 'No'; ?></b>
                    </th>
                    <th><?php echo isset($translations['Country']) ? $translations['Country'] : 'Country'; ?></th>
                    <th>
                      <b><?php echo isset($translations['Name']) ? $translations['Name'] : 'Name'; ?></b>
                    </th>
                    <th><?php echo isset($translations['Address']) ? $translations['Address'] : 'Address'; ?></th>
                    <th><?php echo isset($translations['Phone']) ? $translations['Phone'] : 'Phone'; ?></th>
                    <th><?php echo isset($translations['Email']) ? $translations['Email'] : 'Email'; ?></th>
                    <th><?php echo isset($translations['Contact person']) ? $translations['Contact person'] : 'Contact person'; ?></th>
                    <th><?php echo isset($translations['Edit']) ? $translations['Edit'] : 'Edit'; ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php EntityImportList($con, $userid); ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>
        </div>
      </div>
    </section>
    <?php
      }  // End of Import Entity - List
     ?>
    <!-- =======**************** Import entity Add/Updates - Form ************* ======= -->
     <?php
       if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['btnsubEntityImport'])) {
           $sbupdate_import = ''; // Default to submit action
           // $uid = $_POST['huid'];
           $bustype = $_POST['businesstype_import'];
           $enttype = $_POST['entitytype_import'];
           $name = $_POST['name_import'];
            $address = $_POST['address_import'];
            $zip = $_POST['zipcode_import'];
            $phone = $_POST['phone_import'];
            $email = $_POST['email_import'];
            $countryid = $_POST['country_import'];
            $province = $_POST['province_import'];
            $district = $_POST['district_import'];
            $contact_person = $_POST['contactperson_import'];
            // Null for date= "1990-01-01"
            $datenull = "'1990-01-01'";
            $created_date = date('Y-m-d H:i:s');
            $created_guid = $guid;
        // Insert or update logic here
        if($_POST['btnsubEntityImport'] === 'update') {
          // Update existing entity
          $sbupdate = 'update';
          $entityimport_id = $_GET['id']; // Assuming you pass the entity ID in the URL
          UpdateEntityImport($entityimport_id, $bustype, $enttype, $name, $address, $zip, $province, $district, $countryid, $phone, $email, $contact_person, $con);
        }
        else if($_POST['btnsubEntityImport'] === 'submit') {
          // Add new entity
         AddEntityImport($bustype, $enttype, $name, $address, $zip, $province, $district, $countryid, $phone, $email, $contact_person, $created_date, $created_guid, $con);
        }
          
       }
     ?>
    <!-- Handle form submission for Import Entity/Company -->
    <?php
       if (isset($_GET['frm']) && ($_GET['frm'] === 'newEntity_import' || $_GET['frm'] === 'editEntity_import')) {
          echo "<script>document.title = 'Import Entity/Company Form';</script>";
          if(isset($_GET['id'])) { // id for entity import
            $sbupdate_import = 'update';
            $entityimport_id = $_GET['id'];
            // Declare the same variable as in the form
            $bustype = EntityImportInfo($entityimport_id, $con)['business_type'];
            $enttype = EntityImportInfo($entityimport_id, $con)['entity_type'];
            $name = EntityImportInfo($entityimport_id, $con)['title'];
            $address = EntityImportInfo($entityimport_id, $con)['address'];
            $zip = EntityImportInfo($entityimport_id, $con)['zipcode'];
            $phone = EntityImportInfo($entityimport_id, $con)['phone'];
            $email = EntityImportInfo($entityimport_id, $con)['email'];
            $countryid = EntityImportInfo($entityimport_id, $con)['country_id'];
            $province = EntityImportInfo($entityimport_id, $con)['province'];
            $district = EntityImportInfo($entityimport_id, $con)['district'];
            $contact_person = EntityImportInfo($entityimport_id, $con)['contact_name'];

          }
    ?>
    <div class="pagetitle">
      <h1><?php echo isset($translations['Import entity']) ? $translations['Import entity'] : 'Import entity'; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?uid=<?php echo $userid; ?>"><?php echo isset($translations['Home']) ? $translations['Home'] : 'Home'; ?></a></li>
          <li class="breadcrumb-item"><a href="entity.php?entity=import&uid=<?php echo $userid; ?>"><?php echo isset($translations['Import entity list']) ? $translations['Import entity list'] : 'Import entity list'; ?></a></li>
          <li class="breadcrumb-item active"><?php echo isset($translations['Import entity']) ? $translations['Import entity'] : 'Import entity'; ?></li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
   
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo isset($translations['Import entity']) ? $translations['Import entity'] : 'Import entity'; ?></h5>
              
              <?php if (!$canAddEntity && !$canUpdateEntity && $canReadEntity): ?>
              <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-1"></i>
                You have <strong>read-only</strong> access to this module. You cannot add or edit entity information.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php endif; ?>
              
              <!-- Import Entity/Company Form -->
              <form action="" method="POST">
                <!-- Hidden inputs to preserve parameters -->
                 <input type="hidden" name="huid" value="<?php echo $userid; ?>">
                 <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Business Type']) ? $translations['Business Type'] : 'Business Type'; ?></label>
                  <div class="col-sm-4">
                    <select class="form-select" name="businesstype_import" aria-label="Default select example" <?php echo $formDisabled; ?>>
                      <option selected></option>
                      <option value="1" <?php echo (isset($bustype) && $bustype == '1') ? 'selected' : ''; ?>>Individual</option>
                      <option value="2" <?php echo (isset($bustype) && $bustype == '2') ? 'selected' : ''; ?>>Company</option>
                    </select>
                  </div>
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Entity Type']) ? $translations['Entity Type'] : 'Entity Type'; ?></label>
                  <div class="col-sm-4">
                    <select class="form-select" name="entitytype_import" aria-label="Default select example" <?php echo $formDisabled; ?>>
                      <option selected></option>
                      <?php SelectEntitytype($enttype, $con); ?>
                    </select>
                  </div> 
                </div>

                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translations['Entity Name']) ? $translations['Entity Name'] : 'Entity Name'; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="name_import" id="name_import" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>" <?php echo $formReadonly; ?>>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputPassword" class="col-sm-2 col-form-label"><?php echo isset($translations['Address']) ? $translations['Address'] : 'Address'; ?></label>
                  <div class="col-sm-10">
                    <textarea class="form-control" name="address_import" style="height: 100px" <?php echo $formReadonly; ?>><?php echo isset($address) ? $address : ''; ?></textarea>
                  </div>
                </div>

                <div class="row mb-3">
                  <!-- Zip Code -->
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Zip Code']) ? $translations['Zip Code'] : 'Zip Code'; ?></label>
                  <div class="col-sm-2">
                    <input type="text" name="zipcode_import" class="form-control" value="<?php echo isset($zip) ? $zip : ''; ?>" <?php echo $formReadonly; ?>>
                  </div>

                  <!-- Phone -->
                  <label class="col-sm-1 col-form-label"><?php echo isset($translations['Phone']) ? $translations['Phone'] : 'Phone'; ?></label>
                  <div class="col-sm-7">
                    <input type="text" name="phone_import" class="form-control"  value="<?php echo isset($phone) ? $phone : ''; ?>" <?php echo $formReadonly; ?>>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translations['Email']) ? $translations['Email'] : 'Email'; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="email_import" id="email_import" class="form-control" value="<?php echo isset($email) ? $email : ''; ?>" <?php echo $formReadonly; ?>>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translations['Country']) ? $translations['Country'] : 'Country'; ?></label>
                  <div class="col-sm-10">
                     <select class="form-select" name="country_import" aria-label="Default select example" <?php echo $formDisabled; ?>>
                      <option selected></option>
                      <?php SelectCountry($countryid, $con); ?>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Province']) ? $translations['Province'] : 'Province'; ?></label>
                  <div class="col-sm-4">
                    <input type="text" name="province_import" id="province_import" class="form-control" value="<?php echo isset($province) ? $province : ''; ?>" <?php echo $formReadonly; ?>>
                  </div>
                  <label class="col-sm-2 col-form-label"><?php echo isset($translations['Districts']) ? $translations['Districts'] : 'District/City'; ?></label>
                  <div class="col-sm-4">
                    <input type="text" name="district_import" id="district_import" class="form-control" value="<?php echo isset($district) ? $district : ''; ?>" <?php echo $formReadonly; ?>>
                  </div> 
                </div>

                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label"><?php echo isset($translations['Contact person']) ? $translations['Contact person'] : 'Contact Person'; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="contactperson_import" id="contactperson_import" class="form-control" value="<?php echo isset($contact_person) ? $contact_person : ''; ?>" <?php echo $formReadonly; ?>>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">&nbsp;</label> 
                  <div class="col-sm-10">
                    <?php if ($showSubmitButton): ?>
                    <button type="submit" name="btnsubEntityImport" class="btn btn-primary" value="<?php echo isset($sbupdate_import) ? 'update' : 'submit'; ?>"><?php echo isset($sbupdate_import) ? 'Update' : 'Submit'; ?></button>
                    <?php else: ?>
                    <p class="text-muted"><i class="bi bi-lock me-1"></i>You don't have permission to submit or update entity data.</p>
                    <?php endif; ?>
                  </div>
                </div>

              </form><!-- End Import Form -->
            </div>
          </div>

        </div>
      </div>
    </section>
    <?php 
      }  // End of Import Entity Form
    ?>
    
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

  <!-- Entity Navigation Active State Handler -->
  <script>
    $(document).ready(function() {
      // Function to update active state
      function updateActiveState() {
        // Get current URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const entityType = urlParams.get('entity');
        
        // Remove active class from all entity nav links
        $('.nav-item a[href*="entity.php"]').removeClass('active').addClass('collapsed');
        
        // Add active class to current page
        if (entityType === 'export') {
          $('.nav-item a[href*="entity.php?entity=export"]').removeClass('collapsed').addClass('active');
        } else if (entityType === 'import') {
          $('.nav-item a[href*="entity.php?entity=import"]').removeClass('collapsed').addClass('active');
        }
      }
      
      // Update active state on page load
      updateActiveState();
      
      // Handle click events on entity navigation links
      $('.nav-item a[href*="entity.php"]').click(function(e) {
        // Remove active from all entity nav links
        $('.nav-item a[href*="entity.php"]').removeClass('active').addClass('collapsed');
        
        // Add active to clicked link
        $(this).removeClass('collapsed').addClass('active');
      });
    });
  </script>

</body>

</html>