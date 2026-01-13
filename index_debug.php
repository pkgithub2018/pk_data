<?php
// Simple login page for cloud server testing
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!-- Debug: PHP started -->\n";

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    $message = "You have been logged out successfully.";
} else {
    $message = "";
}

echo "<!-- Debug: Before including files -->\n";

// Try to include required files with error handling
try {
    if (file_exists('php-bin/connection.php')) {
        require_once('php-bin/connection.php');
        echo "<!-- Debug: connection.php included -->\n";
    } else {
        throw new Exception('connection.php not found');
    }
    
    if (file_exists('php-bin/supports.php')) {
        require_once('php-bin/supports.php');
        echo "<!-- Debug: supports.php included -->\n";
    } else {
        throw new Exception('supports.php not found');
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

echo "<!-- Debug: Files included successfully -->\n";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnlogin'])) {
    echo "<!-- Debug: Form submitted -->\n";
    
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';
    
    echo "<!-- Debug: Email: " . $email . " -->\n";
    
    // Variables that would have been session variables
    $session_uid = "";
    $session_username = "";
    $session_email = "";
    $session_passw = "";
    $session_groupid = "";
    $session_groupname = "";
    $session_image = "";
    $session_position = "";

    // IN CASE OF SUBMISSION THROUGH FORM
    if (!empty($email) && !empty($password)) {
        echo "<!-- Debug: Email and password not empty -->\n";
        
        try {
            $sql = "SELECT id, name, psw, position, email, group_id FROM tbusers WHERE email = '$email' AND enabled = 'yes'";
            echo "<!-- Debug: SQL: " . $sql . " -->\n";
            
            $result = pg_query($con, $sql);
            if (!$result) {
                throw new Exception("Database query failed: " . pg_last_error($con));
            }
            
            echo "<!-- Debug: Query executed -->\n";

            if ($row = pg_fetch_array($result)) {
                echo "<!-- Debug: User found -->\n";
                
                // If passwords are hashed, use password_verify
                if ($password === $row['psw']) {
                    echo "<!-- Debug: Password correct -->\n";
                    
                    // Since sessions don't work, redirect with user ID as parameter
                    $userid = $row['id'];
                    $username = urlencode($row['name']);
                   
                    echo "<script type='text/javascript'>
                        console.log('Redirecting to main.php with uid=" . $userid . "');
                        window.location.href = 'main.php?uid=" . $userid . "&us=" . $username . "';
                    </script>";
                    exit();
                } else {
                    echo "<!-- Debug: Password incorrect -->\n";
                    $message = "Incorrect username or password.";
                }
            } else {
                echo "<!-- Debug: User not found -->\n";
                $message = "Incorrect username or password. Email: " . $email;
            }
        } catch (Exception $e) {
            $message = "Database error: " . $e->getMessage();
            echo "<!-- Debug: Database error: " . $e->getMessage() . " -->\n";
        }
    } else {
        echo "<!-- Debug: Email or password empty -->\n";
        $message = "Please enter both email and password.";
    }
}

echo "<!-- Debug: Before HTML output -->\n";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Login - ePhyto System</title>
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

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.php" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/logo.png" alt="">
                  <span class="d-none d-lg-block">ePhyto Certificate</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your email & password to login</p>
                  </div>

                  <?php if ($message): ?>
                  <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                  <?php endif; ?>

                  <form class="row g-3 needs-validation" method="post" action="" novalidate>

                    <div class="col-12">
                      <label for="yourEmail" class="form-label">Email</label>
                      <input type="email" name="email" class="form-control" id="yourEmail" required>
                      <div class="invalid-feedback">Please enter a valid Email address!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                      </div>
                    </div>
                    
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit" name="btnlogin" id="btnlogin">Login</button>
                    </div>
                    
                  </form>

                </div>
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>