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

// AJAX endpoint for importer name search
if (isset($_POST['action']) && $_POST['action'] == 'search_importer') {
    // Debug: Log the request
    error_log("Search request received for term: " . $_POST['term']);
    
    $searchTerm = pg_escape_string($con, $_POST['term']);
    
    $sql = "SELECT title, address FROM tbentity_import 
            WHERE title ILIKE '%$searchTerm%' 
            ORDER BY title ASC 
            LIMIT 10";
    
    // Debug: Log the SQL query
    error_log("SQL Query: " . $sql);
    
    $result = pg_query($con, $sql);
    $importers = array();
    
    if ($result && pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $importers[] = array(
                'title' => $row['title'],
                'address' => $row['address'],
                'full_text' => $row['title'] . ($row['address'] ? ', ' . $row['address'] : '')
            );
        }
    }
    
    // Debug: Log the response
    error_log("Search results count: " . count($importers));
    
    header('Content-Type: application/json');
    echo json_encode($importers);
    exit;
}

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

  <!-- Autocomplete CSS -->
  <style>
    .autocomplete-suggestions {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border: 1px solid #ddd;
      border-top: none;
      border-radius: 0 0 4px 4px;
      max-height: 200px;
      overflow-y: auto;
      z-index: 1000;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      display: none;
    }
    
    .autocomplete-suggestion {
      padding: 8px 12px;
      cursor: pointer;
      border-bottom: 1px solid #f0f0f0;
      font-size: 14px;
    }
    
    .autocomplete-suggestion:hover,
    .autocomplete-suggestion.active {
      background-color: #f8f9fa;
    }
    
    .autocomplete-suggestion:last-child {
      border-bottom: none;
    }
    
    .suggestion-title {
      font-weight: 500;
      color: #333;
    }
    
    .suggestion-address {
      font-size: 12px;
      color: #666;
      margin-top: 2px;
    }
  </style>

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
         // $activeParts = ['application', 'application_list', 'inspection']; // Add all relevant parts here
         // $isPartActive = (isset($_GET['part']) && in_array($_GET['part'], $activeParts));
         ?>
     <!--    
      <li class="nav-item">
        <a class="nav-link <?php echo $isPartActive ? '' : 'collapsed'; ?>" data-bs-target="#transaction-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-folder"></i>
          <span>Transaction</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="transaction-nav" class="nav-content collapse <?php echo $isPartActive ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
          <li>
            <a href="transaction.php?part=application" class="<?php echo isset($_GET['part']) && ($_GET['part'] === 'application' || $_GET['part'] === 'exportentity_list') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Application</span>
            </a>
          </li>
          <li>
            <a href="transaction.php?part=inspection" class="<?php echo isset($_GET['part']) && $_GET['part'] === 'inspection' ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Inspection's results</span>
            </a>
          </li>
        </ul>
      </li>
      -->
      <!-- End Transaction Nav -->

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
     // Export entity list  *******************
    if(isset($_GET['part']) && $_GET['part'] === 'exportentity_list') {
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
          if( isset($_GET['id']) && !empty($_GET['id'])) { // ExporterID -link <a> from Export entity form
            //$_GET['id'] is exporter ID
            $uid = $userid;  // from $_SESSION
            $guidLogin = $guid; // from $_SESSION
          
            // Application Number will generated in FUNCTION: ApplicationNo
            // Application ID (id - auto_increment ($app_id)) along with USER'S ID, are INSERTED INTO tbapplication
            // $app_no - FULL APPLICATION NUMBER
          list($app_id, $app_no) = ApplicationNo($_GET['id'], $uid, $con);
        // }

          // Application information
          // $_GET['id'] is exporter ID from URL in function: EntityExportList() in supports.php
          //if(isset($_GET['id']) && !empty($_GET['id'])) {
            $exporter_id = $_GET['id'];
            $appdate = date('Y-m-d');  // initial application date
            // UPDATE tbapplication with exporter ID
            $sqlupdate = "UPDATE tbapplication SET company_id = '$exporter_id', application_no = '$app_no', application_date = '$appdate', guid = '$guidLogin' WHERE id = '$app_id'";
            pg_query($con, $sqlupdate) or die(pg_last_error($con));

            $app_info = ApplicantInfo_Export($exporter_id, $con);
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

      // Set Application date ****
      if (!isset($date) || empty($date)) {
          $date = date('Y-m-d'); // Store as Y-m-d: for display only
      }

      // EDIT/UPDATE Application *********
        if(isset($_GET['appid_edit'])) {  // from ApplicationList function in supports.php
         // echo "Editing Application ID: " . htmlspecialchars($_GET['appid_edit']);
          $btnSubmit = "update";
          $appEdit_id = is_numeric($_GET['appid_edit']) && $_GET['appid_edit'] > 0 ? (int)$_GET['appid_edit'] : null;  // Integer - From function: ApplicationList

          $app_rows = null;
          if($appEdit_id !== null) {
              $app_rows = ApplicationInfo($appEdit_id, $con);
              if ($app_rows) {
                  // Populate form fields with existing application data
                  $app_no = isset($app_rows['application_no']) ? $app_rows['application_no'] : '';  // application No, not ID
                  $date = isset($app_rows['application_date']) ? $app_rows['application_date'] : '';
                  $reg_no = isset($app_rows['reg_no']) ? $app_rows['reg_no'] : '';
                  $contact_person = isset($app_rows['contact_person']) ? $app_rows['contact_person'] : '';
                  $address = isset($app_rows['address_person']) ? $app_rows['address_person'] : '';
                  $phone = isset($app_rows['phone']) ? $app_rows['phone'] : '';
                  $locid = isset($app_rows['export_point']) ? $app_rows['export_point'] : '';
                  $countryid = isset($app_rows['country_import']) ? $app_rows['country_import'] : '';
                  $import_point = isset($app_rows['import_point']) ? $app_rows['import_point'] : '';
                  $certificate_type = isset($app_rows['certificate_type']) ? $app_rows['certificate_type'] : '';
                  if($certificate_type == 'export') {
                      $export_certificate = true;
                      $transit_certificate = false;
                  } else if($certificate_type == 'transit') {
                      $export_certificate = false;
                      $transit_certificate = true;
                  }
                  $multiple_commodities = isset($app_rows['multi_item']) ? $app_rows['multi_item'] : 0;
                  $support_document = isset($app_rows['print_support']) ? $app_rows['print_support'] : 0;
                  $product_id = isset($app_rows['commodity_id']) ? $app_rows['commodity_id'] : '';
                  $prorows = ProductInfo($product_id, $con);
                  $proName = isset($prorows['name']) ? $prorows['name'] : '';
                  $scientific_name = isset($app_rows['name_scientific']) ? $app_rows['name_scientific'] : '';
                  $number_description = isset($app_rows['commodity_description']) ? $app_rows['commodity_description'] : '';
                  $nquantity = isset($app_rows['quantity_net']) ? $app_rows['quantity_net'] : '';
                  $gquantity = isset($app_rows['quantity_gross']) ? $app_rows['quantity_gross'] : '';
                  $unitid = isset($app_rows['unit_id']) ? $app_rows['unit_id'] : '';
                  $distinguishing_marks = isset($app_rows['marks_item']) ? $app_rows['marks_item'] : '';
                  $countryid_origin = isset($app_rows['place_origin']) ? $app_rows['place_origin'] : '';
                  $conveyanceid = isset($app_rows['conveyance_id']) ? $app_rows['conveyance_id'] : '';
                  $conveyance_sign = isset($app_rows['conveyance_sign']) ? $app_rows['conveyance_sign'] : '';
                  $exporter_address = isset($app_rows['address_exporter']) ? $app_rows['address_exporter'] : '';
                  $importer_address = isset($app_rows['address_importer']) ? $app_rows['address_importer'] : '';
                  $purposeid = isset($app_rows['purpose']) ? $app_rows['purpose'] : '';
                  $provinceid_quarantine = isset($app_rows['place_quarantine']) ? $app_rows['place_quarantine'] : '';
                  $provinceid_treatment = isset($app_rows['place_treatment']) ? $app_rows['place_treatment'] : '';
                  $place_of_quarantine_other = isset($app_rows['place_quarantine_other']) ? $app_rows['place_quarantine_other'] : '';
                  $place_of_treatment_other = isset($app_rows['place_treatment_other']) ? $app_rows['place_treatment_other'] : '';
                  $certificate_date = isset($app_rows['date_certificate']) ? $app_rows['date_certificate'] : '';

              }
          } // End of if - check Null
        }   // END of EDIT/UPDATE Application- isset($_GET['appid_edit'])   
      
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1>Application</h1>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php">Home</a></li>
          <li class="breadcrumb-item"><a href="transaction.php?part=exportentity_list">Export entity</a></li>
          <li class="breadcrumb-item active">Application</li>
        </ol>
        </div>
         <a href="main.php?btn=cancelApp&appid=<?php echo isset($app_id) ? $app_id : ''; ?>" class="btn btn-secondary btn-sm ms-3<?php echo (isset($btnSubmit) && $btnSubmit === 'update') ? ' disabled' : ''; ?>"
   <?php if (isset($btnSubmit) && $btnSubmit === 'update') echo 'tabindex="-1" aria-disabled="true" onclick="return false;"'; ?>>Cancel</a>
      </nav>
    </div><!-- End Application -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Application Form</h5>
               <!-- FORM: Entity/Company Form -->
              <form action="main.php" method="POST">
                <!-- Hidden input to store application ID -->
                <input type="hidden" name="app_id" value="<?php             
                                          if (!empty($app_id)) {
                                              echo $app_id;
                                          } elseif (isset($_GET['appid_edit']) && is_numeric($_GET['appid_edit']) && $_GET['appid_edit'] > 0) {
                                              echo (int)$_GET['appid_edit'];
                                          } else {
                                              echo '';
                                          } 
                                          ?>">
                <div class="row mb-3 align-items-center">
                  <!-- Application No -->
                  <label class="col-sm-2 col-form-label">Application No</label>
                  <div class="col-sm-3">
                    <input type="text" name="application_no" id="application_no" class="form-control" value="<?php echo isset($app_no) ? $app_no : ''; ?>" readonly>
                  </div>
                  <!-- Application Date -->
                  <label class="col-sm-1 col-form-label">Date</label>
                  <div class="col-sm-3">
                    <input type="text" name="app_date" class="form-control" value="<?php echo date('d/m/Y', strtotime($date)); ?>" readonly>
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
                    <select class="form-select" name="import_country" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectCountry($countryid, $con); ?>
                    </select>
                  </div>
                  <label class="col-sm-2 col-form-label">Import entry point</label>
                  <div class="col-sm-4">
  <textarea class="form-control" name="import_point" rows="2" placeholder="Enter import entry point"><?php echo isset($import_point) ? $import_point : ''; ?></textarea>
</div>
                </div>

                <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">Export certificate</label>
                  <div class="col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="export_certificate" id="export_certificate" value="1" <?php echo (isset($export_certificate) && $export_certificate) ? 'checked' : ''; ?>>
                    <label for="export_certificate" class="ms-2 mb-0">Yes (<i class="bi bi-check-lg"></i>)</label>
                  </div>
                  <label class="col-sm-2 col-form-label">Transit certificate</label>
                  <div class="col-sm-2 d-flex align-items-center">
                    <input type="checkbox" name="transit_certificate" id="transit_certificate" value="1" <?php echo (isset($transit_certificate) && $transit_certificate) ? 'checked' : ''; ?>>
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
                 <input type="text" name="marks" id="marks" class="form-control" value="<?php echo isset($distinguishing_marks) ? $distinguishing_marks : ''; ?>">
               </div>
             </div>
             <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">Place of origin</label>
                  <div class="col-sm-10">
                    <select class="form-select" name="place_origin" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectCountry($countryid_origin, $con); ?>
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
                  <label class="col-sm-2 col-form-label">Exporter's address</label> <!-- Application -->
                  <div class="col-sm-4 d-flex align-items-start">
                      <textarea name="exporter" id="exporter" class="form-control" rows="3"><?php echo isset($exporter_address) ? $exporter_address : ''; ?></textarea>
                  </div>
                  <label class="col-sm-2 col-form-label">Importer's address</label> <!-- Application -->

                  <div class="col-sm-4 d-flex align-items-center">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#importerModal">
                      <i class="bi bi-search ms-2" style="font-size: 1.2rem;"></i>
                    </a>&nbsp;<textarea name="importer" id="importer" class="form-control" rows="3"><?php echo isset($importer_address) ? $importer_address : ''; ?></textarea>
                    <input type="hidden" name="importer_id" id="importer_id" value="<?php echo isset($importer_id) ? $importer_id : ''; ?>">
                    <div id="importer_suggestions" class="autocomplete-suggestions"></div>
                    
            </div>
            

            <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">Purpose</label>
                  <div class="col-sm-4">
                    <select class="form-select" name="purpose" id="purpose" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectPurpose($purposeid, $con); ?>
                    </select>
                  </div>
                </div>

              <div class="row mb-3 align-items-center">
                <label class="col-sm-2 col-form-label">Place of Quarantine</label>
                <div class="col-sm-4">
                  <select class="form-select" name="place_quarantine" id="place_quarantine" aria-label="Select packaging type">
                    <option value="">Select</option>
                    <?php SelectProvince($provinceid_quarantine, $con); ?> 
                  </select>
                </div>
                <label class="col-sm-1 col-form-label">Specify</label>
                <div class="col-sm-5">
                  <input type="text" name="place_quarantine_other" id="place_quarantine_other" class="form-control" value="<?php echo isset($place_of_quarantine_other) ? $place_of_quarantine_other : ''; ?>">
                </div>
            </div>
           
             <div class="row mb-3 align-items-center">
                <label class="col-sm-2 col-form-label">Place of treatment</label>
                <div class="col-sm-4">
                  <select class="form-select" name="place_treatment" id="place_treatment" aria-label="Select packaging type">
                    <option value="">Select</option>
                    <?php SelectProvince($provinceid_treatment, $con); ?> 
                  </select>
                </div>
                <label class="col-sm-1 col-form-label">Specify</label>
                <div class="col-sm-5">
                  <input type="text" name="place_treatment_other" id="place_treatment_other" class="form-control" value="<?php echo isset($place_of_treatment_other) ? $place_of_treatment_other : ''; ?>">
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
                    <button type="submit" name="btnsubApplication_save" class="btn btn-primary" value="<?php echo isset($btnSubmit) ? 'update' : 'submit'; ?>">
                      <?php echo isset($btnSubmit) ? 'Update' : 'Submit'; ?>
                    </button>
                    <button type="submit" name="btnsubApplication_save_continue" class="btn btn-secondary" value="save_continue" <?php echo (isset($btnSubmit) && $btnSubmit === 'update') ? 'disabled' : ''; ?>>
                      Save & continue
                    </button>
                   </div>
                </div>
                    
                      <!-- Modal form for Importer ************** -->
                        <div class="modal fade" id="importerModal" tabindex="-1" aria-labelledby="importerModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="importerModalLabel">Search Importer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <!-- Search box above the table -->
                                <div class="mb-3">
                                  <input type="text" id="importerSearch" class="form-control" placeholder="Search importers...">
                                </div>
                                <!-- Data table for importer list -->
                                <div class="table-responsive">
                                  <table class="table table-bordered table-striped" id="importerTable">
                                    <thead>
                                      <tr>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Zip code</th>
                                        <th>Country</th>
                                        <th>Action</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php CertificateImporterList($con); ?>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                  <!-- End Modal form for Importer -->

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
      <div id="spdocModalBody" class="modal-body">
        <!-- Present data in a word-like document style -->
        <div class="border p-3 bg-white" style="min-height:300px;">
          <a href="#" onclick="printSupportDoc('spdocModalBody'); return false;" class="position-absolute" style="top: 10px; right: 15px; color: #333;" title="Print">
            <i class="bi bi-printer" style="font-size: 1.5rem;"></i>
          </a>
           <div class="text-center mb-3">
            <img src="assets/img/national_logo.jpg" alt="National Logo" style="max-height:80px;">
          </div>
          <h6 class="text-center" style="white-space: pre-line;"><b>LAO PEOPLE'S DEMOCRATIC REPUBLIC</b>
            PEACE INDEPENDENCE DEMOCRACY UNITY PROSPERITY
            MINISTRY OF AGRICULTURE AND FORESTRY
            DEPARTMENT OF AGRICULTURE
            LIST OF CONSIGNMENT
            FOR PHYTOSANITARY CERTIFICATE No: <?php echo isset($certificate_no) ? $certificate_no : ''; ?>
          </h6>
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

      // Search/Filter importer **************
      const importerSearch = document.getElementById('importerSearch');
      const importerTable = document.getElementById('importerTable');
      if (importerSearch && importerTable) {
        importerSearch.addEventListener('keyup', function() {
          const filter = importerSearch.value.toLowerCase();
          const rows = importerTable.querySelectorAll('tbody tr');
          rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
          });
        });
      }

  });

  // Print supporting document: DIV id = spdocModalBody
  function printSupportDoc(divId) {
    var printContents = document.getElementById(divId).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); // Optional: reload to restore JS and events
  }

