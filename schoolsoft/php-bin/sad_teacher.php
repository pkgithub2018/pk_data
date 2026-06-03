<?php
 // session_start(); // Already started in content.php
 $guname=$_SESSION["guname"];
 $gpsw=$_SESSION["gpsw"];
?> 
<!-- MAIN DIV -->
<!-- 1. Search *******   -->
<div align="center" style="" class="usform">  <!-- *** class="csfelment" *** -->
  <div align="left" style="margin-left: 60px; width: 70%;">
	  <h2 align="left">ຊອກຫາຂໍ້ມູນ ພະນັກງານ ຄູ-ອາຈານ</h2>
	  <p align="left">ທ່ານ ສາມາດ ຊອກຫາຂໍ້ມູນ ພະນັກງານ ຄູ-ອາຈານ ດ້ວຍພີມຊື່ ລົງໃນຫ້ອງຂ້າງລຸ່ມນີ້</p>
	  <table style="width: 90%;">
	   <tr>
		<td style="width: 70%"><form id="fsearchtch" action="content.php?sad=teacher&username=<?php echo $guname; ?>&passw=<?php $gpsw; ?>" method="post"><i class='fa fa-search' style='font-size:20px; margin-left: 10px'></i>&nbsp;<input type="text" name="txttchsearch" id="txttchsearchid" placeholder="&nbsp;ກະລຸນາ ພີມຊື່ ຂອງ ຄູ-ອາຈານ ທີຕ້ອງການຊອກ" style="width: 85%; color: #bbbbbb" onChange="Searchteacher();"></form>	
		</td> 
	   </tr>
	 
		 <td>&nbsp;</td>
	   </tr>
	  </table>
  </div>
  
  <div align="left" class="adpstud"> <!-- Main DIV *******   -->
   <!-- ****************DIV - Left side ******************** -->
   <div style="width: 30%; float: left; margin-left: 20px;">
  <?php 
	// SEARCH ********************************************************
	
	$txtsearchtch = $_POST["txttchsearch"] ?? ''; // Teacher's name
	    
	if(!empty($txtsearchtch)){
	  $sqltcher = "SELECT id,namelao,snamelao, gender, mphone, email FROM tbusers WHERE namelao LIKE '$txtsearchtch%' AND (usertype='1' AND status='enable')"; // 1 - Teachers
	} else {
	  $sqltcher = "SELECT id,namelao,snamelao, gender, mphone, email FROM tbusers WHERE usertype='1' AND status='enable'"; // 1 - Teachers
	}
	
	$rtcher = mysqli_query($con,$sqltcher) or die(mysqli_connect_error());
	if(mysqli_num_rows($rtcher)>0){
	
	  while($rt=mysqli_fetch_array($rtcher)){
		$uid = $rt["id"];
		$tname = $rt["namelao"];
		$tsname = $rt["snamelao"];
		
		// TEACHER'S INFORMATION
		list($user, $tdegree, $tarea, $tclass, $tknow, $tgraduate, $tpost, $timg) = Rteachinfo($uid, $con);
		$clname = Rclassname($tclass, $con);
		$imgname1 = "../images/users_images/".$timg;  // Photo
		print "<div style='width: 90%; float: left; display: inline-block; margin-left: 50px; margin-bottom: 30px;'>";
		// DIV - Photo *****************************
		print "<div name='dtphoto' id='dtphotoid' style='width:30%; height: 120px; float: left; padding: 10px; margin: 5px'><img src='".$imgname1."' width='130' height='170' style='margin-right: 25px' />$imgname</div>";
		// DIV - Details of teachers ***************
		print "<div style='width:60%; height: 230px; float: left; padding: 3px'>";  // Content
		
		$tsex = $rt["gender"];
		if($tsex=="m"){
		  $tsex ="ຊາຍ";	
		} else {
		  $tsex = "ຍີງ";	
		}
		$tphone = $rt["mphone"];
		$temail = $rt["email"];
		$tnsname = "ອາຈານ.  ".$tname." ".$tsname;
		
	    print "<b>ອາຈານ.  ".$tname." ".$tsname."  "."(".$tsex.")</b>"."<br>";
		print "ໂທລະສັບ: ".$tphone."<br>";
		print "ອີແມ໋ວ: ".$temail."<br>";
		print "<b>ຄູປະຈໍາຫ້ອງ:</b> $clname    ".", ".Rdgree($tdegree, $con).", ".Rsarea($tarea, $con)."<br>";
		print "<b>ລະດັບຄວາມຮູ້:</b> ".Rdgree($tknow,$con)."<br>";
		print "<b>ຂະແໜງ/ວິຊາ:</b> ".Rgraduate($tgraduate, $con)."<br>";
		print "<b>ຕໍ່າແໜ່ງ:</b> ".Rposition($tpost, $con)."<br>";
		$btnid = "btn".$user; // Useed as button id
		print "<form action='content.php?sad=teacher&tcher=add' method='post'>
		        <input type='hidden' name='huserid' value='$uid'>
				<input type='hidden' name='htnsname' value='$tnsname'>
				<input type='submit' name='btaddtcher' id='$btnid' value='ເພີ່ມ' style='width: 60%'>
			  </form>";
		echo "<script>
		       var dgid = '$dgree';
			   var staid = '$starea';
			   var dvalue = '$clname';
			   var knv = '$tknow';
			   var btnsub = document.getElementById('$btnid');
				 
			   if(dgid.length>0 || staid.length>0 || dvalue.length>0 || knv.length>0){
			    btnsub.value = 'ປັບປຸງ';
				btnsub.style.color = 'yellow';
			   }    
			  </script>";
		print "</div>"; // End of content
	   print "</div>";
	  } // End of while
	} // End of if>0
	//$con->set_charset("utf8");
