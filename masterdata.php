<?php
      // Pk: 2025-04-30
  session_start();

  require("php-bin/connection.php"); // replace include with require
  require("php-bin/supports.php"); // replace include with require

  $loginuser = isset($_SESSION["username"]) ? $_SESSION["username"] : ''; // use email or username
  $uname = isset($_SESSION['uname']) ? $_SESSION['uname'] : ''; // Name of user
  //echo "<script>alert('uname: " . $uname . "');</script>"; // Debugging line
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Master data</title>
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
      <a href="main.php?us=<?php echo $uname; ?>" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">ePhytosanitary Certificate</span>
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
              <span>National IT Consultant</span>
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
              <a class="dropdown-item d-flex align-items-center" href="index.php">
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
        <a class="nav-link collapsed" href="main.php?us=<?php echo $uname; ?>" >
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->
      <li class="nav-item">
        <a class="nav-link active" href="entity.php?entity=export" >
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

      <!-- Module Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Modules</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="modules.php?part=entity">
              <i class="bi bi-circle"></i><span>Entity/Company</span>
            </a>
          </li>
          <li>
            <a href="modules.php?part=inspection">
              <i class="bi bi-circle"></i><span>Inspection</span>
            </a>
          </li>
          <li>
            <a href="modules.php?part=sample">
              <i class="bi bi-circle"></i><span>Sample</span>
            </a>
          </li>
          <li>
            <a href="modules.php?part=certificate">
              <i class="bi bi-circle"></i><span>Certificate</span>
            </a>
          </li>
          <li>
            <a href="modules.php?part=printing">
              <i class="bi bi-circle"></i><span>Printing</span>
            </a>
          </li>
        </ul>
      </li><!-- End Forms Nav -->

       <?php
          $masterParts = ['countries', 'locations', 'provinces','product', 'productgroup', 'productunit', 'conveyance', 'inspectionmethod','treatmentmethod', 'entitytype', 'modules']; // Add all relevant parts here
          $isMasterActive = (isset($_GET['part']) && in_array($_GET['part'], $masterParts));
      ?>
      <li class="nav-item">
        <a class="nav-link <?php echo $isMasterActive ? '' : 'collapsed'; ?>" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Master data</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse<?php echo $isMasterActive ? ' show' : ''; ?>" data-bs-parent="#sidebar-nav">
         
          <li>
            <a href="masterdata.php?part=product" class="<?php echo (isset($_GET['part']) && ($_GET['part'] === 'product' || $_GET['part'] ==='productgroup' || $_GET['part'] ==='productunit')) ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Product</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=conveyance" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'conveyance') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Conveyance</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=countries" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'countries') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Countries</span>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="bi bi-circle"></i><span>Districts</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=entitytype" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'entitytype') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Entity_type</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=inspectionmethod" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'inspectionmethod') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Inspection Method</span>
            </a>
          </li>

          <li>
            <a href="masterdata.php?part=locations" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'locations') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Locations</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=modules" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'modules') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Module List</span>
            </a>  
          <li>
            <a href="#">
              <i class="bi bi-circle"></i><span>Provinces</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=treatmentmethod" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'treatmentmethod') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Treatment Method</span>
            </a>
          </li>
        </ul>
      </li><!-- End Tables Nav -->

      <li class="nav-heading">Pages</li>

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
      </li><!-- End User Group permit -->

      <li class="nav-item">
        <a class="nav-link active" href="users.php?part=userslist">
          <i class="bi bi-person-plus"></i><span>Users</span>
        </a>
      </li>  <!-- End Users Nav -->
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
      <h1>Locations</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?us=<?php echo $uname; ?>">Home</a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item active"><a href="masterdata.php?part=locations">Locations</a></li>
        </ol>
      </nav>
      </div>
      <!--
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addGroupModal" data-gid="new">
          <i class="bi bi-plus-circle"></i> Add New Location
        </button>
      </div>
     -->
      <div>
          <a href="masterdata.php?loc=new" class="btn btn-success btn-sm" role="button">
            <i class="bi bi-plus-circle"></i> Add New Location
          </a>
      </div>
    </div><!-- End Page Title -->
    
    <section class="section"> <!-- DATA TABLE - Locations -->
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              
              <h5 class="card-title">Locations</h5>
              <p>ePhytosanitary by Department of Agriculture, MAF - Locations</p>

              <!-- Table with stripped rows -->
               
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b>N</b>o</th>
                   <th>Name(English)</th>
                   <th>Name(Lao)</th>
                   <th>Type</th>
                   <th>District</th>
                   <th>Province</th>
                   <th>Status</th>
                   <th>Edit</th>
                   <th>Delete</th>
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
        <h1>Add/Update Location</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="main.php?us=<?php echo $uname; ?>">Home</a></li>
            <li class="breadcrumb-item">Forms</li>
            <li class="breadcrumb-item active">Locations</li>
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
              <form action="masterdata.php?part=locations" method="POST">
                <!-- Hidden input for id : location  ID -->
                <input type="hidden" id="hlid" name="hlid" value="<?php echo isset($id) ? $id : ''; ?>">
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Location ID</label>
                  <div class="col-sm-10">
                    <input type="text" name="locationid" id="locationid" class="form-control" value="<?php echo isset($locid) ? $locid : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">English Name</label>
                  <div class="col-sm-10">
                    <input type="text" name="nameeng" id="nameeng" class="form-control" value="<?php echo isset($nameeng) ? $nameeng : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Lao Name</label>
                  <div class="col-sm-10">
                    <input type="text" name="namelao" id="namelao" class="form-control" value="<?php echo isset($namelao) ? $namelao : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Location Type</label>
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
                  <label class="col-sm-2 col-form-label">Province</label>
                  <div class="col-sm-5">
                    <select class="form-select" name="province" id="province" aria-label="Default select example" onchange="SelectProvinceOnChange(this)">
                     <option value="">*** Please select one ***</option>
                      <?php SelectProvinces($pid, $con); ?>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">District</label>
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
            echo "<script>window.location.href='masterdata.php?part=locations';</script>"; // Redirect to locations page
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
      <h1>Countries</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?us=<?php echo $uname; ?>">Home</a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item active">Countries</li>
        </ol>
      </nav>
      </div>
      
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCountryModal" data-cid="new">
          <i class="bi bi-plus-circle"></i>Add New Country
        </button>
      </div> 
    </div><!-- End Page Title -->
    <!-- == Modal form - Countries == -->
      <div class="modal fade" id="addCountryModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addGroupModalLabel"><b>Add New Country</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for cid -->
                <input type="hidden" id="countryId" name="countryId">
                <div class="mb-3">
                  <label for="countryName" class="form-label">Country Name</label>
                  <input type="text" class="form-control" id="countryName" name="countrypName" required>
                </div>
                <div class="mb-3">
                  <label for="alphaCode" class="form-label">Alpha code</label>
                  <input type="text" class="form-control" id="alphaCode" name="alphaCode" required>
                </div>
                <div class="mb-3">
                  <label for="numCode" class="form-label">Numeric code</label>
                  <input type="text" class="form-control" id="numCode" name="numCode" required>
                </div>
                <div class="mb-3">
                  <label for="currency" class="form-label">Currency</label>
                  <input type="text" class="form-control" id="currency" name="currency">
                </div>
                <div class="mb-3">
                  <label for="countryDescription" class="form-label">Description</label>
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
              
              <h5 class="card-title">Countries</h5>
              <p>ePhytosanitary by Department of Agriculture, MAF - Countries</p>

              <!-- Table with stripped rows -->
               
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b>N</b>o</th>
                   <th>Name</th>
                   <th>Alpha code</th>
                   <th>Numeric code</th>
                   <th>Currency</th>
                   <th>Status</th>
                   <th>Edit</th>
                   <th>Delete</th>
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
            echo "<script>window.location.href='masterdata.php?part=countries';</script>"; // Redirect to countries page
    }
  }
  ?>
   <!-- ======= *************** Product ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='product') {
      // PK: Product part: product=edit&id=$productid
      //echo "<script>alert('Product part is not implemented yet.');</script>";
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1>Product</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?us=<?php echo $uname; ?>">Home</a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item"><a href="masterdata.php?part=productgroup">Product Group</a></li>
          <li class="breadcrumb-item"><a href="masterdata.php?part=productunit">Product Unit</a></li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal" data-pid="new">
          <i class="bi bi-plus-circle"></i>Add New Product
        </button>
      </div> 
    </div><!-- End Page Title -->
     <!-- == Modal form - Product == -->
      <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel"><b>Add New Product</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for cid -->
                <input type="hidden" id="productId" name="productId">
                <div class="mb-3">
                  <label for="productCode" class="form-label">Code</label>
                  <input type="text" class="form-control" id="productCode" name="productCode" required>
                </div>
                <div class="mb-3">
                  <label for="productName" class="form-label">Name</label>
                  <input type="text" class="form-control" id="productName" name="productName" required>
                </div>
                <div class="mb-3">
                  <label for="scientName" class="form-label">Scientific Name</label>
                  <input type="text" class="form-control" id="scientName" name="scientName" required>
                </div>
                <div class="mb-3">
                  <label for="hsCode" class="form-label">HS Code</label>
                  <input type="text" class="form-control" id="hsCode" name="hsCode">
                </div>
                <div class="row mb-3">
                  <label class="col-sm-8 col-form-label">Product Group</label>
                  <div class="col-sm-15">
                    <select class="form-select" name="productGroup" id="productGroup" aria-label="Default select example" onchange="SelectProvinceOnChange(this)">
                     <option value="">*** Please select one ***</option>
                      <?php SelectProductgroup($pgid, $con); ?>
                    </select>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="description" class="form-label">Description</label>
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
              
              <h5 class="card-title">Product</h5>
              <p>ePhytosanitary by Department of Agriculture, MAF - Product</p>

              <!-- Table with stripped rows -->
               
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b>N</b>o</th>
                   <th>Code</th>
                   <th>Name</th>
                   <th>Scientific Name</th>
                   <th>HS Code</th>
                   <th>Product Group</th>
                   <th>Status</th>
                   <th>Edit</th>
                   <th>Delete</th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    ProductList($con); // List of Product
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
      <h1>Product Group</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?us=<?php echo $uname; ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="masterdata.php?part=product">Product</a></li> 
          <li class="breadcrumb-item active">Product Group</li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProductGroupModal" data-pgroupid="new">
          <i class="bi bi-plus-circle"></i>Add New Product Group
        </button>
      </div>
    </div><!-- End Page Title -->
    <!-- == Modal form - Product Group == -->
      <div class="modal fade" id="addProductGroupModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addProductGroupModalLabel"><b>Add New Product Group</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for cid -->
                <input type="hidden" id="productGroupId" name="productGroupId">
                <div class="mb-3">
                  <label for="productGroupName" class="form-label">Name</label>
                  <input type="text" class="form-control" id="productGroupName" name="productGroupName" required>
                </div>
                <div class="mb-3">
                  <label for="productGroupDescription" class="form-label">Description</label>
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
              
              <h5 class="card-title">Product Group</h5>
              <p>ePhytosanitary by Department of Agriculture, MAF - Product Group</p> 
              <!-- Table with stripped rows -->
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b>N</b>o</th>
                   <th>Name</th>
                   <th>Description</th>
                   <th>Status</th>
                   <th>Edit</th>
                   <th>Delete</th>
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
      <h1>Product Unit</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?us=<?php echo $uname; ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="masterdata.php?part=product">Product</a></li>
          <li class="breadcrumb-item active">Product Unit</li>
        </ol>
      </nav>
      </div>  
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProductUnitModal" data-punitid="new">
          <i class="bi bi-plus-circle"></i>Add New Product Unit
        </button>
      </div>
    </div><!-- End Page Title -->
    <!-- == Modal form - Product Unit == -->
      <div class="modal fade" id="addProductUnitModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addProductUnitModalLabel"><b>Add New Product Unit</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for cid -->
                <input type="hidden" id="productUnitId" name="productUnitId">
                <div class="mb-3">
                  <label for="productUnitCode" class="form-label">Code</label>
                  <input type="text" class="form-control" id="productUnitCode" name="productUnitCode" required>
                </div>
                <div class="mb-3">
                  <label for="productUnitName" class="form-label">Symbol</label>
                  <input type="text" class="form-control" id="productUnitSymbol" name="productUnitSymbol" required>
                </div>
                <div class="mb-3">
                  <label for="productUnitName" class="form-label">Title</label>
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
              
              <h5 class="card-title">Product Unit</h5>
       
              <p>ePhytosanitary by Department of Agriculture, MAF - Product Unit</p>
              <!-- Table with stripped rows --> 
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b>N</b>o</th>
                   <th>Code</th>
                   <th>Symbol</th>
                   <th>Title</th>
                   <th>Status</th>
                   <th>Edit</th>
                   <th>Delete</th>
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
  <!-- ======= *************** Conveyance ************************* ======= -->
    <?php
     if(isset($_GET['part']) && $_GET['part']==='conveyance') {
      // PK: Product part: product=edit&id=$productid
      //echo "<script>alert('Product part is not implemented yet.');</script>";
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1>Conveyance</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?us=<?php echo $uname; ?>">Home</a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item active">Conveyance</li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addConveyenceModal" data-cid="new">
          <i class="bi bi-plus-circle"></i>Add New Conveyance
        </button>
      </div>
    </div><!-- End Page Title -->
    <!-- == Modal form - Conveyance == -->
      <div class="modal fade" id="addConveyenceModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
              <div class="modal-header">
                <h5 class="modal-title" id="addConveyenceModalLabel"><b>Add New Conveyance</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Hidden input for cid -->
                <input type="hidden" id="conveyanceId" name="conveyanceId">
                <div class="mb-3">
                  <label for="conveyanceCode" class="form-label">Code</label>
                  <input type="text" class="form-control" id="conveyanceCode" name="conveyanceCode" required>
                </div>
                <div class="mb-3">
                  <label for="conveyanceType" class="form-label">Conveyance Type</label>
                  <input type="text" class="form-control" id="conveyanceType" name="conveyanceType" required>
                </div>
                <div class="mb-3">
                  <label for="conveyanceDescription" class="form-label">Description</label>
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
              
              <h5 class="card-title">Conveyance</h5>
              <p>ePhytosanitary by Department of Agriculture, MAF - Conveyance</p>

              <!-- Table with stripped rows -->
               
              <table class="table datatable tabledata-fonts">
                <thead>
                  <tr>
                   <th><b>N</b>o</th>
                   <th>Code</th>
                   <th>Conveyance Type</th>
                   <th>Description</th>
                   <th>Status</th>
                   <th>Edit</th>
                   <th>Delete</th>
                 </tr>
                </thead>
                <tbody>
                  <?php
                    Conveyancelist($con); // List of Conveyance
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
        
        if($cid === 'new') {
            // Add new conveyance
            AddConveyance($cCode, $cType, $cDescription, $con); // Function to add new conveyance
            echo "<script>alert('New conveyance added-Done');</script>"; // Debugging line
        } else {
            // Update existing conveyance
            echo "<script>alert('Conveyance with ID: " . $cid . " updated.');</script>"; // Debugging line
            UpdateConveyance($cid, $cCode, $cType, $cDescription, $con); // Function to update conveyance
           
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
      // PK: Product part: product=edit&id=$productid
      //echo "<script>alert('Product part is not implemented yet.');</script>";
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1>Inspection Method</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?us=<?php echo $uname; ?>">Home</a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item active">Inspection Method</li>
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
      <div class="modal fade" id="addInspectionMethodModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="">
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
                    InspectionMethodList($con); // List of Inspection Method 
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
        
        if($imid === 'new') {
            // Add new inspection method
            AddInspectionMethod($imCode, $imName, $imDescription, $con); // Function to add new inspection method
            echo "<script>alert('New inspection method added-Done');</script>"; // Debugging line
        } else {
            // Update existing inspection method
            echo "<script>alert('Inspection method with ID: " . $imid . " updated.');</script>"; // Debugging line
            UpdateInspectionMethod($imid, $imCode, $imName, $imDescription, $con); // Function to update inspection method
           
        }
      } // End of if submitInspectionMethod
?>
   <!--============= Treatment methods =============-->    
    <?php
     if(isset($_GET['part']) && $_GET['part']==='treatmentmethod') {
      // PK: Product part: product=edit&id=$productid
      //echo "<script>alert('Product part is not implemented yet.');</script>";
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1>Treatment Method</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?us=<?php echo $uname; ?>">Home</a></li>
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
      <div class="modal fade" id="addTreatmentMethodModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
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
        $tmCode = $_POST['treatmentMethodCode'];
        $tmName = $_POST['treatmentMethodName'];
        $tmDescription = $_POST['treatmentMethodDescription'];
        
        if($tmid === 'new') {
            // Add new treatment method
            AddTreatmentMethod($tmCode, $tmName, $tmDescription, $con); // Function to add new treatment method
            
        } else {
            // Update existing treatment method
           // echo "<script>alert('Treatment method with ID: " . $tmid . " updated.');</script>"; // Debugging line
            UpdateTreatmentMethod($tmid, $tmCode, $tmName, $tmDescription, $con); // Function to update treatment method
           
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
      // PK: Product part: product=edit&id=$productid
      //echo "<script>alert('Product part is not implemented yet.');</script>";
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1>Entity Type</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?us=<?php echo $uname; ?>">Home</a></li>
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
      <div class="modal fade" id="addEntityTypeModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
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
            AddEntityType($etCode, $etName, $etDescription, $con); // Function to add new entity type
            
        } else {
            // Update existing entity type
            echo "<script>alert('Entity type with ID: " . $etid . " updated.');</script>"; // Debugging line
            UpdateEntityType($etid, $etCode, $etName, $etDescription, $con); // Function to update entity type
           
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
      // PK: Product part: product=edit&id=$productid
      //echo "<script>alert('Product part is not implemented yet.');</script>";
    ?>
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
      <h1>Modules</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php?us=<?php echo $uname; ?>">Home</a></li>
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
      <div class="modal fade" id="addModuleModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
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
  </script>
  
</body>

</html>