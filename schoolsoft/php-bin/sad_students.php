<style>
	.divsc{
	  overflow-y:scroll; 
	  height:200px; 
	  border-bottom: 1px solid lightgrey;	
	}
	
	.btpart{
		width: 80%; 
		float: left; 
		text-align: right;
		margin-left: 2%; 
		margin-top: 10px;
	}
	.btpart select, input[type=submit]{
	  height: 40px;
	}
	.btpart input[type=submit]{
	   font-weight: bold;
       font-size: 12pt;
       color: white;
       background-color: #008bd2; 
       border: 1px solid #ccc;
	}
	
</style>

<?php
 // session_start(); // Already started in content.php
 $guname=$_SESSION["guname"];
 $gpsw=$_SESSION["gpsw"];
 $utype=$_SESSION["usertype"];

// MOVE TO NEXT CLASS ON 1st of Sept in every year
 $tdate = date("d-m-Y");
 
 $cd = substr($tdate,0,2); // First two digits - day 
 $cm = substr($tdate,3,2); // Second two digtis - month: Get two from third digit
// echo "Current:".$cd." ".$cm;


?> 
<style>
	.tbhead{
	  width: 100%;
	  margin-right:30px; 
	  font-size: 11pt;
	  border-radius: 5px;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);	
	  
	}
  .tbhead th{
	background-color: #3b8132; /* #ff9800  */
    color: #fff; 
    font-weight: bold;
    padding: 7px;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-top: 1px solid #fff;
    border-bottom: 1px solid #ccc;
	}
