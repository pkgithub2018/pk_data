<?php 
include("connection.php");
include("supports.php");
$pfor=$_POST["pfor"];  // To receive, just make it happy

switch ($pfor){
// KNOWLEDGE/EDUCATION LEVEL SELECT (in Modal form) with data at beginning ****************
  case "begin":
  // DEGREE SELECT
  echo "<script>
	    var seldegree=document.getElementById('tdegreeid');  
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      seldegree.options.add(opt_non);
	    </script>";
  // KNOWLEDGE/EDUCATION LEVEL
  echo "<script>
	    var selknowlevel=document.getElementById('slevelid');  
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      selknowlevel.options.add(opt_non);
	    </script>";
  $con->set_charset("utf8"); // SET FONT TO "utf-8"
  $sqldegree = "SELECT * FROM tbdegree";
  $rdegree = mysqli_query($con,$sqldegree) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rdegree)){ // 1
	$did = $r["id"];
	$dname = $r["degreename"];
	// DEGREE 
	echo "<script>
	      var dgid ='$did';
		  var dgname = '$dname';
		  var opt_non=document.createElement('option');
		  opt_non.value=dgid;
		  opt_non.text=dgname;
	      seldegree.options.add(opt_non);
	      </script>";
	 // KNOWLEDGE/EDUCATION
	 echo "<script>
		  var opt_non=document.createElement('option');
		  opt_non.value=dgid;
		  opt_non.text=dgname;
	      selknowlevel.options.add(opt_non);
	      </script>";
  } // End of while 1
		
// STUDY AREA/SUBMECT with data at beginning
 echo "<script>
	    var selsarea=document.getElementById('sstareaid');  
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      selsarea.options.add(opt_non);
	    </script>";
  
  $sqlsarea = "SELECT * FROM tbstudyarea";
  $rsarea = mysqli_query($con,$sqlsarea) or die(mysqli_connect_error());
  while($rw = mysqli_fetch_array($rsarea)){
	$sareaid = $rw["id"];
	$nareaname = $rw["sareaname"];
	echo "<script>
	      var sarid ='$sareaid';
		  var sarname = '$nareaname';
		  var opt_non=document.createElement('option');
		  opt_non.value=sarid;
		  opt_non.text=sarname;
	      selsarea.options.add(opt_non);
	      </script>";
  } // End of while
  
 // SUBJECT KNOWLEDGE
 echo "<script>
	    var selsknow=document.getElementById('sknowid');  
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      selsknow.options.add(opt_non);
	    </script>";

 $sqlsubknow = "SELECT id, gname FROM tbgraduate";
 $rsknow = mysqli_query($con, $sqlsubknow) or die(mysqli_connect_error());
   while($rk = mysqli_fetch_array($rsknow)){
	$subkwid = $rk["id"];
	$subkname = $rk["gname"];
	echo "<script>
	      var knid ='$subkwid';
		  var knname = '$subkname';
		  var opt_non=document.createElement('option');
		  opt_non.value=knid;
		  opt_non.text=knname;
	      selsknow.options.add(opt_non);
	      </script>"; 
    } // End of while
  
 // POSITION 	
  echo "<script>
	    var selps=document.getElementById('spositionid');  
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      selps.options.add(opt_non);
	    </script>";	
 
  $sqlps = "SELECT id, psname FROM tbposition";
  $rps = mysqli_query($con,$sqlps) or die(mysqli_connect_error());
  while($r=mysqli_fetch_array($rps)){
	$psid = $r["id"];  
	$psname = $r["psname"];
	echo "<script>
	      var psvid ='$psid';
		  var psvname = '$psname';
		  var opt_non=document.createElement('option');
		  opt_non.value=psvid;
		  opt_non.text=psvname;
	      selps.options.add(opt_non);
	      </script>"; 
  } // End of while
		
 break;

// STUDY AREA SELECT ******************************** 
  case "stuarea": 
	$dgreeid = $_POST["dgid"];
	$stuid = $_POST["stuid"];
	echo "Degree to study area"."  ".$dgreeid."    ".$stuid;
	// REMOVE ELEMENT SELECT - CLASSROOM **********
	// REFILL CLASSROOM SELECT **********
	echo "<script>
	    var selclass=document.getElementById('sclassid');  
	    var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      selclass.options.add(opt_non);
	    </script>";
		
   $con->set_charset("utf8"); // SET FONT TO "utf-8"	
   $sqlclass = "SELECT id, classname FROM tbclass WHERE degree='$dgreeid' AND studyarea='$stuid'";
   $rclass = mysqli_query($con,$sqlclass) or die(mysqli_connect_error());
	while($rc=mysqli_fetch_array($rclass)){
		$clid = $rc["id"];
		$clname = $rc["classname"];
		echo "<script>
	      var classid ='$clid';
		  var classname = '$clname';
		  var opt_non=document.createElement('option');
		  opt_non.value=classid;
		  opt_non.text=classname;
	      selclass.options.add(opt_non);
	      </script>";
	}
  break;
} // End of switch
?>