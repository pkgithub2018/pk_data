<?php
 // session_start(); // Already started in content.php
 $guname=$_SESSION["guname"];
 $gpsw=$_SESSION["gpsw"];

?> 
<style>
	.tbus {
	 font-size: 12pt;
	}
	.tbus th{
	  text-align: center;
	}
	.tbus tr, th, td {
		border: 1px solid white;
	}
</style>

<script>
  var timeat = document.getElementById('timeatid');
	  if(timeat.childNodes.length>0){
		 // alert("Data");
		  timeat.innerHTML ="";
	  }
</script>

<div align="center" style="width: 100%; background-color: white;"> 
 <div align="left" style="margin: 50px 80px 30px 80px"> <!-- top, right , bottom  and left -->
	<?php
	  list($uid) = Userinfo($guname,$gpsw,$con); // Get user id - Teacher ID
	  // HEADING ************
	  print "<table>";
	  print "<tr><td>ກະລຸນາ ເລືອກຊື່ຫ້ອງຮຽນ ເພື່ອສະແດງ ລາຍຊື່ນັກຮຽນ</td></tr>";
	  print "<tr><td align='right'><b>ຫ້ອງຮຽນ</b>&nbsp;</td><td><select name='clname' id='clnameid' style='width: 400px; height: 40px; padding: 3px;margin: 3px;'></td></tr>";
	  print "<tr><td align='right'><b>ວັນທີ</b>&nbsp;</td><td><input type='date' name='datt' id='dattid' style='width: 150px; padding: 3px;margin: 3px;'></td></tr>";
	  print "<tr>
	            <td align='right'><b>ເວລາ</b>&nbsp;</td>
				<td>
	              <select name='timeat' id='timeatid' style='width: 150px; height: 40px; padding: 3px;margin: 3px;'></select>
			    </td>
			 </tr>";
	 
	  print "</table>";
	 
	 // SELECTOR: Class name: class ***************************
	   echo "<script> 
	   var tcl = document.getElementById('clnameid'); 
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      tcl.options.add(opt_non);
	    </script>";
	 
      //$con->set_charset("utf8"); // SET FONT TO "utf-8"

	  $sqlutch = "SELECT subjid, classid, ayear FROM tbteaching WHERE userid='$uid' GROUP BY subjid, classid ORDER BY classid ASC";
	  $rtch = mysqli_query($con, $sqlutch) or die(mysqli_connect_error());
	  while($r=mysqli_fetch_array($rtch)){
		  $subj = $r["subjid"];
		  $clid = $r["classid"];
		  //echo "My Class: ".$clid."<br>";
		  
		  list($dgid, $stid) = Rdgsarea($clid, $con);
		  $ayear = $r["ayear"];
		  list($subjnamelao, $subeng) = Rsubjectname($subj, $con);
		  $classname = Rclassname($clid, $con);
		  $dgname = Rdgree($dgid, $con);
		  $saname = Rsarea($stid, $con);
		  
		  $classname = $subjnamelao." - ".$classname.", ".$saname.", ".$dgname;
		  
		 echo "<script>
	      var cid ='$clid';
		  var cname = '$classname';
		  var opt_non=document.createElement('option');
		  opt_non.value=cid;
		  opt_non.text=cname;
	      tcl.options.add(opt_non);
	      </script>"; 
		  
	  } 

	 // SELECTOR: Time of attendance *************************

	 echo "<script>
		         var seltimeat = document.getElementById('timeatid');
				 var opnone = document.createElement('option');
				     opnone.value = '';
					 opnone.text = '';
					 seltimeat.options.add(opnone);
		      </script>";
		 
		 $sqlttm = "SELECT id,tchtime FROM tbtchtime WHERE tcharea='2'"; // for College
 			$rtm = mysqli_query($con, $sqlttm) or die(mysqli_connect_error());
 			while($r=mysqli_fetch_array($rtm)){
	 			$tid = $r["id"];
	 			$tcht = $r["tchtime"];
				//echo "<option value='$tid'>".$tcht."</option>";
				echo "<script>
				        var tmid = '$tid';
						var tm = '$tcht';
						var op = document.createElement('option');
						    op.value = tmid;
							op.text = tm;
							seltimeat.options.add(op);
					  </script>";
			} // End of while
 

	
	 // Link to AttendanceList - to open attendance form
	 if(isset($_GET['aid'])){
        	$aid = $_GET['aid'];		
        	list($aidr, $sid, $tid, $subj, $cls, $adate, $atime, $ayear) = AttendanceVariables($aid, $con);
	 }

	?>
	<!-- *********** Attendance contents *********** --> 
	 <div id="attid" align="left" style="width: 80%; margin: 50px 80px 30px 80px; background-repeat: no-repeat">&nbsp;</div> <!-- End of DIv - Attendance : top, right bottom and left-->	
    <div id="divTest" style="width: 80%; margin: 50px 80px 30px 80px; background-repeat: no-repeat"></div>
