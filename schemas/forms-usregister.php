<?php
      // Pk: 2025-04-30
  session_start();

  require("php-bin/connection.php"); // replace include with require
  require("php-bin/supports.php"); // replace include with require

  $loginuser = isset($_SESSION["username"]) ? $_SESSION["username"] : ''; 
   // Initialize variables for messages
  $message = "";
    // ADD USER ***
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $surname = isset($_POST['surname']) ? htmlspecialchars($_POST['surname']) : '';
    $uname = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
    $sex = isset($_POST['gender']) ? htmlspecialchars($_POST['gender']) : '';
    $uname = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
    $psw = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $workplace = isset($_POST['workplace']) ? htmlspecialchars($_POST['workplace']) : '';
    $utype = isset($_POST['usertype']) ? htmlspecialchars($_POST['usertype']) : '';
    $status = isset($_POST['status']) ? htmlspecialchars($_POST['status']) : '';

    if (isset($_POST['btnsubuser']) && $_POST['btnsubuser'] == "submit") {
      $cfuser ="";
        $cfuser = Addusers($name,$surname,$sex,$uname,$psw,$phone,$email,$workplace,$utype,$status,$con);
        $message = "Submitted: " . $name . " " . $surname;
    }  
  } // end of POST method
  // UPDATE USER ***
  if (isset($_GET['us'])) {  // URL parameter 'us' is from the link in the user list
    echo "<script>alert('Username: " . $_GET['us'] . "');</script>";
    $usname = htmlspecialchars($_GET['us']); // Sanitize the input
    $userData = Updateuser($usname, $con);
    if ($userData) {
      // Add these variables to the form for update
        list($nameup, $surnameup, $unameup, $sexup, $pswup, $phoneup, $emailup, $workplaceup, $utypeup, $statusup) = $userData;
    } else {
        echo "<script>alert('User not found.');</script>";
    }
  }
  // Check the update on the form 
  if (isset($_POST['btnsubuser']) && $_POST['btnsubuser'] == "update") {
    $namenew = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $surnamenew = isset($_POST['surname']) ? htmlspecialchars($_POST['surname']) : '';
    $unamenew = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
    $sexnew = isset($_POST['gender']) ? htmlspecialchars($_POST['gender']) : '';

    $sqlold = "SELECT * FROM tbusers WHERE username = '$usname'"; // username could not be changed
    $resultold = mysqli_query($con, $sqlold) or die(mysqli_error($con));
    if (mysqli_num_rows($resultold) > 0) {
      list($nameold, $surnameold, $unameold, $sexold, $pswold, $phoneold, $emailold, $workplaceold, $utypeold, $statusold) = mysqli_fetch_array($resultold);
      $sqlupdate = "UPDATE tbusers SET surname='$surnamenew' WHERE username='$usname'";
      $resultupdate = mysqli_query($con, $sqlupdate) or die(mysqli_error($con));
      
    } else {
        echo "<script>alert('User not found-Old.');</script>";
    }

    echo "<script>alert('Users_new: " . $namenew . " " . $surnamenew . " Old: " . $nameold . " " .$surnameold. "');</script>";

    //UpdateuserSubmit($name,$surname,$sex,$uname,$psw,$phone,$email,$workplace,$utype,$status,$con);
    //$message = "Updated: " . $name . " " . $surname. " " . $uname; 
  } 
  ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Users Resistration</title>
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
      <a href="main.php" class="logo d-flex align-items-center">
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

        <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number">4</span>
          </a><!-- End Notification Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              You have 4 new notifications
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-exclamation-circle text-warning"></i>
              <div>
                <h4>Lorem Ipsum</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>30 min. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-x-circle text-danger"></i>
              <div>
                <h4>Atque rerum nesciunt</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>1 hr. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-check-circle text-success"></i>
              <div>
                <h4>Sit rerum fuga</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>2 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-info-circle text-primary"></i>
              <div>
                <h4>Dicta reprehenderit</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>4 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>
            <li class="dropdown-footer">
              <a href="#">Show all notifications</a>
            </li>

          </ul><!-- End Notification Dropdown Items -->

        </li><!-- End Notification Nav -->

        <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-chat-left-text"></i>
            <span class="badge bg-success badge-number">3</span>
          </a><!-- End Messages Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
            <li class="dropdown-header">
              You have 3 new messages
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-1.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>Maria Hudson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>4 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-2.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>Anna Nelson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>6 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-3.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>David Muldon</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>8 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="dropdown-footer">
              <a href="#">Show all messages</a>
            </li>

          </ul><!-- End Messages Dropdown Items -->

        </li><!-- End Messages Nav -->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="assets/img/pk-img.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $loginuser; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>Kevin Anderson</h6>
              <span>Web Designer</span>
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
              <a class="dropdown-item d-flex align-items-center" href="#">
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
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Components</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="components-alerts.html">
              <i class="bi bi-circle"></i><span>Alerts</span>
            </a>
          </li>
          <li>
            <a href="components-accordion.html">
              <i class="bi bi-circle"></i><span>Accordion</span>
            </a>
          </li>
          <li>
            <a href="components-badges.html">
              <i class="bi bi-circle"></i><span>Badges</span>
            </a>
          </li>
          <li>
            <a href="components-breadcrumbs.html">
              <i class="bi bi-circle"></i><span>Breadcrumbs</span>
            </a>
          </li>
          <li>
            <a href="components-buttons.html">
              <i class="bi bi-circle"></i><span>Buttons</span>
            </a>
          </li>
          <li>
            <a href="components-cards.html">
              <i class="bi bi-circle"></i><span>Cards</span>
            </a>
          </li>
          <li>
            <a href="components-carousel.html">
              <i class="bi bi-circle"></i><span>Carousel</span>
            </a>
          </li>
          <li>
            <a href="components-list-group.html">
              <i class="bi bi-circle"></i><span>List group</span>
            </a>
          </li>
          <li>
            <a href="components-modal.html">
              <i class="bi bi-circle"></i><span>Modal</span>
            </a>
          </li>
          <li>
            <a href="components-tabs.html">
              <i class="bi bi-circle"></i><span>Tabs</span>
            </a>
          </li>
          <li>
            <a href="components-pagination.html">
              <i class="bi bi-circle"></i><span>Pagination</span>
            </a>
          </li>
          <li>
            <a href="components-progress.html">
              <i class="bi bi-circle"></i><span>Progress</span>
            </a>
          </li>
          <li>
            <a href="components-spinners.html">
              <i class="bi bi-circle"></i><span>Spinners</span>
            </a>
          </li>
          <li>
            <a href="components-tooltips.html">
              <i class="bi bi-circle"></i><span>Tooltips</span>
            </a>
          </li>
        </ul>
      </li><!-- End Components Nav -->

      <li class="nav-item">
        <a class="nav-link " data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Forms</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="forms-elements.html" class="active">
              <i class="bi bi-circle"></i><span>Request</span>
            </a>
          </li>
          <li>
            <a href="forms-layouts.html">
              <i class="bi bi-circle"></i><span>Form Layouts</span>
            </a>
          </li>
          <li>
            <a href="forms-editors.html">
              <i class="bi bi-circle"></i><span>Form Editors</span>
            </a>
          </li>
          <li>
            <a href="forms-validation.html">
              <i class="bi bi-circle"></i><span>Form Validation</span>
            </a>
          </li>
        </ul>
      </li><!-- End Forms Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Tables</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="tables-general.html">
              <i class="bi bi-circle"></i><span>General Tables</span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span>Data Tables</span>
            </a>
          </li>
        </ul>
      </li><!-- End Tables Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-bar-chart"></i><span>Charts</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="charts-chartjs.html">
              <i class="bi bi-circle"></i><span>Chart.js</span>
            </a>
          </li>
          <li>
            <a href="charts-apexcharts.html">
              <i class="bi bi-circle"></i><span>ApexCharts</span>
            </a>
          </li>
          <li>
            <a href="charts-echarts.html">
              <i class="bi bi-circle"></i><span>ECharts</span>
            </a>
          </li>
        </ul>
      </li><!-- End Charts Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#icons-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-gem"></i><span>Icons</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="icons-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="icons-bootstrap.html">
              <i class="bi bi-circle"></i><span>Bootstrap Icons</span>
            </a>
          </li>
          <li>
            <a href="icons-remix.html">
              <i class="bi bi-circle"></i><span>Remix Icons</span>
            </a>
          </li>
          <li>
            <a href="icons-boxicons.html">
              <i class="bi bi-circle"></i><span>Boxicons</span>
            </a>
          </li>
        </ul>
      </li><!-- End Icons Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.html">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-faq.html">
          <i class="bi bi-question-circle"></i>
          <span>F.A.Q</span>
        </a>
      </li><!-- End F.A.Q Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-contact.html">
          <i class="bi bi-envelope"></i>
          <span>Contact</span>
        </a>
      </li><!-- End Contact Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-register.html">
          <i class="bi bi-card-list"></i>
          <span>Register</span>
        </a>
      </li><!-- End Register Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-login.html">
          <i class="bi bi-box-arrow-in-right"></i>
          <span>Login</span>
        </a>
      </li><!-- End Login Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-error-404.html">
          <i class="bi bi-dash-circle"></i>
          <span>Error 404</span>
        </a>
      </li><!-- End Error 404 Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-blank.html">
          <i class="bi bi-file-earmark"></i>
          <span>Blank</span>
        </a>
      </li><!-- End Blank Page Nav -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Add New Users</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="main.php">Home</a></li>
          <li class="breadcrumb-item">Forms</li>
          <li class="breadcrumb-item active">Users</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-6" style="width: 80%;"> <!-- Pk-Update: style -->

          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo isset($unameup) ? 'Users Update' : 'New Users'; ?></h5>

              <!-- Users Form -->
              <form method="POST">
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Name</label>
                  <div class="col-sm-10">
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($nameup) ? $nameup : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Surname</label>
                  <div class="col-sm-10">
                    <input type="text" name="surname" class="form-control" value="<?php echo isset($surnameup) ? $surnameup : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Gender</label>
                  <div class="col-sm-5">
                    <select class="form-select" name="gender" aria-label="Default select example">
                      <option selected>*** Please select one ***</option>
                      <option value="m" <?php echo (isset($sexup) && $sexup == 'm') ? 'selected' : ''; ?>>Male</option>
                      <option value="f" <?php echo (isset($sexup) && $sexup == 'f') ? 'selected' : ''; ?>>Female</option>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Username</label>
                  <div class="col-sm-5">
                    <input type="text" name="username" class="form-control me-2" value="<?php echo isset($unameup) ? $unameup : ''; ?>" <?php echo isset($unameup) && !empty($unameup) ? 'disabled' : ''; ?>><span id="usermiss"><?php if(!empty($cfuser)){ echo "Already exists";} ?></span>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                  <div class="col-sm-5">
                    <input type="password" name="password" class="form-control" value="<?php echo isset($pswup) ? $pswup : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Phone</label>
                  <div class="col-sm-10">
                    <input type="text" name="phone" class="form-control" value="<?php echo isset($phoneup) ? $phoneup : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-10">
                    <input type="email" name="email" class="form-control" value="<?php echo isset($emailup) ? $emailup : ''; ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputPassword" class="col-sm-2 col-form-label">Workplace</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" name="workplace" style="height: 100px"><?php echo isset($workplaceup) ? $workplaceup : ''; ?></textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Users Group</label>
                  <div class="col-sm-10">
                    <select class="form-select" name="usertype" aria-label="Default select example">
                      <option selected>*** Please select one ***</option>
                      <option value="1" <?php echo (isset($utypeup) && $utypeup == '1') ? 'selected' : ''; ?>>Admin</option> <!-- DOA -->
                      <option value="2" <?php echo (isset($utypeup) && $utypeup == '2') ? 'selected' : ''; ?>>Sub-admin</option> <!-- Provincial level -->
                      <option value="3" <?php echo (isset($utypeup) && $utypeup == '3') ? 'selected' : ''; ?>>Guest</option> <!-- Guest to view information -->
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Status</label>
                  <div class="col-sm-10">
                    <select class="form-select" name="status" aria-label="Default select example">
                      <option selected>*** Please select one ***</option>
                      <option value="act" <?php echo (isset($statusup) && $statusup == 'act') ? 'selected' : ''; ?>>Active</option>
                      <option value="psv" <?php echo (isset($statusup) && $statusup == 'psv') ? 'selected' : ''; ?>>Disable</option> <!-- Not active -->
                    </select>
                  </div>
                </div>
              
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">&nbsp;</label> 
                  <div class="col-sm-10">
                    <button type="submit" name="btnsubuser" class="btn btn-primary" value="<?php echo isset($unameup) ? 'update' : 'submit'; ?>"><?php echo isset($unameup) ? 'Update' : 'Submit'; ?></button>
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

  </main><!-- End #main -->

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
  <script>
    // set focus on the username field when the page loads
    window.onload = function() {
      document.getElementById("name").focus();
    };  
    // Open password field when the username field is clicked
    document.querySelector('.toggle-password').addEventListener('click', function () {
    const passwordField = document.getElementById('password');
    const icon = this;

    if (passwordField.type === 'password') {
      passwordField.type = 'text';
      icon.classList.remove('bi-eye-slash');
      icon.classList.add('bi-eye');
    } else {
      passwordField.type = 'password';
      icon.classList.remove('bi-eye');
      icon.classList.add('bi-eye-slash');
    }
  });
  </script>
</body>

</html>