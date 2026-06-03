<?php
session_start();
//echo "Hello, session name: ".$_SESSION["uname"];

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
// Finally, destroy the session.
session_destroy();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>schoolsoft</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
 <?php
    include("css/lafont.css");
	include("css/initcss.css");
 ?>	

  html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }

 body {
  /* font-family: "Open Sans", sans-serif; */
  height: 100vh;
 /* background: image();  #FFD580 */
  background-image: url("images/bg.jpg"); 
  background-position: right;
  background-repeat: no-repeat;
  background-size: cover;
  /* new added */
   min-height: 100vh;
      display: flex;
      flex-direction: column;
}

 .main-content {
      flex: 1 0 auto;
      min-height: 60vh;
      display: flex;
      align-items: flex-end;
      justify-content: center;
    }
    .footer {
      flex-shrink: 0;
      width: 100%;
      text-align: right;
      background: #b2b200; /*  #f7f7c6; */
      padding: 0 0 0 0;
      border-top: 1px solid #e0e0a0;
      margin: 0;
      position: relative;
      bottom: 0;
    }
    .footer-content {
      margin-right: 30px;
      margin-top: 10px;
      font-size: 1rem;
      color: #444;
    }
</style>	
</head>
<?php
  include("header.php"); // This file helps to run css as it is included here by using php as below
 ?>
<body>
<!--********* HEADER *************-->	

 <!-- <a href="#default" class="logo">&nbsp;</a>    -->
 <!-- <div class="header-left"><img src="../images/logo.jpg" width="170" height="100"></div> -->
<!--
<div class="header"> 
 <div class="header-right">
	<a href="php-bin/institute.php?access=inst">ສະຖາບັນ</a> 
    <a href="php-bin/currlum.php?access=curr">ຫຼັກສູດ</a> 
	<a href="php-bin/astudy.php?access=astu">ສະໜັກຮຽນ</a>
    <a href="php-bin/contact.php?access=cont">ຕິດຕໍ່ພົວພັນ</a>
	<a href="php-bin/login.php"><i class="fa fa-fw fa-user"></i>&nbsp;ເຂົ້າລະບົບ</a> 
  </div>
 </div>	
<div class="footer" style="text-align: right; margin-top: 520px">
	<div style="margin-right: 15px; margin-top: 15px">
	ຮ່ວມພັດທະນາໂດຍ: <br>
	ບໍລິສັດ BCD Co.,Ltd & ສະຖາບັນ BIS <br>
	Mob/WhatsApp: +85620 55112860 <br>
	Email: info@bcdconsult.com
	</div>
</div>
-->
<div class="header" style="background: #b2b200; padding: 18px 0 10px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
  <div style="max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
   <!--
    <div class="header-logo" style="font-size: 1.7rem; font-weight: bold; color: #4b4b1f;">
      <img src="images/logo.jpg" alt="Logo" style="height: 60px; vertical-align: middle; margin-right: 10px;">
      SchoolSoft
    </div>
  -->
    <!-- Left: MySchool icon and text at the absolute left corner -->
    <div style="display: flex; align-items: center; position: absolute; left: 0; top: 0; height: 100%; padding-left: 24px;">
      <i class="fa fa-graduation-cap" style="font-size: 22px; color: #007bff; margin-right: 10px;"></i>
      <span style="font-size: 1.2rem; font-weight: bold; font-family: Arial, sans-serif; color: #333;">MySCHOOL</span>
    </div>
    <!-- Right: Menu -->

    <div class="header-right" style="display: flex; gap: 28px;">
      <a href="php-bin/institute.php?access=inst" style="font-weight:500; color:#333; text-decoration:none;">ສະຖາບັນ</a>
      <a href="php-bin/currlum.php?access=curr" style="font-weight:500; color:#333; text-decoration:none;">ຫຼັກສູດ</a>
      <a href="php-bin/astudy.php?access=astu" style="font-weight:500; color:#333; text-decoration:none;">ສະໜັກຮຽນ</a>
      <a href="php-bin/contact.php?access=cont" style="font-weight:500; color:#333; text-decoration:none;">ຕິດຕໍ່ພົວພັນ</a>
      <a href="php-bin/login.php" style="font-weight:500; color:#333; text-decoration:none;">
        <i class="fa fa-fw fa-user"></i>&nbsp;ເຂົ້າລະບົບ
      </a>
    </div>
  </div>
</div>

<div class="main-content">
  <div style="text-align: right;">
    <h3 style="font-size: 2.5rem; color: #4b4b1f; margin-bottom: 10px;">&nbsp;</h3>
    <h1 style="font-size: 2.5rem; color: #4b4b1f; margin-bottom: 10px;">&nbsp;</h1>
    <p style="font-size: 1.2rem; color: #555;">&nbsp;</p>
  </div>
</div>

<div class="footer">
  <div class="footer-content">
    ຮ່ວມພັດທະນາໂດຍ:<br>
    ບໍລິສັດ BCD Co.,Ltd & ສະຖາບັນ BIS<br>
    Mob/WhatsApp: +85620 55112860<br>
    Email: info@bcdconsult.com
  </div>
</div>

</body>

</html>
