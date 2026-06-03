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
$dgree = $_POST["dgid"];
$area = $_POST["areaid"];

// CLASS select with data;
echo "<script>
	  var msclass=document.getElementById('msclassid');
	  var opt_non=document.createElement('option');
		  opt_non.value='';
		  opt_non.text='';
	      msclass.options.add(opt_non);
	  </script>";
$con->set_charset("utf8"); // SET FONT TO "utf-8"
$sqlclass = "SELECT class FROM tbcstudents WHERE degree='$dgree' AND stuarea='$area' GROUP BY class";
$rclass = mysqli_query($con,$sqlclass) or die(mysqli_connect_error());
while($r=mysqli_fetch_array($rclass)){
	$cid = $r["class"];
	$cname = Rclassname($cid, $con);
	echo "<script>
	      var cid ='$cid';
		  var cname = '$cname';
		  var opt_non=document.createElement('option');
		  opt_non.value=cid;
		  opt_non.text=cname;
	      msclass.options.add(opt_non);
	      </script>";
}
?>
