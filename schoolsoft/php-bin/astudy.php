<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Study application</title>
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
<?php
 include("connection.php");
?>
 <!--********* HEADER *************-->	
<div class="header">
 <!-- <a href="#default" class="logo">&nbsp;</a>    -->
 <!-- <div class="header-left"><img src="../images/logo.jpg" width="170" height="100"></div> -->
  <div class="header-right">
	<a href="../index.php">ໜ້າຫຼັກ</a>
	<a href="institute.php?access=inst">ສະຖາບັນ</a>
	<a href="currlum.php?access=curr">ຫຼັກສູດ</a>
	<a class="active" href="#">ສະໜັກຮຽນ</a>
    <a href="contact.php?access=cont">ຕິດຕໍ່ພົວພັນ</a>
	<a href="login.php"><i class="fa fa-fw fa-user"></i>&nbsp;ເຂົ້າລະບົບ</a>
  </div>
</div>
	
 <div style="width: 100%;float: left; display: inline; margin-top: 85px"> <!-- DIV content -->
	<!-- left DIV ***************** --> 
	<div style="width: 40%; float: left; margin-right: 20px; margin-top: 40px; text-align: right;">
	 <img src="../images/apstudy.jpg">
	 <h1 align="left" style='margin-left: 45px;'>ຄໍາຖາມ & ຄໍາຕອບ</h1>
		<p align="left" style='margin-left: 30px;'><b>1. ຈ່າຍຄ່າຮຽນຫຼາຍເທື່ອໄດ້ບໍ?</b><br>
			&nbsp;&nbsp;&nbsp;ໄດ້, ສາມາດຈ່າຍເປັນເດືອນ, ເປັນເທີມ ແລະ ຈ່າຍເປັນປີ
	 </p>
	</div>
	 <!-- right DIV ***************** --> 
	 <div style="width: 58%; float: left"> 
	   <h1>ສະໝັກເຂົ້າຮຽນ</h1>
	   <hr>	 
	   <h3>ກະລຸນາ ປະກອບແບບຟອມ ສະໝັກເຂົ້າຮຽນ</h3>	 
		 <div>
		    ບ່ອນໝາຍດາວ (*) ເປັນຂໍ້ມູນທີ່ສໍາຄັນ ແລະ ຈໍາເປັນຕ້ອງປະກອບໃສ່ (ຫ້າມປະວ່າງ)
		 </div>
		 <!-- DIV - form -->
		 <div class="csfelment">
		 <form method="post" action="astudy.php">
		   <table style="width: 100%; margin-right: 5px">
			 <tr><td colspan="2">&nbsp;</td><td rowspan="12" style="width: 20%"><div id="consub">&nbsp;</div></td></tr>
			 <tr><td align="right">ຊື່ (*)&nbsp;</td><td align="left"><input type="text" name="sname" id="sid"></td></tr>
			 <tr><td align="right">ນາມສະກຸນ&nbsp;</td><td align="left"><input type="text" name="surname" id="surid"></td></tr>
			 <tr>
				 <td align="right">ເພດ (*)&nbsp;</td>
				 <td align="left">
					 <select name="sex" id="sexid">
					  <option value="">--ກະລຸນາເລືອກຄໍາຕອບ--</option>
					  <option value="m">ຊາຍ</option>
					  <option value="f">ຍິງ</option>
					 </select>
				 </td>
			  </tr>
			  <tr>
				 <td align="right">ສະຖານະພາບ&nbsp;</td>
				 <td align="left">
					 <select name="status" id="statusid">
					  <option value="">--ກະລຸນາເລືອກຄໍາຕອບ--</option>
					  <option value="s">ໂສດ</option>
					  <option value="m">ແຕ່ງງານ</option>
					 </select>
				 </td>
			  </tr>
			  <tr><td align="right">ວັນເດືອນປີ ເກີດ (*)&nbsp;</td><td align="left"><input type="date" name="bdate" id="bdateid"></td></tr>
			  <tr><td align="right">ບ້ານຢູ່ປະຈຸບັນ (*)&nbsp;</td><td align="left"><input type="text" name="vname" id="vid"></td></tr>
			  <tr>
				 <td align="right">ແຂວງ&nbsp;</td>
				 <td align="left"><select name="pvname" id="pvid">&nbsp;</select>
				 </td>
			  </tr>
			  <tr>
				 <td align="right">ເມືອງ&nbsp;</td>
				 <td align="left"><select name="dtname" id="dtid">&nbsp;</select><div id="sdistid">&nbsp;</div></td> <!-- DIV - for making happy -->
			  </tr>
			   <tr><td align="right">ມືຖື/ວອດແອບ (ຕຢ: 55112860) (*)&nbsp;</td><td align="left"><input type="text" name="phone" id="phoneid"></td></tr>
			   <tr><td align="right">ອີແມ໋ວ&nbsp;</td><td align="left"><input type="text" name="email" id="emailid"></td></tr>
			   <tr><td>&nbsp;</td><td align="left"><input type="submit" name="btsubm" id="btsbmid" value="ບັນທຶກ/ສົ່ງ" /></td></tr>
		  </table>
		 </form>
		 </div>	
		 <!-- End of DIV - form -->
	 </div>
	 <!-- End of right DIV ***************** --> 
 </div>
  <!-- End of DIV content ***************** -->
	
