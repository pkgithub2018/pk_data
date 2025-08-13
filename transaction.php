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
// USER ID
$userid = isset($_SESSION["uid"]) ? $_SESSION["uid"] : ''; // use user id
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
         <?php
          $activeParts = ['application', 'application_list', 'inspection']; // Add all relevant parts here
          $isPartActive = (isset($_GET['part']) && in_array($_GET['part'], $activeParts));
         ?>
      <li class="nav-item">
        <a class="nav-link <?php echo $isPartActive ? '' : 'collapsed'; ?>" data-bs-target="#transaction-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-folder"></i>
          <span>Transaction</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="transaction-nav" class="nav-content collapse <?php echo $isPartActive ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
          <li>
            <a href="transaction.php?part=application" class="<?php echo isset($_GET['part']) && ($_GET['part'] === 'application' || $_GET['part'] === 'application_list') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Application</span>
            </a>
          </li>
          <li>
            <a href="transaction.php?part=inspection" class="<?php echo isset($_GET['part']) && $_GET['part'] === 'inspection' ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Inspection's results</span>
            </a>
          </li>
        </ul>
      </li><!-- End Transaction Nav -->

      <li class="nav-item">
        <a class="nav-link" href="entity.php?entity=export" >
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
     // Application list  *******************
    if(isset($_GET['part']) && $_GET['part'] === 'application_list') {
      echo "<script>document.title = 'Application';</script>";
    ?>
     <section class="section">
      <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Application</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="main.php">Home</a></li>
            <li class="breadcrumb-item">Tables</li>
            <li class="breadcrumb-item">Application</li>
          </ol>
          </nav>
        </div>
        <div>
          <a href="entity.php?frm=newEntity_export" class="btn btn-success btn-sm" role="button">
            <i class="bi bi-plus-circle"></i> Add New Application
          </a>
        </div>
      </div><!-- End Page Title - Users list -->
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Application</h5>
              <p>ePhytosanitary by Department of Agriculture, MAF - Application</p>

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
  
   <!-- Application form  -->
   <?php
   // EXPORT ENTITY/COMPANY-FORM  *******************
     if (isset($_GET['part']) && $_GET['part'] === 'application') {
      if( isset($_GET['id']) && !empty($_GET['id'])) {
        //$_GET['id'] is exporter ID
        $uid = $userid;
       // $app_no = $_GET['id']."-".$uid;
       $app_no = ApplicationNo($_GET['id'], $uid, $con);
      }

      // Application information
      // $_GET['id'] is exporter ID from URL in function: EntityExportList() in supports.php
      if(isset($_GET['id']) && !empty($_GET['id'])) {
        echo "<script>document.title = 'Application - " . htmlspecialchars($_GET['id'], ENT_QUOTES) . "';</script>";
        $app_info = ApplicantInfo_Export($_GET['id'], $con);
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
    ?>
    <div class="pagetitle">
      <h1>Application</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php">Home</a></li>
          <li class="breadcrumb-item"><a href="transaction.php?part=application_list">Application List</a></li>
          <li class="breadcrumb-item active">Application</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Application Form</h5>
               <!-- FORM: Entity/Company Form -->
              <form action="" method="POST">
                <div class="row mb-3 align-items-center">
                  <!-- Application No -->
                  <label class="col-sm-2 col-form-label">Application No</label>
                  <div class="col-sm-3">
                    <input type="text" name="application_no" id="application_no" class="form-control" value="<?php echo isset($app_no) ? $app_no : ''; ?>">
                  </div>
                  <!-- Application Date -->
                  <label class="col-sm-1 col-form-label">Date</label>
                  <div class="col-sm-3">
                    <input type="date" name="app_date" class="form-control" value="<?php echo isset($date) ? $date : ''; ?>">
                  </div>
                  <!-- Reg No -->
                  <label class="col-sm-1 col-form-label">Reg No</label>
                  <div class="col-sm-2">
                    <input type="text" name="reg_no" class="form-control" value="<?php echo isset($reg_no) ? $reg_no : ''; ?>">
                  </div>
                </div>
                
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Applicant's Name</label>
                  <div class="col-sm-10">
                    <input type="text" name="applicant_name" id="applicant_name" class="form-control" value="<?php echo isset($contact_person) ? $contact_person : ''; ?>">
                  </div>
                </div>
                
                <div class="row mb-3">
                  <label for="inputPassword" class="col-sm-2 col-form-label">Address</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" name="address" style="height: 100px"><?php echo isset($address) ? $address : ''; ?></textarea>
                  </div>
                </div>
                 <!-- Phone -->
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Phone</label>
                  <div class="col-sm-4">
                    <input type="text" name="phone" class="form-control"  value="<?php echo isset($phone) ? $phone : ''; ?>">
                  </div>
                </div>

                <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">Export entry point</label>
                  <div class="col-sm-10">
                    <select class="form-select" name="entry_point" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectLocation($locid, $con); ?>
                    </select>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Import country</label>
                  <div class="col-sm-4">
                    <select class="form-select" name="country" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectCountry($countryid, $con); ?>
                    </select>
                  </div>
                  <label class="col-sm-2 col-form-label">Import entry point</label>
                  <div class="col-sm-4">
                    <select class="form-select" name="import_entry_point" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectEntitytype($enttype, $con); ?>
                    </select>
                  </div> 
                </div>

                <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">Export certificate</label>
                  <div class="col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="export_certificate" id="export_certificate" value="1" <?php echo (isset($dynamic_option1) && $dynamic_option1) ? 'checked' : ''; ?>>
                    <label for="export_certificate" class="ms-2 mb-0">Yes (<i class="bi bi-check-lg"></i>)</label>
                  </div>
                  <label class="col-sm-2 col-form-label">Transit certificate</label>
                  <div class="col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="transit_certificate" id="transit_certificate" value="1" <?php echo (isset($dynamic_option2) && $dynamic_option2) ? 'checked' : ''; ?>>
                    <label for="transit_certificate" class="ms-2 mb-0">Yes (<i class="bi bi-check-lg"></i>)</label>
                  </div>
                </div>

 
                <div class="row mb-3">
                   <label class="col-sm-2 col-form-label">Multiple commodities</label>
                  <div class="col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="multiple_commodities" id="multiple_commodities" value="1" <?php echo (isset($multiple_commodities) && $multiple_commodities) ? 'checked' : ''; ?>>
                    <label for="multiple_commodities" class="ms-2 mb-0">Yes</label>
                  </div>
                </div>
                
          <div class="row mb-3 align-items-center">
            <label class="col-sm-2 col-form-label">Print supporting document</label>
            <div class="col-sm-2 d-flex align-items-center">
              <input type="checkbox" name="support_document" id="support_document" value="1" <?php echo (isset($support_document) && $support_document) ? 'checked' : ''; ?>>
              <a href="#" data-bs-toggle="modal" data-bs-target="#spdocModal">
      <label for="support_document" class="ms-2 mb-0" style="cursor:pointer;">
        <i class="bi bi-printer"></i>
      </label>
    </a>
            </div>
          </div>
          <div class="row mb-3 align-items-center">
            <label class="col-sm-2 col-form-label">Commodities</label>
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
            <label for="name_oncertificate" class="col-sm-2 col-form-label">Name on certificate</label>
            <div class="col-sm-4">
              <input type="text" name="name_oncertificate" id="name_oncertificate" class="form-control" value="<?php echo isset($proName) ? $proName : ''; ?>">
            </div>
            <label for="scientific_name" class="col-sm-2 col-form-label">Scientific Name</label>
            <div class="col-sm-4">
              <input type="text" name="scientific_name" id="scientific_name" class="form-control" value="<?php echo isset($scientific_name) ? $scientific_name : ''; ?>">
            </div>
          </div>

          <div class="row mb-3">
            <label for="number_description" class="col-sm-2 col-form-label">Number and description</label>
            <div class="col-sm-10">
              <textarea name="number_description" id="number_description" class="form-control" rows="3"><?php echo isset($number_description) ? $number_description : ''; ?></textarea>
            </div>
          </div>

          <div class="row mb-3 align-items-center">
              <label for="nquantity" class="col-sm-2 col-form-label">Net Quantity</label>
            <div class="col-sm-2">
              <input type="number" step="0.01" min="0" name="nquantity" id="nquantity" class="form-control" value="<?php echo isset($nquantity) ? $nquantity : ''; ?>">
            </div>
            <label for="gquantity" class="col-sm-2 col-form-label">Gross Quantity</label>
            <div class="col-sm-2">
              <input type="number" step="0.01" min="0" name="gquantity" id="gquantity" class="form-control" value="<?php echo isset($gquantity) ? $gquantity : ''; ?>">
            </div>
              <label for="unit" class="col-sm-1 col-form-label">Unit</label>
            <div class="col-sm-3">
              <select name="unit" id="unit" class="form-select">
                <option value="">Select</option>
                <?php SelectUnit($unitid, $con); ?>
                <!-- Add more units as needed -->
              </select>
            </div>
            </div>
            <div class="row mb-3">
              <label for="inputText" class="col-sm-2 col-form-label">Distinguishing Marks</label>
               <div class="col-sm-10">
                 <input type="text" name="distinguishing_marks" id="distinguishing_marks" class="form-control" value="<?php echo isset($distinguishing_marks) ? $distinguishing_marks : ''; ?>">
               </div>
             </div>
             <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">Place of origin</label>
                  <div class="col-sm-10">
                    <select class="form-select" name="place_of_origin" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectCountry($countryid, $con); ?>
                    </select>
                  </div>
                </div>  
            <div class="row mb-3 align-items-center">
                <label class="col-sm-2 col-form-label">Conveyance</label>
                <div class="col-sm-4">
                  <select class="form-select" name="conveyance" id="conveyance" aria-label="Select packaging type">
                    <option value="">Select</option>
                    <?php SelectConveyance($conveyanceid, $con); ?> 
                  </select>
                </div>
                <label class="col-sm-2 col-form-label">Conveyance Sign</label>
                <div class="col-sm-4">
                  <input type="text" name="conveyance_sign" id="conveyance_sign" class="form-control" value="<?php echo isset($conveyance_sign) ? $conveyance_sign : ''; ?>">
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">Exporter's name and address</label>
                  <div class="col-sm-4 d-flex align-items-start">
                      <a href="#" data-bs-toggle="modal" data-bs-target="#exporterModal" class="me-2">
                        <i class="bi bi-search ms-2" style="font-size: 1.2rem; cursor:pointer;"></i>
                      </a>
                      <textarea name="exporter_address" id="exporter_address" class="form-control" rows="5"><?php echo isset($exporter_address) ? $exporter_address : ''; ?></textarea>
                  </div>
                  <label class="col-sm-2 col-form-label">Importer's name and address</label>
                  <div class="col-sm-4">
                    <i class="bi bi-search ms-2" style="font-size: 1.2rem;"></i>&nbsp;<textarea name="importer_address" id="importer_address" class="form-control" rows="5"><?php echo isset($importer_address) ? $importer_address : ''; ?></textarea>
                  </div>
            </div>
            

            <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">Purpose</label>
                  <div class="col-sm-4">
                    <select class="form-select" name="purpose" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectEntitytype($enttype, $con); ?>
                    </select>
                  </div>
                </div>

              <div class="row mb-3 align-items-center">
                <label class="col-sm-2 col-form-label">Place of Quarantine</label>
                <div class="col-sm-4">
                  <select class="form-select" name="place_of_quarantine" id="place_of_quarantine" aria-label="Select packaging type">
                    <option value="">Select</option>
                    <?php SelectProvince($provinceid, $con); ?> 
                  </select>
                </div>
                <label class="col-sm-1 col-form-label">Specify</label>
                <div class="col-sm-5">
                  <input type="text" name="place_of_quarantine_other" id="place_of_quarantine_other" class="form-control" value="<?php echo isset($place_of_quarantine_other) ? $place_of_quarantine_other : ''; ?>">
                </div>
            </div>
           
             <div class="row mb-3 align-items-center">
                <label class="col-sm-2 col-form-label">Place of treatment</label>
                <div class="col-sm-4">
                  <select class="form-select" name="place_of_treatment" id="place_of_treatment" aria-label="Select packaging type">
                    <option value="">Select</option>
                    <?php SelectProvince($provinceid, $con); ?> 
                  </select>
                </div>
                <label class="col-sm-1 col-form-label">Specify</label>
                <div class="col-sm-5">
                  <input type="text" name="place_of_treatment_other" id="place_of_treatment_other" class="form-control" value="<?php echo isset($place_of_treatment_other) ? $place_of_treatment_other : ''; ?>">
                </div>
            </div>

                <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">Certificate Date</label>
                    <div class="col-sm-4">
                      <input type="date" name="certificate_date" class="form-control" value="<?php echo isset($certificate_date) ? $certificate_date : ''; ?>">
                    </div>
                </div>
              
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">&nbsp;</label> 
                  <div class="col-sm-10 d-flex gap-2">
                    <button type="submit" name="btnsubApplication_save" class="btn btn-primary" value="<?php echo isset($sbupdate) ? 'update' : 'submit'; ?>">
                      <?php echo isset($sbupdate) ? 'Update' : 'Submit'; ?>
                    </button>
                    <button type="submit" name="btnsubApplication_save_continue" class="btn btn-secondary" value="save_continue">
                      Save & continue
                    </button>
                  </div>
                </div>
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

<!-- Modal for Supporting Document -->
<div class="modal fade" id="spdocModal" tabindex="-1" aria-labelledby="spdocModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="spdocModalLabel">Supporting Document Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Present data in a word-like document style -->
        <div class="border p-3 bg-white" style="min-height:300px;">
          <h6 class="text-center" style="white-space: pre-line;">LAO PEOPLE'S DEMOCRATIC REPUBLIC
            PEACE INDEPENDENCE DEMOCRACY UNITY PROSPERITY
            MINISTRY OF AGRICULTURE AND FORESTRY
            DEPARTMENT OF AGRICULTURE</h6>
          <p><strong>Applicant Name:</strong> <?php echo isset($contact_person) ? $contact_person : ''; ?></p>
          <p><strong>Address:</strong> <?php echo isset($address) ? $address : ''; ?></p>
          <p><strong>Phone:</strong> <?php echo isset($phone) ? $phone : ''; ?></p>
          <p><strong>Commodity:</strong> <?php echo isset($commodities) ? $commodities : ''; ?></p>
          <!-- Add more fields as needed -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal form for searching for export entity -->

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

                $info = htmlspecialchars($row['title'] . "\n" . $row['address'] . "\n" . $row['phone'] . "\n" . $proname . ", " . $distname . ", Laos");
                echo "<tr>
                  <td>" . htmlspecialchars($row['title']) . "</td>
                  <td>" . htmlspecialchars($row['address']) . "</td>
                  <td>" . htmlspecialchars($row['phone']) . "</td>
                  <td>" . htmlspecialchars($proname) . "</td>
                  <td>" . htmlspecialchars($distname) . "</td>
                  <td>
                    <button type='button' class='btn btn-success btn-sm' onclick='selectExporter(`$info`)'>Add</button>
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
</script>

</body>

</html>