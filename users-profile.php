<?php
      // Pk: 2025-07-03
  session_start();

  require("php-bin/connection.php"); // replace include with require
  require("php-bin/supports.php"); // replace include with require

  $userid = isset($_SESSION["uid"]) ? $_SESSION["uid"] : ''; // User ID
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
            <img src="<?php echo $pimagfilepath; ?>" alt="Profile" class="rounded-circle">
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

      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=export" >
          <i class="bi bi-box-arrow-up-right"></i>
          <span>Export entity</span>
        </a>
      </li><!-- End Export Entity Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="entity.php?entity=import" >
          <i class="bi bi-box-arrow-in-down" style="font-size: 1.2rem;"></i>
          <span>Import entity</span>
        </a>
      </li><!-- End Import Entity/Company form Nav -->

    <!--
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
      </li>
    -->
      <!-- End Module Nav -->

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
      </li><!-- End Tables Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'users-profile.php') echo 'active'; ?>" href="users-profile.php">
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
          <i class="bi bi-person-plus"></i><span>Users</span>
        </a>
      </li><!-- End Users Page Nav -->
    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php">Home</a></li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Profile</li>
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
                <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
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
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Settings</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                </li>

              </ul>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                  <h5 class="card-title">About</h5>
                  <p class="small fst-italic"><?php echo $pdescription; ?></p>

                  <h5 class="card-title">Profile Details</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Full Name</div>
                    <div class="col-lg-9 col-md-8"><?php echo $ufullname; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Work Unit</div>
                    <div class="col-lg-9 col-md-8"><?php echo $unit; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Position</div>
                    <div class="col-lg-9 col-md-8"><?php echo $position; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Country</div>
                    <div class="col-lg-9 col-md-8">Lao PDR</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Address</div>
                    <div class="col-lg-9 col-md-8"><?php echo $paddress; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Phone</div>
                    <div class="col-lg-9 col-md-8"><?php echo $phone; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8"><?php echo $email; ?></div>
                  </div>

                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form method="POST" action="#" enctype="multipart/form-data">
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
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
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="fullName" type="text" class="form-control" id="fullName" value="<?php echo $ufullname; ?>">
                        <input name="userid_profile" type="hidden" value="<?php echo $userid; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="about" class="col-md-4 col-lg-3 col-form-label">About</label>
                      <div class="col-md-8 col-lg-9">
                        <textarea name="about" class="form-control" id="about" style="height: 100px"><?php echo $pdescription; ?></textarea>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="company" class="col-md-4 col-lg-3 col-form-label">Work Unit</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="workunit" type="text" class="form-control" id="workunit" value="<?php echo $unit; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Job" class="col-md-4 col-lg-3 col-form-label">Position</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="position" type="text" class="form-control" id="position" value="<?php echo $position; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Country" class="col-md-4 col-lg-3 col-form-label">Country</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="country" type="text" class="form-control" id="Country" value="Lao PDR">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Address" class="col-md-4 col-lg-3 col-form-label">Address</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="address" type="text" class="form-control" id="address" value="<?php echo $paddress; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="phone" type="text" class="form-control" id="phone" value="<?php echo $phone; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="email" type="email" class="form-control" id="email" value="<?php echo $email; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Twitter" class="col-md-4 col-lg-3 col-form-label">Twitter Profile</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="twitter" type="text" class="form-control" id="twitter" value="<?php echo $ptwitter; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Facebook" class="col-md-4 col-lg-3 col-form-label">Facebook Profile</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="facebook" type="text" class="form-control" id="facebook" value="<?php echo $pfacebook; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Instagram" class="col-md-4 col-lg-3 col-form-label">Instagram Profile</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="instagram" type="text" class="form-control" id="instagram" value="<?php echo $pinstagram; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Linkedin" class="col-md-4 col-lg-3 col-form-label">Linkedin Profile</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="linkedin" type="text" class="form-control" id="linkedin" value="<?php echo $plinkedin; ?>">
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary" name="submitEditProfile">Save Changes</button>
                    </div>
                  </form><!-- End Profile Edit Form -->

                </div>

                <div class="tab-pane fade pt-3" id="profile-settings">

                  <!-- Settings Form -->
                  <form>

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
                  <form method="POST" action="#">

                    <div class="row mb-3">
                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="password" type="password" class="form-control" id="currentPassword">
                        <input name="huserid_profile" type="hidden" value="<?php echo $userid; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="newpassword" type="password" class="form-control" id="newPassword">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
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
              echo "<script>alert('Invalid email format');</script>";
              exit;
          }

          if (UpdateProfile($userid, $about, $address, $twitter, $facebook, $instagram, $linkedin, $position, $workunit, $phone, $email, $con)) {
              echo "<script>alert('Profile updated successfully!');</script>";
          } else {
              echo "<script>alert('Error updating profile. Please try again.');</script>";
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
             echo "<script>window.location.href='users-profile.php';</script>";
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
                        window.location.href='users-profile.php';
                      </script>";
            }
          }

          // Validate new password
          $newpassword = $_POST['newpassword'];
          $newpassword = trim($newpassword);
          $renewpassword = $_POST['renewpassword'];
          $renewpassword = trim($renewpassword);

         if (empty($_POST['newpassword']) || empty($_POST['renewpassword'])) {
            
            echo "<script>alert('New password is required.');</script>";
            
             exit;
         } 
        // $showChangePasswordTab = false; // Show change password tab if form is submitted
         if ($_POST['newpassword'] !== $_POST['renewpassword']) {
         // $showChangePasswordTab = true; // Show change password tab if passwords do not match
             echo "<script>
                      alert('New passwords do not match.');
                   </script>";
         } else {
             //echo "<script>alert('New password: " . $newpassword . "');</script>"; // Debugging line
              if (UpdateProfileChangePassword($userid, $newpassword, $con)) {
                  echo "<script>alert('Password changed successfully!');</script>";
                    
              } else {
                    echo "<script>alert('Error changing password. Please try again.');</script>";
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