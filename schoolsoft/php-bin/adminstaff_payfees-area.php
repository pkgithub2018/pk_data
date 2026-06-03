<script>
// Remove items from select CLASS when user clicks on select DEGREE
 var msselect = document.getElementById("msclassid");
 if(msselect.childNodes.length>0){ 
	msselect.innerHTML="";	// Remove items from SELECT
 }
</script>
<?php 
include("connection.php");
include("supports.php");
$dgid=$_POST["msdgid"];

// STUDY AREA with data 
 echo "<script>
	  var msarea=document.getElementById('arsfeeid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      msarea.options.add(opt_non);
	  </script>";

$con->set_charset("utf8"); // SET FONT TO "utf-8"
$sqlarea = "SELECT studyarea FROM tbclass WHERE degree='$dgid' GROUP BY studyarea";
$rarea = mysqli_query($con,$sqlarea) or die(mysqli_connect_error());
while($r=mysqli_fetch_array($rarea)){
	$aid = $r["studyarea"];
	$aname = Rsarea($aid, $con);
	//$aname = $r["classname"];
	echo "<script>
	      var aid ='$aid';
		  var aname = '$aname';
		  var opt_non=document.createElement('option');
		  opt_non.value=aid;
		  opt_non.text=aname;
	      msarea.options.add(opt_non);
	      </script>";
}

?>