</style>
<!-- MAIN DIV -->
<!-- 1. Search *******   -->
<div align="center" style="" class="usform">  <!-- *** class="csfelment" *** -->
  <div align="left" style="margin-left: 30px; width: 70%;">
	  <h2 align="left">ຊອກຫາຂໍ້ມູນ ນັກຮຽນ ແລະ ນັກສຶກສາ</h2>
	  <p align="left">ທ່ານ ສາມາດ ຊອກຫາຂໍ້ມູນໄດ້ ຕາມຊື່ ຂອງ ນັກຮຽນ/ນັກສຶກສາ, ຫຼື ຊອກໄດ້ຕາມ ຂັ້ນຮຽນ ແລະ ຫ້ອງຮຽນ</p>
	  <table style="width: 90%;">
	   <tr><td>&nbsp;</td><td align="left"><i class="fa fa-graduation-cap" style="font-size:20px;"></i>&nbsp;ຂັ້ນ</td><td><i class="fa fa-university" style="font-size:20px;"></i>&nbsp;ຂະແໜງ/ວິຊາ</td><td align="left"><i class="fa-solid fa-house fa-fw" style="font-size:20px;"></i>&nbsp;ຫ້ອງຮຽນ</td><td>&nbsp;</td></tr>
	   <tr>
		<td style="width: 70%"><form id="fsearchstu" action="content.php?sad=stusearch&sadkid=stusearch&username=<?php echo $guname; ?>&passw=<?php $gpsw; ?>" method="post"><i class='fa fa-search' style='font-size:20px; margin-left: 10px'></i>&nbsp;<input type="text" name="txtstusearch" id="txtstusearchid" placeholder="&nbsp;ກະລຸນາ ພີມຊື່ ຂອງ ນັກຣຽນ ຫຼື ນັກສຶກສາທີຕ້ອງການຊອກ" style="width: 85%; color: #bbbbbb" onChange="Subfsearchstu();"></form>	
		</td>
		   <td align="left"><select name="msdegree" id="msdegreeid" style="width:110px; height: 40px">&nbsp;</select></td><td><select name="msarea" id="msareaid" style="width:190px; height: 40px">&nbsp;</select></td><td align="left"><select name="msclass" id="msclassid" style="width: 110px; height: 40px">&nbsp;</select></td><td>&nbsp;</td>  
	   </tr>
	  </table>
  </div>
  <!-- **** SELECCT - Degree and Class -Fill in with data -->
	<?php 
	 // DEGREE SELECT **********
	  echo "<script>
	  var msdg=document.getElementById('msdegreeid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      msdg.options.add(opt_non);
	  </script>";
 //$con->set_charset("utf8");
// var_dump($utype);
 switch ($utype){
	case '6': //ພ/ງ ບໍລິຫານ-ສາມັນ	
		$sqldgree = "SELECT * FROM `tbdegree` WHERE `id`=1";
		break;
	case '7': //ພ/ງ ບໍລິຫານ-ອະນຸບານ
		$sqldgree = "SELECT * FROM `tbdegree` WHERE `id`=8";
		break;
	default: // All other user types
		 $sqldgree = "SELECT * FROM `tbdegree`";
		break;
 }
  
 $rdgree = mysqli_query($con,$sqldgree) or die(mysqli_connect_error());
 while($rw=mysqli_fetch_array($rdgree)){
	$dgid=$rw["id"];
	$dgname=$rw["degreename"];
  // degree select 
	echo "<script>
		  var dgid='$dgid';
		  var dgname='$dgname';	     
		  var opt=document.createElement('option');
			       opt.value=dgid;
			       opt.text=dgname;
			   msdg.options.add(opt);
              </script>";
 }	
	// CLASS SELECT ************
	// The select of class will be completed onchange of degree selection - in Javascript below
?>
 <!-- 2. Show list of students *******   --> 
  <div align="left" class="adpstud"> 
   <!-- ****************Div - Left side - Table******************** -->
   <div style="width: 70%; float: left; margin-left: 15px;">
  <?php 
	// SEARCH ********************************************************
	$txtsearch = $_POST["txtstusearch"] ?? ''; // Student name
	$searchbydgree = $_GET["seardgree"] ?? ''; // Degree name
	$searchbyarea = $_GET["seararea"] ?? '';
	$searchbycl = $_GET["searclass"] ?? ''; // Class name 
	//echo "Hello, Get class and degree".$searchbycl."  ".$searchbydgree;
	$con->set_charset("utf8");
	if(!empty($txtsearch)){ // If usename 
	   $sqlstu = "SELECT * FROM tbusers WHERE (usertype='3' OR usertype='4') AND namelao LIKE '$txtsearch%'"; 
	} else {
	  
	 if(!empty($searchbycl)){ // If classname is selected/not empty
		$sqlstu = "SELECT a.*, b.* FROM tbusers a, tbcstudents b WHERE a.id = b.userid AND (b.degree = '$searchbydgree' AND b.stuarea='$searchbyarea' AND b.class='$searchbycl')"; 
	  } else {
		switch( $utype){
			case "6":  //ພ/ງ ບໍລິຫານ-ສາມັນ
				$sqlstu = "SELECT * FROM tbusers WHERE usertype='4'";
				break;
			case "7":  //ພ/ງ ບໍລິຫານ-ອະນຸບານ
				$sqlstu = "SELECT * FROM tbusers WHERE usertype='8'";
				break;
			default:
				$sqlstu = "SELECT * FROM tbusers WHERE usertype='3' OR usertype='4' OR usertype='8'"; // All Students only
		}
	  } //End of if classname
	} // End of if username
	$rstu = mysqli_query($con,$sqlstu) or die(mysqli_connect_error());
	   
	if(mysqli_num_rows($rstu)>0){ // if student found
	  print "<table id='tbstid' class='tbhead' align='left'>";
	  print "<tr><th width='3%'>ລດ</th><th width='20%'>ຊື່ ແລະ ນາມສະກຸນ</th><th width='10%'>ວດປ ເກີດ</th><th width='10%'>ໂທລະສັບ</th><th width='12%'>&nbsp;&nbsp;ຂັ້ນ&nbsp;&nbsp;</th><th width='16%'>&nbsp;ຂະແໜງ/ວິຊາ&nbsp;&nbsp;</th><th width='10%'>&nbsp;ຫ້ອງຮຽນ&nbsp;&nbsp;</th><th width='8%'>&nbsp;ສົກຮຽນ&nbsp;</th><th>ສະຖານະພາບ&nbsp;&nbsp;</th></tr>";
	  print "</table>";
	  print "<div style='width:100%; height:550px; overflow-x: hidden; overflow-y: auto;'>"; //******* SCROOL BAR
	  print "<table align='left' style='width: 100%; font-size: 11pt'>";
	  $n = 0;
	  while($r=mysqli_fetch_array($rstu)){
		$n = $n + 1;
		$userid = $r["id"]; // Used as id for button
		$fullname ="";
		$nlao = $r["namelao"];
		$snlao = $r["snamelao"];
		$gender = $r["gender"];
		  if($gender=="m"){
			$fullname = "ທ. ".$nlao." ".$snlao;
		  } else {
			$fullname = "ນ. ".$nlao." ".$snlao;  
		  }
		$db = $r["dbirth"];
		$phonew = $r["mphone"];
		$email = $r["email"];
		//************************** Data from tbcstudents
		 $checkstu = Currentstudent($userid,$con);
		 $btlabel = "";
		 $level ="";
		 $class = "";
		 // Academic year : It automatically changes in every 1st of Sept every year
		// $cdate = date('m-d-Y', time());
		 $cyear = date('y'); // this year in 2 digits
		 $fcyear = date('Y'); // this year in 4 digits
		 $lyear = $fcyear - 1;
		 $nyear = $fcyear + 1; // Next year in 2 digits
		 $cmonth = date('m'); // this month in 2 digits
		 $acrange = array("09","10","11","12"); 
		 if(is_array($cmonth)){ // Acedemic year starts in Sept
		   $ayear = $fcyear."-".$nyear; 
		 } else {
		   $ayear = $lyear."-".$cyear;
		 }
		   
		 if($checkstu=="stnew"){ // New students to be added into tbcstudents 
		   $btlabel = "ເພີ່ມ"; 
		   $level ="<span style='color: blue'><i class='fa-solid fa-street-view'></i>&nbspນັກຮຽນໃໝ່</span>";  // Indicates NO DATA YET
		   $class = "<span style='color: blue'><i class='fa-solid fa-street-view'></i>&nbspນັກຮຽນໃໝ່</span>";
		   $sarea = "<span style='color: blue'><i class='fa-solid fa-street-view'></i>&nbspນັກຮຽນໃໝ່</span>";
		   $ayear = "<span style='color: blue'>$ayear</span>";
		 } else {
		   $btlabel = "ປັບປຸງ";
		   list($sid,$gdgree,$gstuarea,$gclass) = Currentstudent($userid,$con);
		   $level = Rdegreename($gdgree, $con);
		   $sarea = Rsarea($gstuarea, $con);
		   $class = Rclassname($gclass, $con);
		 }
	   	   
	   print "<tr onmouseover='Chcrow(this);' onmouseout='Normrow(this);'><td align='center' width='3%'>$n</td><td width='20%'>$fullname&nbsp;&nbsp;</td><td width='10%'>$db&nbsp;&nbsp;</td><td width='10%'>$phonew&nbsp;&nbsp;</td><td width='12%' align='center'>$level&nbsp;&nbsp;</td><td width='18%' align='center'>$sarea&nbsp;&nbsp;</td><td width='10%' align='center'>$class&nbsp;&nbsp;</td><td width='10%'>$ayear</td><td><button name='btnuser' id='$userid' style='width: 80px; height: 25px; border: 1px solid #fff;' onclick='Openmod(this.id);' value='$btlabel'>$btlabel</button></td></tr>";
	  }	// End of while
	 print "</table>";	
	print "</div>"; // End of SCROOL BAR
	} else { // NOT FOUND
	   echo "<script type='text/javascript'>window.location.href = 'content.php?sad=stud&sadkid=stud';</script>";
	   exit();
	  // echo "NOT FOUND ";
	} // End of if>0 - student found
	 
// SAVE STUDENT DATA for new one - from modal form **********
 $mdinpu = ""; // Store value of confirming some inputs are empty
 
 if(($_POST["btsubmof"] ?? '')=="ບັນທຶກ" && !empty($_POST["sdegree"]) && !empty($_POST["sstarea"]) && !empty($_POST["sclass"])){ // if1 huser - HIDDEN INPUT for storing USERID
	 $uidcst = $_POST["huser"];
	 $dgcst =$_POST["sdegree"];
	 $sarecst = $_POST["sstarea"];
	 $clcst = $_POST["sclass"];
	
	 $sqlincs = "INSERT INTO tbcstudents(userid, degree, stuarea, class, acyear) VALUES('$uidcst', '$dgcst', '$sarecst', '$clcst', '2022-23')";
	 mysqli_query($con,$sqlincs) or die(mysqli_connect_error());
	
   	  // Refresh page
     echo "<script type='text/javascript'>window.location.href = 'content.php?sad=stud&sadkid=stud&checkin=$checkin';</script>";
	 exit();
  } // End of if1
	   
 // UPDATE STUDENT DATA - from MODAL FORM****************
if(($_POST["btsubmof"] ?? '')=="ປັບປຸງ" && !empty($_POST["sdegree"]) && !empty($_POST["sstarea"]) && !empty($_POST["sclass"])){
  $uidup = $_POST["huser"];
  
  $sqlstup = "UPDATE tbcstudents 
              SET degree='".$_POST["sdegree"]."', 
			      stuarea='".$_POST["sstarea"]."', 
				  class='".$_POST["sclass"]."' 
			  WHERE userid='$uidup'";
  mysqli_query($con,$sqlstup) or die(mysqli_connect_error());
  // Refresh page
     echo "<script type='text/javascript'>window.location.href = 'content.php?sad=stud&sadkid=stud&checkin=$checkin';</script>";
	 exit();
}
// Modal form - message - SUBMITTED BY CLICKING A BUTTON	   
 if(isset($_POST["btsubmof"]) && (empty($_POST["sdegree"]) || empty($_POST["sstarea"]) || empty($_POST["sclass"]))){ //if2 Submission of modal form by click button in the form, BUT SOME OF INPUT ARE EMPTY
   $mdinpu = "sub-empty"; 
   $useridb = $_POST["huser"]; // hidden input in modal form
   $degreeb = $_POST["sdegree"];
   $stuareab = $_POST["sstarea"];
   $classb = $_POST["sclass"];
 } // End of if2

 ?>
  </div> <!-- End of DIV left side -->
	  
  <!-- ********************************************Div - Right side ********************************** -->
  <div style="width: 25%; float: left; margin-left: 15px; margin-top: 5px;">
   <?php
    if($utype=="6"){  //ພ/ງ ບໍລິຫານ-ສາມັນ
		//$sqlstu = "SELECT * FROM tbusers WHERE usertype='4'";
		//echo "<script>alert('User type 6 selected- high school');</script>";
		$sqlcls = "SELECT class FROM tbcstudents WHERE degree='1' GROUP BY class"; // Class for high school
		$rclass = mysqli_query($con,$sqlcls) or die(mysqli_connect_error());
		if(mysqli_num_rows($rclass)>0){
          echo "<table>";
		  echo "<tr><th>ຫ້ອງຮຽນ</th></tr>";
		  while($r=mysqli_fetch_array($rclass)){
			  $clid = $r["class"];
			  $clname = Rclassname($clid, $con);
			  $stunum = RstudentNumber($clid, $con);
			  //1 - refers to high school
			  echo "<tr><td><a href='content.php?sad=stud&sadkid=stud&dglist=1&stlist=1&cllist=$clid' style='font-size: 12pt;color: red'>$clname</a></td><td> ຈໍານວນນັກຮຽນ: $stunum ຄົນ</td></tr>";
		  }
		  echo "</table>";
		}

	} else if($utype=="7"){  //ພ/ງ ບໍລິຫານ-ອະນຸບານ
		//$sqlstu = "SELECT * FROM tbusers WHERE usertype='8'";
		SummaryClass(8, 12, $con);  // degree and study area for kindergarten
	} else {  // End if is below
	
   ?>

	<div style="float:left; display: inline-block; background-color: #f4f0ec; width: 90%">
	 <div style="width: 90%; float: left; margin-left: 5px;">
	   <p align="center" style="width: 100%; font-size: 14pt; font-weight: bold; padding: 0px;">ລາຍຊື່ ແລະ ການເລື່ອນຫ້ອງ<br><span style="font-size: 10pt">(ການເລື່ອນຫ້ອງ ຫຼື ເລື່ອນຊັ້ນເຮັດໄດ້ດ້ວຍການເລື່ອນຫ້ອງໃຫຍ່ ໃນຂະແໜງນັ້ນ, ສ່ວນຫ້ອງອື່ນໆຈະເລື່ອນຂື້ນແບບອະຕະນຸມັດ ໃນລະຫວ່າງ ວັນທີ 1 ຫາ 15 ກັນຍາ(9) ຂອງ ແຕ່ລະປີ)</span></p>
		<div style="font-size: 11pt">
		 <?php
		    // Get students by degree, 
		    $sqlldg = "SELECT degree FROM tbcstudents GROUP BY degree";
		    $rdg = mysqli_query($con,$sqlldg) or die(mysqli_connect_error());
			if(mysqli_num_rows($rdg)>0){
			  print "<table align='center' style='width: 100%'>";
			   while($rd=mysqli_fetch_array( $rdg)){
				$dg = $rd["degree"];
				$dgname = Rdgree($dg, $con);
				 print "<tr><td align='left' colspan='6' style='border-bottom: 1px solid black; background-color: #d7d370; height: 30px'><b>$dgname</b></td></tr>";
				$sqlarea = "SELECT stuarea FROM tbcstudents WHERE degree='$dg' GROUP BY stuarea";
				$ra = mysqli_query($con, $sqlarea) or die(mysqli_connect_error());
				$i=0;
				while($r=mysqli_fetch_array($ra)){
				$i = $i + 1;
					$ars = $r["stuarea"];
					$area = Rsarea($ars, $con);
					print "<tr><td align='left' colspan='6'>$i)&nbsp;$area</td><tr>";
					
					$sqlcl = "SELECT class FROM tbcstudents WHERE degree='$dg' AND stuarea='$ars' GROUP BY class";
					$rcl = mysqli_query($con, $sqlcl) or die(mysqli_connect_error()); 
					print "<tr>";
					while($rc=mysqli_fetch_array($rcl)){
					  $clid = $rc["class"];
					  $clname = Rclassname($clid, $con);
					  print "<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href='content.php?sad=stud&sadkid=stud&dglist=$dg&stlist=$ars&cllist=$clid' style='font-size: 12pt;color: red'>$clname</a>&nbsp;</td>";
					} // End of while-class
					print "</tr>";
				  print "</tr>";
				} // End of while - study area
				
			} // End of while - degree
			  print "</table>";
			} // End of if
		    
		   ?>
		 </div>  
	 </div>
   </div>
  <?php
    }  // End of if - user type-Check
  ?>
	  <?php
	     // MOVE TO NEXT CLASS - Submission - button
	     if(isset($_POST["btmcl"])){ // if 0
			// Check if user click on last class of study area from tbcstudents
			$sdg = $_POST["mdg"];
			$ssta = $_POST["msta"];
			$sclass = $_POST["mcl"]; // Class submitted
			//echo "Class move- class".$_POST["mdg"]." ".$_POST["mcl"];
			$sqlclast = "SELECT class FROM tbcstudents WHERE degree='".$_POST["mdg"]."' AND stuarea='".$_POST["msta"]."' GROUP BY class ORDER BY class DESC";
			$rs = mysqli_query($con, $sqlclast) or die(mysqli_connect_error());
			list($lclass_stu) = mysqli_fetch_array($rs); // Last class of the study area
			 if($sclass==$lclass_stu){ // If 1
				 // CLASS CAN BE MOVED (submitted class is equal last class in tbcstudents) *************
				$lcl_all = Rlclass($sdg, $ssta, $con); // Last class  from tbclass among all classess in the study area
				 $sqlpcl = "SELECT id FROM tbclass WHERE degree='$sdg' AND studyarea='$ssta' GROUP BY id ORDER BY id ASC"; // small to big
				 
				 if($lcl_all==$lclass_stu){  // if 2 - class in tbcstudents is equal to that in tbclass - Students complete the course - Graduation
					// GRADUATION - FINISH COURSE - Move students form tbcstudents to tbpstudents with academic year
					$rcn = mysqli_query($con,$sqlpcl) or die(mysqli_connect_error());
					$nclass = mysqli_num_rows($rcn); 
					 
					 for($i=1; $i<$nclass; $i++){  // i - Position of class in the study area
						 switch($i){  //Assume that year 5 is HIGHEST CLASS IN on study area
							 case 1: 
							  $yr1 = Rclassmove($sdg, $ssta, $i, $con);
							  break;
								
							  case 2: 
							  $yr2 = Rclassmove($sdg, $ssta, $i, $con);
							  break;
								 
							  case 3: 
							  $yr3 = Rclassmove($sdg, $ssta, $i, $con);
							  break;
								 
							  case 4: 
							  $yr4 = Rclassmove($sdg, $ssta, $i, $con);
							  break;
								 
							  case 5: 
							  $yr5 = Rclassmove($sdg, $ssta, $i, $con);
							  break;	 
						 } // End of switch
					 } // End of for
				 // Move last year students who graduated to tbpstudents (copy to the table and delete from tbcstudents)
					  echo "Class 1, 2 & 3: ".$yr1."  ".$yr2."  ".$lclass_stu;
					 // $lclass_stu - last year class 
					 $cf_gradu = Checlmove($lclass_stu, $con);
					 if($cf_gradu=="yesmove"){
						Copmvstu($lclass_stu, $con); // copy students' data into tbpstudents before delete it in tbcstudents
						// Set user status to "disable" in tbusers and Delete it from tbcstudents
						 Graduatestu($lclass_stu, $con);
						 // Refresh page
						  Refreshstupage($con);
					 } // End of if - yesmove
				} else {
					// if the active class in tbcstudent is not last class of study area in tbclass - check its postion in tbclass
				  //$sqlpcl = "SELECT id FROM tbclass WHERE degree='$sdg' AND studyarea='$ssta' GROUP BY id ORDER BY id ASC"; // Move to top
				  $rp = mysqli_query($con,$sqlpcl) or die(mysqli_connect_error());
				   $nc = 0; // Number of classs in study area
				   
				  while($r=mysqli_fetch_array($rp)){
					$nc = $nc + 1;
					$lcid = $r["id"];
					$clpost_before =""; // Postion of before active class 
					$clpost_last =""; // position of last active class to be moved
					  
					  $cfmv_ls = ""; // Confirmation on move/copy students' data into tbpstudents
					  $cfmv_bf = ""; 
					  if($lcid==$lclass_stu){ // if 3 $nc - Postion of Active class 
						  //$lcid - CURRENT class to be moved
						  // 1. Move first (after) - NEXT class to which CURRENT class to be moved
						   $clpost_last = $nc + 1; // Positon of last active class in tbcstudent
						   $newcl_last = Rclassmove($sdg, $ssta, $clpost_last, $con); // return class id for last active class
						   // Move/Copy student data in current class from tbcstudents to tbpstudents
						   $cfmv_ls = Checlmove($lcid, $con); 
						   if($cfmv_ls=="yesmove"){
						     Copmvstu($lcid, $con); // Insert students' data into tbpstudents
						     Classmove($lcid, $newcl_last, $con); // Move last class in study area first- UPDATE class in tbcstudents with new one 
						   }
						   
						  // 2. Move after - BEFORE class to be moved to CURRENT class  
						 $clpost_before = $nc - 1; // Postion of before active class
						 if($clpost_before==0){
							$clpost_before = 1; 
						 }
						 $newcl_before = Rclassmove($sdg, $ssta, $clpost_before, $con); // Return class id before active class to be changed as well
						  $cfmv_bf = Checlmove($newcl_before, $con); 
						  if($cfmv_bf=="yesmove"){
							 Copmvstu($newcl_before, $con);
							 Classmove($newcl_before, $lcid, $con); // Move before class to current one - UPDATE class in tbcstudents with new one
							  // Refresh page
							  Refreshstupage($con);
						  }
						  
					  }// end of if 3
				  } // End of while
				} // End of if 2
			 } else {
				 // CLASS CAN NOT BE MOVED ***********************
				 echo "Not last"; // Give warning messege
			 } // End of if 1 Class move	 
		 } // End of if 0 - move to next class
	  ?>
</div> <!-- End of DIV right side --> 
</div> <!-- End of MAIN DIV -->
<!-- ************ MODAL FORM 1 - Update on new student or accademic year : move to another class *************** -->

<!-- Update student modal form - 1 - for new student or update student data -->
<div id="studmod" class="modal" tabindex="-1" style="display:none;">
  <div class="modal-content" style="max-width: 500px; margin: 40px auto; border-radius: 8px; box-shadow: 0 2px 16px rgba(0,0,0,0.15); padding: 24px; height: auto;">
   <span class="close" style="position: absolute; top: 16px; right: 16px; font-size: 1.5rem; cursor:pointer;">&times;</span>
    <div class="modal-heading mb-3" style="width: 95%; padding: 5px;">
		<h4 class="msheading fw-bold text-center" id="mdheadid" style="margin: 0; font-size: 1 rem;">
			ຕື່ມຂໍ້ມູນນັກຮຽນ/ນັກສຶກສາໃໝ່
		</h4>
	</div>
    <form id="fmodalfup" action="content.php?sad=stuup&sadkid=stuup" method="post" autocomplete="off">
      <input type="hidden" name="hmfuname" value="<?php echo $lginname; ?>">
      <input type="hidden" name="hmfpsw" value="<?php echo $lginpsw; ?>">
      <input type="hidden" name="huser" id="huserid" value="<?php echo $userid; ?>">
      <input type="hidden" name="hayear" value="<?php echo $ayear; ?>">
      <div class="mb-3 d-flex align-items-center">
		<label for="stid" class="form-label fw-bold me-2" style="min-width: 120px;">ຊື່ ແລະ ນາມສະກຸນ</label>
		<input type="text" name="stname" id="stid" class="form-control" style="width:100%;" required>
		</div>
		<div class="mb-3 d-flex align-items-center">
		<label for="bdateid" class="form-label fw-bold me-2" style="min-width: 120px;">ວດປ ເກີດ</label>
		<input type="text" name="bdate" id="bdateid" class="form-control" style="width:100%;" required>
		</div>
		<div class="mb-3 d-flex align-items-center">
		<label for="phoneid" class="form-label fw-bold me-2" style="min-width: 120px;">ໂທລະສັບ</label>
		<input type="text" name="phone" id="phoneid" class="form-control" style="width:100%;">
		</div>
		<div class="mb-3 d-flex align-items-center">
		<label for="emailid" class="form-label fw-bold me-2" style="min-width: 120px;">ອີແມ໋ວ</label>
		<input type="email" name="email" id="emailid" class="form-control" style="width:100%;">
		</div>
		<div class="mb-3 d-flex align-items-center">
		<label for="sdegreeid" class="form-label fw-bold me-2" style="min-width: 120px;">ຂັ້ນ/ຊັ້ນຮຽນ</label>
		<select name="sdegree" id="sdegreeid" class="form-select" required style="width:100%;">
			<option value="">-- ເລືອກຂັ້ນ/ຊັ້ນ --</option>
		</select>
		</div>
		<div class="mb-3 d-flex align-items-center">
		<label for="sstareaid" class="form-label fw-bold me-2" style="min-width: 120px;">ວິຊາຮຽນ</label>
		<select name="sstarea" id="sstareaid" class="form-select" required style="width:100%;">
			<option value="">-- ເລືອກວິຊາ --</option>
		</select>
		</div>
		<div class="mb-3 d-flex align-items-center">
		<label for="sclassid" class="form-label fw-bold me-2" style="min-width: 120px;">ຫ້ອງຮຽນ</label>
		<select name="sclass" id="sclassid" class="form-select" required style="width:100%;">
			<option value="">-- ເລືອກຫ້ອງ --</option>
		</select>
		</div>
      <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" id="btnexit" class="btn btn-secondary">ປິດ/ອອກ</button>
        <button type="submit" name="btsubmof" id="btsubmofid" class="btn btn-primary">ບັນທຶກ</button>
      </div>
    </form>
  </div>
</div>

<!-- End of Modal form 1 -->
<!-- MODAL FORM 2: Move to next class********************************* -->
 <div id="liststmod" class="mdstu">
 <!-- Modal content -->
  <div class="mdstu-content" style="height:85%;">
    <span class="closestu">&times;&nbsp;</span>
     <div class="mdstu-heading"> <!-- #d7d370 -->
       <div align="center" class="mdstu-heading"><span style="font-size: 13pt; font-weight: bold;">ລາຍຊື່ ນັກຮຽນ/ນັກສຶກສາ</span></div>
      </div>
	  <div align="left" style="margin: 20px 30px 5px 30px">  <!-- DIV - list of students: top right bottom left -->
		   <?php 
		    $cldgst = $_GET["cllist"];
		   
		     $sqlgd = "SELECT degree, stuarea FROM tbcstudents WHERE class='$cldgst' GROUP BY class";
		     $rg = mysqli_query($con, $sqlgd) or die(mysqli_connect_error());
		     list($dgcl, $stacl) = mysqli_fetch_array($rg);
		     $ndg = Rdgree($dgcl, $con);
		     $nsta = Rsarea($stacl, $con);
		     $ncl = Rclassname($cldgst, $con);
		    
		   ?>
		  <p align="center">ຫ້ອງ: <?php echo $ncl;  ?><br>ຂະແໜງ/ວິຊາ: <?php echo $nsta."  (".$ndg.")"; ?></p>
	  	
	    <?php 
			
	  // LIST OF STUDENTS BY CLASS ******************
	        $dglst = $_GET["dglist"];
			$arlst = $_GET["stlist"];
			$cllst = $_GET["cllist"]; // classid from link in this file
			
	       if(!empty($cllst)){
			print "<form action='content.php?sad=stud&sadkid=stud' method='post'>";
			 print "<div align='right'><b>ເລືອກທັງໝົດ</b>&nbsp;<input type='checkbox' name='chstall' id='chstallid' ></div>";
			 // Heading table **********
			   print "<table class='tbus' style='font-size: 12pt;margin: 0px 0px 0px 0px; background-color: #D7EED7'>";
			   print "<tr><th align='center' style='width: 5%'>ລ/ດ</th><th style='width: 25%'>ຊື່ ແລະ ນາມສະກຸນ (ລາວ)</th><th style='width: 25%'>ຊື່ ແລະ ນາມສະກຸນ (ອັງກິດ)</th><th align='center' style='width: 15%'>ເລື່ອນຫ້ອງ</th><th>ຂໍ້ມູນສ່ວນຕົວ</th></tr>";
			   print "</table>";
			 // Content table **********
			 print "<div class='divsc'>";  // This class is in this file-Vertical Scroll bar for the table
			 print "<table class='tbus' style='font-size: 12pt;margin: 0px 0px 0px 0px;'>";
			 $sqlstls = "SELECT * FROM tbcstudents WHERE degree='$dglst' AND stuarea='$arlst' AND class='$cllst' ORDER BY userid ASC"; 
			 $rsls = mysqli_query($con, $sqlstls) or die(mysqli_connect_error());
			  if(mysqli_num_rows($rsls)>0){
				$n = 0;
				$arrclass = array();
				$nstuds = mysqli_num_rows($rsls);
				while($rl=mysqli_fetch_array($rsls)){
				 $n = $n + 1;
				  $uid = $rl["userid"];
				  $chname = "ch".$n; // CHECKBOX NAME *************
				  $dgsl = $rl["degree"];
				  $sasl = $rl["stuarea"];
				 // echo "List: ".$uid."  ".$dgsl."  ".$sasl."<br>";
				  list($id,$uname,$psw,$namel, $namee,$snamel, $snammee,$bdate, $gender,$phone,$email,$addr) = Userbyid($uid, $con); // Still more variables. Look at the function
				   if($gender=="m"){
					 $fnamel = "ທ. ".$namel." ".$snamel;
					 $fnamee = "Mr. ".$namee." ".$snamee;
				   } else {
					 $fnamel = "ນ. ".$namel." ".$snamel;
					 $fnamee = "Ms. ".$namee." ".$snamee;  
				   }
				  print "<tr><td align='center' style='width: 5%'>$n</td><td style='width: 25%'>$fnamel</td><td style='width: 25%'>$fnamee</td><td align='center' style='width: 15%'><input type='checkbox' name='$chname' id='$chname'></td><td>&nbsp</td></tr>";
			    } // End of while  	  
			  } // End of if>0
			  print "</table>";
			  print "</div>";
			  // DIV - Bottom part *************
			   print "<div class='btpart'>";
			   print "<fieldset class='usform-fset' style='border: 1px solid black'>
					  <legend align='left'><b>ການເລື່ອນຫ້ອງ: </b>ກະລຸນາ ເລືອກຫ້ອງຮຽນໃໝ່ເພື່ອເລື່ອນຫ້ອງໃຫ້ນັກຮຽນທີ່ຖືກເລືອກທັງໝົດ</legend>
					      <p align='left' style='margin-left: 25px'>
			            ຊັ້ນຮຽນປະຈຸບັນ: <select name='mdg' id='mdgid' style='width: 15%;'></select>&nbsp;&nbsp;ຂະແໜງ: <select name='msta' id='mstaid' style='width: 25%'></select>&nbsp;&nbsp;ຫ້ອງຮຽນ: <select name='mcl' id='mclid' style='width: 10%'></select>&nbsp;<input type='submit' name='btmcl' id='btmclid' value='ຕົກລົງ' style='width: 15%; margin-left: 10px' />
					   </p>
				      </fieldset>";
			   print "</div>"; // End of DIV - Bottom part
			  print "</form>";
		   } // End of if not empty class
		  
	    ?>
	  </div>
   </div> <!-- End of content -->
</div>	   
<!-- End of Modal form 2 -->
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
//  SEARCHING SELECTORS ****************
  $("#msdegreeid").change(function(){
	var msdgid = $(this).val();
	var sareas = document.getElementById("msareaid");
	   if(sareas.childNodes.length>0){
		   sareas.innerHTML = "";
		  }
	 $.ajax({
		type: "POST",
		url: "sad_students-area.php",  
		data: {msdgid: msdgid},
		success: function(gdata){
		  $("#dresult").html(gdata); // just make it happy
		}
	 }); 
  });

// Study area 
  $("#msareaid").change(function(){
	var msarea = $(this).val();
	var dgrv = document.getElementById("msdegreeid").value;
	var cls = document.getElementById("msclassid");
	  
	  if(cls.childNodes.length>0){
		  cls.innerHTML ="";
	  }
	  
	 $.ajax({
		type: "POST",
		url: "sad_students-class.php",  //sad_mainsclass.php is changed to sad_student-class.php
		data: {dgid: dgrv, areaid: msarea},
		success: function(gdata){
		  $("#dresult").html(gdata); // just make it happy
		}
	 });   
  });
// Class select in main file
  $("#msclassid").change(function(){
	 var mscl = $(this).val();
	 var msarea = document.getElementById("msareaid").value;
	 var msdegree = document.getElementById("msdegreeid").value;
	 window.location.href = 'content.php?sad=stud&sadkid=stud&searclass=' + mscl + '&seararea=' + msarea + '&seardgree=' + msdegree; // SEND JavaScript variable to PHP
  });
	
//  ADD NEW STUDENT SELECTORS ****************
 $("#sdegreeid").change(function(){ // When changing degree select in modal form
	var sdgreeid=$(this).val();  
	$.ajax({
	  type: "POST",
	  url: "sad_students-addnew.php",  // change sad_starea.php to sad_students-addnew.php
	  data: {dgid: sdgreeid},
	  success: function (rdata){
		$("#dresult").html(rdata); // Just make it happy  
	  }
	}); 
  }); // End of sdegreeid
	
  $("#sstareaid").change(function(){
	var dga = document.getElementById("sdegreeid").value;
	var sar = $(this).val();
	  
	$.ajax({
	  type: "POST",
	  url: "sad_students-addnew.php",
	  data: {dga: dga, sar: sar},
	  success: function (gdata){
		$("#dresult").html(gdata);
	  }
	});
   });	
});  // End of READY *********************************
//Function - OPEN MODAL form for students
var stmodf = document.getElementById("studmod");
var btnx = document.getElementsByClassName("close")[0];	

