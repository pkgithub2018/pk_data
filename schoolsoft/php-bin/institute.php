<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Institute</title>
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
}
</style>
<body>
 <!--********* HEADER *************-->	
<div class="header">
 <!-- <a href="#default" class="logo">&nbsp;</a>    -->
 <!-- <div class="header-left"><img src="../images/logo.jpg" width="170" height="100"></div> -->
  <div class="header-right">
	<a href="../index.php">ໜ້າຫຼັກ</a>
	<a class="active" href="#">ສະຖາບັນ</a> <!-- class="active" -->
	<a href="currlum.php?access=curr">ຫຼັກສູດ</a>
	  <a href="astudy.php?access=astu">ສະໜັກຮຽນ</a>
    <a href="contact.php?access=cont">ຕິດຕໍ່ພົວພັນ</a>
	<a href="login.php"><i class="fa fa-fw fa-user"></i>&nbsp;ເຂົ້າລະບົບ</a>
  </div>
</div>	
 <div style="width: 100%;float: left; display: inline; margin-top: 95px">
	<div style="width: 60%; float: left; margin-left: 10px">
	 <img src="../images/inst.jpg" >
	</div>
	 <div style="width: 38%; float: left">
		<h1>ສະຖາບັນ ການສືກສາ ແລະ ສີ່ງອໍານວຍຄວາມສະດວກ</h1>
	   <p>ທີ່ຕັ້ງຢູ່ໃຈກາງ ນະຄອນຫຼວງວຽງຈັນ ແລະ ອ້ອມຮອບດ້ວຍສີ່ງອໍານວຍຄວາມສະດວກຫຼາຍຢ່າງ</p>
	 </div>
 </div>
<!-- Footer ************* -->
 <div class="footer" style="text-align: left;">
 <?php 
   include("footer.php");
 ?>
</div>
</body>
</html>