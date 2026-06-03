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
<title>Login</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
 <?php
  include("../header.php"); // This file helps to run css as it is included here by using php as below
 ?>
<style>
 <?php
    include("../css/lafont.css");
	include("../css/initcss.css");
 ?>	
 body {
  font-family: "Open Sans", sans-serif;
  height: 100vh;
 /* background: image();  #FFD580 */
 /* background-image: url("images/bg.jpg"); */
  background-position: right;
  background-repeat: no-repeat;
  background-size: cover;
  background-color: #F5F5F5;
}
</style>
<body>
 <!--********* HEADER *************-->	
<div class="header">
 <!-- <a href="#default" class="logo">&nbsp;</a>    -->
 <!-- <div class="header-left"><img src="../images/logo.jpg" width="170" height="100"></div> -->
  <div class="header-right">
	<a href="../index.php">ໜ້າຫຼັກ</a>
	<a href="institute.php">ສະຖາບັນ</a> <!-- class="active" -->
	<a href="currlum.php?access=curr">ຫຼັກສູດ</a>
	  <a href="astudy.php?access=astu">ສະໜັກຮຽນ</a>
    <a href="contact.php?access=cont">ຕິດຕໍ່ພົວພັນ</a>
	<a class="active" href="#"><i class="fa fa-fw fa-user"></i>&nbsp;ເຂົ້າລະບົບ</a>
  </div>
</div>	
 <div style="width: 100%;float: left; display: inline; margin-top: 150px">
	<div style="width: 20%; float: left">&nbsp;</div> <!-- LELFT -->
	 <div style="width: 50%; float: left; align-content: center">
	    <div align="center" style="width: 100%; height: auto; border: none">
		  <div align="center" class="inlog">
			<form action="content.php" method="post">  <!-- FORM : access to content.php -->
			  <table align="center" style="padding: 20px; border: none">
				<tr><th align="center" style="border: none"><i class="fa fa-sign-in" style="font-size:36px; color: green"></i></th></tr>
				<tr><th align="center" style="border: none">ຍີນດີຕ້ອນຮັບ</th></tr>
				<tr><td>&nbsp;</td></tr> 
				<tr><td><input type="text" placeholder="ຊື່ຜູ້ໃຊ້" name="username" id="unameid"></td></tr>
				<tr><td><input type="password" placeholder="ລະຫັດຜ່ານ" name="passw" id="pswid"></td></tr>
				<tr><td>&nbsp;</td></tr> 
				<tr><td>&nbsp;</td></tr> 
				<tr><td>&nbsp;</td></tr> 
				<tr><td align="center"><input type="submit" value="ເຂົ້າລະບົບ"></td></tr> 
			  </table>
			  <div id="msid" style="color: red">&nbsp;</div>
			</form>
		  </div>
		</div>
	 </div>
	 <div style="width: 20%; float: left">&nbsp;</div> <!-- RIGHT -->
 </div>
<!-- Footer ************* -->
 <div class="footer" style="text-align: left;">
 <?php 
   include("footer.php");
 ?>
</div>
<script>
 $(document).ready(function(){
	document.getElementById("unameid").focus(); 
    var acclogin="<?php $al=$_GET["access"]; echo $al; ?>";
	var ms=document.getElementById("msid");
	if(acclogin.length>0 && acclogin=="emtp"){
	  ms.innerHTML+="ກະລຸນາ ຕື່ມຊື່ຜູ້ໃຊ້ ແລະ ລະຫັດຜ່ານ !";
	} 
	if(acclogin.length>0 && acclogin=="incorrect"){
	 ms.innerHTML+="ຊື່ຜູ້ໃຊ້ ແລະ ລະຫັດຜ່ານ ຂອງ ທ່ານ ບໍ່ຖືກຕ້ອງ <br>ຫຼື ທ່ານ ບໍ່ໄດ້ຮັບສິດເຂົ້ານໍາໃຊ້ລະບົບ !";	
	}
	 
 }); // End of ready document
</script>
</body>
</html>