<?php
// DATA FOR PROVICE - Select	
 echo "<script>
	  var spv=document.getElementById('pvid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      spv.options.add(opt_non);
	  </script>";
 //mysqli_query("SET NAMES utf8"); - NOT WORKING NOW
 $con->set_charset("utf8");
 $sqlspr="SELECT provid,provname_lao FROM tbprovince ORDER BY provid ASC";
 $respr=mysqli_query($con,$sqlspr) or die(mysqli_connect_error());
 while($rw=mysqli_fetch_array($respr)){
	$pvid=$rw["provid"];
	$pvname=$rw["provname_lao"];
  // Province select 
	echo "<script>
		  var gpvid='$pvid';
		  var gpvname='$pvname';	     
		  var opt=document.createElement('option');
			       opt.value=gpvid;
			       opt.text=gpvname;
			   spv.options.add(opt);
              </script>";
 }	
// FORM SUBMISSION - Application for study
if(isset($_POST["btsubm"])){
	$dconf="";
  // Check if empty input - it must be filled with data
	if(!empty($_POST["sname"]) && 
	  !empty($_POST["sex"]) && 
	  !empty($_POST["bdate"]) &&
	  !empty($_POST["vname"]) && 
	  !empty($_POST["phone"])){
	  $dconf="data";
	} else {
	  $dconf="empty";
	}
} // End of if isset
?>
 <div class="footer" style="text-align: left;">
   <?php 
      include("footer.php");
    ?>
  </div>
</body>
 <!-- ******************* SCRIPT ******************** -->
<script>
 $(document).ready(function(){
	$("#pvid").change(function(){
	//alert("Hello, Province");
	  var spvid=$(this).val();
	    if(spvid){
		  $.ajax({
			type: "POST",
			url: "lsdistrict.php",
			data: {pvid: spvid},
			success: function(rdata){
			  $("#sdistid").html(rdata);  //Assign to DIV (sdistid) to make it happy
			}
		  });	   
		}	
	});
  // Submission MSM
  var dconf ="<?php $ms=$dconf; echo $ms; ?>";
 if(dconf.length>0){
  var dvms=document.getElementById("consub");
  var msms="<p>ຂໍ້ມູນ ຂອງ ທ່ານ ກໍາລັງຖືກສົ່ງມາຫາພວກເຮົາ.ກະລຸນາ <a href='#' style='color: black'>This link</a> ເພື່ອກັບຫາໜ້າຫຼັກ ຫຼື ປຸ່ມອື່ນໆຂ້າງເທີງ</p>";
  if(dconf=="empty"){
	  dvms.innerHTML="ຂໍ້ມູນບໍ່ຄົບຖ້ວນ. ກະລຸນາ ກວດຄືນ ແລະ ຕື່ມຄືນໃໝ່";
	}
  if(dconf=="data"){
	  dvms.innerHTML = msms;
	}
 }
 }); // End of ready document
</script>
</html>