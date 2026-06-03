<?php 
include("connection.php");
include("supports.php");

// Degree *******************
// Study area *****************
$sdgonchg=$_POST["sdgreeonchg"]; // Get study area when Degree select is on change
if(!empty($sdgonchg)){
  $con->set_charset("utf8"); // SET FONT TO "utf-8"
  $sqlsadg = "SELECT studyarea FROM tbclass WHERE degree='$sdgonchg' GROUP BY studyarea";
  $rsadg = mysqli_query($con, $sqlsadg) or die(mysqli_connect_error());
  if(mysqli_num_rows($rsadg)>0){
	 echo "<script> 
        var sareatch = document.getElementById('tchareaid');
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      sareatch.options.add(opt_non);
	    </script>"; 
	 while($r=mysqli_fetch_array($rsadg)){
		$arid = $r["studyarea"]; 
		$arname = Rsarea($arid, $con);
		echo "<script>
	      var arid ='$arid';
		  var arname = '$arname';
		  var opt_non=document.createElement('option');
		  opt_non.value=arid;
		  opt_non.text=arname;
	      sareatch.options.add(opt_non);
	      </script>";
	 }
  } 
} // End of if empty

// Subject *******************
$sarid = $_POST["sarid"]; 
$sadgree = $_POST["sadgree"];

if(!empty($sarid) && !empty($sadgree)){
  // Teaching subject SELECT **********************
  $con->set_charset("utf8"); // SET FONT TO "utf-8"
  $sqlsub = "SELECT id, sublao FROM tbsubjects WHERE dgree='$sadgree' AND sarea='$sarid'";
  $rsub = mysqli_query($con,$sqlsub) or die(mysqli_connect_error());
  if(mysqli_num_rows($rsub)>0){
	 echo "<script> 
        var subtch = document.getElementById('tchsubid');  
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      subtch.options.add(opt_non);
	    </script>";  
	  while($r=mysqli_fetch_array($rsub)){
		  $subid = $r["id"];
		  $subname = $r["sublao"];
		  echo "<script>
	      var subid ='$subid';
		  var subname = '$subname';
		  
		  var opt_non=document.createElement('option');
		  opt_non.value=subid;
		  opt_non.text=subname;
	      subtch.options.add(opt_non);
	      </script>";
	  } // End of while
  }
  // Teaching time SELECT ******************
  $sqlttime ="";
  $sqltday = "";
	
  if($sarid=='1'){ // 1 - Refers to ມໍ5 - ມໍ7
   $sqltday = "SELECT id, dayname FROM tbtchday WHERE id NOT IN(6,7)"; // Teaching day for secondary school
   $sqlttime = "SELECT id, tchtime FROM tbtchtime WHERE tcharea='1'"; // Teaching time for secondary school
  } else {  // Studying time for colleges
    $sqltday = "SELECT id, dayname FROM tbtchday"; // All 7 days
	$sqlttime = "SELECT id, tchtime FROM tbtchtime WHERE tcharea='2'";   // 2 - Refers to teaching time for college
  } // End of if Teaching time
	
// Day for teaching ***************
	echo "<script> 
        var tchday = document.getElementById('tchdayid');
		var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      tchday.options.add(opt_non);
		</script>";
	
	$rtday = mysqli_query($con,$sqltday) or die(mysqli_connect_error());
	while($rd=mysqli_fetch_array($rtday)){
		$did = $rd["id"];
		$dname = $rd["dayname"];
		echo "<script>
	      var did ='$did';
		  var tday = '$dname';
		  
		  var opt_non=document.createElement('option');
		  opt_non.value=did;
		  opt_non.text=tday;
	      tchday.options.add(opt_non);
	      </script>";
	}
	
// Time for teaching *****************
  echo "<script> 
        var tchtime = document.getElementById('tchtimeid');  
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      tchtime.options.add(opt_non);
	    </script>";
 $rttime = mysqli_query($con,$sqlttime) or die(mysqli_connect_error());
 while($rt=mysqli_fetch_array($rttime)){
	$tid = $rt["id"];
	$ttime = $rt["tchtime"];
	echo "<script>
	      var tid ='$tid';
		  var ttime = '$ttime';
		  
		  var opt_non=document.createElement('option');
		  opt_non.value=tid;
		  opt_non.text=ttime;
	      tchtime.options.add(opt_non);
	      </script>";
 } // End of while

// Semester ******************************
	echo "<script> 
          var tchsem = document.getElementById('tchtsemesterid');
		  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      tchsem.options.add(opt_non);
		  
		  var opt_non=document.createElement('option');
		  opt_non.value='1';
		  opt_non.text='I';
	      tchsem.options.add(opt_non);
		  
		  var opt_non=document.createElement('option');
		  opt_non.value='2';
		  opt_non.text='II';
	      tchsem.options.add(opt_non);	  
		</script>";
		
} // End of if - !empty($sarid)
// Subject for class 
$sbtcls = $_POST["subcls"];
$dgcls = $_POST["dgcls"];
$sarcls = $_POST["sarcls"];

if(!empty($sbtcls)){
  $con->set_charset("utf8"); // SET FONT TO "utf-8"
  $sqlcl = "SELECT id, classname FROM tbclass WHERE degree='$dgcls' AND studyarea ='$sarcls'";
  $rcls = mysqli_query($con,$sqlcl) or die(mysqli_connect_error());
  if(mysqli_num_rows($rcls)>0){
	echo "<script> 
        var scls = document.getElementById('tchclassid');
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      scls.options.add(opt_non);
	    </script>";  
	  
	 while($r=mysqli_fetch_array($rcls)){
		  $clid = $r["id"];
		  $clname = $r["classname"];
		  echo "<script>
	      var clid ='$clid';
		  var clname = '$clname';
		  var opt_non=document.createElement('option');
		  opt_non.value=clid;
		  opt_non.text=clname;
	      scls.options.add(opt_non);
	      </script>";
	  }
  } // End of if>0
}
?>
