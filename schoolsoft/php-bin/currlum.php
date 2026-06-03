<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>curriculum</title>
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
	<a href="institute.php?access=inst">ສະຖາບັນ</a> 
	<a class="active" href="#">ຫຼັກສູດ</a>
	<a href="astudy.php?access=astu">ສະໜັກຮຽນ</a>
    <a href="contact.php?access=cont">ຕິດຕໍ່ພົວພັນ</a>
	<a href="login.php"><i class="fa fa-fw fa-user"></i>&nbsp;ເຂົ້າລະບົບ</a>
  </div>
</div>	
 <div style="width: 100%;float: left; display: inline; margin-top: 120px;"> <!-- DIV Content -->
	<div class="cdiv">
		<div align="center" class="cdiv-content">
			ວິທະຍາໄລ
		</div>
		<p>&nbsp;Hello, Please add contents</p>
	</div>
	 <div class="cdiv">
	   <div align="center" class="cdiv-content">
			ວິຊາຊີບ
		</div>
		<p> &nbsp;Hello, Please add contents</p>
	</div>
	 <div class="cdiv">
	   <div align="center" class="cdiv-content">
			ສາມັນສຶກສາ
		</div>
		<p>&nbsp;Hello, Please add contents</p>
	</div>
 </div> <!-- End of DIV Content -->
<div class="footer" style="text-align: left;">
 <?php 
   include("footer.php");
 ?>
</div>
</body>
</html>