/*
	if($_GET["tcher"]){
	  echo "Teacher".$_GET["tcher"];
	}
*/	
	// GET hidden value - huserid
	if(isset($_POST["btaddtcher"])){ // When submit button is clicked. This button is above
	 // echo "Userid: ".$_POST["huserid"];
	}
	
	// TEACHER INFO - SAVE/ADD DATA from Modal form ****************************
	if(isset($_POST["btsubmof"]) && $_POST["btsubmof"]=="ບັນທຶກ"){ // When Submit button in Modal form is clicked
	 // echo "File name:".$_FILES["tphoto"]["name"];	
	  $desfile="../images/users_images/";
	  $filename = basename($_FILES["tphoto"]["name"]); // Return file namme from file path
	  //echo "Filename:".$filename;
	  $filepath=$desfile.$filename;
	  $filetype=pathinfo($filepath,PATHINFO_EXTENSION); // NOT USED FOR NOW
	 // echo "<br>";
	 // echo "Teach degree: ".$_POST["tdegree"]."  ".$_POST["sstarea"]."  ".$_POST["sclass"]."  ".$_POST["slevel"]."  ".$_POST["sknow"]." ".$_POST["sposition"]."<br>";
	//  echo "Photo: ".$_POST["tphoto"];
		// INPUT from MODAL FORM
		if(!empty($_POST["tdegree"]) && 
		  !empty($_POST["sstarea"]) && 
		  !empty($_POST["sclass"]) && 
		  !empty($_POST["slevel"]) && 
		  !empty($_POST["sknow"]) && 
		  !empty($_POST["sposition"])){
			// SAVE INPUT DATA - Teachers ************
		   // Upload images
		  if(move_uploaded_file($_FILES["tphoto"]["tmp_name"], $filepath)){	
			$tid = $_POST["husermof"]; 
		   Savetchinfo($tid, $_POST["tdegree"], $_POST["sstarea"], $_POST["sclass"], $_POST["slevel"], $_POST["sknow"], $_POST["sposition"], $filename, $filepath, $con);
		  } // If image is uploaded into HOST SERVER
		} else {
			// SOME INPUTS ARE EMPTY - Message  
		} // End of if !empty
		
	} // If isset - button - teacher info
  
 // UPDATE TEACHER'S INFOR in MODAL FORM****************************
  if(isset($_POST["btsubmof"]) && $_POST["btsubmof"]=="ປັບປຸງ"){ // Button in modal form
	  $teacherid = $_POST["husermof"]; 
	  $sqlcheckup = "SELECT * FROM tbcteachers WHERE userid='$teacherid'";
	  $rchup = mysqli_query($con, $sqlcheckup) or die(mysqli_connect_error());
	  list($utid, $dgree,$area, $clbs, $dgkn, $gduate, $pstion, $imgn, $imgpath) = mysqli_fetch_array($rchup);
	  
	  $upimgtmp = $_FILES["tphoto"]["tmp_name"]; // In modal form
	  $upimage = basename($_FILES["tphoto"]["name"]); // Name of NEW IMAGE from modal form
	  $extinf = $utid.$dgree.$area.$clbs.$dgkn.$gduate.$pstion;
	  $updinf = $_POST["husermof"].$_POST["tdegree"].$_POST["sstarea"].$_POST["sclass"].$_POST["slevel"].$_POST["sknow"].$_POST["sposition"];
	  
	   if($extinf == $updinf){  // If1 If existing data is the same as the updated one
		 if($imgn == $upimage || empty($upimage)){  // if2 $imgn - Name of EXISTING IMAGE IN DATABASE
			echo "No update"; 
		 } else {
			// UPDATE Photo/image ONLY
			echo "Update on photo only"."<br>";
			echo "Current image's name: ".$imgn."<br>";
			echo "Current image temp: ".$_FILES["tphoto"]["tmp_name"];
			
			$extimg = "../images/users_images/".$imgn;  // Existing/current image 
			
			 if(is_file($extimg)){
				unlink($extimg);   // Delete the old file of photo
			 } else {
				 echo "Image file is not found";
			 }
			 
			$upimgpath = "../images/users_images/".$upimage; // Image for update
			move_uploaded_file($upimgtmp, $upimgpath);  // Upload image to folder: ../images/users_images/	
			$sqlimup = "UPDATE tbcteachers SET imfilename='".$upimage."', imfilepath='".$upimgpath."' WHERE userid='$teacherid'";
			mysqli_query($con, $sqlimup) or die(mysqli_connect_error());
			// Refresh page
            echo "<script type='text/javascript'>window.location.href = 'content.php?sad=teacher';</script>";
	        exit();
		 } // End of if2
		   
	   } else { // Else If1 the data is different : Need to update
		  Updatetchinfo($_POST["husermof"], $_POST["tdegree"], $_POST["sstarea"], $_POST["sclass"], $_POST["slevel"], $_POST["sknow"], $_POST["sposition"], $imgn, $upimage, $upimgtmp, $con);
	   } // End of If1 *************
	  
	//  echo "Update-1: ".$updinf."<br>";
	//  echo "Existing-1: ".$extinf."<br>";
	  
  }
 ?>
  </div> <!-- End of DIV left side -->

  <!-- ****************DIV - Right side******************** -->
  <div id="dvright" style="width: 60%; float: left; margin-left: 5px;">
	 <?php
	  // REPEAT AS LEFT SIDE
	  
	  if(!empty($txtsearchtch)){
		 $sqltcherleft = "SELECT id,namelao,snamelao, gender, mphone, email FROM tbusers WHERE namelao LIKE '$txtsearchtch%' AND (usertype='1' AND status='enable')"; 
	  } else {
		$sqltcherleft = "SELECT id,namelao,snamelao, gender, mphone, email FROM tbusers WHERE usertype='1' AND status='enable'";  
	  }
	  
	   $rtchleft = mysqli_query($con,$sqltcherleft) or die(mysqli_connect_error());
	   if(mysqli_num_rows($rtchleft)>0){
		  $tnum = mysqli_num_rows($rtchleft);  // Number of teachers
		  $tsn = 0; 
		  $nteach = array();
		  while($rlf = mysqli_fetch_array($rtchleft)){
			$tsn = $tsn + 1;
			$userlf = $rlf["id"]; 
			 
			print "<div style='width: 100%; float: left; display: inline-block; margin-left: 10px; margin-bottom: 30px;'>";
			print "<div style='width:95%; height: 230px; float: left; border: 1px solid grey; padding: 3px'>";  // Content
			 // echo $userlf;
			  $spid = "sp".$userlf; 
			
			print "<form id='frteach' name='frteach' action='content.php?sad=teacher&tcher=teaching' method='post' class='ftspan'>";
			print "<span id='$userlf' style='float: right' onClick='subform(this.id);'>ວິຊາສອນ<i class='fa-solid fa-table'></i></span>";
			//print "<input type='hidden' ";
			print "</form>";
			
			 // SHOW/PRESENT Teaching table for teacher *********************
			 Teachingtable($userlf, $con);
			print "</div>"; // End of content
			print "</div>";
		  } // End of while // Users
	   } // End of if>0
	  
  // COMMON VARIABLES (in modal form) FOR SAVE AND UPDATE ON teaching schedule
	    $huser= $_POST["huserteaching"] ?? '';  // Get from hidden input in modal form for teaching
		$tdegree = $_POST["tchdgree"] ?? ''; // NOT USED 
	    $tarea = $_POST["tcharea"] ?? '';  // NOT USED FOR SAVING IN tbteaching
	    $tsub = $_POST["tchsub"] ?? '';
	    $tclass = $_POST["tchclass"] ?? '';
	    $tday = $_POST["tchday"] ?? '';
	    $ttime = $_POST["tchtime"] ?? '';
	    $tsem = $_POST["tchsemester"] ?? '';
  // TEACHING submission - SAVE DATA from Modal form *****************************
	 if(isset($_POST["btsubmoftch"]) && $_POST["btsubmoftch"]=="ບັນທຶກ"){
		
		$acyear = Academicyear($con); // Automatically updated in Sept each year
		 echo "user:".$huser."<br>";
		 echo "Sub:".$tsub."<br>";
		 echo "Class:".$tclass."<br>";
		 echo "Day: ".$tday."<br>";
		 echo "time:".$ttime."<br>";
		 echo "sem: ".$tsem."<br>";
		 echo "Ayear: ".$acyear."<br>";
		 
	  if(!empty($huser) && !empty($tsub) && !empty($tclass) && !empty($tday) && !empty($ttime) && !empty($tsem)){
		  Saveteaching($huser, $tsub, $tclass, $tday, $ttime, $tsem, $acyear, $con); // INSERT DATA INTO tbteaching
		  //echo "Saved teaching"; 
	  } else {
		  echo "Stay on the modal form - teaching - SOME EMPTY";
	  } // End of if - empty checking
	 } // End of isset- SUBMIT btsubmoftch
	  
  //1. DELETE Teaching schedule for teacher ***************
  
	 $tchid = $_GET["deltchid"] ?? ''; // teaching ID in supports.php
	 if(!empty($tchid)){
	   $sqldelt ="DELETE FROM tbteaching WHERE id='$tchid'";	
	   mysqli_query($con,$sqldelt) or die(mysqli_connect_error()); 
	   // Refresh the page TARGETING AT DIV - Right - GO BACK TO Teacher - teaching
	    echo "<script type='text/javascript'>window.location.href = 'content.php?sad=teacher#dvright';</script>";
	    exit();
	 }
	  
   // 2. UPDATE Teaching schedule ******
	 $tchidup = $_POST["htching"] ?? ''; // teaching ID from hidden input in modal form
	 $tchcct = ""; // concat values
	  
	if(isset($_POST["btsubmoftch"]) && $_POST["btsubmoftch"]=="ປັບປຸງ"){
	   $tchcct = $tchidup.$huser.$tsub.$tclass.$tday.$ttime.$tsem;
	   //echo "concat:".$tchcct;
	   $sqltup = "SELECT CONCAT(id, userid, subjid, classid, teachday, teachtime, semester) AS tchval FROM tbteaching WHERE id='$tchidup'";
	   $rtup = mysqli_query($con,$sqltup) or die(mysqli_connect_error());
	   list($tchval) = mysqli_fetch_array($rtup);
	  // echo "Hi Up: ".$tchval;
	   if($tchcct==$tchval && !empty($tchval)){
		 echo "No update";  
	   } else {
		 $sqluptch = "UPDATE tbteaching SET subjid='$tsub', classid='$tclass', teachday='$tday', teachtime='$ttime', semester='$tsem' WHERE id='$tchidup'";
		 mysqli_query($con,$sqluptch) or die(mysqli_connect_error());
		 // Refresh the page  
		 echo "<script type='text/javascript'>window.location.href = 'content.php?sad=teacher#dvright';</script>";
	     exit();
	   }
	}
