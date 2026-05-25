<?php
      // MODULES.PHP ***********
  session_start();
  
  require("php-bin/connection.php"); // replace include with require
  require("php-bin/supports.php"); // replace include with require

  $userid = isset($_SESSION["uid"]) ? $_SESSION["uid"] : ''; // User ID
  
  // Authentication check
  if(empty($userid)){
    // If user ID is not set, redirect to login page
    echo "<script>alert('You are not logged in. Please log in to access this page.');</script>"; 
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
  }
  
  // Permission check for Modules module (FRM - MODULE)
  $modulesPermit = UserPermitCheck($userid, 'FRM - MODULE', $con);
  if (!$modulesPermit['pread']) {
    echo "<script>alert('Access Denied: You do not have permission to access the Modules module.');</script>";
    echo "<script>window.location.href = 'main.php';</script>";
    exit();
  }
  
  $loginuser = isset($_SESSION["username"]) ? $_SESSION["username"] : ''; // use email or username
  $uname = isset($_SESSION['uname']) ? $_SESSION['uname'] : ''; // Name of user
  //echo "<script>alert('Username: " . $uname . "'+'".$userid."');</script>"; // Debugging line
  
  // USER DATA
  $userinfo = Userdata($userid, $con);
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Modules</title>
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
              <a class="dropdown-item d-flex align-items-center" href="#">
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
        <a class="nav-link collapsed" href="main.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link " data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Modules</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
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
      </li><!-- End Module Nav -->
      
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Master data</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          
          <li>
            <a href="masterdata.php?part=entity">
              <i class="bi bi-circle"></i><span>Conveyance</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=countries">
              <i class="bi bi-circle"></i><span>Countries</span>
            </a>
          </li>
          <li>
            <a href="masterdata.php?part=districts">
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
            <a href="masterdata.php?part=product">
              <i class="bi bi-circle"></i><span>Product</span>        
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
      </li><!-- End Tables Nav -->

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
      </li><!-- End User Group permit -->

      <li class="nav-item">
        <a class="nav-link active" href="users.php?part=userslist">
          <i class="bi bi-person-plus"></i><span>Users</span>
        </a>
      </li>  <!-- End Users Nav -->

      <!-- Logout -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Logout</span>
        </a>
      </li><!-- End Logout -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    <!-- ======= *************** Entity/Company ************************* ======= -->
    <div class="pagetitle">
      <h1>Modules - Phytosanitary Certificate</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php">Home</a></li>
          <li class="breadcrumb-item">Forms</li>
          <li class="breadcrumb-item active">Phytosanitary Certificate</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <!-- Card for form elements -->
    <div class="card mb-4">
        <div class="card-body">
          <form class="row g-3 align-items-center">
              <div class="col-md-6">
                <label for="createdDate" class="form-label fw-bold">Created date</label>
                <input type="date" class="form-control" id="createdDate" name="createdDate">
              </div>
              <div class="col-md-6">
                <label for="frameSelect" class="form-label fw-bold">App No.</label>
                <select class="form-select" id="frameSelect" name="frameSelect">
                  <option value="">Please select</option>
                  <option value="option1">Option 1</option>
                  <option value="option2">Option 2</option>
                </select>
              </div>
            </form>
          </div>
        </div>
    </div><!-- End Card -->
    <!-- Tabs for modules -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <a class="nav-link active" id="entity-tab" data-bs-toggle="tab" href="#entity" role="tab" aria-controls="entity" aria-selected="true">Entity/Company</a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="inspection-tab" data-bs-toggle="tab" href="#inspection" role="tab" aria-controls="inspection" aria-selected="false">Inspection</a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="sample-tab" data-bs-toggle="tab" href="#sample" role="tab" aria-controls="sample" aria-selected="false">Sample</a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="certificate-tab" data-bs-toggle="tab" href="#certificate" role="tab" aria-controls="certificate" aria-selected="false">Certificate</a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="printing-tab" data-bs-toggle="tab" href="#printing" role="tab" aria-controls="printing" aria-selected="false">Printing</a>
            </li>
          </ul>
        </div>
      </div>

      <div class="tab-content pt-2">
        <div class="tab-pane fade show active" id="entity" role="tabpanel" aria-labelledby="entity-tab">
          <!-- Content for Entity/Company -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Entity/Company</h5>

              <!-- Entity/Company form -->
              <form>
                <!-- Row 1 -->
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="inputText" class="col-sm-5 col-form-label">App.No</label>
                      <input type="text" name="appNum" class="form-control">
                  </div>
                  <div class="col-md-3">
                     <label for="inputDate" class="col-sm-2 col-form-label">Date</label>
                    <input type="date" name="appDate" class="form-control">
                  </div>
                  <div class="col-md-3">
                  <label for="inputText" class="col-sm-5 col-form-label">Reg.No</label>
                      <input type="text" name="regNum" class="form-control">
                  </div>
                <div class="col-md-3">
                  <label for="inputBorder" class="col-sm-8 col-form-label">Via border</label>
                    <select class="form-select" name="location" aria-label="Default select example">
                     <option value="">*** Please select one ***</option>
                      <?php SelectLocation($location, $con); ?>
                    </select>
                </div>
              </div> <!-- end of row1 -->

              <!-- Row 2 -->
                <div class="row mb-3">

                </div> <!-- end of row2 -->

                <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">File Upload</label>
                  <div class="col-sm-10">
                    <input class="form-control" type="file" id="formFile">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputDate" class="col-sm-2 col-form-label">Date</label>
                  <div class="col-sm-10">
                    <input type="date" class="form-control">
                  </div>
                </div>  

                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Submit Button</label>
                  <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">Submit Form</button>
                  </div>
                </div>

                <!-- Add more form elements as needed -->
              </form><!-- End Entity/Company Form Elements -->

            </div>
        </div>
        </div><!-- End tab content for entity -->
        <div class="tab-pane fade show " id="inspection" role="tabpanel" aria-labelledby="inspection-tab">
          <!-- Content for Inspection -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Inspection</h5>

              <!-- Inspection form -->
              <form>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Text</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control">
                  </div>
                </div>
                <!-- Add more form elements as needed -->
              </form><!-- End Inspection Form Elements -->

            </div>
        </div>
        </div><!-- End tab content for inspection -->
        <div class="tab-pane fade show " id="sample" role="tabpanel" aria-labelledby="sample-tab">
          <!-- Content for Sample -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Sample</h5>

              <!-- Sample form -->
              <form>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Text</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control">
                  </div>
                </div>
                <!-- Add more form elements as needed -->
              </form><!-- End Sample Form Elements -->

            </div>
        </div>
        </div><!-- End tab content for sample -->
        
        <div class="tab-pane fade show " id="certificate" role="tabpanel" aria-labelledby="certificate-tab">
          <!-- Content for Certificate -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Certificate</h5>

              <!-- Certificate form -->
              <form>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Text</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control">
                  </div>
                </div>
                <!-- Add more form elements as needed -->
              </form><!-- End Certificate Form Elements -->

            </div>
        </div>
        </div><!-- End tab content for certificate -->
        <div class="tab-pane fade show " id="printing" role="tabpanel" aria-labelledby="printing-tab">
          <!-- Content for Printing -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Printing</h5>

              <!-- Printing form -->
              <form>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Text</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control">
                  </div>
                </div>
                <!-- Add more form elements as needed -->
              </form><!-- End Printing Form Elements -->

            </div>
        </div>
       </div><!-- End tab content for printing -->
      </div><!-- End tab content -->
    </section>
    <!-- End Tabs for modules -->
   

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
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

  <script>
  document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const part = urlParams.get("part");

    if (part) {
      const tabTriggerEl = document.querySelector(`#${part}-tab`);
      if (tabTriggerEl) {
        const tab = new bootstrap.Tab(tabTriggerEl);
        tab.show();
      }
    }
  });
</script>
</body>

</html>