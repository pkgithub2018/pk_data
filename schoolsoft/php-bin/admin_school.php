<!--********* HEADER *************-->	
<div class="header">
 <!-- <a href="#default" class="logo">&nbsp;</a>    -->
 <!-- <div class="header-left"><img src="../images/logo.jpg" width="170" height="100"></div> -->
  <div class="header-right">
	
	<a id="rguserid" href="content.php?sad=reguser">ທະບຽນຜູ້ໃຊ້</a>  <!-- class="active" -->
	<a id="studid" href="content.php?sad=stud">ນັກຮຽນ</a>
	<a id="classid" href="content.php?sad=class">ຫ້ອງຮຽນ</a>
	<a id="ptfeesid" href="content.php?sad=schfees">ຈ່າຍຄ່າຮຽນ ແລະ ອື່ນໆ</a> 
	<a id="reportid" href="content.php?sad=schfeesreport">ລາຍງານ</a>
	<a id="adminid" href="content.php?sad=user"><i class="fa fa-fw fa-user"></i>&nbsp;<?php echo $un; ?></a>  <!-- $un from content.php -->
	<a href="login.php">ອອກຈາກລະບົບ</a> 
	
  </div>
</div>

<!-- Main DIV -->
<!--
<div style="width: 100%;float: left; display: inline; margin-top: 95px"> 
	<div align="center" class="cominpt">
		List of registered users
	</div>
</div> 
-->
<!-- End of Main DIV -->	
<div style="width: 100%;float: left; display: inline; margin-top: 70px">
 <?php
	$gadmin="";
	if(!empty($_GET["sad"])){
	 $gadmin=$_GET["sad"];	
	} else {
	  $gadmin=$utype; // First login
	 // echo "First login:".$gadmin;
	}
    
	switch($gadmin){
		case "6":   // First login with usertype = 6 - school admin : ພ/ງ ບໍລິຫານ-ສາມັນ
		case "user": // When click on user tap
		  echo "<script>
			   var useradmin=document.getElementById('adminid');
				   useradmin.classList.add('activealink');
			   </script>";
		  include("sadmin_profile.php");
		  break;
		case "reguser":
		case "userfup": // user template file upload
		  echo "<script>
			   var ruser=document.getElementById('rguserid');
				   ruser.classList.add('activealink');
			   </script>";
		  include("sad_userreg.php");
		  break;
		case "stud":
		case "stuup":  // Update for students - from modal form 
		case "stusearch": // Search student by name
			echo "<script>
			   var stud=document.getElementById('studid');
				   stud.classList.add('activealink');
			   </script>";
			//echo "Pupil and student";
			include("sad_students.php");
			break;
		case "adstaff": 
		 echo "<script>
			   var astaff=document.getElementById('astaffid');
				   astaff.classList.add('activealink');
			   </script>";
		
		 echo "Admin staff";
		 break;
			
		case "class":
		case "classadd":
		  echo "<script>
			   var cl=document.getElementById('classid');
				   cl.classList.add('activealink');
			   </script>";
		  include("sad_class.php");
		break;
         // Payment fees ***********
		 case "schfees":
			echo "<script>
				    var stable = document.getElementById('ptfeesid');
					    stable.classList.add('activealink');
					</script>";
			 include("adminstaff_payfees.php");
			break;  
		// Report on fees
		case "schfeesreport":
			echo "<script>
				    var stable = document.getElementById('reportid');
					    stable.classList.add('activealink');
					</script>";
			include("adminstaff_payfees-report.php");
			break;

	} // End of switch
  ?>
</div>
<script>
//****** Modal form - FA creation
var modfch=document.getElementById("mfchpw");
 // Get the <span> element that closes the modal
var btncross = document.getElementsByClassName("close")[0];
var btnmod=document.getElementById("btnchpsw");
 btnmod.onclick=function(){
   modfch.style.display="block";
   document.getElementById("pswoldid").focus(); // Set focus
 }
 btncross.onclick=function(){
   modfch.style.display="none";
 }
 
</script>