function Openmod(btnid){ //****************FUNCTION is used on button along with student data
 stmodf.style.display ="block"; // Open modal form
  // FILL MODAL form with data by mapping btnid (userid) to tbusers;
 // Button along with user is on click
 var btnvalue = document.getElementById(btnid).value;
 var mdheading = document.getElementById("mdheadid"); // heading of modal form
 var btnmodal = document.getElementById("btsubmofid"); // button for SAVE AND UPDATE in modal form
 
 $.ajax({
	type: "POST",
	url: "sad_stumodal.php",
	data: {uid: btnid, btnva: btnvalue},
	success: function (rdata){
	 //alert("Open modal form: "+btnid+"  "+btnvalue);
	//$("modal-content").html(rdata); // Fill modal form with data
	 console.log("Open modal form: "+btnid+"  "+btnvalue);
	 $("#dresult").html(rdata); // Just make it happy
	}
 });
 
   if(btnvalue=="ເພີ່ມ"){ // New student
	   mdheading.innerHTML=""; // instead of value, innerHTML is used for <span>
	   mdheading.innerHTML="ຕື່ມຂໍ້ມູນນັກຮຽນ/ນັກສຶກສາໃໝ່";
	   btnmodal.value="ບັນທຶກ";
	  }
	
	if(btnvalue=="ປັບປຸງ"){
	   // Change heading of modal form
	   mdheading.innerHTML=""; // instead of value, innerHTML is used for <span>
	   mdheading.innerHTML="ປັບປຸງຂໍ້ມູນ";
	   btnmodal.value="ປັບປຸງ";
	  }
} // End of function ******************
	