</div>
<!-- ********************** SCRIPT ***************************** -->
 <script src="../js/handleCheck.js"></script> <!-- To add hadleCheck.js file -->

<script>
$(document).ready(function(){			  
	
var cldgst = document.getElementById('clnameid').value;
//var asubmit = "<?php echo $_POST["btnatt"]; ?>"; // form submit
var dvatt = document.getElementById("attid");	

// SHOW ATTENDANCE LIST by date AT BEGINING/INITIALLY *****************************
var attendance_time = "<?php echo AttendanceRecords($uid, $con); ?>"; // $uid - teacher ID -Attendance FUNCTION IN supports.php

	if(cldgst.length==0 || cldgst==null){
    	dvatt.innerHTML = attendance_time;
   	}

// OPEN AttendanceForm for record *********************************

var attendid = "<?php echo $_GET['aid']; ?>"; // aid - ID for attendance(Just make happy)Links from FUNCTION: AttendanceRecords in supports.php

if(attendid.length>0 && attendid!=null){
	//alert("Attendance link: " + attendid);
	var stuid = "<?php echo $sid; ?>"; // Student ID
	var tcid = "<?php echo $tid; ?>"; // Teacher ID
	var subjid = "<?php echo $subj; ?>"; // Subject ID
	var classid = "<?php echo $cls; ?>"; // Class ID
	var ayear = "<?php echo $ayear; ?>"; // Academic year
	var adate = "<?php echo $adate; ?>"; // Attendance date
	var atime = "<?php echo $atime; ?>"; // Attendance time

	//alert("Ayear: " + ayear);
	
	$.ajax({
		type: "POST",
		url: "teachers_attendance-slist.php",
		data: {tch: tcid, cls: classid, adate: adate, atime: atime, subj: subjid, ayr: ayear},
		success: function(gdata){
		  $("#attid").html(gdata);	// Just make it happy
		}
	 });
	
} // End of if - Open AttendanceList


// UPDATE WAS DONE *****************************
var confup = "<?php echo $statusup; ?>";
	if(confup.length>0 && confup=="done"){
	     dvatt.innerHTML = "<p align='center'>ການປັບປຸງ ສໍາເລັດແລ້ວ !</p>";
	   }
	
// List of students	
  $("#clnameid").change(function(){  // Class id on change
	
	  var adclsnew = $(this).val();
	 // alert("Changing!" + adclsnew);
	  
	   var datetid = document.getElementById("dattid"); // Empty date
	       datetid.value = ""; // Clear date
	   var atttime = document.getElementById("timeatid");
	       atttime.selectedIndex = 0;

	   //var atdiv = document.getElementById("atdiv");
	    //   atdiv.innerHTML = "";  // Clear main DIV contents in AttendForm function in supports.php

  });
	
// Attendance time on change
 $("#timeatid").change(function(){
	var clsvar = document.getElementById("clnameid").value; // Class 
	var dateatvar = document.getElementById("dattid").value; // Date
	var tattvar = $(this).val(); // Time
	 
	var tcherid = "<?php echo $uid; ?>";
	var subjid = "<?php echo $subj; ?>";
	var ayr = "<?php echo $ayear; ?>";
	var dg = "<?php echo $dgid; ?>";  // $dgname
	var sta = "<?php echo $saname; ?>";
	//alert("Hi Class" + clsvar +tcherid +" "+subjid+"  "+ayr); 
	 
	 $.ajax({
		type: "POST",
		url: "teachers_attendance-slist.php",
		data: {tch: tcherid, cls: clsvar, adate: dateatvar, atime: tattvar, subj: subjid, ayr: ayr, dg: dg, sta: sta},
		success: function(gdata){
		  $("#attid").html(gdata);	// Just make it happy
		}
	 });
 });

 }); // End of ready document

</script>
	