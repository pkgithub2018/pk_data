<?php 
  include("connection.php");
  include("supports.php");

  $dgid = $_POST["dgr"]; // When select 
  $dgval = $_POST["sdgval"]; // submit with study area select
  $sarea = $_POST["sarea"];

  $class = $_POST["class"];
  $cldg = $_POST["cldgree"]; // degree with class
  $clsta = $_POST["clstarea"]; // study areas with class
   
// If SELECT degree - selected
 if(!empty($dgid)){
	 
	// ADD area select items
	   echo "<script> 
	   var starea = document.getElementById('stareaid'); 
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      starea.options.add(opt_non);
	    </script>";

$con->set_charset("utf8"); // SET FONT TO "utf-8"
  $sqlsarea = "SELECT sarea FROM tbsubjects WHERE dgree='$dgid' GROUP BY sarea";
  $rsarea = mysqli_query($con,$sqlsarea) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rsarea)){ // 1
	$srid = $r["sarea"];
	$srname = Rsarea($srid, $con);
	echo "<script>
	      var srid ='$srid';
		  var srname = '$srname';
		  var opt_non=document.createElement('option');
		  opt_non.value=srid;
		  opt_non.text=srname;
	      starea.options.add(opt_non);
	      </script>";	  
  } // End of while	
  
  // ADD common study table
	 Studytable(1, $con); // Monday
     Studytable(2, $con); // Tuesday
     Studytable(3, $con); // Wed
     Studytable(4, $con); // Thurs
     Studytable(5, $con); // Friday
     Studytable(6, $con); // Saturday 
 } // End of if $dgid

if(!empty($sarea)){
  // ADD class select items 
  //echo "Hello, study area and degree: ".$_POST["sdgval"];
  echo "<script> 
	   var scl = document.getElementById('stclid'); 
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      scl.options.add(opt_non);
	    </script>";
	
 $con->set_charset("utf8"); // SET FONT TO "utf-8"
 $sqlcl = "SELECT id, classname FROM tbclass WHERE degree='$dgval' AND studyarea='$sarea'";
 $rcl = mysqli_query($con, $sqlcl) or die(mysqli_connect_error());
 while($rs=mysqli_fetch_array($rcl)){
	$clid = $rs["id"];
	$clname = $rs["classname"];
   
	echo "<script>
	      var clid ='$clid';
		  var clname = '$clname';
		  var opt_non=document.createElement('option');
		  opt_non.value=clid;
		  opt_non.text=clname;
	      scl.options.add(opt_non);
	      </script>";
 }	
} // End of if

