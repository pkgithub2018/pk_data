<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Contact</title>
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
	<a href="institute.php?access=inst">ສະຖາບັນ</a> 
	<a href="currlum.php?access=curr">ຫຼັກສູດ</a>
	<a href="astudy.php?access=astu">ສະໜັກຮຽນ</a>
    <a class="active" href="#">ຕິດຕໍ່ພົວພັນ</a>
	<a href="login.php"><i class="fa fa-fw fa-user"></i>&nbsp;ເຂົ້າລະບົບ</a>
  </div>
</div>
<!-- Main DIV -->
<div style="width: 100%;float: left; display: inline; margin-top: 95px"> 
<!-- LEFT --> 
<div style="width: 40%;float: left;"> 
	 <div style="width: 100%; float: left; margin-left: 20px">
		<h1>ສະຖາບັນ ສືກສາ ບຸນເກີດ</h1>
		<h3>Bounkeuth Institute</h3>
		 <p><b>ຫ້ອງການໃຫຍ່</b><br>ບ້ານ ໄຊມຸງຄຸນ, ເຂດໜອງບຶກ, ເມືອງ ນາຊາຍທອງ, ນະຄອນຫຼວງວຽງຈັນ, ຖະໜົນເລກທີ 13 ເໜືອ <br>ໂທ:+856 021 612272 <br>ມືຖື: +856 020 22208486</p>
		 <p><b>ຫໍພັກ ແລະ ເດີ່ນກິລາ</b></p>
		 <p><b>ຮ້ານກີນດື່ມ ແລະ ສະລອຍນໍ້າ</b></p>
	 </div>
 </div>
<!-- RIGHT -->
 <div style="width: 60%;float: left;"> 
   <p><b>ແຜນທີ</b></p>
   <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3793.812304777185!2d102.54105091488553!3d18.033912287694193!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTjCsDAyJzAyLjEiTiAxMDLCsDMyJzM1LjciRQ!5e0!3m2!1sen!2sla!4v1682650259149!5m2!1sen!2sla" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
</div>
<!-- ***********Footer ************* -->
<div class="footer" style="text-align: left;">
   <?php 
      include("footer.php");
    ?>
  </div>
</body>
</html>