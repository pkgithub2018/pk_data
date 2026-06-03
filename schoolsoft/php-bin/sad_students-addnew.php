<script>
 // Remove items from select: STUDY AREA when user clicks on select DEGREE
 var dgch ="<?php $dg=$_POST["dgid"]; echo $dg; ?>"; 
 if(dgch.length>0){ // In case degree select is on change
	  var arselect = document.getElementById("sstareaid");
          if(arselect.childNodes.length>0){ 
	       arselect.innerHTML="";	// Remove items from SELECT
         }
	}	
 
// Remove items from select CLASS when user clicks on select DEGREE
 var sselect = document.getElementById("sclassid");
 if(sselect.childNodes.length>0){ 
	sselect.innerHTML="";	// Remove items from SELECT
 }
</script>
<?php 
include("connection.php");
include("supports.php");

$dgid=$_POST["dgid"]; // In case Degree select is on CHANGE

// ADD NEW STUDENTS INTO CLASS **********
$dgas = $_POST["dga"];  // In case Study area select is on CHANGE
$sarid = $_POST["sar"];

// Fill in select STUDY AREAS with empty for the first item
echo "<script>
	  var ssarea=document.getElementById('sstareaid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      ssarea.options.add(opt_non);
	  </script>";
// Check degree for study area to fill select STUDY AREA WITH relevant data
$sqlstarea = "";
if($dgid==1){  // If secendary school
 $sqlstarea = "SELECT studyarea FROM tbclass WHERE studyarea = '1' GROUP BY studyarea";
} else {
  $sqlstarea = "SELECT studyarea FROM tbclass WHERE degree='$dgid' AND studyarea<>'1' GROUP BY studyarea";
}
$rstarea = mysqli_query($con,$sqlstarea) or die(mysqli_connect_error());
while($rw=mysqli_fetch_array($rstarea)){
  $starid = $rw["studyarea"];
  $starname = Rsarea($starid, $con);
  echo "<script>
	      var cid ='$starid';
		  var cname = '$starname';
		  var opt_non=document.createElement('option');
		  opt_non.value=cid;
		  opt_non.text=cname;
	      ssarea.options.add(opt_non);
	      </script>";
}

// Fill in select - CLASS with data;
echo "<script>
	  var sclass=document.getElementById('sclassid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      sclass.options.add(opt_non);
	  </script>";
$con->set_charset("utf8"); // SET FONT TO "utf-8"
$sqlclass = "SELECT id, classname FROM tbclass WHERE degree='$dgas' AND studyarea='$sarid'";
$rclass = mysqli_query($con,$sqlclass) or die(mysqli_connect_error());
while($r=mysqli_fetch_array($rclass)){
	$cid = $r["id"];
	$cname = $r["classname"];
	echo "<script>
	      var cid ='$cid';
		  var cname = '$cname';
		  var opt_non=document.createElement('option');
		  opt_non.value=cid;
		  opt_non.text=cname;
	      sclass.options.add(opt_non);
	      </script>";
}
?>
