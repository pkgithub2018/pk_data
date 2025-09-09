<?php
session_start();

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

// User group ID
$guid = $_SESSION["groupid"];

// connection to database
 require("php-bin/connection.php");
 require("php-bin/supports.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Entity</title>
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
        <span class="d-none d-lg-block">ePhytosanitary Certificate</span>
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
            <img src="<?php echo $_SESSION['image']; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION['username']; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $_SESSION['username']; ?></h6>
              <span><?php echo $_SESSION['position']; ?></span>
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
        <a class="nav-link collapsed" href="main.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->
      <!--
      <li class="nav-item">
        <a class="nav-link" data-bs-target="#transaction-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-folder"></i>
          <span>Transaction</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="transaction-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="modules.php?part=application_list">
              <i class="bi bi-circle"></i><span>Application</span>
            </a>
          </li>
          <li>
            <a href="modules.php?part=inspection">
              <i class="bi bi-circle"></i><span>Inspection's results</span>
            </a>
          </li>
        </ul>
      </li>
      -->
      <!-- End Transaction Nav -->

      <li class="nav-item">
        <a class="nav-link active" href="#" >
          <i class="bi bi-box-arrow-up-right"></i>
          <span>Export entity</span>
        </a>
      </li><!-- End Export Entity Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=import" >
          <i class="bi bi-box-arrow-in-down" style="font-size: 1.2rem;"></i>
          <span>Import entity</span>
        </a>
      </li><!-- End Import Entity Nav -->

       <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Master data</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
         
          <li>
            <a href="masterdata.php?part=product">
              <i class="bi bi-circle"></i><span>Product</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=conveyance">
              <i class="bi bi-circle"></i><span>Conveyance</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=countries">
              <i class="bi bi-circle"></i><span>Countries</span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span>Districts</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=entitytype">
              <i class="bi bi-circle"></i><span>Entity_type</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=inspectionmethod">
              <i class="bi bi-circle"></i><span>Inspection Method</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=locations">
              <i class="bi bi-circle"></i><span>Locations</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=modules">
              <i class="bi bi-circle"></i><span>Module List</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=provinces">
              <i class="bi bi-circle"></i><span>Provinces</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=treatmentmethod">
              <i class="bi bi-circle"></i><span>Treatment Method</span>
            </a>
          </li>
        </ul>
      </li><!-- End Master Data Nav -->     

      <li class="nav-heading">Users' Management</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=ugroup">
          <i class="bi bi-people"></i>
          <span>Users group</span>
        </a>
      </li><!-- End Users group -->

       <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=upermits">
          <i class="bi bi-shield-lock"></i>
          <span>Group permits</span>
        </a>
      </li><!-- End Permission: User Group and Module -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="users.php?part=userslist">
          <i class="bi bi-person-plus"></i>
          <span>Users</span>
        </a>
      </li>  
      <!-- pk**: End of User Admin-->
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
        <h1>Export entity</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="main.php">Home</a></li>
            <li class="breadcrumb-item">Tables</li>
            <li class="breadcrumb-item">Export entity</li>
          </ol>
          </nav>
        </div>
        <div>
          <a href="entity.php?frm=newEntity_export" class="btn btn-success btn-sm" role="button">
            <i class="bi bi-plus-circle"></i> Add New export entity
          </a>
        </div>
      </div><!-- End Page Title - Users list -->
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Export entity</h5>
              <p>ePhytosanitary by Department of Agriculture, MAF - Export entity</p>

              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts" >
                <thead>
                  <tr>
                   <th>
                      <b>N</b>o
                    </th>
                    <th>
                      <b>N</b>ame
                    </th>
                    <th>Address</th>
                    <th style="white-space: nowrap;">Contact person</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Province</th>
                    <th>Edit</th>
                    <th>Application</th>
                  </tr>
                </thead>
                <tbody>
                  <?php EntityExportList($con); ?>
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
          $sbupdate = 'update';
          $entity_id = $_GET['id']; // Assuming you pass the entity ID in the URL
          UpdateEntityExport($entity_id, $bustype, $enttype, $name, $address, $zip, $pid, $did, $phone, $email, $contact_person, $isregister, $regdate1, $regdate2, $checkreg, $gap, $license_export, $con);
        } else if($_POST['btnsubEntityExport'] === 'submit') {
          // Add new entity
          AddEntityExport($bustype, $enttype, $name, $address, $zip, $pid, $did, $phone, $email, $contact_person, $isregister, $regdate1, $regdate2, $checkreg, $gap, $license_export, $created_date, $guid, $con);
        }
     } 
   ?>
   <!-- Open export entry form  -->
   <?php
   // EXPORT ENTITY/COMPANY-FORM  *******************
     if (isset($_GET['frm']) && ($_GET['frm'] === 'newEntity_export' || $_GET['frm'] === 'editEntity_export')) {
       echo "<script>document.title = 'Export Entity/Company Form';</script>";
       if(isset($_GET['id'])) { 
         $sbupdate = 'update';
         // Fetch the entity data for editing
         // Assuming you have a function GetEntityExport that retrieves the entity data by ID 
         $entity_id = $_GET['id'];
         $entityData = GetEntityExport($entity_id, $con);
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
      <h1>Export Entity/Company</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php">Home</a></li>
          <li class="breadcrumb-item"><a href="entity.php?entity=export">Export entity-List</a></li>
          <li class="breadcrumb-item active">Export Entity/Company</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Entity Data Form</h5>
               <!-- Entity/Company Form -->
              <form action="" method="POST">
                <!-- Hidden input for uid : User ID -->
               <!-- <input type="hidden" id="huid" name="huid" value="<?php echo isset($uid) ? $uid : ''; ?>"> -->
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Business Type</label>
                  <div class="col-sm-4">
                    <select class="form-select" name="business_type" aria-label="Default select example">
                      <option selected></option>
                      <option value="1" <?php echo (isset($bustype) && $bustype == '1') ? 'selected' : ''; ?>>Individual</option>
                      <option value="2" <?php echo (isset($bustype) && $bustype == '2') ? 'selected' : ''; ?>>Company</option>
                    </select>
                  </div>
                  <label class="col-sm-2 col-form-label">Entity Type</label>
                  <div class="col-sm-4">
                    <select class="form-select" name="entity_type" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectEntitytype($enttype, $con); ?>
                    </select>
                  </div> 
                </div>

                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Entity Name</label>
                  <div class="col-sm-10">
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputPassword" class="col-sm-2 col-form-label">Address</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" name="address" style="height: 100px"><?php echo isset($address) ? $address : ''; ?></textarea>
                  </div>
                </div>

                <div class="row mb-3">
                  <!-- Zip Code -->
                  <label class="col-sm-2 col-form-label">Zip Code</label>
                  <div class="col-sm-2">
                    <input type="text" name="zipcode" class="form-control" value="<?php echo isset($zip) ? $zip : ''; ?>">
                  </div>

                  <!-- Phone -->
                  <label class="col-sm-1 col-form-label">Phone</label>
                  <div class="col-sm-7">
                    <input type="text" name="phone" class="form-control"  value="<?php echo isset($phone) ? $phone : ''; ?>">
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-10">
                    <input type="text" name="email" id="email" class="form-control" value="<?php echo isset($email) ? $email : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Province</label>
                  <div class="col-sm-4">
                    <select class="form-select" name="province" id="province" aria-label="Default select example" onchange="SelectProvinceOnChange(this)"> <!-- this function is in users-validate.js -->
                      <option selected></option>
                      <?php SelectProvinces($pid, $con); ?>
                    </select>
                  </div>
                  <label class="col-sm-2 col-form-label">District</label>
                  <div class="col-sm-4">
                    <select class="form-select" name="district" id="district" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectDistricts($did, $pid, $con); ?>
                    </select>
                  </div> 
                </div>

                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Contact Person</label>
                  <div class="col-sm-10">
                    <input type="text" name="contact_person" id="contact_person" class="form-control" value="<?php echo isset($contact_person) ? $contact_person : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-sm-2 col-form-label">Is it registered?</label>
                    <div class="col-sm-1 d-flex align-items-center">
                      <input type="checkbox" name="isregister" id="isregister" value="1" <?php echo (isset($isregister) && $isregister) ? 'checked' : ''; ?>>
                      <label for="isregister" class="ms-2 mb-0">Yes</label>
                    </div>
                    <label class="col-sm-2 col-form-label">Register date from</label>
                    <div class="col-sm-3">
                      <input type="date" name="register_date_from" class="form-control" value="<?php echo isset($regdate1) ? $regdate1 : ''; ?>">
                    </div>
                    <label class="col-sm-1 col-form-label">to</label>
                    <div class="col-sm-3">
                      <input type="date" name="register_date_to" class="form-control" value="<?php echo isset($regdate2) ? $regdate2 : ''; ?>">
                    </div>
                 </div>
                 <div class="row mb-3 align-items-center">
                    <label class="col-sm-2 col-form-label">Checklist registered?</label>
                    <div class="col-sm-2 d-flex align-items-center">
                      <input type="checkbox" name="checklist_registered" id="checklist_registered" value="1" <?php echo (isset($checklist_registered) && $checklist_registered) ? 'checked' : ''; ?>>
                      <label for="checklist_registered" class="ms-2 mb-0">Yes</label>
                    </div>
                  </div>
                  
                <div class="row mb-3">
                   <label class="col-sm-2 col-form-label">License export?</label>
                  <div class="col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="license_export" id="license_export" value="1" <?php echo (isset($license_export) && $license_export) ? 'checked' : ''; ?>>
                    <label for="license_export" class="ms-2 mb-0">Yes</label>
                  </div>
                </div>
                
                <div class="row mb-3 align-items-center">
                    <label class="col-sm-2 col-form-label">Is GAP?</label>
                    <div class="col-sm-2 d-flex align-items-center">
                      <input type="checkbox" name="gap" id="gap" value="1" <?php echo (isset($gap) && $gap) ? 'checked' : ''; ?>>
                      <label for="gap" class="ms-2 mb-0">Yes</label>
                    </div>
                  </div>

                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">&nbsp;</label> 
                  <div class="col-sm-10">
                    <button type="submit" name="btnsubEntityExport" class="btn btn-primary" value="<?php echo isset($sbupdate) ? 'update' : 'submit'; ?>"><?php echo isset($sbupdate) ? 'Update' : 'Submit'; ?></button>
                  </div>
                </div>
              </form><!-- End General Form Elements -->
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
    <div class="pagetitle">
      <h1>Import Entity/Company</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php">Home</a></li>
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

</body>

</html>