// CLASS
if(!empty($class)){
  $sqlclass = "SELECT * FROM tbteaching WHERE classid='$class' ORDER BY teachtime ASC";
  $rclass = mysqli_query($con, $sqlclass) or die(mysqli_connect_error());
 if(mysqli_num_rows($rclass)>0){
	$cln = Rclassname($class, $con);
	print "<h3 style='margin-left: 150px;'>ຫ້ອງຮຽນ: $cln <a href='sad_studytable-print.php?cl=$class&dg=$cldg&sta=$clsta' target='_blank' style='color: black'><i class='fa fa-eye'>&nbspເບີ່ງ</i></a></h3>";
	print "<table class='tbus' style='width: 80%; margin-left: 150px'>";
	print "<tr><th style='color: yellow'>ເວລາ</th><th>ຈັນ</th><th>ຄານ</th><th>ພຸດ</th><th>ພະຫັດ</th><th>ສຸກ</th><th>ເສົາ</th><th>ອາທິດ</th></tr>";
	while($rc=mysqli_fetch_array($rclass)){
	 $tid = $rc["id"];
	 $sbid = $rc["subjid"];
	 $tday = $rc["teachday"];
	 $ttime = $rc["teachtime"];
		switch($ttime){
			case 1: 
			 $tid1 = 1;
			 break;
				
			case 2: 
			 $tid1 = 2;
			 break;
				
			case 3: 
			 $tid1 = 3;
			 break;
				
			case 4: 
			 $tid1 = 4;
			 break;
				
			case 5: 
			 $tid1 = 5;
			 break;
				
			case 6: 
			 $tid1 = 6;
			 break;
		} // End of switch
   } // End of while - tbteaching1
	
   //Get teaching time ONLY from tbteaching
	 $sqlttime ="SELECT teachtime FROM tbteaching WHERE classid='$class' GROUP BY teachtime ORDER BY teachtime ASC";
	 $rtime = mysqli_query($con, $sqlttime) or die(mysqli_connect_error());
	 while($rt=mysqli_fetch_array($rtime)){
		 $tid = $rt["teachtime"];
		 $tchtime = Rttime($tid, $con);
	     // Subject id
		  $sub1 = Studytablecl(1, $tid, $con); // Monday
		  $sub2 = Studytablecl(2, $tid, $con); // Tuesday
		  $sub3 = Studytablecl(3, $tid, $con); // Wednsday
		  $sub4 = Studytablecl(4, $tid, $con); // Thursday
		  $sub5 = Studytablecl(5, $tid, $con); // Friday
		  $sub6 = Studytablecl(6, $tid, $con); // Satu
		  $sub7 = Studytablecl(7, $tid, $con); // Sun
		 // Subject name
		 list($sublao1, $subeng1) = Rsubjectname($sub1, $con);
		 list($sublao2, $subeng2) = Rsubjectname($sub2, $con);
		 list($sublao3, $subeng3) = Rsubjectname($sub3, $con);
		 list($sublao4, $subeng4) = Rsubjectname($sub4, $con);
		 list($sublao5, $subeng5) = Rsubjectname($sub5, $con);
		 list($sublao6, $subeng6) = Rsubjectname($sub6, $con);
		 list($sublao7, $subeng7) = Rsubjectname($sub7, $con);
		 // Teachers
		 $tname1 = Rtchername($sub1, $con);
		 if(!empty($tname1)){
		  $tname1 = "(ອຈ. ".$tname1.")"; 
		 }
		 
		 $tname2 = Rtchername($sub2, $con);
		 if(!empty($tname2)){
		  $tname2 = "(ອຈ. ".$tname2.")"; 
		 }
		 $tname3 = Rtchername($sub3, $con);
		 if(!empty($tname3)){
		  $tname3 = "(ອຈ. ".$tname3.")"; 
		 }
		 $tname4 = Rtchername($sub4, $con);
		 if(!empty($tname4)){
		  $tname4 = "(ອຈ. ".$tname4.")"; 
		 }
		 $tname5 = Rtchername($sub5, $con);
		 if(!empty($tname5)){
		  $tname5 = "(ອຈ. ".$tname5.")"; 
		 }
		 
		 $tname5 = Rtchername($sub5, $con);
		 if(!empty($tname5)){
		  $tname5 = "(ອຈ. ".$tname5.")"; 
		 }
		 
		 $tname5 = Rtchername($sub5, $con);
		 if(!empty($tname5)){
		  $tname5 = "(ອຈ. ".$tname5.")"; 
		 }
		 
		 $tname6 = Rtchername($sub6, $con);
		 if(!empty($tname6)){
		  $tname6 = "(ອຈ. ".$tname6.")"; 
		 }
		 
		 $tname7 = Rtchername($su7, $con);
		 if(!empty($tname7)){
		  $tname7 = "(ອຈ. ".$tname7.")"; 
		 }
		 
		 print "<tr><td>$tchtime</td><td>$sublao1 $tname1</td><td>$sublao2 $tname2</td><td>$sublao3 $tname3</td><td>$sublao4 $tname4</td><td>$sublao5 $tname5</td><td>$sublao6 $tname6</td><td>$sublao7 $tname7</td></tr>";
	 }
   print "</table>";
 } // End of if>0
}
?>