?>
  </div> <!-- End of DIV right side -->
</div> <!-- **** End of DIV - class="adpstud"******* --> 

</div> <!-- End of MAIN DIV -->
<!-- ************ MODAL FORM - Teachers *************** -->
<div id="taddmod" class="modaltcher">
 <!-- Modal content -->
  <div class="modaltcher-content" style="height:85%;">
    <span class="close">&times;&nbsp;</span>
    <div class="modaltcher-heading">
      <div align="center" class="msheading"><span id="mdheadid">ຕື່ມຂໍ້ມູນ ຄູ-ອາຈານ</span></div>
    </div>
    <div style="margin-top:5px" class="usform"> <!-- DIV form -->
	  <p style="margin-left: 45px; font-size: 14pt;"><b><?php $ns = $_POST["htnsname"]; echo $ns; ?></b></p>
    <form id="fmodalfup" action="content.php?sad=taddmof" method="post" class="ftcher" enctype="multipart/form-data">
	  <input type="hidden" name="hmfuname" value="<?php echo $lginname; ?>" >
	  <input type="hidden" name="hmfpsw" value="<?php echo $lginpsw; ?>" >
	    <table align="left" style="width: 100%" class="modaltcher-table"> <!-- top right buttom left -->
		   <tr>
			<td align="right" width="20%">ສອນຂັ້ນ/ຊັ້ນ&nbsp;</td><td width="40%"><select name="tdegree" id="tdegreeid" style="padding: 3px;margin: 3px;">&nbsp;</select></td><td width="35%" rowspan="5" align="left" valign="middle"><div style="width: 90%; height: 250px; padding: 5px; margin-top: 5px;"><img id="photoid" width="200" height="250" /><br>ຮູບ</div></td>
		  </tr>
		   <tr>
			<td align="right">ຂະແໜງ/ວິຊາ&nbsp;</td><td><select name="sstarea" id="sstareaid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		  <tr>
			<td align="right">ຄູປະຈໍາຫ້ອງຮຽນ&nbsp;</td><td><select name="sclass" id="sclassid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		  <tr>
			<td align="right">ລະດັບຄວາມຮູ້&nbsp;</td><td><select name="slevel" id="slevelid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		  <tr>
			<td align="right">ວິຊາຄວາມຮູ້&nbsp;</td><td><select name="sknow" id="sknowid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		  <tr>
			<td align="right">ຕໍ່າແໜ່ງ&nbsp;</td><td><select name="sposition" id="spositionid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		  <tr>
			<td>&nbsp;</td><td>&nbsp;<input type="file" name="tphoto" id="tphotoid" accept="image/gif, image/jpeg, image/png" onchange="loadFile(event)" style="display: none">&nbsp;<label for="tphotoid" style="cursor: pointer;"><span style="font-size: 16px; color: Dodgerblue;">ກົດນີ້ເພື່ອເລືອກຮູບ &nbsp;<i class="fa-solid fa-camera"></i></span></label></td>
		  </tr>
		  <tr>
		  <td>&nbsp;<input type="hidden" name="husermof" id="husermofid" value="<?php $userid=$_POST["huserid"]; echo $userid; ?>" ><input type="hidden" name="hayear" value="<?php echo $ayear; ?>"></td> <!-- HIDDEN INPUT -->
		  </tr>
		  <tr>
		  <td align="right" colspan="2"><input type="button" id="btnexit" value="ຍົກເລີກ/ອອກ" />&nbsp;<input type="submit" name="btsubmof" id="btsubmofid" value="ບັນທຶກ"/></td>
		  </tr>
		</table>
	</form>
	</div> <!-- End of DIV form -->
  </div> <!-- End of DIV - content -->
