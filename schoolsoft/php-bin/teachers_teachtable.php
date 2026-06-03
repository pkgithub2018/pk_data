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

<div align="center" style="width: 100%; background-color: white;"> 
 <div align="left" style="margin: 30px 80px 30px 80px"> <!-- top, right , bottom  and left -->
	<?php
	  list($uid) = Userinfo($guname,$gpsw,$con); // Get user id
	 
	  $sqlutch = "SELECT teachtime FROM tbteaching WHERE userid='$uid' GROUP BY teachtime ORDER BY teachtime ASC";
	 $rut = mysqli_query($con, $sqlutch) or die(mysqli_connect_error());
	 if(mysqli_num_rows($rut)>0){
	   print "<h3>ຕາຕະລາງ ສອນ</h3>";
	   print "<table class='tbus'>";
	   print "<tr><th>ເວລາ</th><th>ຈັນ</th><th>ຄານ</th><th>ພຸດ</th><th>ພະຫັດ</th><th>ສຸກ</th><th>ເສົາ</th><th>ອາທິດ</th></tr>";
	  while($r=mysqli_fetch_array($rut)){
		  $tt = $r["teachtime"];
		  $ttname = Rttime($tt, $con);
		  
		  $submd = Ssubtable($uid, 1, $tt, $con); // Monday
		  $subtue = Ssubtable($uid, 2, $tt, $con); 
		  $subwed = Ssubtable($uid, 3, $tt, $con); 
		  $subthu = Ssubtable($uid, 4, $tt, $con); 
		  $subfr = Ssubtable($uid, 5, $tt, $con); 
		  $subst = Ssubtable($uid, 6, $tt, $con); 
		  $subsun = Ssubtable($uid, 7, $tt, $con); 
		  
		  list($slmd, $smd) = Rsubjectname($submd, $con);
		  list($sltue, $stue) = Rsubjectname($subtue, $con);
		  list($slwed, $swed) = Rsubjectname($subwed, $con);
		  list($slthu, $sthu) = Rsubjectname($subthu, $con);
		  list($slfr, $sfr) = Rsubjectname($subfr, $con);
		  list($slst, $sst) = Rsubjectname($subst, $con);
		  list($slsun, $ssun) = Rsubjectname($subsun, $con);
		  // Class 
		  $sqlcl = "SELECT classid FROM tbteaching WHERE userid='$uid' AND subjid='$submd' AND teachtime='$tt'";
		  $rcl = mysqli_query($con, $sqlcl) or die(mysqli_connect_error());
		  list($clmd) = mysqli_fetch_array($rcl);
		  if(!empty($clmd)){		  
			// Get Degree and study area
			  $sqlds = "SELECT degree, studyarea, location FROM tbclass WHERE id='$clmd'";
			  $rds = mysqli_query($con, $sqlds) or die(mysqli_connect_error());
			  list($cldg, $sta, $lct) = mysqli_fetch_array($rds);
			  
			  $cl1 = Rclassname($clmd, $con); 
			  $cl1 = "(".$cl1.")".$cldg.$sta.$lct;
		  }
		  	  
		 $class1 = Dgstable($uid, $submd, $tt, $con); // Monday
		 $class2 = Dgstable($uid, $subtue, $tt, $con); 
		 $class3 = Dgstable($uid, $subwed, $tt, $con);
		 $class4 = Dgstable($uid, $subthu, $tt, $con);
		 $class5 = Dgstable($uid, $subfr, $tt, $con);
		 $class6 = Dgstable($uid, $subst, $tt, $con);
		 $class7 = Dgstable($uid, $subsun, $tt, $con);
		  
		print "<tr><td>$ttname</td><td>$slmd<br>$class1</td><td>$sltue<br>$class2</td><td>$slwed<br>$class3</td><td>$slthu<br>$class4</td><td>$slfr<br>$class5</td><td>$slst<br>$class6</td><td>$slsun<br>$class7</td></tr>";
	  } // End of while 
	  print "</table>"; 
	 }
	  
	  // Studytable(1, $con); // Monday - common
	?>
 </div>
</div>
	