btnx.onclick=function(){ // Modal form - Cross X
 stmodf.style.display ="none";	
}
var btnexit = document.getElementById("btnexit"); // Exit button in modal form
	btnexit.onclick = function(){
	 stmodf.style.display ="none";	 // close modal form	
	}

// MODAL FORM - LIST OF STUDENT and moving to next class
 var cls = "<?php $cl = $_GET["cllist"]; echo $cl; ?>";	
 var listmd = document.getElementById("liststmod");
     if(cls.length>0){
	  listmd.style.display = "block";
	  // Fill selects in the modal form - move to new class
		 var dgclm = "<?php echo $dgcl; ?>";
		 var stclm = "<?php echo $stacl; ?>";
		 var clcm = "<?php echo $cldgst; ?>";
		 
		$.ajax({
	  	  type: "POST",
	  	  url: "sad_students-classmove.php", // 
	  	  data: {mdg: dgclm, sarm: stclm, clm: clcm},
	  	  success: function (gdata){
			$("#dresult").html(gdata);
	  		}
		}); // End of ajax
	 }
	
var btnclst = document.getElementsByClassName("closestu")[0];	
    btnclst.onclick = function(){
	  listmd.style.display = "none";
	}
// MODAL FORM - MESSAGE ******************************	
var mfmsg = document.getElementById("modmess");
var btnxmsg = document.getElementsByClassName("closemsg")[0];	
var cfempty = "<?php echo $mdinpu; ?>"; // Get confirm a button is submitted/clicked
// data from modal form after its submission by clicking a button. The data will be sent back to message modal form
var useridb = "<?php echo $useridb; ?>"; // Keep it in case some input are empty
var degrb = "<?php echo $degreeb; ?>";
var starea = "<?php echo $stuareab; ?>";
var cl = "<?php echo $classb; ?>";
	
	if(cfempty.length>0){
	    mfmsg.style.display = "block";
	  }
	btnxmsg.onclick = function(){ // Close X to close message modal form
	  mfmsg.style.display = "none";  
	  stmodf.style.display ="block"; // Open modal form again
	  $.ajax({
	  type: "POST",
	   url: "sad_stumodal.php",
	   data: {uid: useridb, gdegrb: degrb, stuarea: starea, class: cl}, // REFILL MODAL FORM with data 
	   success: function (rdata){
	      $("#dresult").html(rdata); // Just make it happy
	   }
      });
	}
// function Subfsearchstu
   function Subfsearchstu(){
	 $("#fsearchstu").submit();  
   }

// Checkbox - SELECT ALL CHECKBOXS
	
   $("#chstallid").change(function(){
		 var nstudent = "<?php echo $nstuds; ?>";  // Number of students in the class 
	      for(i=1; i<=nstudent; i++){
		     var chid = "ch" + i;
			  if(this.checked){
				document.getElementById(chid).checked=true;  
			  } else {
				document.getElementById(chid).checked=false;  
			  }   
		       
	       } 
	 
   });
	 
</script>