</div>
<!-- End of Modal form -->

<!-- MODAL FORM - Teaching ********************************** -->
  <div id="teachingmod" class="modalteaching">
 <!-- Modal content -->
  <div class="modalteaching-content">
    <span class="closetch">&times;&nbsp;</span>
    <div class="modalteaching-heading">
      <div align="center" class="msheading"><span id="mdheadid">ຕື່ມຂໍ້ມູນ ຕາຕະລາງສິດສອນ</span></div>
    </div>
	<div style="margin-top:5px" class="usform"> <!-- DIV form -->
	  <p align="left" id="tnameid" style="margin-left: 45px; font-size: 14pt;">&nbsp;</p>
    <form id="fmodaltch" action="content.php?sad=ttch" method="post" class="ftcher">
		<input type="hidden" name="huserteaching" id="huserteachingfid" value="" /> <!-- $uid from sad_teacher-teaching.php -->
		<input type="hidden" name="htching" id="htchingid" value="<?php echo $_GET["cteach"]; ?>" /> <!-- the value from sad_teacher-teaching-update.php -->
		<table align="left" style="width: 100%" class="modaltcher-table"> <!-- top right buttom left -->
		   <tr>
			<td align="right" width="20%">ສອນຂັ້ນ/ຊັ້ນ&nbsp;</td><td width="40%"><select name="tchdgree" id="tchdgreeid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		  <tr>
			<td align="right" width="20%">ຂະແໜງ/ສາຂາວິຊາ&nbsp;</td><td width="40%"><select name="tcharea" id="tchareaid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		  <tr>
			<td align="right" width="20%">ວິຊາສອນ&nbsp;</td><td width="40%"><select name="tchsub" id="tchsubid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		  <tr>
			<td align="right" width="20%">ຫ້ອງຮຽນ&nbsp;</td><td width="40%"><select name="tchclass" id="tchclassid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		  <tr>
			<td align="right" width="20%">ມື້/ວັນ&nbsp;</td><td width="40%"><select name="tchday" id="tchdayid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		  <tr>
			<td align="right" width="20%">ເວລາ&nbsp;</td><td width="40%"><select name="tchtime" id="tchtimeid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		  <tr>
			<td align="right" width="20%">ພາກຮຽນ&nbsp;</td><td width="40%"><select name="tchsemester" id="tchtsemesterid" style="padding: 3px;margin: 3px;">&nbsp;</select></td>
		  </tr>
		<!-- ACADEMIC YEAR is added automatically -->
		  <tr>
		  <td>&nbsp;</td><td align="right"><input type="button" id="btnexit" value="ຍົກເລີກ/ອອກ" style="width: 25%" />&nbsp;<input type="submit" name="btsubmoftch" id="btsubmoftchid" value="ບັນທຶກ" style="width: 25%; margin-right: 35px" /></td>
		  </tr>
		</table>	
	</form>  
  </div> <!-- DIV: Content -->
