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
            <img src="assets/img/pk-img.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION['uname']; ?></span>
          </a><!-- End Profile Iamge Icon -->
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $_SESSION['uname']; ?></h6>
              <span>National IT Consultant</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
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
       <?php
          $masterParts = ['countries', 'locations', 'provinces','product']; // Add all relevant parts here
          $isMasterActive = (isset($_GET['part']) && in_array($_GET['part'], $masterParts));
      ?>
      <li class="nav-item">
        <a class="nav-link <?php echo $isMasterActive ? '' : 'collapsed'; ?>" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Master data</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse<?php echo $isMasterActive ? ' show' : ''; ?>" data-bs-parent="#sidebar-nav">
         
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span>Companies/Entities</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=product" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'product') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Product</span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span>Conveyence</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=countries" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'countries') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Countries</span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span>Districts</span>
            </a>
          </li>
          
          <li>
            <a href="masterdata.php?part=locations" class="<?php echo (isset($_GET['part']) && $_GET['part'] === 'locations') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i><span>Locations</span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span>Provinces</span>
            </a>
          </li>
        </ul>
      </li><!-- End Tables Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.html">
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
          <li class="breadcrumb-item active">Locations</li>
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
          Locationupdate($id,$nameeng, $namelao, $loctype,$pid, $did, $con); // Function to update location
       } else if ($_POST['btnsublocation'] === 'submit') {
           // Add new location
           echo "<script>alert('Add new location: " . $locid . "');</script>"; // Debugging line
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
      
   <?php  } ?> <!-- ********* End of if part=countries ********* -->
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

    if(isset($_GET['del']) && $_GET['del'] === 'yes') {
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
          <li class="breadcrumb-item">Product Unit</li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCountryModal" data-cid="new">
          <i class="bi bi-plus-circle"></i>Add New Product
        </button>
      </div> 
    </div><!-- End Page Title -->
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
          <li class="breadcrumb-item">Tables</li> 
          <li class="breadcrumb-item active">Product Group</li>
        </ol>
      </nav>
      </div>
      <div>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCountryModal" data-cid="new">
          <i class="bi bi-plus-circle"></i>Add New Product Group
        </button>
      </div>
    </div><!-- End Page Title -->
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
                    //ProductGroupList($con); // List of Product Group
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
    //  Modal form - Countries 
    // 1) Set focus on the country name input field when the modal is shown
    document.addEventListener('DOMContentLoaded', function() {
    var addCountryModal = document.getElementById('addCountryModal');
      if (addCountryModal) {
          addCountryModal.addEventListener('shown.bs.modal', function () {
          var countryNameInput = document.getElementById('countryName');
      if (countryNameInput) {
        countryNameInput.focus();
        countryNameInput.select();
      }
      });
    }
    });
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
    // PK: Focus on locationid input field in data form when the page loads
    window.addEventListener('DOMContentLoaded', function() {
    var nameInput = document.getElementById('locationid');
        if (nameInput) {
            nameInput.focus();
            nameInput.select();
      }
    });
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