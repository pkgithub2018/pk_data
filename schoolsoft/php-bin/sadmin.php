<!--********* HEADER *************-->	
<div class="header">
 <!-- <a href="#default" class="logo">&nbsp;</a>    -->
 <!-- <div class="header-left"><img src="../images/logo.jpg" width="170" height="100"></div> -->
  <div class="header-right">
	
	<a id="rguserid" href="content.php?sad=reguser">ທະບຽນຜູ້ໃຊ້</a>  <!-- class="active" -->
	<a id="studid" href="content.php?sad=stud">ນັກຮຽນ ແລະ ນັກສືກສາ</a>
	<a id="astaffid" href="content.php?sad=adstaff">ພະນັກງານບໍລິຫານ</a>
	<a id="teachid" href="content.php?sad=teacher">ຄູອາຈານ</a>
	<a id="stuappid" href="content.php?sad=stuapply">ຜູ້ສະໝັກຮຽນ</a>
	<a id="studyid" href="content.php?sad=studyarea">ຂະແໜງຮຽນ</a>
	<a id="subid" href="content.php?sad=subject">ວິຊາຮຽນ</a>
	<a id="classid" href="content.php?sad=class">ຫ້ອງຮຽນ</a>
	<a id="stableid" href="content.php?sad=stable">ຕາຕະລາງຮຽນ</a> 
	<a id="adminid" href="content.php?sad=user"><i class="fa fa-fw fa-user"></i>&nbsp;<?php echo $un; ?></a>  <!-- $un from content.php -->
	<a href="login.php">ອອກຈາກລະບົບ</a> 
	<a id="setting" href="#"><i class="fa fa-cog"></i>&nbsp;ຕັ້ງຄ່າ</a>
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
		case "2":   // First login with usertype = 2 - system admin
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
		case "teacher":
		case "taddmof":       
		case "ttch":
		 echo "<script>
			   var adteach=document.getElementById('teachid');
				   adteach.classList.add('activealink');
			   </script>";
		    include("sad_teacher.php");
		 break;
		case "stuapply":
		 echo "<script>
			   var adteach=document.getElementById('stuappid');
				   adteach.classList.add('activealink');
			   </script>";
		   echo "Apply for study";
		  break;
			
		case "studyarea":
			echo "<script>
			   var adteach=document.getElementById('studyid');
				   adteach.classList.add('activealink');
			   </script>";
		   include("sad_studyarea.php");
		  break;
			
	    case "subject":
		case "subadd": 
		  echo "<script>
			   var subj=document.getElementById('subid');
				   subj.classList.add('activealink');
			   </script>";
			include("sad_subject.php");
		 //echo "Subject";
		 break;
			
		case "class":
		case "classadd":
		  echo "<script>
			   var cl=document.getElementById('classid');
				   cl.classList.add('activealink');
			   </script>";
		  include("sad_class.php");
		break;
			
		case "stable": 
		 echo "<script>
			   var stb=document.getElementById('stableid');
				   stb.classList.add('activealink');
			   </script>";
		   include("sad_studytable.php");	
		 break;
			
		case "admn":
		 echo "<script>
			   var aduser=document.getElementById('userid');
				   aduser.classList.add('activealink');
			   </script>";
		 echo "Hello, user";
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