</div>	  
<!-- End of Modal form - Teaching -->

<!-- MODAL FORM - Message -->
<div id="modmess" class="mdmess">
 <!-- Modal content -->
  <div class="mdmess-content" style="height:25%;">
    <span class="closemsg">&times;&nbsp;</span>
    <div class="mdmess-heading">
      <div align="center" class="msheading">ແຈ້ງເຕືອນ</div>
    </div>
	  <div style="display: inline-block;margin-left: 60px; margin-top: 40px">
		  <div style="float: left; vertical-align: middle"><i class="fa-solid fa-triangle-exclamation" style="font-size: 30pt; color: #F6BE00"></i></div>
		  <div style="float: left; vertical-align: middle; margin-top: 10px; font-size: 14pt">ຂໍ້ມູນບໍ່ຄົບຖ້ວນ. ກະລຸນາ ຕື່ມເຂົ້າໃໝ່</div>
	  </div>
  </div>
</div>
<!-- End of Modal form - message -->
<div id="dresult">&nbsp;</div> <!-- **** Receive result - Just make it happy -->

<script>	
$(document).ready(function () {
 // TEACHER'S INFO ****************************** 
$("#sstareaid").change(function(){ // When changing Study area SELECT in MODAL FORM
	var pfor = "stuarea"; //Study area/Subject
	var dgid = document.getElementById("tdegreeid").value;
	var clid = document.getElementById("sclassid");
	// Remove element of CLASSROOM SELECT - every time
	    if(clid.childNodes.length>0){
		    clid.innerHTML=""; // Remove element
		   }
	var stuarid = $(this).val(); // Value of study area
	$.ajax({
	   type: "POST",
	   url: "sad_teacher-info.php",
	   data: {pfor: pfor, dgid: dgid, stuid: stuarid},
	   success: function (rdata){
		$("#dresult").html(rdata); // Just make it happy  
	   }
	  }); 
 }); // End of ssstareaid
// TEACHING - MODAL FORM ***********************************
  var sareatch = document.getElementById('tchareaid'); 
  var subtch = document.getElementById('tchsubid');
  var cltch = document.getElementById('tchclassid');
  var daytch = document.getElementById('tchdayid');
  var teachingtime = document.getElementById('tchtimeid');
  var sttch = document.getElementById('tchtsemesterid');
	
// 1. Degree 
	$("#tchdgreeid").change(function(){
       var sdgree = $(this).val();
	  // alert("Degree Updated: " + sdgree);	
		
       if(sareatch.childNodes.length>0){ 
	      sareatch.innerHTML = "";	// Empty study area at the begin
        }
	   if(subtch.childNodes.length>0){ 
	      subtch.innerHTML = "";	// Empty subject when change on Degree select
        }
		if(cltch.childNodes.length>0){ // Empty class
		  cltch.innerHTML = ""; 
		}
		
		if(daytch.childNodes.length>0){ // Day
		   daytch.innerHTML = ""
		}
		
		if(teachingtime.childNodes.length>0){
		   teachingtime.innerHTML = "";
		}
		if(sttch.childNodes.length>0){
		    sttch.innerHTML= "";
		   }
		
	  $.ajax({
		type: "POST",
		url: "sad_teacher-teaching-class.php",
		data: {sdgreeonchg: sdgree},
		success: function(rdata){
		  $("#dresult").html(rdata); // Just make it happy 	
		}
	  });
	});
// 2. Study area for teaching on change
$("#tchareaid").change(function(){
 var sarid = $(this).val();
 var dgree = document.getElementById("tchdgreeid").value;
 //alert("Area change subject");
 var ssub = document.getElementById('tchsubid'); 
       if(ssub.childNodes.length>0){ 
	      ssub.innerHTML="";	// Empty subject at the begin
        }
  //alert("Area and degree: " + sarid + " "+dgree);
  $.ajax({
	 type: "POST",
	 url: "sad_teacher-teaching-class.php",
	 data: {sarid: sarid, sadgree: dgree},
	 success: function(rdata){
	   $("#dresult").html(rdata); // Just make it happy 
	 }
  });
});
// 3. Subject for teaching on chang
$("#tchsubid").change(function(){
  var subfcls = $(this).val(); // Subject for class
  var dgreecls = document.getElementById("tchdgreeid").value;
  var stareacls = document.getElementById('tchareaid').value;
	
  $.ajax({
	  type: "POST",
	  url: "sad_teacher-teaching-class.php",
	  data: {subcls: subfcls, dgcls: dgreecls, sarcls: stareacls},
	  success: function(gdata){
		$("#dresult").html(gdata); // Just make it happy  
	  }
  });
  
});
	
}); // End of document.ready
// MODAL FORM - Adding teacher's data **************************************
var btnxtmod = document.getElementsByClassName("close")[0]; // Cross button
var tchmof = document.getElementById("taddmod"); 
	