</script>
  <?php
    }  // End of Export Entity Form - $_GET['part'] === 'application'
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
     <!-- ***************INSPECTION *************** -->
     <?php
      if (isset($_GET['part']) && $_GET['part'] === 'inspection') { // Open form -Get link from main.php - dashboard
        // Code for inspection part
            if($_GET['inspect'] == 'Add'){
             // echo "<script>alert('Inspection - Add.');</script>";
                // Button state
                $btnSubmit = 'submit';
                $appid_inspection = isset($_GET['appid']) ? (int)$_GET['appid'] : 0;
                $approws = ApplicationInfo($appid_inspection, $con);
                if ($approws) {
                  $appno_inspection = $approws['application_no']; // Application No, not ID
                  $entity_id = $approws['company_id'];
                  $entity_rows = GetEntityExport($entity_id, $con);
                  $entityexport_name = $entity_rows['title'];
                }
            } elseif ($_GET['inspect'] == 'View/Edit') {
                $appid_inspection = isset($_GET['appid']) ? (int)$_GET['appid'] : 0;
                $insprows = InspectionInfo($appid_inspection, $con);
                if ($insprows) {
                  // Button state
                  $btnSubmit = 'update';
                  // Populate inspection fields
                  $appid = $insprows['application_id'];
                  $appno_inspection = ApplicationInfo($appid, $con)['application_no'];
                  $entity_id = ApplicationInfo($appid, $con)['company_id'];
                  $entityexport_name = GetEntityExport($entity_id, $con)['title'];
                  $inspection_date = $insprows['inspection_date'];
                  $sampleno = $insprows['sample_no'];
                  $sample_volume = $insprows['sample_quantity'];
                  $unitid = $insprows['unit_id'];
                  $sample_collectedby = $insprows['sample_collected_by'];
                  $sample_inspected = $insprows['inspected_by'];
                  $certificate_fee = $insprows['certificate_fee'];
                  $receipt_no = $insprows['receipt_no'];
                  $lot_no = $insprows['lot_number'];
                  $inspection_method = $insprows['inspection_method'];
                  $detected_pest = $insprows['pest_detected'];
                  $treatment_ability = $insprows['treat_ability'];
                  $lab_analysis = $insprows['lab_required'];
                  $treatment_method = $insprows['treatment_method'];
                  $treatment_date = $insprows['treatment_date'];
                  $chemical_used = $insprows['chemical_used'];
                  $chemical_fortreat = $insprows['chemical_fortreat'];
                  $duration_temp = $insprows['duration_temp'];
                  $concentration = $insprows['concentration'];
                  $sample_inspectedby = $insprows['sample_inspectedby'];
                  $additional_info = $insprows['additional_info'];
                  $reason = $insprows['treatment_reason'];
                  $post_details = $insprows['post_treatment_details'];
                
            }
        } else {
            // Invalid action
            echo "<div class='alert alert-danger'>Invalid action specified.</div>";
            exit;

        }
      ?>
     <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Inspection</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="main.php">Home</a></li>
            <li class="breadcrumb-item"><a href="transaction.php?part=exportentity_list">Export entity</a></li>
            <li class="breadcrumb-item active">Inspection</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

<!-- ********* Inspection form *********** -->
<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Inspection Form</h5>
          <!-- FORM: Inspection Form -->
          <form id="inspectionFormID" action="main.php" method="POST">
            <!-- Hidden input to store application ID -->
            <input type="hidden" name="appid" value="<?php echo $appid_inspection; ?>">
             <div class="row mb-3 align-items-center">
                  <!-- Application No -->
                  <label class="col-sm-2 col-form-label">Application No</label>
                  <div class="col-sm-2">
                    <input type="text" name="appno_insp" id="appno_insp" class="form-control" style="background-color: #2ec691ff;" value="<?php echo isset($appno_inspection) ? $appno_inspection : ''; ?>" readonly >
                  </div>
                  <!-- Entity's name -->
                  <label class="col-sm-2 col-form-label">Entity's Name</label>
                  <div class="col-sm-6">
                    <input type="text" name="entity_name" class="form-control" style="background-color: #f0f0f0;" value="<?php echo isset($entityexport_name) ? $entityexport_name : ''; ?>" readonly >
                  </div>  
                </div>

            <div class="row mb-3">
              <label for="inspection_date" class="col-sm-2 col-form-label">Inspection Date</label>
              <div class="col-sm-4">
                <input type="date" class="form-control" id="inspection_date" name="inspection_date" value="<?php echo isset($inspection_date) ? $inspection_date : ''; ?>">
              </div>
            </div>
           
            <div class="row mb-3 align-items-center">
                <label for="sampleno" class="col-sm-2 col-form-label">Sample No</label>
                <div class="col-sm-2">
                  <input type="text" name="sampleno" id="sampleno" class="form-control" value="<?php echo isset($sampleno) ? $sampleno : ''; ?>">
                </div>
                <label for="sample_volume" class="col-sm-2 col-form-label">Sample Volume</label>
                <div class="col-sm-2">
                  <input type="number" step="0.01" min="0" name="sample_volume" id="sample_volume" class="form-control" value="<?php echo isset($sample_volume) ? $sample_volume : ''; ?>">
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
              <label for="sample_collectedby" class="col-sm-2 col-form-label">Sample collected by</label>
               <div class="col-sm-10">
                 <input type="text" name="sample_collectedby" id="sample_collectedby" class="form-control" value="<?php echo isset($sample_collectedby) ? $sample_collectedby : ''; ?>">
               </div>
             </div>
             <div class="row mb-3">
              <label for="sample_inspected" class="col-sm-2 col-form-label">Inspected by</label>
               <div class="col-sm-10">
                 <input type="text" name="sample_inspectedby" id="sample_inspectedby" class="form-control" value="<?php echo isset($sample_inspected) ? $sample_inspected : ''; ?>">
               </div>
             </div>

             <div class="row mb-3 align-items-center">
                  <!-- Certificate fee -->
                  <label class="col-sm-2 col-form-label">Certificate fee</label>
                  <div class="col-sm-4">
                    <input type="number" name="certificate_fee" id="certificate_fee" class="form-control" value="<?php echo isset($certificate_fee) ? $certificate_fee : ''; ?>" >
                  </div>
                  <!-- Receipt No -->
                  <label class="col-sm-2 col-form-label">Receipt No</label>
                  <div class="col-sm-4">
                    <input type="text" name="receipt_no" class="form-control" value="<?php echo isset($receipt_no) ? $receipt_no : ''; ?>" >
                  </div>  
              </div>

              <div class="row mb-3 align-items-center">
                  <!-- Lot Number -->
                  <label class="col-sm-2 col-form-label">Lot No</label>
                  <div class="col-sm-2">
                    <input type="text" name="lot_no" id="lot_no" class="form-control" value="<?php echo isset($lot_no) ? $lot_no : ''; ?>" >
                  </div>
              </div>

              <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">Inspection Method</label>
                  <div class="col-sm-10">
                    <select class="form-select" name="inspection_method" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectInspectionMethod($inspection_method, $con); ?>
                    </select>
                  </div>
              </div>  

              <div class="row mb-3 align-items-center">
                <label class="col-sm-2 col-form-label">Inspection Findings</label>
                <div class="col-sm-10">
                  <div class="form-check mb-2">
                    <input class="form-check-input border border-success" type="checkbox" name="detected_pest" id="detected_pest" style="width: 1.5em; height: 1.5em;" value="1" <?php if (isset($detected_pest) && $detected_pest) echo 'checked'; ?>>
                    <label class="form-check-label" for="detected_pest">&nbsp;Detected pest</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input border border-warning" type="checkbox" name="treatment_ability" id="treatment_ability" style="width: 1.5em; height: 1.5em;" value="1" <?php if (isset($treatment_ability) && $treatment_ability) echo 'checked'; ?>>
                    <label class="form-check-label" for="treatment_ability">&nbsp;Treatment ability</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input border-primary" type="checkbox" name="lab_analysis" id="lab_analysis" style="width: 1.5em; height: 1.5em;" value="1" <?php if (isset($lab_analysis) && $lab_analysis) echo 'checked'; ?>>
                    <label class="form-check-label" for="lab_analysis">&nbsp;Lab analysis required</label>
                  </div>
                </div>
              </div>

               <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">Treatment Method</label>
                  <div class="col-sm-10">
                    <select class="form-select" name="treatment_method" aria-label="Default select example">
                      <option selected></option>
                      <?php SelectTreatmentMethod($treatment_method, $con); ?>
                    </select>
                  </div>
              </div> 
              
              <div class="row mb-3">
                <label for="treatment_date" class="col-sm-2 col-form-label">Treatment Date</label>
                <div class="col-sm-4">
                  <input type="date" class="form-control" name="treatment_date" id="treatment_date"
                    value="<?php echo isset($treatment_date) && $treatment_date ? date('Y-m-d', strtotime($treatment_date)) : ''; ?>">
                  <?php if (!empty($treatment_date)) { ?>
                    <small class="text-muted">Selected: <?php echo date('d/m/Y', strtotime($treatment_date)); ?></small>
                  <?php } ?>
                </div>
            </div>

          <div class="card mb-4">
          <div class="card-header">
            <strong>Details of treatment</strong>
          </div>
          <div class="card-body">
            <div class="row mb-3 align-items-center">
          <label class="col-sm-2 col-form-label">Chemical Used</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="chemical_used" id="chemical_used" value="<?php echo isset($chemical_used) ? $chemical_used : ''; ?>">
          </div>
          <label class="col-sm-2 col-form-label">Treated by</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="chemical_fortreat" id="chemical_fortreat" value="<?php echo isset($chemical_fortreat) ? $chemical_fortreat : ''; ?>">
          </div>
        </div>
        <div class="row mb-3 align-items-center">
          <label class="col-sm-2 col-form-label">Duration - Temperature</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="duration_temp" id="duration_temp" placeholder="e.g., 30 minutes - 50°C" value="<?php echo isset($duration_temp) ? $duration_temp : ''; ?>">
          </div>
          <label class="col-sm-2 col-form-label">Concentration</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="concentration" id="concentration" placeholder="e.g., 0.5%" value="<?php echo isset($concentration) ? $concentration : ''; ?>">
          </div>
        </div>
        <div class="row mb-3 align-items-center">
          <label class="col-sm-2 col-form-label">Sample Inspected by</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="sample_inspectedby" id="sample_inspectedby" value="<?php echo isset($sample_inspectedby) ? $sample_inspectedby : ''; ?>">
          </div>
        </div>
            <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label">Additional information</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="additional_info" id="additional_info" placeholder="Enter additional information" value="<?php echo isset($additional_info) ? $additional_info : ''; ?>">
              </div>
            </div>
            <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label">Reason</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="reason" id="reason" placeholder="Enter reason" value="<?php echo isset($reason) ? $reason : ''; ?>">
              </div>
            </div>
            <div class="row mb-3 align-items-center">
            <label class="col-sm-2 col-form-label">Post Treatment Details</label>
            <div class="col-sm-10">
              <textarea class="form-control" name="post_details" id="post_details" rows="3" placeholder="Enter post treatment details"><?php echo isset($post_details) ? htmlspecialchars($post_details) : ''; ?></textarea>
            </div>
          </div>
        </div> <!-- details of treatment -->
        </div>
          <div class="row mb-3">
            <div class="col-sm-10 offset-sm-2 d-flex gap-2">
              <button type="submit" name="btnSubmitInspection" value="<?php echo $btnSubmit === 'update' ? 'update' : 'submit'; ?>" class="btn btn-success">
                <i class="bi bi-save"></i><?php echo $btnSubmit === 'update' ? ' Update' : ' Submit'; ?>
              </button>
              <a href="main.php" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancel
              </a>
            </div>
          </div>
      </form> <!-- End Form for Inspection -->
     </div>
    </div>
    </div>
    </div>
  </section>
   <?php
    }  // End of if- Inspection
  ?>
 <!-- ***************CERTIFICATE *************** -->
 <?php
   if (isset($_GET['part']) && $_GET['part'] === 'certificate') { // Open form -Get link from main.php - dashboard
        
            $appid_certificate = isset($_GET['appid']) ? (int)$_GET['appid'] : 0;
            $application_no = ApplicationInfo($appid_certificate, $con)['application_no'];
            $import_country_id = ApplicationInfo($appid_certificate, $con)['country_import'];
            $import_country = CountryInfo($import_country_id, $con)['title'];
            $import_point = ApplicationInfo($appid_certificate, $con)['import_point'];
            $uid = ApplicationInfo($appid_certificate, $con)['uid'];
            $locationid = Userdata($uid, $con)['location_id'];
            $place_issue = Locationname($locationid, $con);
            $export_pointid = ApplicationInfo($appid_certificate, $con)['export_point'];
            $export_point = Locationname($export_pointid, $con);
            $exporterid = ApplicationInfo($appid_certificate, $con)['company_id'];
            // Get exporter details
            $exporter_name = GetEntityExport($exporterid, $con)['title'];
            $exporter_address = GetEntityExport($exporterid, $con)['address'];
            $provinceid = GetEntityExport($exporterid, $con)['province'];
            $districtid = GetEntityExport($exporterid, $con)['district'];
            $phone = GetEntityExport($exporterid, $con)['phone'];
            $email = GetEntityExport($exporterid, $con)['email'];
            // Importer details
            $import_countryid = ApplicationInfo($appid_certificate, $con)['country_import'];
            $import_country = CountryInfo($import_countryid, $con)['title'];

    // ADD NEW CERTIFICATE ************
         if($_GET['certify'] == 'Add'){   
            // create new certificate number 
            $btnSubmitCertificate = 'submit';
            list($certificate_id, $certificate_no) = CertificateNo($appid_certificate, $userid, $guid, $con);
            $current_date = date('Y-m-d');
            // Button state     //
         } else if ($_GET['certify'] == 'View/Edit') {
            $btnSubmitCertificate = 'update';
            $certrows = CertificateInfo($appid_certificate, $con);
            if ($certrows) {
              // Button state
             // $btnSubmit = 'update';
              // Populate certificate fields
              $certificate_id = $certrows['id'];
              $certificate_no = $certrows['certificate_no'];
              $carbonpaper_id = $certrows['carbonpaper_id'];
              $approved_by = $certrows['approved_by'];
              $approver_position = $certrows['position_approved'];
              $place_issued = $certrows['place_issued'];
              $consignment_value = $certrows['consignment_value'];
              $value_currency = $certrows['value_currency'];
              $additional_scientificname = $certrows['additional_scientificname'];
              $additional_declaration = $certrows['additional_declaration'];
              $date_issued = $certrows['date_issued'];
                if ($date_issued == '0000-00-00' || is_null($date_issued)) {
                    $date_issued = '';
                }
            } // End of populate certificate fields
         } else {
            // Invalid action
            echo "<div class='alert alert-danger'>Invalid action specified.</div>";
            exit;
         }
          
        
 ?>
  <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Certificate</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="main.php">Home</a></li>
            <li class="breadcrumb-item"><a href="transaction.php?part=exportentity_list">Export entity</a></li>
            <li class="breadcrumb-item active">Certificate</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->
    <section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Certificate Form</h5>
          <form id="certificateFormID" action="main.php" method="POST">
            <!-- Certificate ID (hidden) -->
            <input type="hidden" name="certificate_id" value="<?php echo isset($certificate_id) ? $certificate_id : ''; ?>">

            <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label">Cerificate No</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="certificate_no" id="certificate_no" required value="<?php echo isset($certificate_no) ? $certificate_no : ''; ?>" readonly>
              </div>
              <label class="col-sm-2 col-form-label">Application No</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="application_no" id="application_no" value="<?php echo isset($application_no) ? $application_no : ''; ?>" readonly>
              </div>
            </div>

             <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label">Import country</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="import_country" id="import_country" required value="<?php echo isset($import_country) ? $import_country : ''; ?>" readonly>
              </div>
              <label class="col-sm-2 col-form-label">Import entry point</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="import_entrypoint" id="import_entrypoint" required value="<?php echo isset($import_point) ? $import_point : ''; ?>">
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label">Place of Issue</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="place_issue" id="place_issue" required value="<?php echo isset($place_issue) ? $place_issue : ''; ?>">
              </div>
              <label class="col-sm-2 col-form-label">Export entry point</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="export_entrypoint" id="export_entrypoint" required value="<?php echo isset($export_point) ? $export_point : ''; ?>">
              </div>
            </div>

            <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">Exporter's name and address</label> <!-- Certificate-->
                  <div class="col-sm-4 d-flex align-items-start">
                      <input type="text" class="form-control" name="exporter_name" id="exporter_name" class="form-control" value="<?php echo isset($exporter_name) ? $exporter_name : ''; ?>"></input>
                  </div>
                  <label class="col-sm-2 col-form-label">Importer's name and address</label> <!-- Certificate-->
                  <div class="col-sm-4 d-flex align-items-center position-relative">
                    <!--
                    <a href="#" data-bs-toggle="modal" data-bs-target="#importerModal">
                      <i class="bi bi-search ms-2" style="font-size: 1.2rem;"></i>
                    </a>&nbsp;
                    -->
                    <input type="text" class="form-control" name="importer_name" id="importer_name" required 
                             value="<?php echo isset($importer_name) ? $importer_name : ''; ?>">
                    <!--
                    <input type="hidden" name="importer_id" id="importer_id" value="<?php echo isset($importer_id) ? $importer_id : ''; ?>">
                    <div id="importer_suggestions" class="autocomplete-suggestions"></div>
                    -->
                  </div>
            </div>

            <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">&nbsp;</label>
                  <div class="col-sm-4 d-flex align-items-start">
                      <textarea name="exporter_address" id="exporter_address" class="form-control" rows="3"><?php echo isset($exporter_address) ? $exporter_address : ''; ?></textarea>
                  </div>
                  <label class="col-sm-2 col-form-label">&nbsp;</label>
                  <div class="col-sm-4">
                    <textarea name="importer_address" id="importer_address" class="form-control" rows="3"><?php echo isset($importer_address) ? $importer_address : ''; ?></textarea>
                  </div>
            </div>
            
             <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label">&nbsp;</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" class="form-control" name="exporter_oncertificate" id="exporter_oncertificate" placeholder="Name on Certificate" required value="<?php echo isset($exporter_oncertificate) ? $exporter_oncertificate : ''; ?>" style="font-style: italic;">
              </div>
              <label class="col-sm-2 col-form-label">&nbsp;</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="importer_oncertificate" id="importer_oncertificate" placeholder="Name on Certificate" required value="<?php echo isset($importer_oncertificate) ? $importer_oncertificate : ''; ?>" style="font-style: italic;">
              </div>
            </div>

             <div class="row mb-3 align-items-center"> 
              <label class="col-sm-2 col-form-label">Carbon Paper No</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="carbonpaper_id" id="carbonpaper_id" required value="<?php echo isset($carbonpaper_id) ? $carbonpaper_id : ''; ?>">
              </div>
            </div>

            
            <div class="row mb-3 align-items-center">
                  <label class="col-sm-2 col-form-label">Approved by</label>
                  <div class="col-sm-4">
                    <select class="form-select" name="approved_by" aria-label="Select approver">
                      <option value="">Select approver...</option>
                      <?php CertificateApprovedBy($con); ?>
                    </select>
                  </div>
              <label class="col-sm-2 col-form-label">Approver's position</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="approver_position" id="approver_position" required value="<?php echo isset($approver_position) ? $approver_position : ''; ?>">
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label">Consignment Value</label>
              <div class="col-sm-4">
                <input type="number" step="0.01" class="form-control" name="consignment_value" id="consignment_value" required value="<?php echo isset($consignment_value) ? $consignment_value : ''; ?>">
              </div>
              <label class="col-sm-2 col-form-label">Value Currency</label>
              <div class="col-sm-4">
                <select class="form-select" name="value_currency" aria-label="Select currency">
                  <option value="">Select currency...</option>
                  <?php SelectCurrency($value_currency, $con); ?>
                </select>
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label class="col-sm-2 col-form-label">Another Scientific Name</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="another_scientificname" id="another_scientificname" value="<?php echo isset($another_scientificname) ? $another_scientificname : ''; ?>">
              </div>
            </div>

             <div class="row mb-3 align-items-center">
            <label class="col-sm-2 col-form-label">Additional Declaration</label>
            <div class="col-sm-10">
              <textarea class="form-control" name="additional_declaration" id="additional_declaration" rows="3" placeholder="Enter additional declaration"><?php echo isset($additional_declaration) ? htmlspecialchars($additional_declaration) : ''; ?></textarea>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-10 offset-sm-2 d-flex gap-2">
                <button type="submit" name="btnSubmitCertificate" class="btn btn-primary" value="<?php echo $btnSubmitCertificate === 'update' ? 'update' : 'submit'; ?>">
                  <i class="bi bi-save"></i> <?php echo $btnSubmitCertificate === 'update' ? ' Update' : ' Submit'; ?>
                </button>
                <a href="main.php" class="btn btn-secondary">
                  <i class="bi bi-x-circle"></i> Cancel
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  </section>
  <?php
      } // End of if- Certificate
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

  <!-- Importer Autocomplete Script -->
  <script>
   /*
    $(document).ready(function() {
      console.log('Autocomplete script loaded'); // Debug line
      
      let searchTimeout;
      let selectedIndex = -1;
      
      // Make sure we target all importer_name inputs
      $(document).on('input', '#importer_name', function() {
        console.log('Input detected: ' + $(this).val()); // Debug line
        
        const searchTerm = $(this).val().trim();
        const inputElement = $(this);
        const suggestionsContainer = inputElement.siblings('#importer_suggestions').length > 0 
          ? inputElement.siblings('#importer_suggestions') 
          : inputElement.parent().find('#importer_suggestions');
        
        clearTimeout(searchTimeout);
        
        if (searchTerm.length >= 1) {
          console.log('Searching for: ' + searchTerm); // Debug line
          searchTimeout = setTimeout(function() {
            searchImporters(searchTerm, suggestionsContainer);
          }, 300); // Delay to avoid too many requests
        } else {
          suggestionsContainer.hide().empty();
          selectedIndex = -1;
        }
      });
      
      // Handle keyboard navigation
      $(document).on('keydown', '#importer_name', function(e) {
        const inputElement = $(this);
        const suggestionsContainer = inputElement.siblings('#importer_suggestions').length > 0 
          ? inputElement.siblings('#importer_suggestions') 
          : inputElement.parent().find('#importer_suggestions');
        const suggestions = suggestionsContainer.find('.autocomplete-suggestion');
        
        if (e.which === 40) { // Down arrow
          e.preventDefault();
          selectedIndex = Math.min(selectedIndex + 1, suggestions.length - 1);
          updateSelection(suggestions);
        } else if (e.which === 38) { // Up arrow
          e.preventDefault();
          selectedIndex = Math.max(selectedIndex - 1, -1);
          updateSelection(suggestions);
        } else if (e.which === 13) { // Enter key
          e.preventDefault();
          if (selectedIndex >= 0 && suggestions.length > 0) {
            selectSuggestion(suggestions.eq(selectedIndex), inputElement);
          }
        } else if (e.which === 27) { // Escape key
          suggestionsContainer.hide();
          selectedIndex = -1;
        }
      });
      
      // Hide suggestions when clicking outside
      $(document).on('click', function(e) {
        if (!$(e.target).closest('#importer_name, #importer_suggestions').length) {
          $('#importer_suggestions').hide();
          selectedIndex = -1;
        }
      });
      
      function searchImporters(term, container) {
        console.log('Making AJAX request for: ' + term); // Debug line
        
        $.ajax({
          url: window.location.href,
          type: 'POST',
          dataType: 'json',
          data: {
            action: 'search_importer',
            term: term
          },
          success: function(response) {
            console.log('AJAX response:', response); // Debug line
            displaySuggestions(response, container);
          },
          error: function(xhr, status, error) {
            console.error('AJAX error:', status, error); // Debug line
            container.hide().empty();
          }
        });
      }
      
      function displaySuggestions(importers, container) {
        container.empty();
        selectedIndex = -1;
        
        if (importers.length > 0) {
          importers.forEach(function(importer, index) {
            const suggestion = $('<div class="autocomplete-suggestion" data-index="' + index + '">')
              .html('<div class="suggestion-title">' + escapeHtml(importer.title) + '</div>' +
                    (importer.address ? '<div class="suggestion-address">' + escapeHtml(importer.address) + '</div>' : ''))
              .data('full-text', importer.full_text);
            
            suggestion.on('click', function() {
              const inputElement = container.siblings('#importer_name').length > 0 
                ? container.siblings('#importer_name') 
                : container.parent().find('#importer_name');
              selectSuggestion($(this), inputElement);
            });
            
            container.append(suggestion);
          });
          
          container.show();
        } else {
          container.hide();
        }
      }
      
      function updateSelection(suggestions) {
        suggestions.removeClass('active');
        if (selectedIndex >= 0 && selectedIndex < suggestions.length) {
          suggestions.eq(selectedIndex).addClass('active');
        }
      }
      
      function selectSuggestion(suggestion, inputElement) {
        const fullText = suggestion.data('full-text');
        inputElement.val(fullText);
        suggestion.closest('#importer_suggestions').hide();
        selectedIndex = -1;
      }
      
      function escapeHtml(text) {
        return $('<div>').text(text).html();
      }
    });
    */
  </script>

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