var btsmit = "<?php $bt = $_POST["btaddtcher"]; echo $bt; ?>"; // Button is left DIV - Teacher's infor

// ADD TEACHER'S INFOR - ເພີ່ມ *****************
	if(btsmit.length>0 && btsmit=="ເພີ່ມ"){ 
	  tchmof.style.display="block"; // Open Modal form 
	// FILL the data with SELECT tdegreeid AS modal form pops up
	  var pfor = "begin";
	  $.ajax({
	   type: "POST",
	   url: "sad_teacher-info.php",
	   data: {pfor: pfor},
	   success: function (rdata){
		$("#dresult").html(rdata); // Just make it happy  
	   }
	  }); 
	} // End of if>0 - button 'ເພີ່ມ' is clicked
	
// UPDATE TEACHER'S INFOR - ປັບປຸງ *****************
	if(btsmit.length>0 && btsmit=="ປັບປຸງ"){  
	  tchmof.style.display="block"; // Open Modal form
	  
	  var tuid = "<?php echo $_POST["huserid"]; ?>"; // huserid is from hidden input in teacher's form
	  //alert("Hi, PKeo" + tuid);
	  $.ajax({
		 type: "POST",
		 url: "sad_teacher-info-update.php",
		 data: {usid: tuid},
		 success: function(gdata){
			$("#dresult").html(gdata);  
		 }
	  });
	}
	
btnxtmod.onclick=function(){ // Cross X in MODAL FORM
 tchmof.style.display ="none";	
}

// Button 'Cancle in Modal form'
var btexitmof = document.getElementById("btnexit");
btexitmof.onclick = function(){
 tchmof.style.display ="none";	
}

// DISPLAY PHOTO/IMAGE in <img> in DIV  ***********************************
var loadFile = function(event) {
	var image = document.getElementById('photoid');
	image.src = URL.createObjectURL(event.target.files[0]);
};
// CHANGE BUTTON ACTION: ເພີ່ມ To ປັບປຸງ
function chbtnvalue(dvalue){
 if(dvalue.length>0){
	 var btnt = document.getElementById("btaddtcherid");
	 btnt.value = "ປັບປຸງ";
	}
}
// OPEN MODAL FORM - Teaching - BEGIN **********************
function subform(btval){
var teachfmod = document.getElementById("teachingmod");
    teachfmod.style.display="block";
// btval - userid
var uid = btval;
$.ajax({
	type: "POST",
	url: "sad_teacher-teaching.php",
	data: {userid: uid}, 
	success: function(gdata){
	 $("#dresult").html(gdata);
	}
});
//$("#frteach").submit();
	
}
var btmodteach = document.getElementsByClassName("closetch")[0]; // Cross button - teaching	
btmodteach.onclick = function(){
 var teachfmodcl = document.getElementById("teachingmod");
 teachfmodcl.style.display="none";
}
// OPEN MODAL FORM - Teaching - UPDATE ********************
var teachfmodup = document.getElementById("teachingmod");
var cfmodup = "<?php $opmod = $_GET["cteach"]; echo $opmod; ?>";  // Teachig ID
	if(cfmodup.length>0){
	   teachfmodup.style.display = "block";
	   $.ajax({
		  type: "POST",
		  url: "sad_teacher-teaching-update.php",
		  data: {tchid: cfmodup}, // Teaching ID
		  success: function(rdata){
			$("#dresult").html(rdata);  
		  }
	   });	
	  }

// Search for teacher
function Searchteacher(){
  $("#fsearchtch").submit(); 
}
</script>